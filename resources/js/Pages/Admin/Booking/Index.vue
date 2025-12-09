<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { 
    CurrencyDollarIcon, 
    UserGroupIcon, 
    ClockIcon, 
    CheckBadgeIcon,
    TrashIcon,
    CheckCircleIcon,
    PrinterIcon,
    ChatBubbleLeftRightIcon
} from '@heroicons/vue/24/outline';
import { computed } from 'vue';
import Swal from 'sweetalert2';

const props = defineProps({
    bookings: Array
});

// --- HELPERS ---
const calculateDebt = (total, paid) => {
    return Number(total) - Number(paid);
};

const formatRupiah = (number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(number);
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'
    });
};

// --- LOGIC STATISTIK (Gabungan) ---
const stats = computed(() => {
    const total = props.bookings.length;
    const paid = props.bookings.filter(b => b.status_pembayaran === 'paid').length;
    const pending = props.bookings.filter(b => ['unpaid', 'pending'].includes(b.status_pembayaran)).length;
    
    // Revenue = Sum of nominal_bayar (Uang Real Masuk)
    const revenue = props.bookings
        .filter(b => b.status_pembayaran === 'paid')
        .reduce((sum, b) => sum + Number(b.nominal_bayar || 0), 0);
        
    return { total, paid, pending, revenue };
});

// --- SWEETALERT SETUP ---
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    background: '#ffffff',
    color: '#1e293b',
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
});

// --- ACTIONS ---
const approveBooking = (id) => {
    Swal.fire({
        title: 'Konfirmasi Pembayaran?',
        text: "Pastikan uang sudah masuk. Status kavling akan berubah menjadi SOLD.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#94a3b8',
        confirmButtonText: 'Ya, Validasi Lunas!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(route('admin.booking.approve', id), {}, {
                onSuccess: () => {
                    Toast.fire({ icon: 'success', title: 'Pembayaran Berhasil Divalidasi!' });
                }
            });
        }
    });
};

const deleteBooking = (id) => {
    Swal.fire({
        title: 'Hapus Transaksi?',
        text: "Data yang dihapus tidak bisa dikembalikan. Kavling akan kembali Available.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#94a3b8',
        confirmButtonText: 'Ya, Hapus Data',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        focusCancel: true
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('admin.booking.destroy', id), {
                onSuccess: () => {
                    Toast.fire({ icon: 'success', title: 'Data Transaksi Dihapus' });
                }
            });
        }
    });
};

const sendWa = (phone, name) => {
    const msg = `Halo kak ${name}, kami dari Tiaramu Greenland ingin mengonfirmasi pesanan Anda...`;
    window.open(`https://wa.me/${phone}?text=${encodeURIComponent(msg)}`, '_blank');
};
</script>

