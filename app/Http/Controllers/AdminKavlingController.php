<?php

namespace App\Http\Controllers;

use App\Models\Kavling;
use App\Models\KavlingGallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class AdminKavlingController extends Controller
{
    // 1. HALAMAN INDEX: Menampilkan daftar semua kavling
    public function index(Request $request)
    {
        // 1. Tangkap input pencarian & filter dari Vue
        $search = $request->input('search');
        $filterStatus = $request->input('status');

        // 2. Query Dasar
        $query = Kavling::with('galleries')
            ->orderBy('blok', 'asc')
            ->orderBy('nomor', 'asc');

        // 3. Logika Pencarian (Jika ada ketikan)
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('kode_kavling', 'like', "%{$search}%")
                  ->orWhere('tipe_rumah', 'like', "%{$search}%");
            });
        }

        // 4. Logika Filter Status (Jika ada pilihan)
        if ($filterStatus && $filterStatus !== 'all') {
            $query->where('status', $filterStatus);
        }

        // 5. Eksekusi (Get Data)
        $kavlings = $query->get();

        // 6. Kirim balik ke Vue (Sertakan filters biar inputnya gak ilang pas reload)
        return Inertia::render('Admin/Kavling/Index', [
            'kavlings' => $kavlings,
            'filters' => $request->only(['search', 'status']), // Kirim balik state filter
        ]);
    }

    // 2. HALAMAN EDIT: Form untuk edit harga, status, & upload foto
    public function edit($id)
    {
        // Ambil kavling + foto-fotonya
        $kavling = Kavling::with('galleries')->findOrFail($id);

        return Inertia::render('Admin/Kavling/Edit', [
            'kavling' => $kavling
        ]);
    }

    // 3. UPDATE DATA: Simpan perubahan harga/status
    public function update(Request $request, $id)
    {
        $kavling = Kavling::findOrFail($id);
        
        // Update data dasar
        $kavling->update([
            'harga' => $request->harga,
            'status' => $request->status,
            // luas_tanah dll biasanya fix, jadi gak usah diedit dulu gpp
        ]);

        return redirect()->route('admin.kavling.index');
    }

    // 4. UPLOAD FOTO: Khusus menangani upload gambar baru
    public function uploadPhoto(Request $request, $id)
    {
        $request->validate([
            'photo' => 'required|image|max:2048', // Maksimal 2MB
        ]);

        $kavling = Kavling::findOrFail($id);

        // Simpan file ke folder 'public/kavling_images'
        $path = $request->file('photo')->store('kavling_images', 'public');

        // Catat di database
        KavlingGallery::create([
            'kavling_id' => $kavling->id,
            'image_path' => '/storage/' . $path, // Tambahkan '/storage/' biar bisa dibaca browser
            'caption'    => 'Foto Kavling ' . $kavling->kode_kavling
        ]);

        // Redirect balik (biar admin bisa lihat hasilnya langsung)
        return back();
    }

    // 5. HAPUS FOTO
    public function deletePhoto($photoId)
    {
        $photo = KavlingGallery::findOrFail($photoId);

        // Hapus file fisik dari harddisk (Opsional, biar hemat space)
        // Kita perlu membuang prefix '/storage/' dulu untuk dapat path aslinya
        $realPath = str_replace('/storage/', '', $photo->image_path);
        Storage::disk('public')->delete($realPath);

        // Hapus record di database
        $photo->delete();

        return back();
    }
}