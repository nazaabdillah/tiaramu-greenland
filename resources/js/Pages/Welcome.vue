<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, onMounted, watch } from 'vue';
import { 
    ChevronRightIcon, 
    ChevronLeftIcon, 
    XMarkIcon, 
    UserIcon, 
    PhoneIcon, 
    IdentificationIcon 
} from '@heroicons/vue/24/solid';

// ==========================================
// 1. DATA & STATE
// ==========================================
const props = defineProps({
    kavlings: Array,
    canLogin: Boolean,
});

const selectedKavling = ref(null);
const currentSlide = ref(0);
const isPanelOpen = ref(true);
const showBookingModal = ref(false); // State Modal

// Form Booking (Data yang akan dikirim ke Backend Xendit)
const bookingForm = useForm({
    nama_pembeli: '',
    nomor_wa: '',
    nik_ktp: '',
    kavling_id: null,
});

const formatRupiah = (number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(number);
};

const togglePanel = () => {
    isPanelOpen.value = !isPanelOpen.value;
};

// ==========================================
// 2. LOGIKA INTERAKSI & BOOKING (FIXED)
// ==========================================
watch(selectedKavling, (newVal) => {
    if (newVal) {
        isPanelOpen.value = true;
        currentSlide.value = 0;
    }
});

// Buka Modal & Set ID Kavling
const openBookingModal = () => {
    if (!selectedKavling.value) return;
    bookingForm.kavling_id = selectedKavling.value.id;
    showBookingModal.value = true;
};

// KIRIM KE BACKEND (XENDIT)
const submitBooking = () => {
    bookingForm.post(route('booking.store'), {
        preserveScroll: true,
        onSuccess: () => {
            // Kalau sukses, biasanya Backend langsung redirect ke Xendit (Inertia::location)
            // Tapi kalau masih di halaman ini:
            showBookingModal.value = false;
            bookingForm.reset();
            alert('Mengarahkan ke pembayaran...');
        },
        onError: (errors) => {
            alert('Gagal memproses booking. Harap lengkapi data.');
            console.error(errors);
        }
    });
};

const nextSlide = () => {
    if (!selectedKavling.value?.galleries) return;
    if (currentSlide.value === selectedKavling.value.galleries.length - 1) currentSlide.value = 0;
    else currentSlide.value++;
};

const prevSlide = () => {
    if (!selectedKavling.value?.galleries) return;
    if (currentSlide.value === 0) currentSlide.value = selectedKavling.value.galleries.length - 1;
    else currentSlide.value--;
};

// ==========================================
// 3. LOGIKA PETA (WARNA & KLIK)
// ==========================================
const colorizeMap = () => {
    props.kavlings.forEach(kavling => {
        const element = document.getElementById(kavling.kode_kavling);
        if (element) {
            if (kavling.status === 'sold') {
                element.style.fill = 'url(#roofSold)'; 
                element.style.pointerEvents = 'none';
            } else if (kavling.status === 'booking') {
                element.style.fill = 'url(#roofBooking)'; 
            } else {
                element.style.fill = 'url(#roofAvailable)'; 
            }
        }
    });
};

const handleMapClick = (event) => {
    // Deteksi klik pada Group (Path + Text)
    const targetGroup = event.target.closest('.lot-group');
    if (!targetGroup) return;

    // Ambil ID dari Path di dalam grup
    const pathElement = targetGroup.querySelector('.lot');
    const targetId = pathElement ? pathElement.id : null;

    if (!targetId) return;

    const found = props.kavlings.find(k => k.kode_kavling === targetId);

    if (found) {
        if (selectedKavling.value) {
            const prevEl = document.getElementById(selectedKavling.value.kode_kavling);
            if (prevEl) updateColorSingle(selectedKavling.value, prevEl);
        }
        selectedKavling.value = found;
        
        // Highlight Atap
        pathElement.style.fill = 'url(#roofSelected)'; 
    }
};

const updateColorSingle = (kavling, element) => {
    if (kavling.status === 'sold') element.style.fill = 'url(#roofSold)';
    else if (kavling.status === 'booking') element.style.fill = 'url(#roofBooking)';
    else element.style.fill = 'url(#roofAvailable)';
};

// Pantau perubahan data (Realtime Update)
watch(() => props.kavlings, () => {
    colorizeMap();
}, { deep: true });

