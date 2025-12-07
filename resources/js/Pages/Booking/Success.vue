<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { CheckBadgeIcon, PrinterIcon, HomeIcon, CalculatorIcon } from '@heroicons/vue/24/solid';
import { computed } from 'vue';

const props = defineProps({
    booking: Object
});

const formatRupiah = (number) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
};

const bookingFee = 2000000; 
const hargaUnit = props.booking.kavling.harga;
const sisaTagihan = computed(() => hargaUnit - bookingFee);
</script>

<template>
    <Head title="Booking Berhasil" />

    <div class="min-h-screen bg-slate-50 flex items-center justify-center p-4 relative overflow-hidden font-sans">
        <div class="absolute inset-0 bg-premium-dots opacity-50 pointer-events-none"></div>
        <div class="absolute top-[-20%] left-[-10%] w-[500px] h-[500px] bg-amber-300/30 rounded-full blur-3xl"></div>

        <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl overflow-hidden relative z-10 animate-fade-in-up border border-slate-100">
            
            <div class="bg-gradient-to-br from-amber-400 to-orange-500 p-8 text-center text-white relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-full bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
                
                <div class="bg-white/20 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 backdrop-blur-md shadow-lg border border-white/20">
                    <CheckBadgeIcon class="w-10 h-10 text-white" />
                </div>
                <h1 class="text-2xl font-black tracking-tight uppercase">Booking Confirmed!</h1>
                <p class="text-amber-50 text-sm mt-1">Unit ini resmi dikunci atas nama Anda.</p>
            </div>

            <div class="p-8">
                <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 mb-6">
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-slate-500">Harga Unit</span>
                        <span class="font-bold text-slate-700">{{ formatRupiah(hargaUnit) }}</span>
                    </div>
                    <div class="flex justify-between text-sm mb-4">
                        <span class="text-emerald-600 font-bold flex items-center gap-1"><CheckBadgeIcon class="w-4 h-4"/> Sudah Dibayar (Booking Fee)</span>
                        <span class="font-bold text-emerald-600">- {{ formatRupiah(bookingFee) }}</span>
                    </div>
                    <div class="border-t border-slate-200 pt-3 flex justify-between items-center">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Sisa Pelunasan</span>
                        <span class="text-xl font-black text-slate-800">{{ formatRupiah(sisaTagihan) }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-y-4 gap-x-2 text-sm mb-8">
                    <div>
                        <p class="text-[10px] text-slate-400 uppercase font-bold mb-0.5">Nama Pembeli</p>
                        <p class="font-bold text-slate-700 truncate">{{ booking.nama_pembeli }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 uppercase font-bold mb-0.5">Kode Referensi</p>
                        <p class="font-mono font-bold text-slate-600 text-xs bg-slate-100 px-2 py-0.5 rounded w-fit">{{ booking.midtrans_booking_id }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 uppercase font-bold mb-0.5">Unit Terkunci</p>
                        <p class="font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-md inline-block border border-amber-100">
                            Blok {{ booking.kavling.kode_kavling }}
                        </p>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 uppercase font-bold mb-0.5">Jatuh Tempo</p>
                        <p class="font-bold text-red-500 text-xs">
                            {{ new Date(new Date(booking.created_at).getTime() + (14 * 24 * 60 * 60 * 1000)).toLocaleDateString('id-ID') }}
                        </p>
                    </div>
                </div>

                <div class="bg-blue-50 border border-blue-100 p-4 rounded-xl mb-6 text-xs text-blue-800">
                    <p class="font-bold mb-1 flex items-center gap-2"><CalculatorIcon class="w-4 h-4"/> Instruksi:</p>
                    <p class="opacity-80">Silakan unduh invoice resmi di bawah ini untuk proses administrasi pelunasan.</p>
                </div>

                <div class="space-y-3">
                    <a :href="route('booking.invoice', booking.id)" target="_blank" class="w-full py-3 bg-slate-800 text-white font-bold rounded-xl hover:bg-slate-900 transition flex justify-center items-center gap-2 text-sm shadow-lg cursor-pointer">
                        <PrinterIcon class="w-4 h-4" /> Download Invoice Resmi (PDF)
                    </a>
                    
                    <Link :href="route('home')" class="w-full py-3 bg-white text-slate-600 font-bold rounded-xl hover:bg-slate-50 border border-slate-200 transition flex justify-center items-center gap-2 text-sm">
                        <HomeIcon class="w-4 h-4" /> Kembali ke Peta
                    </Link>
                </div>
            </div>

            <div class="h-4 bg-slate-50 relative -mb-2" style="background: linear-gradient(45deg, transparent 33.333%, #ffffff 33.333%, #ffffff 66.667%, transparent 66.667%), linear-gradient(-45deg, transparent 33.333%, #ffffff 33.333%, #ffffff 66.667%, transparent 66.667%); background-size: 20px 40px; transform: rotate(180deg)"></div>
        </div>
    </div>
</template>

<style scoped>
.bg-premium-dots { background-image: radial-gradient(#94a3b8 1px, transparent 1px); background-size: 30px 30px; }
.animate-fade-in-up { animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
@keyframes fadeInUp { from { opacity: 0; transform: translateY(40px); } to { opacity: 1; transform: translateY(0); } }
</style>