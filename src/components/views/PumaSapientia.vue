<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import Navbar from '../Navbar.vue' 
import Footer from '../Footer.vue'   

interface Division {
    name: string
    title: string
    image: string
    description: string
}

const router = useRouter()

const handleImageError = (event: Event, divisionName: string) => {
  const target = event.target as HTMLImageElement
  target.src = `https://via.placeholder.com/400x300?text=${divisionName}`
}
const isLoading = ref<boolean>(true)

const divisions: Division[] = [
    {
        name: 'BOD',
        title: 'Board of Directors',
        image: '/PUMA-Website/division/bod.JPG',
        description: 'Memimpin strategis dan mengelola tata kelola organisasi secara menyeluruh'
    },
    {
        name: 'RNC',
        title: 'Relation And Comunication',
        image: '/PUMA-Website/division/icm.JPG',
        description: 'Mengelola komunikasi digital dan konten kreatif untuk membangun brand awareness organisasi'
    },
    {
        name: 'RNT',
        title: 'Research and Technology',
        image: 'https://i.pinimg.com/736x/f2/96/65/f296659f98543ad0ee11738a62e7652f.jpg',
        description: 'Mengembangkan inovasi teknologi dan melakukan riset untuk kemajuan organisasi'
    },
    {
        name: 'SAC',
        title: 'Student Academic & Competition',
        image: '/PUMA-Website/division/sac.JPG',
        description: 'Mendorong prestasi akademik dan pencapaian kompetitif mahasiswa'
    },
    {
        name: 'HRD',
        title: 'Human Resources And Development',
        image: '/PUMA-Website/division/hrd.JPG',
        description: 'Mengembangkan talenta dan kapasitas sumber daya manusia dalam organisasi'
    },
    {
        name: 'ICM',
        title: 'Information Creative Media',
        image: '/PUMA-Website/division/EXT.JPG',
        description: 'Membangun hubungan eksternal dan mengelola media informasi organisasi'
    },
    {
        name: 'SPT',
        title: 'Student Passions & Talents',
        image: '/PUMA-Website/division/spt.JPG',
        description: 'Mengembangkan kreativitas dan bakat individual setiap anggota'
    },
    {
        name: 'BD',
        title: 'Businees Development',
        image: '/PUMA-Website/division/Technoprenet.JPG',
        description: 'Membangun jiwa kewirausahaan berbasis teknologi dan inovasi'
    }
]

const goToDivisionMembers = (divisionName: string) => {
    router.push(`/division/${divisionName}?cabinet=Sapientia`)
}

onMounted(() => {
    setTimeout(() => {
        isLoading.value = false
    }, 300);
})
</script>

