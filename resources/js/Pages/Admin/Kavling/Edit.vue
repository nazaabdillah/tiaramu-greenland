<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { 
    ArrowLeftIcon, 
    CurrencyDollarIcon, 
    TagIcon, 
    PhotoIcon, 
    TrashIcon, 
    CloudArrowUpIcon,
    CheckCircleIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    kavling: Object,
});

// 1. FORM DATA UTAMA
const form = useForm({
    harga: props.kavling.harga,
    status: props.kavling.status,
});

// 2. FORM UPLOAD
const photoForm = useForm({
    photo: null,
});

const updateKavling = () => {
    form.put(route('admin.kavling.update', props.kavling.id), {
        onSuccess: () => {
            // Optional: Kasih notifikasi toast disini kalau ada library toast
        },
    });
};

const uploadPhoto = () => {
    if (!photoForm.photo) return;
    photoForm.post(route('admin.kavling.photo.store', props.kavling.id), {
        onSuccess: () => {
            photoForm.reset();
            router.reload();
        },
    });
};

const deletePhoto = (photoId) => {
    if (confirm('Hapus foto ini?')) {
        router.delete(route('admin.kavling.photo.delete', photoId));
    }
};

// Helper preview saat pilih file
const previewImage = ref(null);
const handleFileSelect = (event) => {
    const file = event.target.files[0];
    photoForm.photo = file;
    if (file) {
        previewImage.value = URL.createObjectURL(file);
    }
};
</script>

