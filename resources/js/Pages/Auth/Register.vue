<script setup>
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const isLoaded = ref(false);

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

onMounted(() => {
    setTimeout(() => {
        isLoaded.value = true;
    }, 100);
});
</script>

<template>
    <Head title="Daftar Akun Baru" />

    <div class="min-h-screen bg-[#F0FDF4] relative overflow-hidden font-sans text-slate-800 flex flex-col justify-between">
        
        <div class="absolute inset-0 bg-premium-dots opacity-60 pointer-events-none"></div>
        <div class="absolute top-[-10%] left-[-10%] w-[500px] h-[500px] bg-emerald-300/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-[20%] right-[-10%] w-[300px] h-[300px] bg-lime-300/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-20 flex-1 flex flex-col items-center justify-center px-4 pb-40 sm:pb-0 pt-10 transition-all duration-700 transform"
             :class="isLoaded ? 'translate-y-0 opacity-100' : '-translate-y-10 opacity-0'">
            
            <div class="text-center mb-6">
                <h1 class="text-3xl font-black text-emerald-900 tracking-tight">Gabung Tiaramu! 🚀</h1>
                <p class="text-slate-500 font-medium">Buat akun untuk mulai booking kavling.</p>
            </div>

            <div class="w-full max-w-sm bg-white rounded-3xl shadow-2xl shadow-emerald-100/50 p-8 border border-slate-100 backdrop-blur-sm bg-opacity-95">
                
                <form @submit.prevent="submit" class="space-y-4">
                    
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 ml-1">Nama Lengkap</label>
                        <input 
                            v-model="form.name" type="text" required autofocus autocomplete="name"
                            class="w-full px-4 py-3 bg-slate-50 border-transparent focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/20 rounded-xl font-bold text-slate-800 transition placeholder:text-slate-300"
                            placeholder="Cth: Sultan Andara"
                        />
                        <InputError class="mt-1" :message="form.errors.name" />
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 ml-1">Email</label>
                        <input 
                            v-model="form.email" type="email" required autocomplete="username"
                            class="w-full px-4 py-3 bg-slate-50 border-transparent focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/20 rounded-xl font-bold text-slate-800 transition placeholder:text-slate-300"
                            placeholder="nama@email.com"
                        />
                        <InputError class="mt-1" :message="form.errors.email" />
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 ml-1">Password</label>
                        <input 
                            v-model="form.password" type="password" required autocomplete="new-password"
                            class="w-full px-4 py-3 bg-slate-50 border-transparent focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/20 rounded-xl font-bold text-slate-800 transition placeholder:text-slate-300"
                            placeholder="Minimal 8 karakter"
                        />
                        <InputError class="mt-1" :message="form.errors.password" />
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 ml-1">Ulangi Password</label>
                        <input 
                            v-model="form.password_confirmation" type="password" required autocomplete="new-password"
                            class="w-full px-4 py-3 bg-slate-50 border-transparent focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/20 rounded-xl font-bold text-slate-800 transition placeholder:text-slate-300"
                            placeholder="Ketik ulang password"
                        />
                        <InputError class="mt-1" :message="form.errors.password_confirmation" />
                    </div>

                    <button type="submit" :disabled="form.processing" class="w-full py-4 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white rounded-xl font-black text-sm shadow-lg shadow-emerald-200/50 transition transform active:scale-95 flex justify-center items-center gap-2 mt-6">
                        <span v-if="form.processing">Memproses...</span>
                        <span v-else>DAFTAR SEKARANG ✨</span>
                    </button>
                </form>

                <div class="mt-6 pt-6 border-t border-slate-100 text-center">
                    <p class="text-xs text-slate-400 font-medium mb-1">Sudah punya akun?</p>
                    <Link :href="route('login')" class="text-emerald-700 font-black text-sm hover:underline decoration-2 underline-offset-4 uppercase tracking-wide">
                        Masuk di Sini
                    </Link>
                </div>

            </div>

        </div>

        <div class="absolute bottom-0 left-0 w-full flex justify-center items-end z-10 pointer-events-none transition-transform duration-1000 delay-300"
             :class="isLoaded ? 'translate-y-0' : 'translate-y-full'">
            
            <img 
                src="/images/login-mascot.png" 
                alt="Mascot" 
                class="h-32 md:h-48 object-contain drop-shadow-2xl opacity-90"
                style="filter: drop-shadow(0 -10px 20px rgba(16, 185, 129, 0.15));"
            />
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