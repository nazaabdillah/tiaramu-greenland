<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

defineProps({
    canResetPassword: { type: Boolean },
    status: { type: String },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const isLoaded = ref(false);

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

onMounted(() => {
    setTimeout(() => {
        isLoaded.value = true;
    }, 100);
});
</script>

<template>
    <Head title="Masuk Dulu Yuk" />

    <div class="min-h-screen bg-[#F0FDF4] relative overflow-hidden font-sans text-slate-800 flex items-center justify-center">
        
        <div class="absolute inset-0 bg-premium-dots opacity-60 pointer-events-none"></div>
        <div class="absolute top-[-10%] right-[-10%] w-[500px] h-[500px] bg-emerald-300/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute top-[20%] left-[-10%] w-[300px] h-[300px] bg-lime-300/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="absolute bottom-0 left-0 w-full flex justify-center items-end z-0 pointer-events-none transition-transform duration-1000 delay-300"
             :class="isLoaded ? 'translate-y-0' : 'translate-y-full'">
            
            <!-- <img 
                src="/images/login-mascot.png" 
                alt="Mascot" 
                class="h-48 md:h-72 object-contain drop-shadow-2xl opacity-90"
                style="filter: drop-shadow(0 -10px 20px rgba(16, 185, 129, 0.15));"
            /> -->
        </div>

        <div class="relative z-10 w-full max-w-sm px-4 pb-20 transition-all duration-700 transform"
             :class="isLoaded ? 'translate-y-0 opacity-100' : '-translate-y-10 opacity-0'">
            
            <div class="text-center mb-6">
                <h1 class="text-3xl font-black text-emerald-900 tracking-tight drop-shadow-sm">
                    Halo, Warga Tiaramu! 👋
                </h1>
                <p class="text-slate-600 font-medium text-sm mt-1 bg-white/60 backdrop-blur-sm inline-block px-4 py-1.5 rounded-full shadow-sm">
                    Masuk dulu yuk biar bisa pilih kavling.
                </p>
            </div>

            <div class="bg-white rounded-[2rem] shadow-2xl shadow-emerald-900/10 p-8 border border-slate-100/80 backdrop-blur-sm">
                
                <div v-if="status" class="mb-4 font-bold text-sm text-green-600 bg-green-50 p-3 rounded-xl border border-green-200 text-center">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-5">
                    <div>
                        <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Email Kamu</label>
                        <input 
                            v-model="form.email" type="email" required autofocus
                            class="w-full px-5 py-3.5 bg-slate-50 border-2 border-transparent focus:bg-white focus:border-emerald-400 focus:ring-4 focus:ring-emerald-100 rounded-2xl font-bold text-slate-700 transition placeholder:text-slate-300 placeholder:font-medium"
                            placeholder="nama@email.com"
                        />
                        <InputError class="mt-1" :message="form.errors.email" />
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-1.5 ml-1">
                            <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Kata Sandi</label>
                        </div>
                        <input 
                            v-model="form.password" type="password" required
                            class="w-full px-5 py-3.5 bg-slate-50 border-2 border-transparent focus:bg-white focus:border-emerald-400 focus:ring-4 focus:ring-emerald-100 rounded-2xl font-bold text-slate-700 transition placeholder:text-slate-300 placeholder:font-medium"
                            placeholder="••••••••"
                        />
                        <InputError class="mt-1" :message="form.errors.password" />
                    </div>

                    <div class="flex items-center justify-between pt-1 px-1">
                        <label class="flex items-center cursor-pointer group">
                            <Checkbox name="remember" v-model:checked="form.remember" class="w-5 h-5 text-emerald-500 rounded-md border-slate-300 focus:ring-emerald-400 transition" />
                            <span class="ms-2 text-xs font-bold text-slate-500 group-hover:text-emerald-600 transition">Ingat Aku</span>
                        </label>
                        <Link v-if="canResetPassword" :href="route('password.request')" class="text-xs font-bold text-emerald-600 hover:text-emerald-800 hover:underline decoration-2 underline-offset-2">
                            Lupa Sandi?
                        </Link>
                    </div>

                    <button type="submit" :disabled="form.processing" class="w-full py-4 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white rounded-2xl font-black text-base shadow-xl shadow-emerald-200 transition-all transform active:scale-95 flex justify-center items-center gap-2 mt-4 hover:-translate-y-0.5">
                        <span v-if="form.processing">Sebentar...</span>
                        <span v-else>Masuk & Lanjut 🚀</span>
                    </button>
                </form>

                <div class="relative flex py-2 items-center">
                    <div class="flex-grow border-t border-slate-200"></div>
                    <span class="flex-shrink-0 mx-4 text-slate-400 text-[10px] font-bold uppercase tracking-widest">Atau</span>
                    <div class="flex-grow border-t border-slate-200"></div>
                </div>

                <a :href="route('google.login')" class="w-full py-3.5 bg-white border-2 border-slate-100 hover:border-slate-300 text-slate-600 rounded-2xl font-bold text-sm shadow-sm hover:shadow-md transition transform active:scale-95 flex justify-center items-center gap-3">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                    Masuk dengan Google
                </a>

                <div class="mt-6 pt-6 border-t border-slate-100 text-center">
                    <p class="text-xs text-slate-400 font-bold mb-1">Belum gabung Tiaramu?</p>
                    <Link :href="route('register')" class="text-emerald-600 font-black text-sm hover:text-emerald-800 transition uppercase tracking-wide">
                        Bikin Akun Baru
                    </Link>
                </div>

            </div>

        </div>

    </div>
</template>

<style scoped>
.bg-premium-dots {
    background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
    background-size: 24px 24px;
}
input:focus { outline: none; }
</style>