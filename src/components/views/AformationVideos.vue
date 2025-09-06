<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import Navbar from '../Navbar.vue'
import Footer from '../Footer.vue'

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
  <div class="min-h-screen bg-gray-50">
    <Navbar />
    
    <div class="pt-20 pb-16">
      <!-- Token Input Section -->
      <div v-if="showTokenInput" class="max-w-md mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-lg p-8">
          <div class="text-center mb-6">
            <div class="w-16 h-16 bg-black rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
              </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Access Aformation Videos</h1>
            <p class="text-gray-600">Enter your access token to view exclusive aformation content</p>
          </div>
          
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Access Token</label>
              <input v-model="accessToken" type="text" 
                     :class="tokenError ? 'border-red-300 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-black focus:border-black'"
                     class="w-full px-3 py-2 border rounded-md shadow-sm focus:ring-2 focus:ring-opacity-50"
                     placeholder="Enter your access token"
                     @keyup.enter="validateToken">
              <p v-if="tokenError" class="mt-1 text-sm text-red-600">{{ tokenError }}</p>
            </div>
            
            <button @click="validateToken" :disabled="isValidating" 
                    class="w-full px-4 py-2 bg-black text-white font-medium rounded-md hover:bg-gray-800 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center">
              <svg v-if="isValidating" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ isValidating ? 'Validating...' : 'Access Videos' }}
            </button>
          </div>
          
          <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex items-start">
              <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              <div class="text-sm text-blue-800">
                <p class="font-medium mb-1">Demo Tokens:</p>
                <ul class="list-disc list-inside space-y-1">
                  <li><code class="bg-blue-100 px-1 rounded">aformation_demo_001</code> - Access to 2 videos</li>
                  <li><code class="bg-blue-100 px-1 rounded">aformation_demo_002</code> - Access to all videos</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Video Library Section -->
      <div v-else class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
          <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Aformation Video Library</h1>
            <p class="text-gray-600">Welcome back, {{ userAccess?.name }}! You have access to {{ userAccess?.videoAccess.length }} videos.</p>
          </div>
          <button @click="logout" 
                  class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
            Logout
          </button>
        </div>

        <!-- Filters -->
        <div class="flex flex-col sm:flex-row gap-4 mb-8">
          <div class="flex-1">
            <input v-model="searchQuery" type="text" placeholder="Search videos..." 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent">
          </div>
          <div class="sm:w-48">
            <select v-model="selectedCategory" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent">
              <option value="all">All Categories</option>
              <option v-for="category in categories.filter(c => c !== 'all')" :key="category" :value="category">
                {{ category }}
              </option>
            </select>
          </div>
        </div>

        <!-- Videos Grid -->
        <div v-if="filteredVideos.length === 0" class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
          </svg>
          <p class="mt-2 text-sm text-gray-500">No videos found matching your criteria</p>
        </div>
        
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div v-for="video in filteredVideos" :key="video.id" 
               class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
            <!-- Video Thumbnail -->
            <div class="aspect-video bg-gradient-to-br from-gray-800 to-gray-900 flex items-center justify-center relative group cursor-pointer"
                 @click="playVideo(video)">
              <div class="absolute inset-0 bg-black bg-opacity-40 group-hover:bg-opacity-20 transition-all duration-300"></div>
              <div class="relative z-10 text-center">
                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-2 group-hover:bg-opacity-30 transition-all duration-300">
                  <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M8 5v14l11-7z"/>
                  </svg>
                </div>
                <p class="text-white text-sm font-medium">{{ formatDuration(video.duration) }}</p>
              </div>
              
              <!-- Category Badge -->
              <div v-if="video.category" class="absolute top-3 left-3">
                <span class="px-2 py-1 bg-black bg-opacity-70 text-white text-xs font-medium rounded">
                  {{ video.category }}
                </span>
              </div>
            </div>
            
            <!-- Video Info -->
            <div class="p-6">
              <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">{{ video.title }}</h3>
              <p class="text-sm text-gray-600 mb-4 line-clamp-3">{{ video.description }}</p>
              
              <div class="flex items-center justify-between text-xs text-gray-500">
                <span>{{ formatDate(video.uploadDate) }}</span>
                <button @click="playVideo(video)" 
                        class="px-4 py-2 bg-black text-white text-sm font-medium rounded hover:bg-gray-800 transition-colors">
                  Watch Now
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Video Player Modal -->
    <div v-if="showVideoPlayer && selectedVideo" 
         class="fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-50">
      <div class="w-full max-w-6xl mx-4">
        <!-- Video Player Header -->
        <div class="flex items-center justify-between mb-4">
          <div class="text-white">
            <h2 class="text-xl font-semibold">{{ selectedVideo.title }}</h2>
            <p class="text-gray-300 text-sm">{{ selectedVideo.description }}</p>
          </div>
          <button @click="closeVideoPlayer" 
                  class="text-white hover:text-gray-300 transition-colors">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        
        <!-- Video Player -->
        <div class="aspect-video bg-gray-900 rounded-lg flex items-center justify-center">
          <div class="text-center text-white">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
            </svg>
            <p class="text-lg font-medium mb-2">Video Player</p>
            <p class="text-gray-400 text-sm">{{ selectedVideo.filename }}</p>
            <p class="text-gray-500 text-xs mt-2">In a real implementation, this would be a video player component</p>
          </div>
        </div>
        
        <!-- Video Controls/Info -->
        <div class="mt-4 text-white">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4 text-sm text-gray-300">
              <span>Duration: {{ formatDuration(selectedVideo?.duration) }}</span>
              <span>•</span>
              <span>Uploaded: {{ formatDate(selectedVideo?.uploadDate) }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <Footer />
  </div>
</template>

<style scoped>
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
</style>