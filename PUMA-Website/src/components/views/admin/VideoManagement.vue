<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import AdminLayout from './Layout.vue'

interface Video {
  id: string
  title: string
  description: string
  filename: string
  uploadDate: string
  accessToken: string
  viewCount: number
  duration?: string
  size?: string
}

interface RegistrationToken {
  id: string
  token: string
  email: string
  name: string
  createdAt: string
  isUsed: boolean
  videoAccess: string[]
}

const videos = ref<Video[]>([
  {
    id: '1',
    title: 'Aformation Session 1 - Introduction',
    description: 'Pengenalan program aformation dan overview materi',
    filename: 'aformation_session_1.mp4',
    uploadDate: '2024-01-15',
    accessToken: 'af2024_intro_001',
    viewCount: 25,
    duration: '45:30',
    size: '1.2 GB'
  },
  {
    id: '2', 
    title: 'Aformation Session 2 - Technical Skills',
    description: 'Pembelajaran technical skills untuk development',
    filename: 'aformation_session_2.mp4',
    uploadDate: '2024-01-22',
    accessToken: 'af2024_tech_002',
    viewCount: 18,
    duration: '52:15',
    size: '1.5 GB'
  }
])

const registrationTokens = ref<RegistrationToken[]>([
  {
    id: '1',
    token: 'reg_af2024_001',
    email: 'john@example.com',
    name: 'John Doe',
    createdAt: '2024-01-10',
    isUsed: true,
    videoAccess: ['1', '2']
  },
  {
    id: '2',
    token: 'reg_af2024_002', 
    email: 'jane@example.com',
    name: 'Jane Smith',
    createdAt: '2024-01-12',
    isUsed: false,
    videoAccess: ['1']
  }
])

const activeTab = ref<'upload' | 'manage' | 'tokens'>('upload')
const showUploadModal = ref(false)
const showTokenModal = ref(false)
const isUploading = ref(false)
const uploadProgress = ref(0)

const newVideo = ref({
  title: '',
  description: '',
  file: null as File | null,
  accessToken: ''
})

const newToken = ref({
  email: '',
  name: '',
  videoAccess: [] as string[]
})

const searchQuery = ref('')
const selectedVideos = ref<string[]>([])

const filteredVideos = computed(() => {
  if (!searchQuery.value) return videos.value
  return videos.value.filter(video => 
    video.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    video.description.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
})

const generateAccessToken = () => {
  const timestamp = Date.now().toString(36)
  const random = Math.random().toString(36).substr(2, 5)
  return `af2024_${timestamp}_${random}`
}

const generateRegistrationToken = () => {
  const timestamp = Date.now().toString(36)
  const random = Math.random().toString(36).substr(2, 8)
  return `reg_af2024_${timestamp}_${random}`
}

const handleFileUpload = (event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files && target.files[0]) {
    newVideo.value.file = target.files[0]
  }
}

const uploadVideo = async () => {
  if (!newVideo.value.file || !newVideo.value.title) {
    alert('Please fill in all required fields')
    return
  }

  isUploading.value = true
  uploadProgress.value = 0

  // Simulate upload progress
  const progressInterval = setInterval(() => {
    uploadProgress.value += Math.random() * 15
    if (uploadProgress.value >= 100) {
      uploadProgress.value = 100
      clearInterval(progressInterval)
      
      // Add video to list
      const video: Video = {
        id: Date.now().toString(),
        title: newVideo.value.title,
        description: newVideo.value.description,
        filename: newVideo.value.file!.name,
        uploadDate: new Date().toISOString().split('T')[0],
        accessToken: newVideo.value.accessToken || generateAccessToken(),
        viewCount: 0,
        duration: '00:00',
        size: `${(newVideo.value.file!.size / (1024 * 1024 * 1024)).toFixed(1)} GB`
      }
      
      videos.value.unshift(video)
      
      // Reset form
      newVideo.value = {
        title: '',
        description: '',
        file: null,
        accessToken: ''
      }
      
      isUploading.value = false
      showUploadModal.value = false
      alert('Video uploaded successfully!')
    }
  }, 200)
}

const createRegistrationToken = () => {
  if (!newToken.value.email || !newToken.value.name) {
    alert('Please fill in all required fields')
    return
  }

  const token: RegistrationToken = {
    id: Date.now().toString(),
    token: generateRegistrationToken(),
    email: newToken.value.email,
    name: newToken.value.name,
    createdAt: new Date().toISOString().split('T')[0],
    isUsed: false,
    videoAccess: newToken.value.videoAccess
  }

  registrationTokens.value.unshift(token)
  
  // Reset form
  newToken.value = {
    email: '',
    name: '',
    videoAccess: []
  }
  
  showTokenModal.value = false
  alert(`Registration token created: ${token.token}`)
}

const deleteVideo = (videoId: string) => {
  if (confirm('Are you sure you want to delete this video?')) {
    videos.value = videos.value.filter(v => v.id !== videoId)
  }
}

const copyToClipboard = (text: string) => {
  navigator.clipboard.writeText(text)
  alert('Copied to clipboard!')
}

