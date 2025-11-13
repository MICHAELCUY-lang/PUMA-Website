<template>
  <div class="comment-section bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <!-- Comment Header -->
    <div class="flex items-center justify-between mb-6">
      <h3 class="text-lg font-mono font-bold tracking-wider text-gray-900 uppercase">
        Diskusi ({{ commentStats.totalComments }})
      </h3>
      <div class="flex items-center space-x-4">
        <select 
          v-model="filter.sortBy" 
          class="text-sm font-mono border border-gray-300 rounded-md px-3 py-1 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        >
          <option value="newest">Terbaru</option>
          <option value="oldest">Terlama</option>
          <option value="mostLiked">Paling Disukai</option>
        </select>
      </div>
    </div>

    <!-- Add Comment Form -->
    <div class="mb-6">
      <div class="flex items-start space-x-3">
        <div class="flex-shrink-0">
          <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
            <span class="text-white text-sm font-medium">{{ userInitials }}</span>
          </div>
        </div>
        <div class="flex-1">
          <textarea
            v-model="newComment.content"
            placeholder="Tulis komentar..."
            class="w-full p-3 font-mono border border-gray-300 rounded-lg resize-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            rows="3"
            @keydown.ctrl.enter="submitComment"
          ></textarea>
          <div class="flex items-center justify-between mt-2">
            <span class="text-xs font-mono text-gray-500">Ctrl + Enter untuk kirim</span>
            <button
              @click="submitComment"
              :disabled="!newComment.content.trim()"
              class="px-4 py-2 bg-blue-600 text-white font-mono font-bold tracking-wider uppercase rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
              Kirim
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Comments List -->
    <div class="space-y-4">
      <div v-if="sortedComments.length === 0" class="text-center py-8 text-gray-500">
        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>
        <p class="font-mono">Belum ada komentar. Jadilah yang pertama berkomentar!</p>
      </div>
      
      <CommentItem
        v-for="comment in sortedComments"
        :key="comment.id"
        :comment="comment"
        :current-user="currentUser"
        @reply="handleReply"
        @like="handleLike"
        @edit="handleEdit"
        @delete="handleDelete"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import type { Comment, CommentFormData, CommentStats, CommentFilter } from '@/types/comment'
import CommentItem from './CommentItem.vue'

interface Props {
  videoId: string
  currentUser: {
    id: string
    name: string
    role: 'student' | 'admin' | 'instructor'
  }
}

const props = defineProps<Props>()

// Reactive data
const comments = ref<Comment[]>([])
const newComment = ref<CommentFormData>({ content: '' })
const filter = ref<CommentFilter>({
  sortBy: 'newest',
  showReplies: true
})

// Computed properties
const userInitials = computed(() => {
  return props.currentUser.name
    .split(' ')
    .map(name => name.charAt(0))
    .join('')
    .toUpperCase()
    .slice(0, 2)
})

const sortedComments = computed(() => {
  const mainComments = comments.value.filter(comment => !comment.parentId)
  
  return mainComments.sort((a, b) => {
    switch (filter.value.sortBy) {
      case 'oldest':
        return new Date(a.timestamp).getTime() - new Date(b.timestamp).getTime()
      case 'mostLiked':
        return b.likes - a.likes
      case 'newest':
      default:
        return new Date(b.timestamp).getTime() - new Date(a.timestamp).getTime()
    }
  })
})

const commentStats = computed((): CommentStats => {
  const totalComments = comments.value.filter(comment => !comment.parentId).length
  const totalReplies = comments.value.filter(comment => comment.parentId).length
  
  return {
    totalComments,
    totalReplies
  }
})

// Methods
const generateId = (): string => {
  return Date.now().toString(36) + Math.random().toString(36).substr(2)
}

const submitComment = () => {
  if (!newComment.value.content.trim()) return
  
  const comment: Comment = {
    id: generateId(),
    videoId: props.videoId,
    userId: props.currentUser.id,
    userName: props.currentUser.name,
    userRole: props.currentUser.role,
    content: newComment.value.content.trim(),
    timestamp: new Date(),
    parentId: newComment.value.parentId,
    replies: [],
    likes: 0,
    likedBy: [],
    isEdited: false
  }
  
  if (comment.parentId) {
    // Add as reply
    const parentComment = findCommentById(comment.parentId)
    if (parentComment) {
      if (!parentComment.replies) parentComment.replies = []
      parentComment.replies.push(comment)
    }
  } else {
    // Add as main comment
    comments.value.unshift(comment)
  }
  
  // Reset form
  newComment.value = { content: '' }
  
  // Save to localStorage
  saveComments()
}

