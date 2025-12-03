<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3'; // <--- Tambah ini
import { ref, watch } from 'vue'; // <--- Tambah watch

import { 
    PencilSquareIcon, 
    PhotoIcon, 
    HomeModernIcon, 
    BanknotesIcon, 
    CheckBadgeIcon,
    BuildingOffice2Icon 
} from '@heroicons/vue/24/outline';

const props = defineProps({
    kavlings: Array,
    filters: Object
});

const search = ref(props.filters.search || '');
const filterStatus = ref(props.filters.status || 'all');

let timeout = null;

watch([search, filterStatus], ([newSearch, newStatus]) => {
    clearTimeout(timeout);
    
    timeout = setTimeout(() => {
        router.get(route('admin.kavling.index'), { 
            search: newSearch, 
            status: newStatus 
        }, {
            preserveState: true, // Biar gak reload full page
            replace: true,       // Biar gak menuhin history browser
        });
    }, 300); // Tunggu 300ms setelah user berhenti ngetik
});

// Fungsi Reset
const resetFilter = () => {
    search.value = '';
    filterStatus.value = 'all';
};

const formatRupiah = (number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(number);
};

const stats = {
    total: props.kavlings.length,
    available: props.kavlings.filter(k => k.status === 'available').length,
    sold: props.kavlings.filter(k => k.status === 'sold').length,
    booking: props.kavlings.filter(k => k.status === 'booking').length,
};
</script>

<template>
    <Head title="Dashboard Kavling" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                💎 Dashboard Kavling
            </h2>
        </template>

        <div class="py-12 bg-slate-50 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                    
                    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center gap-4">
                        <div class="p-3 bg-slate-100 text-slate-600 rounded-lg">
                            <BuildingOffice2Icon class="w-6 h-6" />
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase">Total Unit</p>
                            <p class="text-2xl font-bold text-slate-800">{{ stats.total }}</p>
                        </div>
                    </div>

                    <div class="bg-white p-5 rounded-xl border border-emerald-100 shadow-sm flex items-center gap-4">
                        <div class="p-3 bg-emerald-50 text-emerald-600 rounded-lg">
                            <CheckBadgeIcon class="w-6 h-6" />
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-emerald-500 uppercase">Available</p>
                            <p class="text-2xl font-bold text-emerald-700">{{ stats.available }}</p>
                        </div>
                    </div>

                    <div class="bg-white p-5 rounded-xl border border-amber-100 shadow-sm flex items-center gap-4">
                        <div class="p-3 bg-amber-50 text-amber-600 rounded-lg">
                            <BanknotesIcon class="w-6 h-6" />
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-amber-500 uppercase">Booking</p>
                            <p class="text-2xl font-bold text-amber-700">{{ stats.booking }}</p>
                        </div>
                    </div>

                    <div class="bg-white p-5 rounded-xl border border-red-100 shadow-sm flex items-center gap-4">
                        <div class="p-3 bg-red-50 text-red-600 rounded-lg">
                            <HomeModernIcon class="w-6 h-6" />
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-red-500 uppercase">Sold Out</p>
                            <p class="text-2xl font-bold text-red-700">{{ stats.sold }}</p>
                        </div>
                    </div>
                </div>


                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex flex-col md:flex-row gap-4 justify-between items-center backdrop-blur-sm">
    
                        <div class="relative w-full md:w-96 group">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <MagnifyingGlassIcon class="h-5 w-5 text-slate-400 group-focus-within:text-emerald-500 transition" />
                            </div>
                            <input 
                                v-model="search"
                                type="text" 
                                class="block w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm shadow-sm placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition" 
                                placeholder="Cari kode kavling (Contoh: A1)..."
                            >
                        </div>

                        <div class="flex items-center gap-3 w-full md:w-auto">
                            
                            <div class="relative w-full md:w-48">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <FunnelIcon class="h-4 w-4 text-slate-400" />
                                </div>
                                <select 
                                    v-model="filterStatus"
                                    class="block w-full pl-9 pr-8 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-600 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 cursor-pointer appearance-none"
                                >
                                    <option value="all">Semua Status</option>
                                    <option value="available">🟢 Available</option>
                                    <option value="booking">🟡 Booking</option>
                                    <option value="sold">🔴 Sold Out</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>

                            <button 
                                v-if="search || filterStatus !== 'all'"
                                @click="resetFilter"
                                class="p-2.5 bg-slate-200 text-slate-600 rounded-xl hover:bg-red-100 hover:text-red-600 transition tooltip"
                                title="Reset Filter"
                            >
                                <XMarkIcon class="w-5 h-5" />
                            </button>

                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Unit</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Spesifikasi</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Harga</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Galeri</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 bg-white">
                                <tr v-for="kavling in kavlings" :key="kavling.id" class="hover:bg-slate-50 transition-colors">
                                    
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 bg-slate-100 text-slate-600 rounded-lg flex items-center justify-center font-bold text-sm border border-slate-200">
                                                {{ kavling.kode_kavling }}
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-bold text-slate-800">Blok {{ kavling.blok }}</div>
                                                <div class="text-xs text-slate-400">No. {{ kavling.nomor }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-slate-700">Type {{ kavling.tipe_rumah }}</div>
                                        <div class="text-xs text-slate-400 mt-0.5">LT: {{ kavling.luas_tanah }} • LB: {{ kavling.luas_bangunan }}</div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-bold text-emerald-600">
                                            {{ formatRupiah(kavling.harga) }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span 
                                            class="px-3 py-1 inline-flex items-center gap-1.5 text-[10px] font-bold rounded-full uppercase tracking-wide border"
                                            :class="{
                                                'bg-emerald-50 text-emerald-700 border-emerald-100': kavling.status === 'available',
                                                'bg-amber-50 text-amber-700 border-amber-100': kavling.status === 'booking',
                                                'bg-red-50 text-red-700 border-red-100': kavling.status === 'sold',
                                            }"
                                        >
                                            <span class="w-1.5 h-1.5 rounded-full" :class="{
                                                'bg-emerald-500': kavling.status === 'available',
                                                'bg-amber-500': kavling.status === 'booking',
                                                'bg-red-500': kavling.status === 'sold',
                                            }"></span>
                                            {{ kavling.status }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-600">
                                            <PhotoIcon class="w-3 h-3 mr-1" />
                                            {{ kavling.galleries?.length || 0 }}
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <Link :href="route('admin.kavling.edit', kavling.id)" 
                                              class="text-sm font-medium text-slate-600 hover:text-emerald-600 hover:underline decoration-2 underline-offset-4 transition">
                                            Kelola
                                        </Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="bg-slate-50 px-6 py-3 border-t border-slate-200 flex justify-between items-center">
                        <span class="text-xs text-slate-500">Menampilkan seluruh data</span>
                        <span class="text-xs font-bold text-slate-600">Total: {{ stats.total }} Unit</span>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>