const formatFileSize = (bytes: number) => {
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  if (bytes === 0) return '0 Bytes'
  const i = Math.floor(Math.log(bytes) / Math.log(1024))
  return Math.round(bytes / Math.pow(1024, i) * 100) / 100 + ' ' + sizes[i]
}
</script>

<template>
  <AdminLayout>
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Video Management</h1>
          <p class="text-gray-600">Manage aformation video recordings and access control</p>
        </div>
        <div class="flex space-x-3">
          <button @click="showTokenModal = true" 
                  class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
            Create Access Token
          </button>
          <button @click="showUploadModal = true" 
                  class="px-4 py-2 text-sm font-medium text-white bg-black rounded-lg hover:bg-gray-800">
            Upload Video
          </button>
        </div>
      </div>

      <!-- Tabs -->
      <div class="border-b border-gray-200">
        <nav class="flex space-x-8">
          <button @click="activeTab = 'upload'" 
                  :class="activeTab === 'upload' ? 'border-black text-black' : 'border-transparent text-gray-500 hover:text-gray-700'"
                  class="py-2 px-1 border-b-2 font-medium text-sm">
            Upload & Manage
          </button>
          <button @click="activeTab = 'manage'" 
                  :class="activeTab === 'manage' ? 'border-black text-black' : 'border-transparent text-gray-500 hover:text-gray-700'"
                  class="py-2 px-1 border-b-2 font-medium text-sm">
            Video Library
          </button>
          <button @click="activeTab = 'tokens'" 
                  :class="activeTab === 'tokens' ? 'border-black text-black' : 'border-transparent text-gray-500 hover:text-gray-700'"
                  class="py-2 px-1 border-b-2 font-medium text-sm">
            Access Tokens
          </button>
        </nav>
      </div>

      <!-- Upload & Manage Tab -->
      <div v-if="activeTab === 'upload'" class="space-y-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
          <div class="bg-white p-6 rounded-lg border border-gray-200">
            <div class="flex items-center">
              <div class="p-2 bg-blue-100 rounded-lg">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Videos</p>
                <p class="text-2xl font-semibold text-gray-900">{{ videos.length }}</p>
              </div>
            </div>
          </div>
          
          <div class="bg-white p-6 rounded-lg border border-gray-200">
            <div class="flex items-center">
              <div class="p-2 bg-green-100 rounded-lg">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Views</p>
                <p class="text-2xl font-semibold text-gray-900">{{ videos.reduce((sum, v) => sum + v.viewCount, 0) }}</p>
              </div>
            </div>
          </div>
          
          <div class="bg-white p-6 rounded-lg border border-gray-200">
            <div class="flex items-center">
              <div class="p-2 bg-purple-100 rounded-lg">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Active Tokens</p>
                <p class="text-2xl font-semibold text-gray-900">{{ registrationTokens.filter(t => !t.isUsed).length }}</p>
              </div>
            </div>
          </div>
          
          <div class="bg-white p-6 rounded-lg border border-gray-200">
            <div class="flex items-center">
              <div class="p-2 bg-orange-100 rounded-lg">
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Registered Users</p>
                <p class="text-2xl font-semibold text-gray-900">{{ registrationTokens.filter(t => t.isUsed).length }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Videos -->
        <div class="bg-white rounded-lg border border-gray-200">
          <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Recent Videos</h3>
          </div>
          <div class="p-6">
            <div v-if="videos.length === 0" class="text-center py-8">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
              </svg>
              <p class="mt-2 text-sm text-gray-500">No videos uploaded yet</p>
            </div>
            <div v-else class="space-y-4">
              <div v-for="video in videos.slice(0, 3)" :key="video.id" 
                   class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                <div class="flex items-center space-x-4">
                  <div class="w-16 h-12 bg-gray-200 rounded flex items-center justify-center">
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h8m-9-4V8a2 2 0 012-2h8a2 2 0 012 2v2M7 16h10a2 2 0 002-2V8a2 2 0 00-2-2H7a2 2 0 00-2 2v6a2 2 0 002 2z"></path>
                    </svg>
                  </div>
                  <div>
                    <h4 class="font-medium text-gray-900">{{ video.title }}</h4>
                    <p class="text-sm text-gray-500">{{ video.description }}</p>
                    <div class="flex items-center space-x-4 mt-1 text-xs text-gray-400">
                      <span>{{ video.duration }}</span>
                      <span>{{ video.size }}</span>
                      <span>{{ video.viewCount }} views</span>
                    </div>
                  </div>
                </div>
                <div class="flex items-center space-x-2">
                  <button @click="copyToClipboard(video.accessToken)" 
                          class="px-3 py-1 text-xs font-medium text-gray-700 bg-gray-100 rounded hover:bg-gray-200">
                    Copy Token
                  </button>
                  <button @click="deleteVideo(video.id)" 
                          class="px-3 py-1 text-xs font-medium text-red-700 bg-red-100 rounded hover:bg-red-200">
                    Delete
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Video Library Tab -->
      <div v-if="activeTab === 'manage'" class="space-y-6">
        <!-- Search -->
        <div class="flex items-center space-x-4">
          <div class="flex-1">
            <input v-model="searchQuery" type="text" placeholder="Search videos..." 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent">
          </div>
        </div>

        <!-- Videos Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div v-for="video in filteredVideos" :key="video.id" 
               class="bg-white rounded-lg border border-gray-200 overflow-hidden">
            <div class="aspect-video bg-gray-200 flex items-center justify-center">
              <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h8m-9-4V8a2 2 0 012-2h8a2 2 0 012 2v2M7 16h10a2 2 0 002-2V8a2 2 0 00-2-2H7a2 2 0 00-2 2v6a2 2 0 002 2z"></path>
              </svg>
            </div>
            <div class="p-4">
              <h3 class="font-medium text-gray-900 mb-2">{{ video.title }}</h3>
              <p class="text-sm text-gray-600 mb-3">{{ video.description }}</p>
              <div class="flex items-center justify-between text-xs text-gray-500 mb-3">
                <span>{{ video.duration }}</span>
                <span>{{ video.viewCount }} views</span>
              </div>
              <div class="flex items-center space-x-2">
                <button @click="copyToClipboard(video.accessToken)" 
                        class="flex-1 px-3 py-2 text-xs font-medium text-gray-700 bg-gray-100 rounded hover:bg-gray-200">
                  Copy Token
                </button>
                <button @click="deleteVideo(video.id)" 
                        class="px-3 py-2 text-xs font-medium text-red-700 bg-red-100 rounded hover:bg-red-200">
                  Delete
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Access Tokens Tab -->
      <div v-if="activeTab === 'tokens'" class="space-y-6">
        <div class="bg-white rounded-lg border border-gray-200">
          <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Registration Tokens</h3>
            <p class="text-sm text-gray-600 mt-1">Manage access tokens for aformation participants</p>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Token</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Video Access</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="token in registrationTokens" :key="token.id">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div>
                      <div class="text-sm font-medium text-gray-900">{{ token.name }}</div>
                      <div class="text-sm text-gray-500">{{ token.email }}</div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <code class="text-xs bg-gray-100 px-2 py-1 rounded">{{ token.token }}</code>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="token.isUsed ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'" 
                          class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                      {{ token.isUsed ? 'Used' : 'Pending' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ token.videoAccess.length }} videos
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ token.createdAt }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <button @click="copyToClipboard(token.token)" 
                            class="text-indigo-600 hover:text-indigo-900 mr-3">Copy</button>
                    <button class="text-red-600 hover:text-red-900">Delete</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Upload Modal -->
    <div v-if="showUploadModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Upload Video</h3>
        
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
            <input v-model="newVideo.title" type="text" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-black focus:border-transparent">
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea v-model="newVideo.description" rows="3" 
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-black focus:border-transparent"></textarea>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Video File *</label>
            <input type="file" @change="handleFileUpload" accept="video/*" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-black focus:border-transparent">
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Access Token (optional)</label>
            <input v-model="newVideo.accessToken" type="text" :placeholder="generateAccessToken()" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-black focus:border-transparent">
            <p class="text-xs text-gray-500 mt-1">Leave empty to auto-generate</p>
          </div>
        </div>
        
        <div v-if="isUploading" class="mt-4">
          <div class="flex items-center justify-between text-sm text-gray-600 mb-2">
            <span>Uploading...</span>
            <span>{{ Math.round(uploadProgress) }}%</span>
          </div>
          <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="bg-black h-2 rounded-full transition-all duration-300" :style="{ width: uploadProgress + '%' }"></div>
          </div>
        </div>
        
        <div class="flex justify-end space-x-3 mt-6">
          <button @click="showUploadModal = false" :disabled="isUploading" 
                  class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 disabled:opacity-50">
            Cancel
          </button>
          <button @click="uploadVideo" :disabled="isUploading" 
                  class="px-4 py-2 text-sm font-medium text-white bg-black rounded-md hover:bg-gray-800 disabled:opacity-50">
            {{ isUploading ? 'Uploading...' : 'Upload' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Token Creation Modal -->
    <div v-if="showTokenModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Create Access Token</h3>
        
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
            <input v-model="newToken.name" type="text" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-black focus:border-transparent">
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
            <input v-model="newToken.email" type="email" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-black focus:border-transparent">
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Video Access</label>
            <div class="space-y-2 max-h-32 overflow-y-auto">
              <label v-for="video in videos" :key="video.id" class="flex items-center">
                <input v-model="newToken.videoAccess" :value="video.id" type="checkbox" 
                       class="rounded border-gray-300 text-black focus:ring-black">
                <span class="ml-2 text-sm text-gray-700">{{ video.title }}</span>
              </label>
            </div>
          </div>
        </div>
        
        <div class="flex justify-end space-x-3 mt-6">
          <button @click="showTokenModal = false" 
                  class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
            Cancel
          </button>
          <button @click="createRegistrationToken" 
                  class="px-4 py-2 text-sm font-medium text-white bg-black rounded-md hover:bg-gray-800">
            Create Token
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>