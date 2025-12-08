<script setup>
import { ref, computed } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import { BellIcon, ArrowDownTrayIcon, CheckCircleIcon } from '@heroicons/vue/24/solid';

const isOpen = ref(false);
const page = usePage();

// Ambil semua notifikasi dari backend
const allNotifications = computed(() => page.props.auth.notifications || []);

// Hitung jumlah yang BELUM dibaca (read_at == null)
const unreadCount = computed(() => {
    return allNotifications.value.filter(n => n.read_at === null).length;
});

const formatRupiah = (angka) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(angka);
}

const handleReadAndDownload = (notif) => {
    // 1. Download File (Buka di tab baru biar aman)
    if (notif.data.download_url) {
        window.open(notif.data.download_url, '_blank');
    }

    // 2. Kalau belum dibaca, tandai sudah dibaca di database
    if (!notif.read_at) {
        router.post(route('notifications.read', notif.id), {}, {
            preserveScroll: true,
            // Kita tidak menutup dropdown biar user bisa lihat perubahan statusnya
        });
    }
};
</script>

<template>
    <div class="relative">
        <button @click="isOpen = !isOpen" 
                class="relative p-2 rounded-full text-gray-500 hover:bg-emerald-50 hover:text-emerald-600 transition focus:outline-none group">
            
            <BellIcon class="w-6 h-6" :class="{ 'animate-wiggle text-emerald-600': unreadCount > 0 }" />

            <span v-if="unreadCount > 0" 
                  class="absolute top-1 right-1 flex h-3 w-3">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500 border-2 border-white"></span>
            </span>
        </button>

        <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0 translate-y-1"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 translate-y-1"
        >
            <div v-if="isOpen" 
                 class="absolute right-0 mt-3 w-80 sm:w-96 bg-white rounded-2xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.2)] border border-gray-100 z-50 overflow-hidden origin-top-right">
                
                <div class="px-5 py-4 bg-white border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-wide">Notifikasi</h3>
                    <span v-if="unreadCount > 0" class="text-[10px] font-bold bg-red-100 text-red-600 px-2 py-1 rounded-full">{{ unreadCount }} Baru</span>
                </div>

                <div class="max-h-[350px] overflow-y-auto custom-scrollbar bg-slate-50">
                    
                    <div v-if="allNotifications.length === 0" class="py-10 text-center flex flex-col items-center opacity-50">
                        <BellIcon class="w-10 h-10 text-slate-300 mb-2" />
                        <p class="text-slate-500 text-xs font-bold">Belum ada notifikasi</p>
                    </div>

                    <div v-else 
                         v-for="notif in allNotifications" 
                         :key="notif.id"
                         @click="handleReadAndDownload(notif)"
                         class="p-4 border-b border-gray-100 cursor-pointer transition duration-200 relative group"
                         :class="notif.read_at ? 'bg-white hover:bg-slate-50' : 'bg-emerald-50/60 hover:bg-emerald-50'">
                        
                        <div v-if="!notif.read_at" class="absolute left-0 top-0 bottom-0 w-1 bg-emerald-500"></div>

                        <div class="flex gap-3">
                            <div class="flex-shrink-0 mt-1">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center"
                                     :class="notif.read_at ? 'bg-slate-100 text-slate-400' : 'bg-emerald-100 text-emerald-600'">
                                    <CheckCircleIcon class="w-5 h-5" />
                                </div>
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-start">
                                    <p class="text-sm font-bold truncate" :class="notif.read_at ? 'text-slate-600' : 'text-slate-800'">
                                        {{ notif.data.title }}
                                    </p>
                                    <span class="text-[10px] text-slate-400 whitespace-nowrap ml-2">
                                        {{ new Date(notif.created_at).toLocaleDateString('id-ID', {day: 'numeric', month: 'short'}) }}
                                    </span>
                                </div>
                                
                                <p class="text-xs text-slate-500 mt-0.5 line-clamp-2 leading-relaxed">
                                    {{ notif.data.message }}
                                </p>

                                <div v-if="notif.data.sisa_hutang" class="mt-2 flex items-center gap-2">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase">Sisa:</span>
                                    <span class="text-xs font-black text-slate-700 bg-slate-100 px-1.5 py-0.5 rounded">
                                        {{ formatRupiah(notif.data.sisa_hutang) }}
                                    </span>
                                </div>
                                
                                <button class="mt-3 flex items-center gap-1.5 text-[10px] font-bold text-emerald-600 bg-emerald-50 hover:bg-emerald-100 px-3 py-1.5 rounded-lg transition w-fit">
                                    <ArrowDownTrayIcon class="w-3 h-3" />
                                    Download Struk
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>

        <div v-if="isOpen" @click="isOpen = false" class="fixed inset-0 z-40 bg-transparent cursor-default"></div>
    </div>
</template>

<style scoped>
/* Animasi Lonceng Goyang (Wiggle) */
@keyframes wiggle {
    0%, 100% { transform: rotate(0deg); }
    10% { transform: rotate(-10deg); }
    30% { transform: rotate(10deg); }
    50% { transform: rotate(-10deg); }
    70% { transform: rotate(10deg); }
}

.animate-wiggle {
    animation: wiggle 2s infinite ease-in-out;
    transform-origin: top center;
}

/* Scrollbar Halus */
.custom-scrollbar::-webkit-scrollbar { width: 5px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>