const handleReply = (parentId: string) => {
  newComment.value.parentId = parentId
  // Focus on textarea
  const textarea = document.querySelector('textarea')
  if (textarea) {
    textarea.focus()
    textarea.placeholder = 'Tulis balasan...'
  }
}

const handleLike = (commentId: string) => {
  const comment = findCommentById(commentId)
  if (!comment) return
  
  const userIndex = comment.likedBy.indexOf(props.currentUser.id)
  if (userIndex > -1) {
    // Unlike
    comment.likedBy.splice(userIndex, 1)
    comment.likes--
  } else {
    // Like
    comment.likedBy.push(props.currentUser.id)
    comment.likes++
  }
  
  saveComments()
}

const handleEdit = (commentId: string, newContent: string) => {
  const comment = findCommentById(commentId)
  if (!comment) return
  
  comment.content = newContent
  comment.isEdited = true
  comment.editedAt = new Date()
  
  saveComments()
}

const handleDelete = (commentId: string) => {
  // Find and remove comment
  const commentIndex = comments.value.findIndex(c => c.id === commentId)
  if (commentIndex > -1) {
    comments.value.splice(commentIndex, 1)
  } else {
    // Look in replies
    for (const comment of comments.value) {
      if (comment.replies) {
        const replyIndex = comment.replies.findIndex(r => r.id === commentId)
        if (replyIndex > -1) {
          comment.replies.splice(replyIndex, 1)
          break
        }
      }
    }
  }
  
  saveComments()
}

const findCommentById = (id: string): Comment | null => {
  // Search in main comments
  for (const comment of comments.value) {
    if (comment.id === id) return comment
    
    // Search in replies
    if (comment.replies) {
      for (const reply of comment.replies) {
        if (reply.id === id) return reply
      }
    }
  }
  return null
}

const saveComments = () => {
  const key = `aformation_comments_${props.videoId}`
  localStorage.setItem(key, JSON.stringify(comments.value))
}

const loadComments = () => {
  const key = `aformation_comments_${props.videoId}`
  const saved = localStorage.getItem(key)
  if (saved) {
    try {
      comments.value = JSON.parse(saved)
      // Convert timestamp strings back to Date objects
      comments.value.forEach(comment => {
        comment.timestamp = new Date(comment.timestamp)
        if (comment.editedAt) comment.editedAt = new Date(comment.editedAt)
        if (comment.replies) {
          comment.replies.forEach(reply => {
            reply.timestamp = new Date(reply.timestamp)
            if (reply.editedAt) reply.editedAt = new Date(reply.editedAt)
          })
        }
      })
    } catch (error) {
      console.error('Error loading comments:', error)
      comments.value = []
    }
  }
}

// Add some dummy comments for demonstration
const addDummyComments = () => {
  if (comments.value.length === 0) {
    const dummyComments: Comment[] = [
      {
        id: 'demo-1',
        videoId: props.videoId,
        content: 'Video ini sangat membantu! Penjelasannya jelas dan mudah dipahami.',
        userId: 'user-1',
        userName: 'Sarah Wijaya',
        userRole: 'student',
        timestamp: new Date(Date.now() - 2 * 60 * 60 * 1000), // 2 hours ago
        likes: 5,
        likedBy: ['user-4', 'user-5', 'user-6', 'user-7', 'user-8'],
        isEdited: false,
        replies: [
          {
            id: 'demo-1-reply-1',
            videoId: props.videoId,
            content: 'Setuju! Materinya sangat relevan dengan kebutuhan industri.',
            userId: 'user-2',
            userName: 'Ahmad Rizki',
            userRole: 'student',
            parentId: 'demo-1',
            timestamp: new Date(Date.now() - 1 * 60 * 60 * 1000), // 1 hour ago
            likes: 2,
            likedBy: ['user-9', 'user-10'],
            isEdited: false,
            replies: []
          }
        ]
      },
      {
        id: 'demo-2',
        videoId: props.videoId,
        content: 'Apakah ada materi tambahan yang bisa dipelajari untuk memperdalam topik ini?',
        userId: 'user-3',
        userName: 'Dina Pratiwi',
        userRole: 'student',
        timestamp: new Date(Date.now() - 30 * 60 * 1000), // 30 minutes ago
        likes: 1,
        likedBy: ['user-11'],
        isEdited: false,
        replies: []
      }
    ]
    comments.value = dummyComments
    saveComments()
  }
}

// Lifecycle
onMounted(() => {
  loadComments()
  addDummyComments()
})
</script>

<style scoped>
.comment-section {
  max-height: 400px;
  overflow-y: auto;
}

/* Custom scrollbar for better appearance */
.comment-section::-webkit-scrollbar {
  width: 6px;
}

.comment-section::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 3px;
}

.comment-section::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

.comment-section::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}
</style>