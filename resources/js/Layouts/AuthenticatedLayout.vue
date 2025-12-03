<script setup>
import { ref } from 'vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';
import { Bars3Icon, XMarkIcon, UserCircleIcon } from '@heroicons/vue/24/outline';
import GlobalLoader from '@/Components/GlobalLoader.vue';

const showingNavigationDropdown = ref(false);
</script>

<template>
    <div class="min-h-screen bg-slate-50 relative font-sans text-slate-900">

        <GlobalLoader />
        
        <div class="fixed inset-0 bg-premium-dots z-0 pointer-events-none"></div>

        <nav class="sticky top-0 z-50 w-full bg-white/80 backdrop-blur-xl border-b border-white/50 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-20">
                    
                    <div class="flex items-center gap-8">
                        <Link :href="route('dashboard')" class="flex items-center gap-3 group">
                            <div class="bg-gradient-to-br from-emerald-500 to-teal-600 text-white p-2 rounded-xl shadow-lg shadow-emerald-200 group-hover:scale-105 transition">
                                <span class="text-2xl">🏡</span>
                            </div>
                            <div>
                                <h1 class="font-black text-emerald-900 leading-none tracking-tight text-lg">Tiaramu</h1>
                                <p class="text-[10px] text-emerald-600 font-bold tracking-widest uppercase">Admin Console</p>
                            </div>
                        </Link>

                        <div class="hidden space-x-8 sm:-my-px sm:flex h-full items-center">
                            <NavLink :href="route('admin.kavling.index')" :active="route().current('admin.kavling.*')">
                                🏙️ Data Kavling
                            </NavLink>
                            <NavLink :href="route('admin.booking.index')" :active="route().current('admin.booking.*')">
                                📄 Data Transaksi
                            </NavLink>
                            </div>
                    </div>

                    <div class="hidden sm:flex sm:items-center sm:ms-6">
                        <div class="ms-3 relative">
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <span class="inline-flex rounded-md">
                                        <button
                                            type="button"
                                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm leading-4 font-bold rounded-full text-emerald-700 bg-emerald-50 hover:bg-emerald-100 focus:outline-none transition ease-in-out duration-150 gap-2"
                                        >
                                            <UserCircleIcon class="w-5 h-5" />
                                            {{ $page.props.auth.user.name }}

                                            <svg
                                                class="ms-2 -me-0.5 h-4 w-4"
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                        </button>
                                    </span>
                                </template>

                                <template #content>
                                    <div class="py-1 bg-white rounded-xl shadow-xl border border-slate-100">
                                        <DropdownLink :href="route('profile.edit')"> Profile </DropdownLink>
                                        <DropdownLink :href="route('logout')" method="post" as="button" class="text-red-600 hover:bg-red-50">
                                            Log Out
                                        </DropdownLink>
                                    </div>
                                </template>
                            </Dropdown>
                        </div>
                    </div>

                    <div class="-me-2 flex items-center sm:hidden">
                        <button
                            @click="showingNavigationDropdown = !showingNavigationDropdown"
                            class="inline-flex items-center justify-center p-2 rounded-md text-slate-400 hover:text-slate-500 hover:bg-slate-100 focus:outline-none transition duration-150 ease-in-out"
                        >
                            <Bars3Icon v-if="!showingNavigationDropdown" class="h-6 w-6" />
                            <XMarkIcon v-else class="h-6 w-6" />
                        </button>
                    </div>
                </div>
            </div>

            <div
                :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
                class="sm:hidden bg-white border-t border-slate-100"
            >
                <div class="pt-2 pb-3 space-y-1">
                    <ResponsiveNavLink :href="route('admin.kavling.index')" :active="route().current('admin.kavling.*')">
                        Data Kavling
                    </ResponsiveNavLink>
                </div>

                <div class="pt-4 pb-1 border-t border-slate-200">
                    <div class="px-4">
                        <div class="font-medium text-base text-slate-800">
                            {{ $page.props.auth.user.name }}
                        </div>
                        <div class="font-medium text-sm text-slate-500">{{ $page.props.auth.user.email }}</div>
                    </div>

                    <div class="mt-3 space-y-1">
                        <ResponsiveNavLink :href="route('profile.edit')"> Profile </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('logout')" method="post" as="button">
                            Log Out
                        </ResponsiveNavLink>
                    </div>
                </div>
            </div>
        </nav>

        <main class="relative z-10 animate-fade-in">
            <header class="bg-white/50 backdrop-blur-sm border-b border-slate-100 shadow-sm" v-if="$slots.header">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <slot />
        </main>
    </div>
</template>

<style>
/* STYLE PREMIUM DOTS (Sama dengan Welcome.vue) */
.bg-premium-dots {
    background-color: #f8fafc;
    background-image: radial-gradient(#94a3b8 1px, transparent 1px);
    background-size: 30px 30px;
}

/* ANIMASI HALAMAN MUNCUL */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
    animation: fadeIn 0.5s ease-out forwards;
}
</style>