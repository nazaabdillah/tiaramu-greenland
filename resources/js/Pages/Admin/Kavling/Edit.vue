<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    kavling: Object,
});

// 1. FORM DATA UTAMA (Harga & Status)
const form = useForm({
    harga: props.kavling.harga,
    status: props.kavling.status,
});

// 2. FORM KHUSUS UPLOAD FOTO
const photoForm = useForm({
    photo: null,
});

// Fungsi Update Data Dasar
const updateKavling = () => {
    form.put(route('admin.kavling.update', props.kavling.id), {
        onSuccess: () => alert('Data berhasil diupdate!'),
    });
};

// Fungsi Upload Foto
const uploadPhoto = () => {
    if (!photoForm.photo) return;
    
    photoForm.post(route('admin.kavling.photo.store', props.kavling.id), {
        onSuccess: () => {
            photoForm.reset();
            // Refresh halaman biar foto baru muncul
            router.reload(); 
        },
    });
};

// Fungsi Hapus Foto
const deletePhoto = (photoId) => {
    if (confirm('Yakin mau hapus foto ini?')) {
        router.delete(route('admin.kavling.photo.delete', photoId));
    }
};
</script>

<template>
    <Head title="Edit Kavling" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Edit Kavling: {{ kavling.kode_kavling }}
                </h2>
                <button @click="$inertia.visit(route('admin.kavling.index'))" class="text-sm text-gray-500 hover:text-gray-700">
                    &larr; Kembali
                </button>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 h-fit">
                    <h3 class="text-lg font-bold mb-4">⚙️ Data Utama</h3>
                    
                    <form @submit.prevent="updateKavling" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tipe Rumah</label>
                            <input type="text" :value="kavling.tipe_rumah" disabled class="mt-1 block w-full bg-gray-100 border-gray-300 rounded-md shadow-sm cursor-not-allowed">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Harga (Rupiah)</label>
                            <input v-model="form.harga" type="number" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <select v-model="form.status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="available">Available (Hijau)</option>
                                <option value="booking">Booking (Kuning)</option>
                                <option value="sold">Sold (Merah)</option>
                            </select>
                        </div>

                        <div class="pt-4">
                            <button type="submit" class="w-full py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-4">📸 Galeri Foto</h3>
                    
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Upload Foto Baru</label>
                        <div class="flex gap-2">
                            <input 
                                type="file" 
                                @input="photoForm.photo = $event.target.files[0]"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                            />
                            <button 
                                @click="uploadPhoto" 
                                :disabled="photoForm.processing"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm disabled:opacity-50"
                            >
                                Upload
                            </button>
                        </div>
                        <p v-if="photoForm.progress" class="text-xs text-indigo-600 mt-1">Uploading... {{ photoForm.progress.percentage }}%</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div v-for="foto in kavling.galleries" :key="foto.id" class="relative group">
                            <img :src="foto.image_path" class="w-full h-32 object-cover rounded-lg shadow-sm">
                            
                            <button 
                                @click="deletePhoto(foto.id)"
                                class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition shadow-md"
                                title="Hapus Foto"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div v-if="kavling.galleries.length === 0" class="col-span-2 text-center py-8 text-gray-400 text-sm">
                            Belum ada foto.
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>