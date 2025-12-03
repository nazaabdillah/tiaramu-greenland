<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { 
    CurrencyDollarIcon, 
    UserGroupIcon, 
    ClockIcon, 
    CheckBadgeIcon,
    TrashIcon,
    CheckCircleIcon
} from '@heroicons/vue/24/outline';
import { computed } from 'vue';

const props = defineProps({
    bookings: Array
});

// Helper Format Rupiah
const formatRupiah = (number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(number);
};

// Helper Format Tanggal (Indonesia)
const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit'
    });
};

// Hitung Statistik Sederhana
const stats = computed(() => {
    const total = props.bookings.length;
    const paid = props.bookings.filter(b => b.status_pembayaran === 'paid').length;
    const pending = props.bookings.filter(b => ['unpaid', 'pending'].includes(b.status_pembayaran)).length;
    // Hitung total duit masuk (hanya yang paid)
    const revenue = props.bookings
        .filter(b => b.status_pembayaran === 'paid')
        .reduce((sum, b) => sum + Number(b.total_harga), 0);
        
    return { total, paid, pending, revenue };
});

// Aksi Tombol
const approveBooking = (id) => {
    if (confirm('Yakin ingin menyetujui pembayaran ini? Status kavling akan menjadi SOLD.')) {
        router.post(route('admin.booking.approve', id));
    }
};

const deleteBooking = (id) => {
    if (confirm('Hapus data booking ini? Kavling akan kembali Available.')) {
        router.delete(route('admin.booking.destroy', id));
    }
};

// Kirim WA ke Pembeli
const sendWa = (phone, name) => {
    const msg = `Halo kak ${name}, kami dari Tiaramu Greenland ingin mengonfirmasi pesanan Anda...`;
    window.open(`https://wa.me/${phone}?text=${encodeURIComponent(msg)}`, '_blank');
};
</script>