<template>
    <Head title="Dashboard Transaksi" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div>
                    <h2 class="font-black text-2xl text-slate-800 leading-tight">
                        Dashboard Transaksi
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">Monitoring penjualan kavling real-time</p>
                </div>
                
                <a :href="route('admin.booking.export')" target="_blank" 
                   class="group flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-slate-200 transition-all hover:-translate-y-0.5 active:translate-y-0">
                    <PrinterIcon class="w-5 h-5 group-hover:animate-pulse" />
                    Cetak Laporan
                </a>
            </div>
        </template>

        <div class="py-12 bg-slate-50/50 min-h-screen relative overflow-hidden">
            <div class="absolute -top-20 -right-20 w-96 h-96 bg-emerald-100/40 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute top-40 -left-20 w-72 h-72 bg-blue-100/40 rounded-full blur-3xl pointer-events-none"></div>

            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 relative z-10">
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
                    <div class="bg-white p-6 rounded-2xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-slate-100 flex flex-col justify-between transition-all duration-300 hover:shadow-lg hover:-translate-y-1 group">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                                <CurrencyDollarIcon class="w-6 h-6" />
                            </div>
                            <span class="text-[10px] font-extrabold text-emerald-600/60 bg-emerald-50 px-2 py-1 rounded-full uppercase tracking-wider">Keuangan</span>
                        </div>
                        <div>
                            <p class="text-slate-400 text-xs font-semibold mb-1">Total Pemasukan</p>
                            <h3 class="text-2xl font-black text-slate-800">{{ formatRupiah(stats.revenue) }}</h3>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-slate-100 flex flex-col justify-between transition-all duration-300 hover:shadow-lg hover:-translate-y-1 group">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                                <UserGroupIcon class="w-6 h-6" />
                            </div>
                        </div>
                        <div>
                            <p class="text-slate-400 text-xs font-semibold mb-1">Total Booking</p>
                            <h3 class="text-2xl font-black text-slate-800">{{ stats.total }} <span class="text-sm font-normal text-slate-400">Unit</span></h3>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-slate-100 flex flex-col justify-between transition-all duration-300 hover:shadow-lg hover:-translate-y-1 group">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-amber-50 text-amber-600 rounded-xl group-hover:bg-amber-500 group-hover:text-white transition-colors">
                                <ClockIcon class="w-6 h-6" />
                            </div>
                            <span class="text-[10px] font-extrabold text-amber-600/60 bg-amber-50 px-2 py-1 rounded-full uppercase tracking-wider">Action Needed</span>
                        </div>
                        <div>
                            <p class="text-slate-400 text-xs font-semibold mb-1">Menunggu Bayar</p>
                            <h3 class="text-2xl font-black text-slate-800">{{ stats.pending }} <span class="text-sm font-normal text-slate-400">Transaksi</span></h3>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-slate-100 flex flex-col justify-between transition-all duration-300 hover:shadow-lg hover:-translate-y-1 group">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-blue-50 text-blue-600 rounded-xl group-hover:bg-blue-600 group-hover:text-white transition-colors">
                                <CheckBadgeIcon class="w-6 h-6" />
                            </div>
                        </div>
                        <div>
                            <p class="text-slate-400 text-xs font-semibold mb-1">Pembayaran Sukses</p>
                            <h3 class="text-2xl font-black text-slate-800">{{ stats.paid }} <span class="text-sm font-normal text-slate-400">Transaksi</span></h3>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
                    <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-center bg-white">
                        <h3 class="font-bold text-slate-700">Daftar Transaksi Terbaru</h3>
                        <div class="text-xs text-slate-400 italic">Menampilkan {{ bookings.length }} data</div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead>
                                <tr class="bg-slate-50/50">
                                    <th class="px-8 py-5 text-left text-xs font-extrabold text-slate-400 uppercase tracking-wider">Info Pembeli</th>
                                    <th class="px-6 py-5 text-left text-xs font-extrabold text-slate-400 uppercase tracking-wider">Unit & Tipe</th>
                                    <th class="px-6 py-5 text-left text-xs font-extrabold text-slate-400 uppercase tracking-wider">Nilai Transaksi</th>
                                    <th class="px-6 py-5 text-center text-xs font-extrabold text-slate-400 uppercase tracking-wider">Status Bayar</th>
                                    <th class="px-6 py-5 text-center text-xs font-extrabold text-slate-400 uppercase tracking-wider">Waktu</th>
                                    <th class="px-8 py-5 text-right text-xs font-extrabold text-slate-400 uppercase tracking-wider">Kontrol</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                <tr v-for="item in bookings" :key="item.id" class="group hover:bg-slate-50/80 transition duration-200">
                                    
                                    <td class="px-8 py-5 align-top">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-slate-800">{{ item.nama_pembeli }}</span>
                                            <button @click="sendWa(item.nomor_wa, item.nama_pembeli)" 
                                                class="text-xs text-slate-500 hover:text-emerald-600 hover:font-bold flex items-center gap-1 mt-1 transition-all w-fit">
                                                <ChatBubbleLeftRightIcon class="w-3 h-3" /> {{ item.nomor_wa }}
                                            </button>
                                        </div>
                                    </td>

                                    <td class="px-6 py-5 align-top">
                                        <div class="flex items-center gap-3">
                                            <div class="h-10 w-10 min-w-[2.5rem] rounded-xl bg-slate-100 flex items-center justify-center font-black text-slate-600 text-xs border border-slate-200 group-hover:border-slate-300 group-hover:bg-white transition-colors">
                                                {{ item.kavling?.kode_kavling }}
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-xs font-bold text-slate-700">Tipe {{ item.kavling?.tipe_rumah }}</span>
                                                <span class="text-[10px] text-slate-400">Cluster A</span>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-5 align-top">
                                        <div class="flex flex-col">
                                            <span class="text-xs text-slate-400">Harga Unit:</span>
                                            <span class="text-sm font-bold text-slate-700 font-mono">
                                                {{ formatRupiah(item.total_harga) }}
                                            </span>

                                            <div class="h-px bg-slate-200 my-2"></div>

                                            <div v-if="item.status_pembayaran === 'paid'">
                                                <div class="flex justify-between text-xs gap-4">
                                                    <span class="text-emerald-600 font-bold">Masuk:</span>
                                                    <span class="text-emerald-600">{{ formatRupiah(item.nominal_bayar || 0) }}</span>
                                                </div>
                                                <div class="flex justify-between text-xs mt-0.5 gap-4">
                                                    <span class="text-rose-500 font-bold">Sisa:</span>
                                                    <span class="text-rose-500 font-bold">{{ formatRupiah(calculateDebt(item.total_harga, item.nominal_bayar || 0)) }}</span>
                                                </div>
                                            </div>
                                            <div v-else>
                                                <span class="text-xs text-slate-400 italic">Belum ada pembayaran</span>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-5 align-top text-center">
                                        <span class="px-3 py-1.5 inline-flex items-center justify-center gap-1.5 text-[10px] font-bold rounded-full uppercase tracking-wide border shadow-sm"
                                            :class="{
                                                'bg-emerald-50 text-emerald-700 border-emerald-100': item.status_pembayaran === 'paid',
                                                'bg-amber-50 text-amber-700 border-amber-100': ['unpaid', 'pending'].includes(item.status_pembayaran),
                                                'bg-rose-50 text-rose-700 border-rose-100': ['expired', 'cancel'].includes(item.status_pembayaran),
                                            }">
                                            <span class="w-1.5 h-1.5 rounded-full" :class="{
                                                'bg-emerald-500': item.status_pembayaran === 'paid',
                                                'bg-amber-500': ['unpaid', 'pending'].includes(item.status_pembayaran),
                                                'bg-rose-500': ['expired', 'cancel'].includes(item.status_pembayaran),
                                            }"></span>
                                            {{ item.status_pembayaran }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-5 align-top text-center">
                                        <div class="flex flex-col items-center">
                                            <span class="text-xs font-semibold text-slate-600">{{ formatDate(item.created_at).split(',')[0] }}</span>
                                            <span class="text-[10px] text-slate-400">{{ formatDate(item.created_at).split(',')[1] }}</span>
                                        </div>
                                    </td>

                                    <td class="px-8 py-5 align-top">
                                        <div class="flex justify-end items-center gap-2">
                                            <button 
                                                v-if="item.status_pembayaran !== 'paid'"
                                                @click="approveBooking(item.id)"
                                                class="p-2 text-emerald-600 bg-emerald-50 rounded-lg hover:bg-emerald-600 hover:text-white transition-all duration-200 shadow-sm tooltip"
                                                title="Verifikasi Manual Lunas">
                                                <CheckCircleIcon class="w-5 h-5" />
                                            </button>

                                            <button 
                                                @click="deleteBooking(item.id)"
                                                class="p-2 text-rose-500 bg-rose-50 rounded-lg hover:bg-rose-600 hover:text-white transition-all duration-200 shadow-sm tooltip"
                                                title="Hapus Data">
                                                <TrashIcon class="w-5 h-5" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <tr v-if="bookings.length === 0">
                                    <td colspan="6" class="px-8 py-16 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                                                <ClockIcon class="w-8 h-8 text-slate-300" />
                                            </div>
                                            <h3 class="text-slate-800 font-bold">Belum ada transaksi</h3>
                                            <p class="text-slate-400 text-sm">Data booking akan muncul di sini.</p>
                                        </div>
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