<template>
    <Head title="Edit Kavling" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link :href="route('admin.kavling.index')" class="p-2 bg-white rounded-xl shadow-sm border border-slate-200 text-slate-500 hover:text-emerald-600 hover:border-emerald-200 transition">
                    <ArrowLeftIcon class="w-5 h-5" />
                </Link>
                <div>
                    <h2 class="font-black text-2xl text-slate-800 leading-tight">
                        Edit Unit {{ kavling.kode_kavling }}
                    </h2>
                    <p class="text-sm text-slate-500 font-medium">Kelola harga, status, dan galeri foto.</p>
                </div>
            </div>
        </template>

        <div class="py-12 bg-slate-50 min-h-screen relative overflow-hidden">
            
            <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-emerald-100/40 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-blue-100/40 rounded-full blur-3xl pointer-events-none"></div>

            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 relative z-10">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <div class="lg:col-span-1 space-y-6">
                        
                        <div class="bg-white rounded-3xl p-6 shadow-xl shadow-slate-200/50 border border-slate-100">
                            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Spesifikasi Teknis</h3>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center p-3 bg-slate-50 rounded-xl border border-slate-100">
                                    <span class="text-sm text-slate-500 font-medium">Tipe Rumah</span>
                                    <span class="text-sm font-bold text-slate-800">{{ kavling.tipe_rumah }}</span>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 text-center">
                                        <span class="block text-[10px] text-slate-400 uppercase font-bold">Luas Tanah</span>
                                        <span class="text-lg font-black text-slate-700">{{ kavling.luas_tanah }}<span class="text-xs">m²</span></span>
                                    </div>
                                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 text-center">
                                        <span class="block text-[10px] text-slate-400 uppercase font-bold">Luas Bangunan</span>
                                        <span class="text-lg font-black text-slate-700">{{ kavling.luas_bangunan }}<span class="text-xs">m²</span></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-3xl p-6 shadow-xl shadow-emerald-100/50 border border-emerald-100 relative overflow-hidden">
                            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-emerald-400 to-teal-500"></div>
                            
                            <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">
                                <span class="p-1.5 bg-emerald-100 text-emerald-600 rounded-lg"><PencilSquareIcon class="w-5 h-5"/></span>
                                Update Data
                            </h3>

                            <form @submit.prevent="updateKavling" class="space-y-5">
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Harga Jual (Rupiah)</label>
                                    <div class="relative group">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-slate-400 font-bold group-focus-within:text-emerald-500 transition">Rp</span>
                                        </div>
                                        <input 
                                            v-model="form.harga" 
                                            type="number" 
                                            class="block w-full pl-10 pr-4 py-3 bg-slate-50 border-slate-200 text-slate-800 font-bold rounded-xl focus:ring-emerald-500 focus:border-emerald-500 transition shadow-sm"
                                        >
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Status Unit</label>
                                    <div class="relative">
                                        <select 
                                            v-model="form.status"
                                            class="block w-full pl-4 pr-10 py-3 bg-slate-50 border-slate-200 text-slate-800 font-bold rounded-xl focus:ring-emerald-500 focus:border-emerald-500 transition shadow-sm appearance-none"
                                        >
                                            <option value="available">🟢 Available</option>
                                            <option value="booking">🟡 Booking</option>
                                            <option value="sold">🔴 Sold Out</option>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none text-slate-500">
                                            <TagIcon class="w-5 h-5" />
                                        </div>
                                    </div>
                                </div>

                                <button 
                                    type="submit" 
                                    :disabled="form.processing"
                                    class="w-full py-3.5 bg-slate-800 hover:bg-emerald-600 text-white rounded-xl font-bold shadow-lg hover:shadow-emerald-200 transition-all transform hover:-translate-y-0.5 flex justify-center items-center gap-2"
                                >
                                    <CheckCircleIcon class="w-5 h-5" />
                                    Simpan Perubahan
                                </button>
                            </form>
                        </div>

                    </div>

                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-3xl p-8 shadow-xl shadow-slate-200/50 border border-slate-100 h-full flex flex-col">
                            
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                                    <span class="p-1.5 bg-blue-100 text-blue-600 rounded-lg"><PhotoIcon class="w-6 h-6"/></span>
                                    Galeri Foto
                                </h3>
                                <span class="text-xs font-medium px-3 py-1 bg-slate-100 text-slate-500 rounded-full">{{ kavling.galleries.length }} Foto</span>
                            </div>

                            <div class="mb-8">
                                <div class="flex gap-4 items-start">
                                    <div class="flex-1">
                                        <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-emerald-200 rounded-2xl bg-emerald-50/50 hover:bg-emerald-50 cursor-pointer transition group">
                                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                <CloudArrowUpIcon class="w-10 h-10 text-emerald-400 mb-2 group-hover:scale-110 transition duration-300" />
                                                <p class="text-sm text-emerald-700 font-medium group-hover:text-emerald-800">Klik untuk pilih foto</p>
                                                <p class="text-xs text-emerald-500/70">JPG, PNG (Max 2MB)</p>
                                            </div>
                                            <input type="file" class="hidden" @change="handleFileSelect" accept="image/*" />
                                        </label>
                                    </div>

                                    <div v-if="previewImage" class="w-32 h-32 relative rounded-2xl overflow-hidden shadow-md border border-slate-200 group">
                                        <img :src="previewImage" class="w-full h-full object-cover" />
                                        <div class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                                            <button @click="uploadPhoto" :disabled="photoForm.processing" class="text-white text-xs font-bold bg-emerald-600 px-3 py-1.5 rounded-lg hover:bg-emerald-500">
                                                {{ photoForm.processing ? '...' : 'Upload' }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div v-if="kavling.galleries.length > 0" class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                <div v-for="foto in kavling.galleries" :key="foto.id" class="group relative aspect-[4/3] bg-slate-100 rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition duration-300">
                                    
                                    <img 
                                        :src="foto.image_path.startsWith('http') ? foto.image_path : foto.image_path" 
                                        class="w-full h-full object-cover transition duration-500 group-hover:scale-110"
                                    >
                                    
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition duration-300 flex flex-col justify-end p-4">
                                        <button 
                                            @click="deletePhoto(foto.id)"
                                            class="w-full py-2 bg-red-600/90 backdrop-blur text-white text-xs font-bold rounded-lg hover:bg-red-500 flex items-center justify-center gap-2 transition"
                                        >
                                            <TrashIcon class="w-4 h-4" /> Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div v-else class="flex-1 flex flex-col items-center justify-center text-slate-300 border-2 border-dashed border-slate-100 rounded-2xl min-h-[200px]">
                                <PhotoIcon class="w-16 h-16 mb-2 opacity-50" />
                                <p class="text-sm font-medium">Belum ada foto galeri.</p>
                            </div>

                        </div>
                    </div>
                
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>