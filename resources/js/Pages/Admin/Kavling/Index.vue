<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    kavlings: Array
});

// Helper format uang
const formatRupiah = (number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(number);
};
</script>

<template>
    <Head title="Manajemen Kavling" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manajemen Kavling</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="kavling in kavlings" :key="kavling.id" class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap font-bold text-gray-700">
                                            {{ kavling.kode_kavling }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ kavling.tipe_rumah }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ formatRupiah(kavling.harga) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span 
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full uppercase"
                                                :class="{
                                                    'bg-green-100 text-green-800': kavling.status === 'available',
                                                    'bg-yellow-100 text-yellow-800': kavling.status === 'booking',
                                                    'bg-red-100 text-red-800': kavling.status === 'sold',
                                                }"
                                            >
                                                {{ kavling.status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <Link :href="route('admin.kavling.edit', kavling.id)" class="text-indigo-600 hover:text-indigo-900 font-bold">
                                                Edit / Upload Foto
                                            </Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>