<template>
    <Navbar />
    <div class="min-h-screen bg-gray-100">
        <!-- Hero Section -->
        <section class="relative bg-black text-white py-20 overflow-hidden">
            <!-- Playing Cards Background -->
    <div class="absolute inset-0 opacity-15">
      <!-- Ace of Spades -->
      <div class="absolute top-10 left-10 w-24 h-36 bg-white rounded-lg transform rotate-12 shadow-lg">
        <div class="p-2 text-black text-xs font-bold">A</div>
        <div class="absolute top-2 right-2 text-black text-xs font-bold rotate-180">A</div>
        <div class="absolute inset-0 flex items-center justify-center text-black text-2xl">♠</div>
      </div>
      
      <!-- Ace of Hearts -->
      <div class="absolute top-32 right-20 w-24 h-36 bg-white rounded-lg transform -rotate-6 shadow-lg">
        <div class="p-2 text-red-500 text-xs font-bold">A</div>
        <div class="absolute top-2 right-2 text-red-500 text-xs font-bold rotate-180">A</div>
        <div class="absolute inset-0 flex items-center justify-center text-red-500 text-2xl">♥</div>
      </div>
      
      <!-- Ace of Clubs -->
      <div class="absolute bottom-20 left-1/4 w-24 h-36 bg-white rounded-lg transform rotate-45 shadow-lg">
        <div class="p-2 text-black text-xs font-bold">A</div>
        <div class="absolute top-2 right-2 text-black text-xs font-bold rotate-180">A</div>
        <div class="absolute inset-0 flex items-center justify-center text-black text-2xl">♣</div>
      </div>
      
      <!-- Ace of Diamonds -->
      <div class="absolute bottom-32 right-1/4 w-24 h-36 bg-white rounded-lg transform -rotate-12 shadow-lg">
        <div class="p-2 text-red-500 text-xs font-bold">A</div>
        <div class="absolute top-2 right-2 text-red-500 text-xs font-bold rotate-180">A</div>
        <div class="absolute inset-0 flex items-center justify-center text-red-500 text-2xl">♦</div>
      </div>
      
      <!-- Additional scattered cards -->
      <div class="absolute top-1/2 left-16 w-20 h-30 bg-white rounded-lg transform rotate-75 shadow-xl opacity-80"></div>
      <div class="absolute top-1/3 right-32 w-20 h-30 bg-white rounded-lg transform -rotate-30 shadow-xl opacity-80"></div>
    </div>
            
            <div class="container mx-auto px-6 relative z-10">
                <div class="text-center max-w-4xl mx-auto">
                    <!-- Badge -->
                    <div class="inline-flex items-center px-6 py-3 bg-white text-black rounded-full text-sm font-bold mb-8 border-2 border-gray-300 shadow-lg">
                        <span class="w-2 h-2 bg-black rounded-full mr-3 animate-pulse"></span>
                        CURRENT CABINET
                    </div>
                    
                    <!-- Main Title -->
                    <h1 class="text-6xl md:text-8xl font-black mb-8 leading-tight tracking-tight">
                        <span class="text-white drop-shadow-2xl">
                            PUMA
                        </span>
                        <br>
                        <span class="text-white drop-shadow-2xl">
                            SAPIENTIA
                        </span>
                    </h1>
                    
                    <!-- Description -->
                    <p class="text-xl md:text-2xl text-gray-300 mb-10 leading-relaxed font-light">
                        Kabinet yang mengusung keunggulan akademik dan intelektual melalui strategi yang tepat, seperti permainan kartu yang membutuhkan kecerdasan dan perhitungan matang.
                    </p>
                    
                    <!-- Stats -->
                    <div class="flex justify-center items-center space-x-12 text-lg">
                        <div class="flex items-center bg-white/10 px-4 py-2 rounded-full backdrop-blur-sm">
                            <div class="w-3 h-3 bg-white rounded-full mr-3"></div>
                            <span class="text-white font-medium">7 ELITE DIVISON</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Divisions Section -->
        <section class="py-20 bg-white">
            <div class="container mx-auto px-6">
                <!-- Section Header -->
                <div class="text-center mb-16">
                    <h2 class="text-4xl md:text-5xl font-black text-black mb-4 tracking-tight">
                        DIVISION
                    </h2>
                    <div class="w-24 h-1 bg-black mx-auto mb-6"></div>
                    <p class="text-xl text-gray-600 max-w-2xl mx-auto font-light">
                        Setiap divisi adalah kartu as dalam permainan besar organisasi
                    </p>
                </div>

                <!-- Loading State -->
                <div v-if="isLoading" class="flex justify-center items-center py-20">
                    <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-black"></div>
                </div>

                <!-- Divisions Grid -->
                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div 
                        v-for="(division, index) in divisions" 
                        :key="division.name"
                        class="group relative bg-black text-white rounded-lg shadow-2xl hover:shadow-3xl transition-all duration-500 overflow-hidden cursor-pointer transform hover:scale-105 border-2 border-gray-800 hover:border-white"
                        :style="{ animationDelay: `${index * 100}ms` }"
                        @click="goToDivisionMembers(division.name)"
                    >
                        <!-- Card Corner Decoration -->
                        <div class="absolute top-3 left-3 w-6 h-6 border-l-2 border-t-2 border-white opacity-60"></div>
                        <div class="absolute top-3 right-3 w-6 h-6 border-r-2 border-t-2 border-white opacity-60"></div>
                        <div class="absolute bottom-3 left-3 w-6 h-6 border-l-2 border-b-2 border-white opacity-60"></div>
                        <div class="absolute bottom-3 right-3 w-6 h-6 border-r-2 border-b-2 border-white opacity-60"></div>
                        
                        <!-- Card Image -->
                        <div class="relative h-48 overflow-hidden">
                            <img 
                                :src="division.image" 
                                :alt="division.title"
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110 filter grayscale group-hover:grayscale-0"
                                @error="(event) => handleImageError(event, division.name)"
                            />
                            
                            <!-- Gradient Overlay -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black via-black/50 to-transparent"></div>
                            
                            <!-- Playing Card Symbol -->
                            <div class="absolute top-4 right-4 w-12 h-12 bg-white text-black rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 transform rotate-12 group-hover:rotate-0">
                                <span class="text-2xl font-bold">A</span>
                            </div>
                            
                            <!-- Division Badge -->
                            <div class="absolute bottom-4 left-4">
                                <span class="inline-flex items-center px-4 py-2 rounded border-2 border-white text-sm font-bold bg-black/80 text-white backdrop-blur-sm">
                                    {{ division.name }}
                                </span>
                            </div>
                        </div>
                        
                        <!-- Card Content -->
                        <div class="p-6">
                            <h3 class="text-2xl font-black text-white mb-3 group-hover:text-gray-300 transition-colors duration-300 tracking-tight">
                                {{ division.title }}
                            </h3>
                            <p class="text-gray-300 text-sm leading-relaxed line-clamp-3 mb-6 font-light">
                                {{ division.description }}
                            </p>
                            
                            <!-- Action Button -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center text-white font-bold text-sm group-hover:text-gray-300 transition-colors duration-300">
                                    <span>LIHAT ANGGOTA</span>
                                    <svg class="w-4 h-4 ml-2 transform group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </div>
                                <div class="w-8 h-8 border-2 border-white rounded-full flex items-center justify-center opacity-60 group-hover:opacity-100 transition-opacity duration-300">
                                    <span class="text-xs font-bold">{{ index + 1 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Bottom Section -->
        <section class="py-20 bg-black text-white relative overflow-hidden">
            <!-- Playing Cards Background -->
     <div class="absolute inset-0 opacity-20">
       <div class="absolute top-10 left-20 w-16 h-24 bg-white rounded transform rotate-45 shadow-lg"></div>
       <div class="absolute top-32 right-32 w-16 h-24 bg-white rounded transform -rotate-12 shadow-lg"></div>
       <div class="absolute bottom-20 left-1/3 w-16 h-24 bg-white rounded transform rotate-75 shadow-lg"></div>
       <div class="absolute bottom-32 right-20 w-16 h-24 bg-white rounded transform -rotate-30 shadow-lg"></div>
     </div>
            
            <div class="container mx-auto px-6 text-center relative z-10">
                <div class="max-w-4xl mx-auto">
                    <h2 class="text-4xl md:text-6xl font-black mb-6 tracking-tight">
                        CABINET
                        <br>
                        <span class="text-white">SAPIENTIA</span>
                    </h2>
                    <div class="w-32 h-1 bg-white mx-auto mb-8"></div>
                    <p class="text-xl md:text-2xl text-gray-300 mb-12 max-w-3xl mx-auto font-light leading-relaxed">
                        Setiap langkah adalah strategi, setiap keputusan adalah kartu yang dimainkan dengan perhitungan matang.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-6 justify-center items-center">
                        <button class="bg-white text-black px-8 py-4 rounded-lg font-black text-lg hover:bg-gray-200 transition-all duration-300 shadow-2xl transform hover:scale-105 border-2 border-white">
                            HUBUNGI KAMI
                        </button>
                        <button class="border-2 border-white text-white px-8 py-4 rounded-lg font-black text-lg hover:bg-white hover:text-black transition-all duration-300 transform hover:scale-105">
                            PELAJARI LEBIH LANJUT
                        </button>
                    </div>
                    
                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-16">
                        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-6 border border-white/20">
                            <div class="text-3xl font-black text-white mb-2">7</div>
                            <div class="text-gray-300 font-medium">ELITE DIVISION</div>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-6 border border-white/20">
                            <div class="text-3xl font-black text-white mb-2">2025</div>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-6 border border-white/20">
                            <div class="text-3xl font-black text-white mb-2">56</div>
                            <div class="text-gray-300 font-medium">ANGGOTA AKTIF</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <Footer />
</template>

<style scoped>
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.grid > div {
    animation: fadeIn 0.5s ease-out forwards;
}

.grid > div:nth-child(1) { animation-delay: 0.1s; }
.grid > div:nth-child(2) { animation-delay: 0.2s; }
.grid > div:nth-child(3) { animation-delay: 0.3s; }
.grid > div:nth-child(4) { animation-delay: 0.4s; }
.grid > div:nth-child(5) { animation-delay: 0.5s; }
.grid > div:nth-child(6) { animation-delay: 0.6s; }
.grid > div:nth-child(7) { animation-delay: 0.7s; }
.grid > div:nth-child(8) { animation-delay: 0.8s; }
.grid > div:nth-child(9) { animation-delay: 0.9s; }
</style>