onMounted(() => {
    colorizeMap();
});
</script>

<template>
    <Head title="Tiaramu Greenland - Premium Estate" />

    <div class="relative h-screen w-screen overflow-hidden bg-slate-50 font-sans text-slate-900">
        
        <div class="absolute top-[-300px] left-[-300px] w-[1200px] h-[1200px] bg-gradient-to-br from-orange-200/40 via-amber-100/10 to-transparent rounded-full blur-3xl pointer-events-none z-10 mix-blend-screen"></div>
        <div class="absolute inset-0 z-20 pointer-events-none overflow-hidden">
            <svg class="w-full h-full opacity-70">
                <defs><filter id="softEdge"><feGaussianBlur in="SourceGraphic" stdDeviation="6" /></filter></defs>
                <g class="cloud-move-1" filter="url(#softEdge)" fill="white" opacity="0.5">
                    <path d="M100,100 Q120,60 160,70 Q190,40 230,70 Q270,50 300,90 Q330,90 330,120 Q330,150 290,150 L120,150 Q90,150 90,120 Q90,100 100,100 Z" transform="scale(1.5)" />
                </g>
                <g class="cloud-move-2" filter="url(#softEdge)" fill="white" opacity="0.3">
                    <path d="M50,50 Q70,20 100,30 Q120,10 150,30 Q180,30 180,60 Q180,80 150,80 L70,80 Q50,80 50,60 Z" transform="translate(0, 400) scale(1.2)" />
                </g>
            </svg>
        </div>

        <div class="absolute inset-0 z-0 flex items-center justify-center overflow-auto p-4 md:p-10 cursor-move bg-premium-dots">
            <div class="transform scale-90 md:scale-100 lg:scale-110 transition-transform duration-700 ease-out relative z-0">
                
                <svg @click="handleMapClick" class="premium-map drop-shadow-2xl" version="1.2" xmlns="http://www.w3.org/2000/svg" viewBox="-50 -250 950 950" width="950" height="950">
                    
                    <defs>
                        <pattern id="asphalt" x="0" y="0" width="128" height="128" patternUnits="userSpaceOnUse">
                            <rect width="128" height="128" fill="#1e293b"/>
                            <filter id="noiseFilter"><feTurbulence type="fractalNoise" baseFrequency="0.9" numOctaves="4" stitchTiles="stitch"/></filter>
                            <rect width="128" height="128" fill="white" opacity="0.15" filter="url(#noiseFilter)"/>
                        </pattern>
                        <linearGradient id="roofAvailable" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#ffffff" /> <stop offset="100%" stop-color="#cbd5e1" /></linearGradient>
                        <linearGradient id="roofSold" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#f87171" /> <stop offset="100%" stop-color="#991b1b" /></linearGradient>
                        <linearGradient id="roofBooking" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#fcd34d" /> <stop offset="100%" stop-color="#b45309" /></linearGradient>
                        <linearGradient id="roofSelected" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#60a5fa" /> <stop offset="100%" stop-color="#1e40af" /></linearGradient>
                        <radialGradient id="treeGradient" cx="30%" cy="30%" r="70%" fx="30%" fy="30%"><stop offset="0%" stop-color="#4ade80" /> <stop offset="100%" stop-color="#14532d" /></radialGradient>
                        <filter id="roadShadow"><feGaussianBlur in="SourceAlpha" stdDeviation="2"/> <feOffset dx="0" dy="2"/><feComponentTransfer><feFuncA type="linear" slope="0.4"/></feComponentTransfer><feMerge><feMergeNode/><feMergeNode in="SourceGraphic"/></feMerge></filter>
                        <filter id="houseShadow" x="-50%" y="-50%" width="200%" height="200%"><feGaussianBlur in="SourceAlpha" stdDeviation="5"/> <feOffset dx="5" dy="8"/><feComponentTransfer><feFuncA type="linear" slope="0.3"/></feComponentTransfer><feMerge><feMergeNode/><feMergeNode in="SourceGraphic"/></feMerge></filter>
                    </defs>

                    <g class="roads-layer" filter="url(#roadShadow)">
                        <g class="curb" fill="#94a3b8" stroke="#94a3b8" stroke-width="25" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m323.19 50.14l-18.45 195.13-14.93-1.41 18.45-195.13z"/><path d="m344.19 56.14l-18.45 195.13-14.93-1.41 18.45-195.13z"/><path d="m456.62 245.17l-2.51 19.84-159.73-20.18 2.51-19.84z"/><path d="m450.7 258.21l-11.48 121.46-19.92-1.88 11.48-121.46z"/><path d="m470.38 88.48l-14.87 157.3-23.89-2.26 14.87-157.3z"/>
                        </g>
                        <g class="asphalt" fill="url(#asphalt)" stroke="url(#asphalt)" stroke-width="15" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m323.19 50.14l-18.45 195.13-14.93-1.41 18.45-195.13z"/><path d="m344.19 56.14l-18.45 195.13-14.93-1.41 18.45-195.13z"/><path d="m456.62 245.17l-2.51 19.84-159.73-20.18 2.51-19.84z"/><path d="m450.7 258.21l-11.48 121.46-19.92-1.88 11.48-121.46z"/><path d="m470.38 88.48l-14.87 157.3-23.89-2.26 14.87-157.3z"/>
                        </g>
                        <g class="markings" fill="none" stroke="#ffffff" stroke-width="1.5" stroke-dasharray="15,15" opacity="0.6">
                             <path d="M315 50 L290 240 L440 250" /> <path d="M460 90 L445 250" /> <path d="M435 270 L425 380" />
                        </g>
                    </g>

                    <g class="trees" fill="url(#treeGradient)" filter="url(#houseShadow)">
                        <circle cx="280" cy="80" r="18" /> <circle cx="270" cy="100" r="14" /> <circle cx="530" cy="200" r="22" /> <circle cx="550" cy="220" r="16" /> <circle cx="540" cy="300" r="18" /> <circle cx="350" cy="30" r="16" /> <circle cx="410" cy="220" r="12" />
                    </g>

                    <g class="lots-layer" filter="url(#houseShadow)" stroke="#ffffff" stroke-width="2" stroke-linejoin="round">
                        <g class="lot-group"> <path id="A7" class="lot" d="m306.12 49.32l-2.45 25.88-47.79-4.52 2.45-25.88z"/> <text x="280" y="62" class="lot-label">A7</text> </g>
                        <g class="lot-group"> <path id="A6" class="lot" d="m304.12 76.32l-2.45 25.88-47.79-4.52 2.45-25.88z"/> <text x="278" y="89" class="lot-label">A6</text> </g>
                        <g class="lot-group"> <path id="A5" class="lot" d="m300.12 102.32l-2.45 25.88-47.79-4.52 2.45-25.88z"/> <text x="274" y="115" class="lot-label">A5</text> </g>
                        <g class="lot-group"> <path id="A4" class="lot" d="m298.12 127.32l-2.45 25.88-47.79-4.52 2.45-25.88z"/> <text x="272" y="140" class="lot-label">A4</text> </g>
                        <g class="lot-group"> <path id="A3" class="lot" d="m295.12 152.32l-2.45 25.88-47.79-4.52 2.45-25.88z"/> <text x="269" y="165" class="lot-label">A3</text> </g>
                        <g class="lot-group"> <path id="A2" class="lot" d="m293.12 177.32l-2.45 25.88-47.79-4.52 2.45-25.88z"/> <text x="267" y="190" class="lot-label">A2</text> </g>
                        <g class="lot-group"> <path id="A1" class="lot" d="m291.12 204.32l-2.45 25.88-47.79-4.52 2.45-25.88z"/> <text x="265" y="217" class="lot-label">A1</text> </g>

                        <g class="lot-group"> <path id="B1" class="lot" d="m381.12 202.32l-2.45 25.88-47.79-4.52 2.45-25.88z"/> <text x="355" y="215" class="lot-label">B1</text> </g>
                        <g class="lot-group"> <path id="B2" class="lot" d="m383.12 176.32l-2.45 25.88-47.79-4.52 2.45-25.88z"/> <text x="357" y="189" class="lot-label">B2</text> </g>
                        <g class="lot-group"> <path id="B3" class="lot" d="m385.12 151.32l-2.45 25.88-47.79-4.52 2.45-25.88z"/> <text x="359" y="164" class="lot-label">B3</text> </g>
                        <g class="lot-group"> <path id="B4" class="lot" d="m388.12 126.32l-2.45 25.88-47.79-4.52 2.45-25.88z"/> <text x="362" y="139" class="lot-label">B4</text> </g>
                        <g class="lot-group"> <path id="B5" class="lot" d="m390.12 100.32l-2.45 25.88-47.79-4.52 2.45-25.88z"/> <text x="364" y="113" class="lot-label">B5</text> </g>
                        <g class="lot-group"> <path id="B6" class="lot" d="m392.12 76.32l-2.45 25.88-47.79-4.52 2.45-25.88z"/> <text x="366" y="89" class="lot-label">B6</text> </g>

                        <g class="lot-group"> <path id="C5" class="lot" d="m431.12 213.32l-2.45 25.88-47.79-4.52 2.45-25.88z"/> <text x="407" y="226" class="lot-label">C5</text> </g>
                        <g class="lot-group"> <path id="C6" class="lot" d="m434.12 187.32l-2.45 25.88-47.79-4.52 2.45-25.88z"/> <text x="410" y="200" class="lot-label">C6</text> </g>
                        <g class="lot-group"> <path id="C7" class="lot" d="m435.12 162.32l-2.45 25.88-47.79-4.52 2.45-25.88z"/> <text x="411" y="175" class="lot-label">C7</text> </g>
                        <g class="lot-group"> <path id="C8" class="lot" d="m438.12 136.32l-2.45 25.88-47.79-4.52 2.45-25.88z"/> <text x="414" y="149" class="lot-label">C8</text> </g>
                        <g class="lot-group"> <path id="C9" class="lot" d="m441.12 111.32l-2.45 25.88-47.79-4.52 2.45-25.88z"/> <text x="417" y="124" class="lot-label">C9</text> </g>
                        <g class="lot-group"> <path id="C10" class="lot" d="m443.12 85.32l-2.45 25.88-47.79-4.52 2.45-25.88z"/> <text x="419" y="98" class="lot-label">C10</text> </g>
                        
                        <g class="lot-group"> <path id="C4" class="lot" d="m426.12 272.32l-2.45 25.88-47.79-4.52 2.45-25.88z"/> <text x="402" y="285" class="lot-label">C4</text> </g>
                        <g class="lot-group"> <path id="C3" class="lot" d="m423.12 299.32l-2.45 25.88-47.79-4.52 2.45-25.88z"/> <text x="399" y="312" class="lot-label">C3</text> </g>
                        <g class="lot-group"> <path id="C2" class="lot" d="m421.12 326.32l-2.45 25.88-47.79-4.52 2.45-25.88z"/> <text x="397" y="339" class="lot-label">C2</text> </g>
                        <g class="lot-group"> <path id="C1" class="lot" d="m418.12 353.32l-2.45 25.88-47.79-4.52 2.45-25.88z"/> <text x="394" y="366" class="lot-label">C1</text> </g>

                        <g class="lot-group"> <path id="D11" class="lot" d="m522.12 106.32l-2.45 25.88-47.79-4.52 2.45-25.88z"/> <text x="498" y="119" class="lot-label">D11</text> </g>
                        <g class="lot-group"> <path id="D10" class="lot" d="m519.12 131.32l-2.45 25.88-47.79-4.52 2.45-25.88z"/> <text x="495" y="144" class="lot-label">D10</text> </g>
                        <g class="lot-group"> <path id="D9" class="lot" d="m515.12 157.32l-2.45 25.88-47.79-4.52 2.45-25.88z"/> <text x="491" y="170" class="lot-label">D9</text> </g>
                        <g class="lot-group"> <path id="D8" class="lot" d="m512.12 183.32l-2.45 25.88-47.79-4.52 2.45-25.88z"/> <text x="488" y="196" class="lot-label">D8</text> </g>
                        <g class="lot-group"> <path id="D7" class="lot" d="m509.12 207.32l-2.45 25.88-47.79-4.52 2.45-25.88z"/> <text x="485" y="220" class="lot-label">D7</text> </g>
                        <g class="lot-group"> <path id="D6" class="lot" d="m505.12 232.32l-2.45 25.88-47.79-4.52 2.45-25.88z"/> <text x="481" y="245" class="lot-label">D6</text> </g>
                        <g class="lot-group"> <path id="D5" class="lot" d="m502.12 258.32l-2.45 25.88-47.79-4.52 2.45-25.88z"/> <text x="478" y="271" class="lot-label">D5</text> </g>
                        <g class="lot-group"> <path id="D4" class="lot" d="m499.12 284.32l-2.45 25.88-47.79-4.52 2.45-25.88z"/> <text x="475" y="297" class="lot-label">D4</text> </g>
                        <g class="lot-group"> <path id="D3" class="lot" d="m495.12 310.32l-2.45 25.88-47.79-4.52 2.45-25.88z"/> <text x="471" y="323" class="lot-label">D3</text> </g>
                        <g class="lot-group"> <path id="D2" class="lot" d="m491.12 337.32l-2.45 25.88-47.79-4.52 2.45-25.88z"/> <text x="467" y="350" class="lot-label">D2</text> </g>
                        <g class="lot-group"> <path id="D1" class="lot" d="m488.12 362.32l-2.45 25.88-47.79-4.52 2.45-25.88z"/> <text x="464" y="375" class="lot-label">D1</text> </g>
                    </g>
                </svg>
            </div>
        </div>

        <div class="fixed top-0 left-0 right-0 z-40 p-4 pointer-events-none"> 
            <div class="max-w-7xl mx-auto flex justify-between items-center pointer-events-auto">
                <div class="bg-white/80 backdrop-blur-xl shadow-2xl rounded-full px-6 py-3 border border-white/40 flex items-center gap-3">
                    <span class="text-3xl drop-shadow-md">🏡</span>
                    <div><h1 class="font-bold text-emerald-900 leading-none">Tiaramu Greenland</h1><p class="text-[10px] text-emerald-600 font-extrabold tracking-widest uppercase">Premium Estate</p></div>
                </div>
                <div v-if="canLogin">
                    <Link v-if="$page.props.auth.user" :href="route('dashboard')" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-full text-sm font-bold shadow-lg transition transform hover:scale-105">Dashboard</Link>
                    <Link v-else :href="route('login')" class="bg-white/80 backdrop-blur-xl hover:bg-white text-emerald-800 px-6 py-2 rounded-full text-sm font-bold shadow-lg transition border border-white/40">Admin Login</Link>
                </div>
            </div>
        </div>

        <div class="fixed inset-y-0 right-0 z-50 w-full sm:w-[480px] bg-white/90 backdrop-blur-2xl shadow-2xl border-l border-white/20 transform transition-transform duration-500 cubic-bezier(0.4, 0, 0.2, 1) flex flex-col" :class="isPanelOpen ? 'translate-x-0' : 'translate-x-full'">
            <button @click="togglePanel" class="absolute top-1/2 -left-12 -translate-y-1/2 bg-white text-emerald-700 p-3 rounded-l-2xl shadow-xl hover:bg-gray-50 transition border border-r-0 border-gray-100 flex items-center justify-center group">
                <ChevronRightIcon v-if="isPanelOpen" class="w-6 h-6"/><ChevronLeftIcon v-else class="w-6 h-6"/>
            </button>
            <div class="flex-1 overflow-y-auto h-full relative">
                <div class="relative h-80 bg-slate-200">
                    <div v-if="selectedKavling && selectedKavling.galleries?.length > 0" class="h-full w-full relative group">
                        <img :src="selectedKavling.galleries[currentSlide].image_path" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
                        <div class="absolute bottom-6 right-6 flex gap-2">
                             <button v-if="selectedKavling.galleries.length > 1" @click="prevSlide" class="bg-white/20 backdrop-blur-md hover:bg-emerald-500 text-white p-3 rounded-full transition border border-white/10"><ChevronLeftIcon class="w-5 h-5"/></button>
                             <button v-if="selectedKavling.galleries.length > 1" @click="nextSlide" class="bg-white/20 backdrop-blur-md hover:bg-emerald-500 text-white p-3 rounded-full transition border border-white/10"><ChevronRightIcon class="w-5 h-5"/></button>
                        </div>
                        <div class="absolute bottom-6 left-6 flex gap-1.5"><span v-for="(f, i) in selectedKavling.galleries" :key="i" class="h-1.5 rounded-full transition-all duration-300" :class="currentSlide === i ? 'bg-white w-8' : 'bg-white/30 w-2'"></span></div>
                    </div>
                    <div v-else class="h-full flex flex-col items-center justify-center text-slate-400 bg-slate-100"><span class="text-7xl mb-4 grayscale opacity-30">🗺️</span><p class="text-sm font-bold uppercase tracking-widest opacity-60">Pilih Unit</p></div>
                </div>
                <div class="p-8">
                    <div v-if="selectedKavling" class="animate-fade-in-up">
                        <div class="flex justify-between items-start mb-8">
                            <div><h2 class="text-6xl font-black text-slate-800 tracking-tighter">{{ selectedKavling.kode_kavling }}</h2><p class="text-emerald-600 font-bold text-xl mt-1">Tipe {{ selectedKavling.tipe_rumah }}</p></div>
                            <span class="px-5 py-2 rounded-xl text-xs font-black uppercase tracking-widest shadow-sm border-2" :class="{'bg-emerald-50 text-emerald-700 border-emerald-100': selectedKavling.status === 'available', 'bg-red-50 text-red-700 border-red-100': selectedKavling.status === 'sold', 'bg-amber-50 text-amber-700 border-amber-100': selectedKavling.status === 'booking'}">{{ selectedKavling.status }}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mb-8">
                            <div class="bg-white p-6 rounded-3xl border border-slate-100 text-center hover:border-emerald-300 hover:shadow-xl hover:shadow-emerald-100/50 transition group cursor-default"><span class="block text-slate-400 text-[10px] font-bold uppercase tracking-widest mb-2 group-hover:text-emerald-500 transition">Luas Tanah</span><span class="text-3xl font-black text-slate-700">{{ selectedKavling.luas_tanah }} <span class="text-sm font-bold text-slate-400">m²</span></span></div>
                            <div class="bg-white p-6 rounded-3xl border border-slate-100 text-center hover:border-emerald-300 hover:shadow-xl hover:shadow-emerald-100/50 transition group cursor-default"><span class="block text-slate-400 text-[10px] font-bold uppercase tracking-widest mb-2 group-hover:text-emerald-500 transition">Luas Bangunan</span><span class="text-3xl font-black text-slate-700">{{ selectedKavling.luas_bangunan }} <span class="text-sm font-bold text-slate-400">m²</span></span></div>
                        </div>
                        <div class="mb-8 p-8 bg-gradient-to-br from-emerald-50 to-white rounded-3xl border border-emerald-100 relative overflow-hidden"><div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-emerald-100 rounded-full blur-2xl opacity-50"></div><span class="block text-emerald-600/80 text-xs font-bold uppercase tracking-wider mb-2 relative z-10">Harga Cash Keras</span><div class="text-4xl font-black text-emerald-800 relative z-10 tracking-tight">{{ formatRupiah(selectedKavling.harga) }}</div></div>
                        
                        <button v-if="selectedKavling.status === 'available'" @click="openBookingModal" class="w-full py-5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white rounded-2xl font-bold text-lg shadow-2xl shadow-emerald-300/50 transition transform hover:-translate-y-1 active:scale-95 flex items-center justify-center gap-3"><span>💎</span> Booking Unit Sekarang</button>
                        <button v-else disabled class="w-full py-5 bg-slate-100 text-slate-400 rounded-2xl font-bold text-lg cursor-not-allowed border-2 border-slate-200">🔒 Unit Tidak Tersedia</button>
                    </div>
                    <div v-else class="text-center py-24 opacity-30"><p class="text-xl font-bold text-slate-400">Pilih unit pada peta.</p></div>
                </div>
                <div class="p-8 text-center text-[10px] font-bold text-slate-300 border-t border-slate-100 mt-auto bg-slate-50 uppercase tracking-widest">&copy; 2025 Tiaramu Greenland.</div>
            </div>
        </div>

        <Transition name="modal">
            <div v-if="showBookingModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md" @click="showBookingModal = false"></div>
                <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md relative z-10 overflow-hidden transform transition-all">
                    <div class="bg-gradient-to-r from-emerald-600 to-teal-600 p-6 text-white flex justify-between items-start">
                        <div><h3 class="text-xl font-black">Formulir Pemesanan</h3><p class="text-emerald-100 text-sm mt-1">Lengkapi data untuk mengunci unit.</p></div>
                        <button @click="showBookingModal = false" class="bg-white/20 hover:bg-white/40 rounded-full p-1 transition"><XMarkIcon class="w-6 h-6" /></button>
                    </div>
                    <div class="p-8 space-y-5">
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 flex items-center gap-4">
                            <div class="h-12 w-12 bg-white rounded-lg flex items-center justify-center font-black text-xl shadow-sm text-slate-700 border border-slate-200">{{ selectedKavling?.kode_kavling }}</div>
                            <div><p class="text-xs font-bold text-slate-400 uppercase">Unit Pilihan</p><p class="text-emerald-600 font-bold">{{ formatRupiah(selectedKavling?.harga) }}</p></div>
                        </div>
                        <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nama Lengkap</label><div class="relative"><UserIcon class="w-5 h-5 absolute left-3 top-3 text-slate-400" /><input v-model="bookingForm.nama_pembeli" type="text" class="w-full pl-10 py-3 bg-slate-50 border-slate-200 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 font-medium" placeholder="Cth: Budi Santoso"></div></div>
                        <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nomor WhatsApp</label><div class="relative"><PhoneIcon class="w-5 h-5 absolute left-3 top-3 text-slate-400" /><input v-model="bookingForm.nomor_wa" type="text" class="w-full pl-10 py-3 bg-slate-50 border-slate-200 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 font-medium" placeholder="0812..."></div></div>
                        <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">NIK KTP (Opsional)</label><div class="relative"><IdentificationIcon class="w-5 h-5 absolute left-3 top-3 text-slate-400" /><input v-model="bookingForm.nik_ktp" type="text" class="w-full pl-10 py-3 bg-slate-50 border-slate-200 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 font-medium" placeholder="16 digit NIK"></div></div>
                        <button @click="submitBooking" class="w-full py-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-lg shadow-lg shadow-emerald-200 transition transform hover:-translate-y-1 active:scale-95 mt-4">Lanjut Pembayaran 💳</button>
                    </div>
                </div>
            </div>
        </Transition>

        <div class="fixed bottom-10 left-10 bg-white/80 backdrop-blur-xl p-6 rounded-3xl shadow-2xl border border-white/60 z-30 hidden sm:block hover:scale-105 transition duration-300">
            <h3 class="font-bold text-[10px] text-slate-400 uppercase tracking-widest mb-4 border-b border-slate-200 pb-3">Status Unit</h3>
            <div class="space-y-4 text-xs font-bold text-slate-600">
                <div class="flex items-center gap-4"><span class="w-4 h-4 rounded-lg bg-gradient-to-br from-white to-slate-200 border border-slate-300 shadow-sm"></span> Available</div>
                <div class="flex items-center gap-4"><span class="w-4 h-4 rounded-lg bg-gradient-to-br from-amber-300 to-amber-500 shadow-lg shadow-amber-200"></span> Booking</div>
                <div class="flex items-center gap-4"><span class="w-4 h-4 rounded-lg bg-gradient-to-br from-red-400 to-red-600 shadow-lg shadow-red-200"></span> Sold</div>
            </div>
        </div>

    </div>
