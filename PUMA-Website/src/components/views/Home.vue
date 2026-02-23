<script setup lang="ts">
import { ref, onMounted, onUnmounted, computed } from 'vue';
import Navbar from '../Navbar.vue';
import Events from '../Events.vue';
import Divisions from '../Divisions.vue';
import test from '../test.vue';
import Footer from '../Footer.vue';
import News from '../News.vue';
import FAQ from '../Faq.vue';
import { useBanners } from '@/composables/useBanners';

const { banners, fetchActiveBanners } = useBanners();

const velocity = ref(2);
const targetVelocity = ref(2);
const position = ref(0);
const scrollContainerRef = ref<HTMLElement | null>(null);
let animationFrame: number;

const startScrolling = () => {
    const animate = () => {
        if (scrollContainerRef.value) {
            velocity.value += (targetVelocity.value - velocity.value) * 0.1;
            position.value -= velocity.value;
            if (position.value <= -scrollContainerRef.value.scrollWidth / 2) {
                position.value = 0;
            }
            scrollContainerRef.value.style.transform = `translateX(${position.value}px)`;
        }
        animationFrame = requestAnimationFrame(animate);
    };
    animationFrame = requestAnimationFrame(animate);
};

const stopScrolling = () => {
    cancelAnimationFrame(animationFrame);
};

const slowDown = () => {
    targetVelocity.value = 0.5;
};

const speedUp = () => {
    targetVelocity.value = 2;
};

const showBannerPopup = ref(false);
const currentBannerIndex = ref(0);

const activeBanner = computed(() => {
    if (banners.value.length > 0) {
        return banners.value[currentBannerIndex.value];
    }
    return null;
});

const closeBannerPopup = () => {
    showBannerPopup.value = false;
    // Save current timestamp + 6 hours to localStorage
    const now = new Date();
    // 6 hours * 60 minutes * 60 seconds * 1000 milliseconds
    const expiry = now.getTime() + (6 * 60 * 60 * 1000); 
    localStorage.setItem('banner_expiry', expiry.toString());
};

// Use the first active banner image, fallback to default
const heroImage = computed(() => {
    // Reverted to static image as per request
    const baseUrl = import.meta.env.BASE_URL;
    return `${baseUrl}puma-bag.JPG`; 
});

onMounted(async () => {
    startScrolling();
    rotateText();
    
    // Fetch banners
    await fetchActiveBanners();
    
    // Check if cooldown has expired
    const expiry = localStorage.getItem('banner_expiry');
    const now = new Date().getTime();
    
    // Show if never closed OR expiry time has passed
    const shouldShow = !expiry || now > parseInt(expiry);

    if (banners.value.length > 0 && shouldShow) {
        showBannerPopup.value = true;
    }

    if (scrollContainerRef.value) {
        scrollContainerRef.value.addEventListener("mouseenter", slowDown);
        scrollContainerRef.value.addEventListener("mouseleave", speedUp);
    }
});

onUnmounted(() => {
    stopScrolling();

    if (scrollContainerRef.value) {
        scrollContainerRef.value.removeEventListener("mouseenter", slowDown);
        scrollContainerRef.value.removeEventListener("mouseleave", speedUp);
    }
});

const texts = [
    '<span class="text-blue-500">Kaustav Cabinet</span>',
    'Empowering Innovation',
    'Leading with Vision'
];

const currentIndex = ref(0);
const animatedText = ref(texts[currentIndex.value]);

const rotateText = () => {
    setInterval(() => {
        currentIndex.value = (currentIndex.value + 1) % texts.length;
        animatedText.value = texts[currentIndex.value];
    }, 3000);
};

const formatDate = (dateString: string | undefined) => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
};
</script>

