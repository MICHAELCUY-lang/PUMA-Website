<template>
  <div class="comment-item">
    <!-- Main Comment -->
    <div class="flex space-x-3">
      <!-- Avatar -->
      <div class="flex-shrink-0">
        <div class="w-8 h-8 rounded-full flex items-center justify-center" :class="avatarClass">
          <span class="text-white text-sm font-medium">{{ userInitials }}</span>
        </div>
      </div>
      
      <!-- Comment Content -->
      <div class="flex-1 min-w-0">
        <!-- Header -->
        <div class="flex items-center space-x-2 mb-1">
          <span class="font-mono font-bold text-gray-900 tracking-wider">{{ comment.userName }}</span>
          <span class="px-2 py-0.5 text-xs font-mono font-bold tracking-wider uppercase rounded-full" :class="roleClass">
            {{ roleText }}
          </span>
          <span class="text-sm font-mono text-gray-500">{{ formatTime(comment.timestamp) }}</span>
          <span v-if="comment.isEdited" class="text-xs font-mono text-gray-400">(diedit)</span>
        </div>
        
        <!-- Content -->
        <div v-if="!isEditing" class="font-mono text-gray-800 mb-2 whitespace-pre-wrap leading-relaxed">{{ comment.content }}</div>
        
        <!-- Edit Form -->
        <div v-else class="mb-2">
          <textarea
            v-model="editContent"
            class="w-full p-2 font-mono border border-gray-300 rounded-md resize-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            rows="3"
          ></textarea>
          <div class="flex items-center space-x-2 mt-2">
            <button
              @click="saveEdit"
              class="px-3 py-1 bg-blue-600 text-white font-mono font-bold tracking-wider uppercase text-sm rounded-md hover:bg-blue-700"
            >
              Simpan
            </button>
            <button
              @click="cancelEdit"
              class="px-3 py-1 bg-gray-300 text-gray-700 font-mono font-bold tracking-wider uppercase text-sm rounded-md hover:bg-gray-400"
            >
              Batal
            </button>
          </div>
        </div>
        
        <!-- Actions -->
        <div class="flex items-center space-x-4 text-sm">
          <!-- Like Button -->
          <button
            @click="$emit('like', comment.id)"
            class="flex items-center space-x-1 text-gray-500 hover:text-blue-600 transition-colors"
            :class="{ 'text-blue-600': isLiked }"
          >
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
              <path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z" />
            </svg>
            <span>{{ comment.likes }}</span>
          </button>
          
          <!-- Reply Button -->
          <button
            @click="$emit('reply', comment.id)"
            class="font-mono font-bold tracking-wider uppercase text-gray-500 hover:text-blue-600 transition-colors"
          >
            Balas
          </button>
          
          <!-- Edit Button (only for own comments) -->
          <button
            v-if="canEdit"
            @click="startEdit"
            class="font-mono font-bold tracking-wider uppercase text-gray-500 hover:text-blue-600 transition-colors"
          >
            Edit
          </button>
          
          <!-- Delete Button (only for own comments or admin) -->
          <button
            v-if="canDelete"
            @click="confirmDelete"
            class="font-mono font-bold tracking-wider uppercase text-gray-500 hover:text-red-600 transition-colors"
          >
            Hapus
          </button>
        </div>
        
        <!-- Replies -->
        <div v-if="comment.replies && comment.replies.length > 0" class="mt-4 space-y-3">
          <div class="border-l-2 border-gray-200 pl-4">
            <CommentItem
              v-for="reply in comment.replies"
              :key="reply.id"
              :comment="reply"
              :current-user="currentUser"
              :is-reply="true"
              @reply="$emit('reply', $event)"
              @like="$emit('like', $event)"
              @edit="$emit('edit', $event, $event)"
              @delete="$emit('delete', $event)"
            />
          </div>
        </div>
      </div>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 max-w-sm mx-4">
        <h3 class="text-lg font-mono font-bold tracking-wider uppercase text-gray-900 mb-4">Hapus Komentar</h3>
        <p class="font-mono text-gray-600 mb-6 leading-relaxed">Apakah Anda yakin ingin menghapus komentar ini? Tindakan ini tidak dapat dibatalkan.</p>
        <div class="flex space-x-3">
          <button
            @click="deleteComment"
            class="flex-1 bg-red-600 text-white font-mono font-bold tracking-wider uppercase py-2 px-4 rounded-md hover:bg-red-700 transition-colors"
          >
            Hapus
          </button>
          <button
            @click="showDeleteModal = false"
            class="flex-1 bg-gray-300 text-gray-700 font-mono font-bold tracking-wider uppercase py-2 px-4 rounded-md hover:bg-gray-400 transition-colors"
          >
            Batal
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import type { Comment } from '@/types/comment'

interface Props {
  comment: Comment
  currentUser: {
    id: string
    name: string
    role: 'student' | 'admin' | 'instructor'
  }
  isReply?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  isReply: false
})

// Get emit function
const emit = defineEmits<{
  reply: [commentId: string]
  like: [commentId: string]
  edit: [commentId: string, newContent: string]
  delete: [commentId: string]
}>()

// Reactive data
const isEditing = ref(false)
const editContent = ref('')
const showDeleteModal = ref(false)

// Computed properties
const userInitials = computed(() => {
  return props.comment.userName
    .split(' ')
    .map(name => name.charAt(0))
    .join('')
    .toUpperCase()
    .slice(0, 2)
})

const avatarClass = computed(() => {
  switch (props.comment.userRole) {
    case 'admin':
      return 'bg-red-500'
    case 'instructor':
      return 'bg-green-500'
    case 'student':
    default:
      return 'bg-blue-500'
  }
})

const roleClass = computed(() => {
  switch (props.comment.userRole) {
    case 'admin':
      return 'bg-red-100 text-red-800'
    case 'instructor':
      return 'bg-green-100 text-green-800'
    case 'student':
    default:
      return 'bg-blue-100 text-blue-800'
  }
})

const roleText = computed(() => {
  switch (props.comment.userRole) {
    case 'admin':
      return 'Admin'
    case 'instructor':
      return 'Instruktur'
    case 'student':
    default:
      return 'Student'
  }
})

const isLiked = computed(() => {
  return props.comment.likedBy.includes(props.currentUser.id)
})

const canEdit = computed(() => {
  return props.comment.userId === props.currentUser.id
})

const canDelete = computed(() => {
  return props.comment.userId === props.currentUser.id || props.currentUser.role === 'admin'
})

// Methods
const formatTime = (timestamp: Date): string => {
  const now = new Date()
  const diff = now.getTime() - new Date(timestamp).getTime()
  const minutes = Math.floor(diff / 60000)
  const hours = Math.floor(diff / 3600000)
  const days = Math.floor(diff / 86400000)
  
  if (minutes < 1) return 'Baru saja'
  if (minutes < 60) return `${minutes} menit yang lalu`
  if (hours < 24) return `${hours} jam yang lalu`
  if (days < 7) return `${days} hari yang lalu`
  
  return new Date(timestamp).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const startEdit = () => {
  isEditing.value = true
  editContent.value = props.comment.content
}

const saveEdit = () => {
  if (editContent.value.trim() && editContent.value !== props.comment.content) {
    emit('edit', props.comment.id, editContent.value.trim())
  }
  isEditing.value = false
}

const cancelEdit = () => {
  isEditing.value = false
  editContent.value = ''
}

const confirmDelete = () => {
  showDeleteModal.value = true
}

const deleteComment = () => {
  emit('delete', props.comment.id)
  showDeleteModal.value = false
}
</script>

<style scoped>
.comment-item {
  @apply relative;
}
</style>