<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import Navbar from '../Navbar.vue'
import Footer from '../Footer.vue'
import CommentSection from '../CommentSection.vue'

interface Video {
  id: string
  title: string
  description: string
  filename: string
  uploadDate: string
  accessToken: string
  duration?: string
  thumbnail?: string
  category?: string
}

interface UserAccess {
  token: string
  name: string
  email: string
  videoAccess: string[]
  isValid: boolean
}

const videos = ref<Video[]>([
  {
    id: '1',
    title: 'Aformation Session 1 - Introduction to Programming',
    description: 'Pengenalan dasar-dasar programming dan konsep fundamental yang perlu dipahami setiap developer.',
    filename: 'aformation_session_1.mp4',
    uploadDate: '2024-01-15',
    accessToken: 'af2024_intro_001',
    duration: '45:30',
    category: 'Introduction'
  },
  {
    id: '2',
    title: 'Aformation Session 2 - Web Development Fundamentals',
    description: 'Pembelajaran fundamental web development menggunakan HTML, CSS, dan JavaScript.',
    filename: 'aformation_session_2.mp4',
    uploadDate: '2024-01-22',
    accessToken: 'af2024_web_002',
    duration: '52:15',
    category: 'Web Development'
  },
  {
    id: '3',
    title: 'Aformation Session 3 - Database Design',
    description: 'Konsep database design, normalisasi, dan best practices dalam merancang database.',
    filename: 'aformation_session_3.mp4',
    uploadDate: '2024-01-29',
    accessToken: 'af2024_db_003',
    duration: '48:20',
    category: 'Database'
  },
  {
    id: '4',
    title: 'Aformation Session 4 - API Development',
    description: 'Membangun RESTful API dan memahami konsep backend development.',
    filename: 'aformation_session_4.mp4',
    uploadDate: '2024-02-05',
    accessToken: 'af2024_api_004',
    duration: '55:45',
    category: 'Backend'
  }
])

const userAccess = ref<UserAccess | null>(null)
const accessToken = ref('')
const showTokenInput = ref(true)
const isValidating = ref(false)
const selectedVideo = ref<Video | null>(null)
const showVideoPlayer = ref(false)
const searchQuery = ref('')
const selectedCategory = ref('all')
const tokenError = ref('')

const categories = computed(() => {
  const cats = ['all', ...new Set(videos.value.map(v => v.category).filter(Boolean))]
  return cats
})

const filteredVideos = computed(() => {
  let filtered = videos.value
  
  // Filter by user access
  if (userAccess.value) {
    filtered = filtered.filter(video => userAccess.value!.videoAccess.includes(video.id))
  }
  
  // Filter by search query
  if (searchQuery.value) {
    filtered = filtered.filter(video => 
      video.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      video.description.toLowerCase().includes(searchQuery.value.toLowerCase())
    )
  }
  
  // Filter by category
  if (selectedCategory.value !== 'all') {
    filtered = filtered.filter(video => video.category === selectedCategory.value)
  }
  
  return filtered
})

const validateToken = async () => {
  if (!accessToken.value.trim()) {
    tokenError.value = 'Please enter your access token'
    return
  }
  
  isValidating.value = true
  tokenError.value = ''
  
  try {
    // Simulate API call to validate token
    await new Promise(resolve => setTimeout(resolve, 1500))
    
    // Check if token exists in localStorage (demo purposes)
    const registrations = JSON.parse(localStorage.getItem('aformationRegistrations') || '[]')
    const userRegistration = registrations.find((reg: any) => reg.token === accessToken.value)
    
    if (userRegistration) {
      // Valid token from registration
      userAccess.value = {
        token: accessToken.value,
        name: userRegistration.name,
        email: userRegistration.email,
        videoAccess: ['1', '2', '3', '4'], // Grant access to all videos for registered users
        isValid: true
      }
      showTokenInput.value = false
    } else {
      // Check predefined demo tokens
      const demoTokens: Record<string, UserAccess> = {
        'aformation_demo_001': {
          token: 'aformation_demo_001',
          name: 'Demo User 1',
          email: 'demo1@example.com',
          videoAccess: ['1', '2'],
          isValid: true
        },
        'aformation_demo_002': {
          token: 'aformation_demo_002',
          name: 'Demo User 2',
          email: 'demo2@example.com',
          videoAccess: ['1', '2', '3', '4'],
          isValid: true
        }
      }
      
      if (demoTokens[accessToken.value]) {
        userAccess.value = demoTokens[accessToken.value]
        showTokenInput.value = false
      } else {
        tokenError.value = 'Invalid access token. Please check your token and try again.'
      }
    }
  } catch (error) {
    tokenError.value = 'Failed to validate token. Please try again.'
  } finally {
    isValidating.value = false
  }
}

