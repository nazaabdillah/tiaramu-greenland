<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';

const isLoading = ref(false);
let removeStartListener = null;
let removeFinishListener = null;

onMounted(() => {
    removeStartListener = router.on('start', () => isLoading.value = true);
    removeFinishListener = router.on('finish', () => setTimeout(() => isLoading.value = false, 300));
});

onUnmounted(() => {
    if (removeStartListener) removeStartListener();
    if (removeFinishListener) removeFinishListener();
});
</script>

<template>
    <Transition name="slide-fade">
        <div v-if="isLoading" class="fixed bottom-6 right-6 z-[99999] flex items-center gap-3 bg-white/90 backdrop-blur-md px-5 py-3 rounded-full shadow-2xl border border-slate-200">
            
            <div class="w-4 h-4 border-2 border-slate-100 border-t-emerald-500 rounded-full animate-spin"></div>
            
            <span class="text-[10px] font-bold tracking-widest text-slate-600 uppercase">Memuat...</span>
        </div>
    </Transition>
</template>

<style scoped>
.slide-fade-enter-active, .slide-fade-leave-active { transition: all 0.3s ease; }
.slide-fade-enter-from, .slide-fade-leave-to { transform: translateY(20px); opacity: 0; }
</style>