</template>

<style>
.bg-premium-dots { background-color: #f1f5f9; background-image: radial-gradient(#94a3b8 1px, transparent 1px); background-size: 30px 30px; }
.lot { fill: url(#roofAvailable); transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1); cursor: pointer; }
.lot-label { font-family: sans-serif; font-size: 10px; font-weight: 900; fill: #334155; text-anchor: middle; dominant-baseline: middle; pointer-events: none; opacity: 0.8; transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1); }
.lot-group:hover .lot { transform: translateY(-6px) scale(1.02); z-index: 50; filter: drop-shadow(0 20px 10px rgba(0,0,0,0.15)); }
.lot-group:hover .lot-label { transform: translateY(-6px) scale(1.02); }
.lot-group .lot, .lot-group .lot-label { transform-origin: center; transform-box: fill-box; }
@keyframes fadeInUp { from { opacity: 0; transform: translateY(30px) scale(0.95); } to { opacity: 1; transform: translateY(0) scale(1); } }
.animate-fade-in-up { animation: fadeInUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
.modal-enter-active, .modal-leave-active { transition: all 0.3s ease; }
.modal-enter-from, .modal-leave-to { opacity: 0; transform: scale(0.9); }
@keyframes floatCloud1 { 0% { transform: translateX(0px); } 100% { transform: translateX(120vw); } }
.cloud-move-1 { animation: floatCloud1 60s linear infinite; }
@keyframes floatCloud2 { 0% { transform: translateX(0px); } 100% { transform: translateX(100vw); } }
.cloud-move-2 { animation: floatCloud2 80s linear infinite; animation-delay: -20s; }
</style>