const logout = () => {
  userAccess.value = null
  accessToken.value = ''
  showTokenInput.value = true
  showVideoPlayer.value = false
  selectedVideo.value = null
  tokenError.value = ''
}

const playVideo = (video: Video) => {
  selectedVideo.value = video
  showVideoPlayer.value = true
  
  // Increment view count (in real app, this would be sent to backend)
  // For demo purposes, we'll just log it
  console.log(`Playing video: ${video.title}`)
}

const closeVideoPlayer = () => {
  showVideoPlayer.value = false
  selectedVideo.value = null
}

const formatDuration = (duration?: string) => {
  return duration || '00:00'
}

const formatDate = (dateString?: string) => {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const getCardBg = (index: number): string => {
  const backgrounds = [
    'from-white/90 to-blue-50/90',
    'from-white/90 to-green-50/90',
    'from-white/90 to-purple-50/90',
    'from-white/90 to-orange-50/90',
    'from-white/90 to-pink-50/90',
    'from-white/90 to-yellow-50/90'
  ];
  return backgrounds[index % backgrounds.length];
}

const getCardRotation = (index: number): string => {
  const rotations = ['rotate-1', '-rotate-1', 'rotate-0', 'rotate-2', '-rotate-2'];
  return rotations[index % rotations.length];
}

const getPinColor = (index: number): string => {
  const colors = [
    'bg-red-500',
    'bg-blue-500',
    'bg-green-500',
    'bg-yellow-500',
    'bg-purple-500',
    'bg-pink-500',
    'bg-orange-500',
    'bg-indigo-500'
  ];
  return colors[index % colors.length];
}

// Check for token in URL params on mount
onMounted(() => {
  const urlParams = new URLSearchParams(window.location.search)
  const tokenFromUrl = urlParams.get('token')
  if (tokenFromUrl) {
    accessToken.value = tokenFromUrl
    validateToken()
  }
})
</script>

<template>
  <div class="min-h-screen relative overflow-hidden">
    <!-- Animated Background -->
    <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-gray-900 to-black">
      <div class="absolute inset-0 dot-background opacity-30"></div>
      <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 1px 1px, rgba(100,116,139,0.15) 1px, transparent 0); background-size: 32px 32px;"></div>
      
      <!-- Enhanced Animated Dots Pattern -->
      <div class="absolute inset-0">
        <div class="absolute w-2 h-2 bg-blue-400/30 rounded-full animate-pulse" style="top: 10%; left: 15%; animation-delay: 0s;"></div>
        <div class="absolute w-1 h-1 bg-purple-400/40 rounded-full animate-pulse" style="top: 25%; left: 80%; animation-delay: 1s;"></div>
        <div class="absolute w-3 h-3 bg-orange-400/20 rounded-full animate-pulse" style="top: 60%; left: 10%; animation-delay: 2s;"></div>
        <div class="absolute w-1.5 h-1.5 bg-green-400/35 rounded-full animate-pulse" style="top: 80%; left: 70%; animation-delay: 3s;"></div>
        <div class="absolute w-2 h-2 bg-pink-400/25 rounded-full animate-pulse" style="top: 40%; left: 90%; animation-delay: 4s;"></div>
        <div class="absolute w-1 h-1 bg-yellow-400/40 rounded-full animate-pulse" style="top: 15%; left: 60%; animation-delay: 5s;"></div>
        <div class="absolute w-2.5 h-2.5 bg-indigo-400/20 rounded-full animate-pulse" style="top: 70%; left: 30%; animation-delay: 6s;"></div>
        <div class="absolute w-1 h-1 bg-red-400/30 rounded-full animate-pulse" style="top: 35%; left: 45%; animation-delay: 7s;"></div>
      </div>
      
      <!-- Floating Geometric Shapes -->
      <div class="absolute inset-0">
        <div class="absolute w-20 h-20 border border-blue-200/20 rotate-45 animate-spin" style="top: 20%; right: 10%; animation-duration: 20s;"></div>
        <div class="absolute w-16 h-16 border border-purple-200/15 rotate-12 animate-spin" style="bottom: 30%; left: 5%; animation-duration: 25s; animation-direction: reverse;"></div>
        <div class="absolute w-12 h-12 bg-gradient-to-br from-orange-200/10 to-pink-200/10 rotate-45" style="top: 50%; right: 20%; animation: float 15s ease-in-out infinite;"></div>
      </div>
      
      <!-- Floating Pins -->
      <div class="absolute w-3 h-3 bg-blue-500 rounded-full shadow-md top-20 left-10 rotate-12 animate-pulse">
        <div class="absolute bg-white rounded-full inset-0.5 opacity-40"></div>
      </div>
      <div class="absolute w-2 h-2 bg-purple-500 rounded-full shadow-md top-32 right-20 rotate-45 animate-bounce">
        <div class="absolute bg-white rounded-full inset-0.5 opacity-40"></div>
      </div>
      <div class="absolute w-4 h-4 bg-green-500 rounded-full shadow-md bottom-40 left-16 -rotate-12 animate-pulse">
        <div class="absolute bg-white rounded-full inset-0.5 opacity-40"></div>
      </div>
    </div>
    
    <Navbar />
    
    <div class="relative z-10 pt-20 pb-16">
      <!-- Token Input Section -->
      <div v-if="showTokenInput" class="max-w-lg mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative group">
          <div class="relative p-8 transform bg-gradient-to-br from-white/95 to-gray-50/95 border border-gray-200/50 shadow-2xl hover:shadow-3xl transition-all duration-500 -rotate-1 backdrop-blur-lg" style="clip-path: polygon(0 0, 100% 0, 100% 92%, 92% 100%, 0 100%);">
            <!-- Decorative Pins -->
            <div class="absolute -top-2 -left-2 w-4 h-4 bg-blue-500 rounded-full shadow-lg z-10 group-hover:scale-110 transition-transform duration-300">
              <div class="absolute bg-white rounded-full inset-1 opacity-40"></div>
            </div>
            <div class="absolute -top-2 -right-2 w-4 h-4 bg-purple-500 rounded-full shadow-lg z-10 group-hover:scale-110 transition-transform duration-300">
              <div class="absolute bg-white rounded-full inset-1 opacity-40"></div>
            </div>
            
            <!-- Paper Tape Effect -->
            <div class="absolute w-8 h-4 transform bg-gray-100/80 border border-gray-200 shadow-sm top-4 right-4 opacity-80 rotate-25"></div>
            
            <div class="text-center mb-6">
              <div class="w-20 h-20 bg-gradient-to-br from-black to-gray-800 rounded-full flex items-center justify-center mx-auto mb-4 shadow-xl group-hover:scale-105 transition-transform duration-300">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                </svg>
              </div>
              <h1 class="text-3xl font-bold tracking-wider text-black uppercase mb-2">Access Portal</h1>
              <div class="w-24 h-px mx-auto mb-4 bg-gray-400"></div>
              <p class="text-gray-700 font-medium">Enter your classified access token to unlock exclusive aformation content</p>
            </div>
          
            <div class="space-y-6">
              <div>
                <label class="block text-sm font-bold tracking-wider text-gray-800 uppercase mb-2">Classified Token</label>
                <div class="relative">
                  <input v-model="accessToken" type="text" 
                         :class="tokenError ? 'border-red-400 focus:ring-red-500 focus:border-red-500 bg-red-50/50' : 'border-gray-300 focus:ring-black focus:border-black bg-white/70'"
                         class="w-full px-4 py-3 border-2 rounded-lg shadow-sm focus:ring-2 focus:ring-opacity-50 font-mono text-sm backdrop-blur-sm transition-all duration-300"
                         placeholder="Enter classified access code..."
                         @keyup.enter="validateToken">
                  <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                  </div>
                </div>
                <p v-if="tokenError" class="mt-2 text-sm text-red-600 font-medium flex items-center">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  {{ tokenError }}
                </p>
              </div>
              
              <button @click="validateToken" :disabled="isValidating" 
                      class="w-full px-6 py-3 bg-gradient-to-r from-black to-gray-800 text-white font-bold tracking-wider uppercase rounded-lg hover:from-gray-800 hover:to-black disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                <svg v-if="isValidating" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ isValidating ? 'Authenticating...' : 'Unlock Access' }}
              </button>
            </div>
          
            <div class="mt-6 p-4 bg-gradient-to-r from-blue-50/80 to-indigo-50/80 border-l-4 border-blue-400 rounded-lg backdrop-blur-sm">
              <div class="flex items-start">
                <div class="px-2 py-1 mr-3 text-xs font-bold tracking-wider text-blue-700 uppercase border border-blue-400 rounded bg-blue-100/50">
                  Intel
                </div>
                <div class="text-sm text-blue-800">
                  <p class="font-bold mb-2 tracking-wider uppercase">Demo Access Codes:</p>
                  <div class="space-y-2">
                    <div class="p-2 bg-white/60 border border-blue-200 rounded">
                      <code class="font-mono text-xs bg-blue-100 px-2 py-1 rounded">aformation_demo_001</code>
                      <span class="ml-2 text-xs text-blue-700">• Limited Access (2 videos)</span>
                    </div>
                    <div class="p-2 bg-white/60 border border-blue-200 rounded">
                      <code class="font-mono text-xs bg-blue-100 px-2 py-1 rounded">aformation_demo_002</code>
                      <span class="ml-2 text-xs text-blue-700">• Full Access (All videos)</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Evidence Tag -->
            <div class="absolute px-3 py-1 font-mono text-xs font-bold tracking-wider text-gray-600 uppercase transform border border-gray-600 -bottom-2 -left-4 -rotate-12 opacity-70 bg-white/80">
              ACCESS #001
            </div>
            <div class="absolute bottom-0 right-0 w-8 h-8 bg-gray-200 opacity-60" style="clip-path: polygon(100% 0, 0 100%, 100% 100%);"></div>
          </div>
        </div>
      </div>

      <!-- Video Library Section -->
      <div v-else class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="relative mb-12">
          <div class="relative group">
            <div class="relative p-8 transform bg-gradient-to-br from-white/95 to-gray-50/95 border border-gray-200/50 shadow-2xl hover:shadow-3xl transition-all duration-500 rotate-1 backdrop-blur-lg" style="clip-path: polygon(0 0, 100% 0, 100% 92%, 92% 100%, 0 100%);">
              <!-- Decorative Pins -->
              <div class="absolute -top-2 -left-2 w-4 h-4 bg-green-500 rounded-full shadow-lg z-10 group-hover:scale-110 transition-transform duration-300">
                <div class="absolute bg-white rounded-full inset-1 opacity-40"></div>
              </div>
              <div class="absolute -top-2 -right-2 w-4 h-4 bg-yellow-500 rounded-full shadow-lg z-10 group-hover:scale-110 transition-transform duration-300">
                <div class="absolute bg-white rounded-full inset-1 opacity-40"></div>
              </div>
              
              <!-- Paper Tape Effect -->
              <div class="absolute w-10 h-5 transform bg-gray-100/80 border border-gray-200 shadow-sm top-4 right-4 opacity-80 rotate-25"></div>
              
              <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div class="mb-4 lg:mb-0">
                  <div class="flex items-center mb-3">
                    <div class="px-3 py-1 mr-4 text-xs font-bold tracking-wider text-green-700 uppercase border border-green-400 rounded bg-green-100/50">
                      Authorized
                    </div>
                    <h1 class="text-3xl font-bold tracking-wider text-black uppercase">Video Archive</h1>
                  </div>
                  <div class="w-32 h-px mb-3 bg-gray-400"></div>
                  <p class="text-gray-700 font-medium">Agent <span class="font-bold text-black">{{ userAccess?.name }}</span> • Clearance Level: <span class="font-bold text-green-600">{{ userAccess?.videoAccess.length }} Files</span></p>
                </div>
                
                <button @click="logout" 
                        class="px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white font-bold tracking-wider uppercase rounded-lg hover:from-red-700 hover:to-red-800 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center">
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                  </svg>
                  Terminate Session
                </button>
              </div>
              
              <!-- Evidence Tag -->
              <div class="absolute px-3 py-1 font-mono text-xs font-bold tracking-wider text-gray-600 uppercase transform border border-gray-600 -bottom-2 -left-4 -rotate-12 opacity-70 bg-white/80">
                ARCHIVE #002
              </div>
              <div class="absolute bottom-0 right-0 w-8 h-8 bg-gray-200 opacity-60" style="clip-path: polygon(100% 0, 0 100%, 100% 100%);"></div>
            </div>
          </div>
        </div>

        <!-- Filters -->
        <div class="relative mb-12">
          <div class="relative group">
            <div class="relative p-6 transform bg-gradient-to-br from-white/90 to-gray-50/90 border border-gray-200/50 shadow-xl hover:shadow-2xl transition-all duration-300 -rotate-1 backdrop-blur-lg" style="clip-path: polygon(0 0, 100% 0, 100% 95%, 95% 100%, 0 100%);">
              <!-- Decorative Pins -->
              <div class="absolute -top-2 -left-2 w-3 h-3 bg-blue-500 rounded-full shadow-lg z-10 group-hover:scale-110 transition-transform duration-300">
                <div class="absolute bg-white rounded-full inset-0.5 opacity-40"></div>
              </div>
              <div class="absolute -top-2 -right-2 w-3 h-3 bg-purple-500 rounded-full shadow-lg z-10 group-hover:scale-110 transition-transform duration-300">
                <div class="absolute bg-white rounded-full inset-0.5 opacity-40"></div>
              </div>
              
              <div class="flex items-center mb-4">
                <div class="px-2 py-1 mr-3 text-xs font-bold tracking-wider text-gray-700 uppercase border border-gray-400 rounded bg-gray-100/50">
                  Filter
                </div>
                <h3 class="text-lg font-bold tracking-wider text-black uppercase">Search Parameters</h3>
              </div>
              
              <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                  <label class="block text-xs font-bold tracking-wider text-gray-700 uppercase mb-2">Search Query</label>
                  <div class="relative">
                    <input v-model="searchQuery" type="text" placeholder="Enter search terms..." 
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black bg-white/70 backdrop-blur-sm font-mono text-sm transition-all duration-300">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                      <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                      </svg>
                    </div>
                  </div>
                </div>
                <div class="sm:w-64">
                  <label class="block text-xs font-bold tracking-wider text-gray-700 uppercase mb-2">Category Filter</label>
                  <select v-model="selectedCategory" 
                          class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black bg-white/70 backdrop-blur-sm font-mono text-sm transition-all duration-300">
                    <option value="all">All Classifications</option>
                    <option v-for="category in categories.filter(c => c !== 'all')" :key="category" :value="category">
                      {{ category }}
                    </option>
                  </select>
                </div>
              </div>
              
              <!-- Evidence Tag -->
              <div class="absolute px-2 py-1 font-mono text-xs font-bold tracking-wider text-gray-600 uppercase transform border border-gray-600 -bottom-1 -right-3 rotate-12 opacity-70 bg-white/80">
                CTRL
              </div>
            </div>
          </div>
        </div>

        <!-- Videos Grid -->
        <div v-if="filteredVideos.length === 0" class="text-center py-16">
          <div class="relative group max-w-md mx-auto">
            <div class="relative p-8 transform bg-gradient-to-br from-white/90 to-gray-50/90 border border-gray-200/50 shadow-xl transition-all duration-300 rotate-1 backdrop-blur-lg" style="clip-path: polygon(0 0, 100% 0, 100% 92%, 92% 100%, 0 100%);">
              <!-- Decorative Pins -->
              <div class="absolute -top-2 -left-2 w-3 h-3 bg-red-500 rounded-full shadow-lg z-10">
                <div class="absolute bg-white rounded-full inset-0.5 opacity-40"></div>
              </div>
              <div class="absolute -top-2 -right-2 w-3 h-3 bg-orange-500 rounded-full shadow-lg z-10">
                <div class="absolute bg-white rounded-full inset-0.5 opacity-40"></div>
              </div>
              
              <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
              </svg>
              <h3 class="text-lg font-bold tracking-wider text-black uppercase mb-2">No Files Found</h3>
              <div class="w-16 h-px mx-auto mb-3 bg-gray-400"></div>
              <p class="text-sm text-gray-600 font-medium">No classified videos match your search parameters</p>
              
              <!-- Evidence Tag -->
              <div class="absolute px-2 py-1 font-mono text-xs font-bold tracking-wider text-gray-600 uppercase transform border border-gray-600 -bottom-1 -right-3 rotate-12 opacity-70 bg-white/80">
                NULL
              </div>
            </div>
          </div>
        </div>
        
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div v-for="(video, index) in filteredVideos" :key="video.id" class="relative group">
            <div :class="`relative transform bg-gradient-to-br ${getCardBg(index)} border border-gray-200/50 shadow-xl hover:shadow-2xl transition-all duration-500 ${getCardRotation(index)} backdrop-blur-lg overflow-hidden`" style="clip-path: polygon(0 0, 100% 0, 100% 92%, 92% 100%, 0 100%);">
              <!-- Decorative Pins -->
              <div :class="`absolute -top-2 -left-2 w-3 h-3 ${getPinColor(index * 2)} rounded-full shadow-lg z-10 group-hover:scale-110 transition-transform duration-300`">
                <div class="absolute bg-white rounded-full inset-0.5 opacity-40"></div>
              </div>
              <div :class="`absolute -top-2 -right-2 w-3 h-3 ${getPinColor(index * 2 + 1)} rounded-full shadow-lg z-10 group-hover:scale-110 transition-transform duration-300`">
                <div class="absolute bg-white rounded-full inset-0.5 opacity-40"></div>
              </div>
              
              <!-- Paper Tape Effect -->
              <div class="absolute w-6 h-3 transform bg-gray-100/80 border border-gray-200 shadow-sm top-3 right-3 opacity-80 rotate-12"></div>
              
              <!-- Video Thumbnail -->
              <div class="aspect-video bg-gradient-to-br from-gray-800 via-gray-900 to-black flex items-center justify-center relative cursor-pointer group-hover:from-gray-700 group-hover:via-gray-800 group-hover:to-gray-900 transition-all duration-500"
                   @click="playVideo(video)">
                <div class="absolute inset-0 bg-black bg-opacity-30 group-hover:bg-opacity-10 transition-all duration-300"></div>
                <div class="relative z-10 text-center">
                  <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:bg-white/30 group-hover:scale-110 transition-all duration-300 shadow-lg">
                    <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M8 5v14l11-7z"/>
                    </svg>
                  </div>
                  <div class="px-3 py-1 bg-black/70 rounded-full backdrop-blur-sm">
                    <p class="text-white text-sm font-bold tracking-wider">{{ formatDuration(video.duration) }}</p>
                  </div>
                </div>
                
                <!-- Category Badge -->
                <div v-if="video.category" class="absolute top-4 left-4">
                  <div class="px-3 py-1 bg-gradient-to-r from-black/80 to-gray-800/80 text-white text-xs font-bold tracking-wider uppercase rounded-full backdrop-blur-sm border border-white/20">
                    {{ video.category }}
                  </div>
                </div>
                
                <!-- Classification Level -->
                <div class="absolute top-4 right-4">
                  <div class="px-2 py-1 bg-red-600/90 text-white text-xs font-bold tracking-wider uppercase rounded border border-red-400">
                    CLASSIFIED
                  </div>
                </div>
              </div>
              
              <!-- Video Info -->
              <div class="p-6 bg-white/80 backdrop-blur-sm">
                <div class="flex items-center mb-3">
                  <div class="px-2 py-1 mr-3 text-xs font-bold tracking-wider text-gray-700 uppercase border border-gray-400 rounded bg-gray-100/70">
                    File
                  </div>
                  <div class="w-full h-px bg-gray-300"></div>
                </div>
                
                <h3 class="font-mono font-bold text-black mb-3 line-clamp-2 tracking-wider uppercase text-sm">{{ video.title }}</h3>
                <p class="font-mono text-xs text-gray-700 mb-4 line-clamp-3 leading-relaxed">{{ video.description }}</p>
                
                <div class="flex items-center justify-between">
                  <div class="text-xs text-gray-600 font-mono">
                    <div class="mb-1">{{ formatDate(video.uploadDate) }}</div>
                    <div class="text-gray-500">ID: {{ video.id.padStart(3, '0') }}</div>
                  </div>
                  <button @click="playVideo(video)" 
                          class="px-4 py-2 bg-gradient-to-r from-black to-gray-800 text-white text-xs font-mono font-bold tracking-wider uppercase rounded hover:from-gray-800 hover:to-black transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                    Access File
                  </button>
                </div>
              </div>
              
              <!-- Evidence Tag -->
              <div class="absolute px-2 py-1 font-mono text-xs font-bold tracking-wider text-gray-600 uppercase transform border border-gray-600 -bottom-1 -left-3 -rotate-12 opacity-70 bg-white/90">
                VID #{{ video.id.padStart(3, '0') }}
              </div>
              <div class="absolute bottom-0 right-0 w-6 h-6 bg-gray-200/60" style="clip-path: polygon(100% 0, 0 100%, 100% 100%);"></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Video Player Modal -->
    <div v-if="showVideoPlayer && selectedVideo" 
         class="fixed inset-0 bg-black/90 backdrop-blur-sm flex items-center justify-center z-50 p-4">
      <div class="relative max-w-7xl w-full max-h-[95vh] overflow-hidden">
        <!-- Background Card with Clip Path -->
        <div class="relative bg-gradient-to-br from-white/95 to-gray-50/95 border border-gray-200/50 shadow-2xl backdrop-blur-lg" style="clip-path: polygon(0 0, 100% 0, 100% 96%, 96% 100%, 0 100%);">
          <!-- Decorative Pins -->
          <div class="absolute -top-3 -left-3 w-4 h-4 bg-red-500 rounded-full shadow-lg z-20">
            <div class="absolute bg-white rounded-full inset-0.5 opacity-40"></div>
          </div>
          <div class="absolute -top-3 -right-3 w-4 h-4 bg-blue-500 rounded-full shadow-lg z-20">
            <div class="absolute bg-white rounded-full inset-0.5 opacity-40"></div>
          </div>
          <div class="absolute -bottom-3 -left-3 w-4 h-4 bg-green-500 rounded-full shadow-lg z-20">
            <div class="absolute bg-white rounded-full inset-0.5 opacity-40"></div>
          </div>
          
          <!-- Paper Tape Effects -->
          <div class="absolute w-8 h-4 transform bg-gray-100/80 border border-gray-200 shadow-sm top-4 right-8 opacity-80 rotate-12"></div>
          <div class="absolute w-6 h-3 transform bg-gray-100/80 border border-gray-200 shadow-sm bottom-8 left-12 opacity-80 -rotate-6"></div>
          
          <!-- Modal Header -->
          <div class="flex items-center justify-between p-8 border-b border-gray-200/50">
            <div class="flex items-center space-x-4">
              <div class="px-3 py-1 text-xs font-bold tracking-wider text-gray-700 uppercase border border-gray-400 rounded bg-gray-100/70">
                Classified File
              </div>
              <div class="w-32 h-px bg-gray-300"></div>
              <h2 class="text-xl font-mono font-bold text-black tracking-wider uppercase">{{ selectedVideo.title }}</h2>
            </div>
            <button @click="closeVideoPlayer" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100/50 rounded-full transition-all duration-200">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
          
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 p-8">
            <!-- Video Player Section -->
            <div class="lg:col-span-2">
              <!-- Video Player -->
              <div class="aspect-video bg-gradient-to-br from-gray-900 via-black to-gray-800 rounded-lg overflow-hidden shadow-xl border border-gray-700">
                <div class="w-full h-full flex items-center justify-center relative">
                  <!-- Classification Overlay -->
                  <div class="absolute top-4 left-4 z-10">
                    <div class="px-3 py-1 bg-red-600/90 text-white text-xs font-bold tracking-wider uppercase rounded border border-red-400">
                      CLASSIFIED
                    </div>
                  </div>
                  <div class="absolute top-4 right-4 z-10">
                    <div class="px-2 py-1 bg-black/80 text-white text-xs font-mono rounded">
                      ID: {{ selectedVideo.id.padStart(3, '0') }}
                    </div>
                  </div>
                  
                  <div class="text-center text-white">
                    <div class="w-32 h-32 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-6 hover:bg-white/30 transition-all duration-300 shadow-2xl">
                      <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M8 5v14l11-7z"/>
                      </svg>
                    </div>
                    <div class="px-4 py-2 bg-black/70 rounded-full backdrop-blur-sm mb-2">
                      <p class="text-lg font-mono font-bold tracking-wider">{{ selectedVideo.title }}</p>
                    </div>
                    <div class="px-3 py-1 bg-gray-800/70 rounded-full backdrop-blur-sm">
                      <p class="text-sm font-mono opacity-90">Duration: {{ formatDuration(selectedVideo.duration) }}</p>
                    </div>
                    <p class="text-gray-400 font-mono text-xs mt-4">{{ selectedVideo.filename }}</p>
                    <p class="text-gray-500 font-mono text-xs mt-1">In a real implementation, this would be a video player component</p>
                  </div>
                </div>
              </div>
              
              <!-- Video Controls -->
              <div class="mt-6 p-6 bg-white/60 backdrop-blur-sm rounded-lg border border-gray-200/50">
                <div class="flex items-center justify-between mb-4">
                  <div class="flex items-center space-x-3">
                    <div class="px-2 py-1 text-xs font-bold tracking-wider text-gray-700 uppercase border border-gray-400 rounded bg-gray-100/70">
                      Info
                    </div>
                    <div class="text-xs text-gray-600 font-mono">
                      {{ formatDate(selectedVideo.uploadDate) }} • {{ selectedVideo.category }}
                    </div>
                  </div>
                  <div class="flex space-x-3">
                     <button class="px-4 py-2 bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 text-xs font-bold tracking-wider uppercase rounded hover:from-gray-200 hover:to-gray-300 transition-all duration-300 shadow-md">
                       Download
                     </button>
                   </div>
                </div>
                <div class="w-full h-px bg-gray-300 mb-4"></div>
                <p class="text-sm font-mono text-gray-700 leading-relaxed">{{ selectedVideo.description }}</p>
              </div>
            </div>
            
            <!-- Comments Section -->
            <div class="lg:col-span-1">
              <div class="bg-white/60 backdrop-blur-sm rounded-lg border border-gray-200/50 h-full">
                <div class="p-4 border-b border-gray-200/50">
                  <div class="flex items-center space-x-3">
                    <div class="px-2 py-1 text-xs font-bold tracking-wider text-gray-700 uppercase border border-gray-400 rounded bg-gray-100/70">
                      Comments
                    </div>
                    <div class="w-full h-px bg-gray-300"></div>
                  </div>
                </div>
                <div class="max-h-96 overflow-y-auto">
                  <CommentSection 
                    :video-id="selectedVideo.id"
                    :current-user="{
                      id: userAccess?.email || 'anonymous',
                      name: userAccess?.name || 'Anonymous User',
                      role: 'student'
                    }"
                  />
                </div>
              </div>
            </div>
          </div>
          
          <!-- Evidence Tag -->
          <div class="absolute px-3 py-1 font-mono text-xs font-bold tracking-wider text-gray-600 uppercase transform border border-gray-600 -bottom-2 -right-4 rotate-12 opacity-70 bg-white/90">
            FILE #{{ selectedVideo.id.padStart(3, '0') }}
          </div>
          <div class="absolute bottom-0 right-0 w-8 h-8 bg-gray-200/60" style="clip-path: polygon(100% 0, 0 100%, 100% 100%);"></div>
        </div>
      </div>
    </div>
    
    <Footer />
  </div>
</template>

<style scoped>
@keyframes float {
  0%, 100% {
    transform: translateY(0px) rotate(45deg);
  }
  50% {
    transform: translateY(-20px) rotate(45deg);
  }
}

@keyframes drift {
  0%, 100% {
    background-position: 0% 0%, 0% 0%, 0% 0%;
  }
  33% {
    background-position: 30% 30%, -10% 10%, 20% -20%;
  }
  66% {
    background-position: -20% 20%, 20% -10%, -10% 30%;
  }
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.dot-background {
  background-image: radial-gradient(circle at 25% 25%, rgba(59, 130, 246, 0.1) 2px, transparent 2px),
                    radial-gradient(circle at 75% 75%, rgba(168, 85, 247, 0.1) 1px, transparent 1px),
                    radial-gradient(circle at 50% 50%, rgba(34, 197, 94, 0.1) 1.5px, transparent 1.5px);
  background-size: 50px 50px, 30px 30px, 40px 40px;
  animation: drift 30s ease-in-out infinite;
}
</style>