<template>
    <Head title="Data Transaksi" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-black text-2xl text-slate-800 leading-tight tracking-tight drop-shadow-sm">
                📄 Data Transaksi
            </h2>
        </template>

        <div class="py-12 bg-slate-50 min-h-screen relative overflow-hidden">
            <div class="absolute top-0 right-1/4 w-96 h-96 bg-blue-300/20 rounded-full blur-3xl pointer-events-none"></div>

            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 relative z-10">
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
                    
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-emerald-100 flex flex-col justify-between transition hover:-translate-y-1">
                        <div class="flex items-center gap-4 mb-2">
                            <div class="p-3 bg-emerald-100 text-emerald-600 rounded-xl"><CurrencyDollarIcon class="w-6 h-6" /></div>
                            <span class="text-xs font-bold text-emerald-400 uppercase tracking-widest">Total Revenue</span>
                        </div>
                        <p class="text-2xl font-black text-emerald-700">{{ formatRupiah(stats.revenue) }}</p>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4 transition hover:-translate-y-1">
                        <div class="p-3 bg-slate-100 text-slate-600 rounded-xl"><UserGroupIcon class="w-8 h-8" /></div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Total Order</p>
                            <p class="text-2xl font-black text-slate-800">{{ stats.total }}</p>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-amber-100 flex items-center gap-4 transition hover:-translate-y-1">
                        <div class="p-3 bg-amber-100 text-amber-600 rounded-xl"><ClockIcon class="w-8 h-8" /></div>
                        <div>
                            <p class="text-xs font-bold text-amber-400 uppercase tracking-widest">Menunggu Bayar</p>
                            <p class="text-2xl font-black text-amber-700">{{ stats.pending }}</p>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-blue-100 flex items-center gap-4 transition hover:-translate-y-1">
                        <div class="p-3 bg-blue-100 text-blue-600 rounded-xl"><CheckBadgeIcon class="w-8 h-8" /></div>
                        <div>
                            <p class="text-xs font-bold text-blue-400 uppercase tracking-widest">Sukses / Lunas</p>
                            <p class="text-2xl font-black text-blue-700">{{ stats.paid }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white/90 backdrop-blur-lg overflow-hidden shadow-2xl sm:rounded-3xl border border-white/60">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead class="bg-slate-50/80">
                                <tr>
                                    <th class="px-8 py-5 text-left text-xs font-extrabold text-slate-400 uppercase tracking-wider">Pembeli</th>
                                    <th class="px-6 py-5 text-left text-xs font-extrabold text-slate-400 uppercase tracking-wider">Unit Kavling</th>
                                    <th class="px-6 py-5 text-left text-xs font-extrabold text-slate-400 uppercase tracking-wider">Total Harga</th>
                                    <th class="px-6 py-5 text-center text-xs font-extrabold text-slate-400 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-5 text-center text-xs font-extrabold text-slate-400 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-8 py-5 text-right text-xs font-extrabold text-slate-400 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                <tr v-for="item in bookings" :key="item.id" class="group hover:bg-white transition duration-200">
                                    
                                    <td class="px-8 py-5 whitespace-nowrap">
                                        <div>
                                            <div class="text-sm font-bold text-slate-800">{{ item.nama_pembeli }}</div>
                                            <button @click="sendWa(item.nomor_wa, item.nama_pembeli)" class="text-xs text-emerald-600 hover:text-emerald-800 hover:underline flex items-center gap-1 mt-1">
                                                <span>📱</span> {{ item.nomor_wa }}
                                            </button>
                                        </div>
                                    </td>

                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="h-10 w-10 rounded-lg bg-slate-100 flex items-center justify-center font-black text-slate-600 text-xs border border-slate-200">
                                                {{ item.kavling?.kode_kavling }}
                                            </div>
                                            <div class="text-xs text-slate-500">
                                                Tipe {{ item.kavling?.tipe_rumah }}
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <div class="text-sm font-bold text-slate-700">
                                            {{ formatRupiah(item.total_harga) }}
                                        </div>
                                    </td>

                                    <td class="px-6 py-5 whitespace-nowrap text-center">
                                        <span 
                                            class="px-3 py-1 inline-flex text-[10px] font-bold rounded-full uppercase tracking-wide border"
                                            :class="{
                                                'bg-emerald-50 text-emerald-700 border-emerald-200': item.status_pembayaran === 'paid',
                                                'bg-amber-50 text-amber-700 border-amber-200': ['unpaid', 'pending'].includes(item.status_pembayaran),
                                                'bg-red-50 text-red-700 border-red-200': ['expired', 'cancel'].includes(item.status_pembayaran),
                                            }"
                                        >
                                            {{ item.status_pembayaran }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-5 whitespace-nowrap text-center">
                                        <div class="text-xs text-slate-500">
                                            {{ formatDate(item.created_at) }}
                                        </div>
                                    </td>

                                    <td class="px-8 py-5 whitespace-nowrap text-right text-sm font-medium flex justify-end gap-2">
                                        
                                        <button 
                                            v-if="item.status_pembayaran !== 'paid'"
                                            @click="approveBooking(item.id)"
                                            class="p-2 bg-emerald-100 text-emerald-600 rounded-lg hover:bg-emerald-200 transition tooltip"
                                            title="Konfirmasi Lunas"
                                        >
                                            <CheckCircleIcon class="w-5 h-5" />
                                        </button>

                                        <button 
                                            @click="deleteBooking(item.id)"
                                            class="p-2 bg-red-50 text-red-500 rounded-lg hover:bg-red-100 transition tooltip"
                                            title="Hapus / Batalkan"
                                        >
                                            <TrashIcon class="w-5 h-5" />
                                        </button>
                                    </td>
                                </tr>

                                <tr v-if="bookings.length === 0">
                                    <td colspan="6" class="px-8 py-12 text-center text-slate-400 text-sm">
                                        Belum ada transaksi masuk.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>