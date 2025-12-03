<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';

const isLoading = ref(false);

// Listener Global Inertia
// Saat mulai navigasi -> Muncul
// Saat selesai -> Hilang
let removeStartListener = null;
let removeFinishListener = null;

onMounted(() => {
    removeStartListener = router.on('start', () => {
        isLoading.value = true;
    });

    removeFinishListener = router.on('finish', () => {
        // Kasih delay dikit biar ga kedip kecepetan kalau internet kenceng
        setTimeout(() => {
            isLoading.value = false;
        }, 300); 
    });
});

onUnmounted(() => {
    if (removeStartListener) removeStartListener();
    if (removeFinishListener) removeFinishListener();
});
</script>

<template>
    <Transition name="fade">
        <div v-if="isLoading" class="fixed inset-0 z-[99999] flex items-center justify-center bg-white/60 backdrop-blur-sm">
            
            <div class="relative flex items-center justify-center">
                <div class="absolute w-24 h-24 border-4 border-emerald-200 rounded-full"></div>
                <div class="absolute w-24 h-24 border-4 border-emerald-500 rounded-full border-t-transparent animate-spin"></div>
                
                <div class="relative z-10 bg-white rounded-full p-3 shadow-lg animate-pulse-slow">
                    <span class="text-3xl">🏡</span>
                </div>
            </div>

            <div class="absolute mt-32 text-emerald-700 font-bold text-sm tracking-widest animate-pulse">
                MEMUAT DATA...
            </div>

        </div>
    </Transition>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

/* Animasi Denyut Lambat */
@keyframes pulse-slow {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}
.animate-pulse-slow {
    animation: pulse-slow 2s infinite;
}
</style>