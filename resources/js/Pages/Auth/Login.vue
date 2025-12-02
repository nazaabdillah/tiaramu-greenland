<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { Vue3Lottie } from 'vue3-lottie';

defineProps({
    canResetPassword: { type: Boolean },
    status: { type: String },
});

const showForm = ref(false);

// ✅ FIX: KEMBALI KE LINK YANG TERBUKTI JALAN
const wavingAnimation = 'https://raw.githubusercontent.com/thesvbd/Lottie-examples/master/assets/animations/skip-forward.json';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

onMounted(() => {
    setTimeout(() => {
        showForm.value = true;
    }, 2000);
});
</script>

<template>
    <Head title="Admin Portal" />

    <div class="min-h-screen w-full bg-slate-50 relative overflow-hidden flex flex-col items-center justify-center font-sans text-slate-900">
        
        <div class="absolute inset-0 bg-premium-dots z-0"></div>

        <div class="absolute top-[-200px] left-[-200px] w-[800px] h-[800px] bg-gradient-to-br from-emerald-200/40 via-teal-100/20 to-transparent rounded-full blur-3xl pointer-events-none z-0"></div>
        <div class="absolute bottom-[-100px] right-[-100px] w-[600px] h-[600px] bg-gradient-to-tl from-amber-200/30 via-orange-100/10 to-transparent rounded-full blur-3xl pointer-events-none z-0"></div>

        <div class="relative z-10 w-full max-w-md px-6">
            
            <div class="flex flex-col items-center justify-center transition-all duration-700 ease-out"
                 :class="showForm ? 'scale-75 mb-2' : 'scale-110 translate-y-12'">
                
                <div class="bg-white/50 backdrop-blur-sm rounded-full p-4 shadow-xl border border-white/50 relative">
                    <div class="absolute inset-0 bg-emerald-400/20 blur-xl rounded-full"></div>
                    
                    <Vue3Lottie
                        :animationLink="wavingAnimation"
                        :height="180"
                        :width="180"
                        :loop="true"
                        :autoPlay="true"
                    />
                </div>

                <div v-if="!showForm" class="mt-8 text-center">
                    <h1 class="text-3xl font-black text-emerald-800 tracking-tight mb-2">Tiaramu Greenland</h1>
                    <p class="text-slate-500 animate-pulse font-medium">Menyiapkan dashboard...</p>
                </div>
            </div>

            <transition name="slide-up">
                <div v-if="showForm" class="bg-white/80 backdrop-blur-xl border border-white/60 p-8 rounded-3xl shadow-2xl shadow-emerald-100/50 relative overflow-hidden group">
                    
                    <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-emerald-400 via-teal-500 to-emerald-600"></div>

                    <div class="text-center mb-8">
                        <h2 class="text-2xl font-bold text-slate-800">Admin Access</h2>
                        <p class="text-sm text-slate-500 mt-1">Masuk untuk mengelola data kavling.</p>
                    </div>

                    <div v-if="status" class="mb-4 text-sm font-medium text-emerald-600 bg-emerald-50 p-3 rounded-lg border border-emerald-100 text-center">
                        {{ status }}
                    </div>

                    <form @submit.prevent="submit" class="space-y-5">
                        
                        <div class="group/input">
                            <InputLabel for="email" value="Email Address" class="text-slate-600 font-semibold" />
                            <div class="relative mt-1">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 group-focus-within/input:text-emerald-500 transition">
                                    📧
                                </span>
                                <TextInput
                                    id="email"
                                    type="email"
                                    class="block w-full pl-10 pr-4 py-3 bg-white/50 border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl transition shadow-sm placeholder:text-slate-300"
                                    v-model="form.email"
                                    required
                                    autofocus
                                    autocomplete="username"
                                    placeholder="nama@tiaramu.com"
                                />
                            </div>
                            <InputError class="mt-1" :message="form.errors.email" />
                        </div>

                        <div class="group/input">
                            <InputLabel for="password" value="Password" class="text-slate-600 font-semibold" />
                            <div class="relative mt-1">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 group-focus-within/input:text-emerald-500 transition">
                                    🔒
                                </span>
                                <TextInput
                                    id="password"
                                    type="password"
                                    class="block w-full pl-10 pr-4 py-3 bg-white/50 border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl transition shadow-sm placeholder:text-slate-300"
                                    v-model="form.password"
                                    required
                                    autocomplete="current-password"
                                    placeholder="••••••••"
                                />
                            </div>
                            <InputError class="mt-1" :message="form.errors.password" />
                        </div>

                        <div class="flex items-center justify-between mt-4">
                            <label class="flex items-center cursor-pointer">
                                <Checkbox name="remember" v-model:checked="form.remember" class="text-emerald-600 focus:ring-emerald-500 rounded border-slate-300" />
                                <span class="ms-2 text-sm text-slate-600 font-medium">Ingat Saya</span>
                            </label>
                            
                            <Link v-if="canResetPassword" :href="route('password.request')" class="text-sm text-emerald-600 hover:text-emerald-800 font-semibold underline decoration-2 decoration-transparent hover:decoration-emerald-600 transition-all">
                                Lupa Password?
                            </Link>
                        </div>

                        <button 
                            type="submit"
                            :disabled="form.processing"
                            class="w-full py-4 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold rounded-xl shadow-lg shadow-emerald-200 transform transition hover:-translate-y-0.5 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed flex justify-center items-center gap-2"
                        >
                            <span v-if="form.processing" class="animate-spin">⏳</span>
                            <span>Masuk Dashboard</span>
                            <span>🚀</span>
                        </button>

                    </form>
                </div>
            </transition>

            <div class="mt-8 text-center">
                <p class="text-xs text-slate-400 font-medium uppercase tracking-widest">&copy; 2025 Tiaramu Greenland Premium Estate</p>
            </div>

        </div>
    </div>
</template>

<style scoped>
.bg-premium-dots {
    background-color: #f8fafc;
    background-image: radial-gradient(#94a3b8 1px, transparent 1px);
    background-size: 30px 30px;
}

.slide-up-enter-active {
    transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}
.slide-up-enter-from {
    opacity: 0;
    transform: translateY(60px) scale(0.95);
}
.slide-up-enter-to {
    opacity: 1;
    transform: translateY(0) scale(1);
}
</style>