<template>
    <div class="min-h-screen font-montserrat">
        <Navbar />

        <!-- Banner Popup -->
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="showBannerPopup && activeBanner" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-md" @click.self="closeBannerPopup">
                 <div class="relative w-full max-w-5xl overflow-hidden transition-all transform bg-white shadow-2xl rounded-2xl flex flex-col md:flex-row max-h-[90vh]">
                     
                     <!-- Close Button -->
                     <button @click="closeBannerPopup" class="absolute top-4 right-4 z-20 p-2 text-black transition-transform bg-white/80 backdrop-blur rounded-full hover:bg-white hover:scale-110 shadow-lg">
                         <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                         </svg>
                     </button>

                     <!-- Image Section (Left/Top) -->
                     <div class="relative w-full md:w-1/2 lg:w-3/5 bg-gray-100 flex items-center justify-center p-4 overflow-hidden group">
                         <!-- Background Blur Effect -->
                         <div class="absolute inset-0 bg-center bg-cover blur-xl opacity-30 scale-110" :style="{ backgroundImage: `url(${activeBanner.image_path})` }"></div>
                         
                         <a :href="activeBanner.link || '#'" :target="activeBanner.link ? '_blank' : '_self'" class="relative z-10 block w-full h-full transition-transform duration-500 group-hover:scale-[1.02]">
                            <img :src="activeBanner.image_path" :alt="activeBanner.title" class="object-contain w-full h-full max-h-[40vh] md:max-h-full shadow-lg rounded-lg" />
                         </a>

                         <!-- Navigation dots if multiple banners -->
                         <div v-if="banners.length > 1" class="absolute z-20 flex space-x-2 -translate-x-1/2 bottom-4 left-1/2">
                             <button v-for="(banner, index) in banners" :key="banner.id" 
                                     @click.stop="currentBannerIndex = index"
                                     :class="['w-2 h-2 rounded-full transition-all duration-300', currentBannerIndex === index ? 'bg-black w-6' : 'bg-black/30 hover:bg-black/50']">
                             </button>
                         </div>
                     </div>

                     <!-- Content Section (Right/Bottom) -->
                     <div class="flex flex-col w-full p-8 md:w-1/2 lg:w-2/5 bg-white relative">
                        <div class="flex-grow overflow-y-auto pr-2 custom-scrollbar">
                            <!-- Date Badge -->
                            <div class="mb-4" v-if="activeBanner.start_date">
                                <span class="inline-flex items-center px-3 py-1 text-xs font-bold tracking-wider text-blue-700 uppercase bg-blue-50 rounded-full border border-blue-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ formatDate(activeBanner.start_date) }}
                                    <span v-if="activeBanner.end_date" class="mx-1">-</span>
                                    <span v-if="activeBanner.end_date">{{ formatDate(activeBanner.end_date) }}</span>
                                </span>
                            </div>

                            <h3 class="mb-4 text-3xl font-extrabold leading-tight text-gray-900 font-montserrat">{{ activeBanner.title }}</h3>
                            
                            <div class="prose prose-sm text-gray-600 mb-6 leading-relaxed whitespace-pre-line">
                                {{ activeBanner.description }}
                            </div>
                        </div>

                        <!-- Footer / CTA -->
                        <div class="pt-6 mt-auto border-t border-gray-100">
                            <div v-if="activeBanner.link">
                                <a :href="activeBanner.link" target="_blank" class="flex items-center justify-center w-full px-8 py-3.5 text-sm font-bold text-white uppercase tracking-widest transition-all bg-black rounded-lg hover:bg-gray-800 hover:shadow-lg hover:-translate-y-0.5 group">
                                    Learn More
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 ml-2 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </a>
                            </div>
                            <div v-else>
                                <button @click="closeBannerPopup" class="w-full px-8 py-3.5 text-sm font-bold text-gray-500 uppercase tracking-widest transition-all bg-gray-100 rounded-lg hover:bg-gray-200">
                                    Close
                                </button>
                            </div>
                        </div>
                     </div>
                 </div>
            </div>
        </Transition>

        <section id="home" class="relative flex items-center h-screen">
            <div class="absolute inset-0">
                <img :src="heroImage" class="object-cover w-full h-full" alt="Background" />
                <div class="absolute inset-0 bg-gradient-to-b from-black/70 to-black/50"></div>
            </div>
            <div class="relative max-w-4xl px-8 pt-20 md:px-16 md:pt-32">
                <h1 class="text-5xl font-extrabold text-white md:text-7xl animate-fadeInUp">
                    PUMA INFORMATICS
                </h1>
                <p class="mt-3 text-2xl font-medium text-white/90 animate-fadeInUp animate-delay-200">
                    <span class="inline-block animate-rotateText" v-html="animatedText"></span>
                </p>
                <p class="mt-2 text-lg text-white/80 md:text-xl animate-fadeInUp animate-delay-300">
                    PUMA Informatics is the official student organization for the Informatics Department at President University. We are dedicated to developing students' capabilities in technology and fostering a community of forward-thinking tech enthusiasts who contribute positively to society.
                </p>
                <a href="/PUMA-Website/about"
                    class="inline-block px-8 py-3 mt-6 text-black transition-colors bg-white rounded-full hover:bg-gray-100 animate-fadeInUp animate-delay-400">
                    About Us
                </a>
            </div>
        
        </section>
        <Events />
        <News />
        <Divisions />
        <test />
        <FAQ />
        <Footer />
    </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;800&display=swap');

.font-montserrat {
    font-family: 'Montserrat', sans-serif;
}

@keyframes rotateText {
    0% {
        opacity: 0;
        transform: rotateX(90deg);
    }

    50% {
        opacity: 1;
        transform: rotateX(0deg);
    }

    100% {
        opacity: 0;
        transform: rotateX(-90deg);
    }
}

.animate-rotateText {
    display: inline-block;
    animation: rotateText 3s infinite ease-in-out;
}
</style>
