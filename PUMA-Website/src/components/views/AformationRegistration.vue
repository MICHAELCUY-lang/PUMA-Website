<script setup lang="ts">
import { ref, computed } from 'vue'
import Navbar from '../Navbar.vue'
import Footer from '../Footer.vue'

interface RegistrationData {
  name: string
  email: string
  studentId: string
  batch: string
  phone: string
  motivation: string
}

const registrationData = ref<RegistrationData>({
  name: '',
  email: '',
  studentId: '',
  batch: '',
  phone: '',
  motivation: ''
})

const isSubmitting = ref(false)
const isSubmitted = ref(false)
const generatedToken = ref('')
const errors = ref<Record<string, string>>({})



const batches = [
  '2023',
  '2024'
]

const validateForm = () => {
  errors.value = {}
  
  if (!registrationData.value.name.trim()) {
    errors.value.name = 'Name is required'
  }
  
  if (!registrationData.value.email.trim()) {
    errors.value.email = 'Email is required'
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(registrationData.value.email)) {
    errors.value.email = 'Please enter a valid email address'
  }
  
  if (!registrationData.value.studentId.trim()) {
    errors.value.studentId = 'Student ID is required'
  }
  

  
  if (!registrationData.value.batch) {
    errors.value.batch = 'Batch is required'
  }
  
  if (!registrationData.value.phone.trim()) {
    errors.value.phone = 'Phone number is required'
  }
  
  if (!registrationData.value.motivation.trim()) {
    errors.value.motivation = 'Motivation is required'
  } else if (registrationData.value.motivation.trim().length < 50) {
    errors.value.motivation = 'Please provide at least 50 characters for your motivation'
  }
  
  return Object.keys(errors.value).length === 0
}

const generateAccessToken = () => {
  const timestamp = Date.now().toString(36)
  const random = Math.random().toString(36).substr(2, 8)
  return `aformation_${timestamp}_${random}`
}

const submitRegistration = async () => {
  if (!validateForm()) {
    return
  }
  
  isSubmitting.value = true
  
  try {
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 2000))
    
    // Generate access token
    generatedToken.value = generateAccessToken()
    
    // Store registration data (in real app, this would be sent to backend)
    const registrationRecord = {
      ...registrationData.value,
      token: generatedToken.value,
      registeredAt: new Date().toISOString(),
      status: 'pending'
    }
    
    // Store in localStorage for demo purposes
    const existingRegistrations = JSON.parse(localStorage.getItem('aformationRegistrations') || '[]')
    existingRegistrations.push(registrationRecord)
    localStorage.setItem('aformationRegistrations', JSON.stringify(existingRegistrations))
    
    isSubmitted.value = true
  } catch (error) {
    alert('Registration failed. Please try again.')
  } finally {
    isSubmitting.value = false
  }
}

const copyToken = () => {
  navigator.clipboard.writeText(generatedToken.value)
  alert('Token copied to clipboard!')
}

const resetForm = () => {
  registrationData.value = {
    name: '',
    email: '',
    studentId: '',
    batch: '',
    phone: '',
    motivation: ''
  }
  errors.value = {}
  isSubmitted.value = false
  generatedToken.value = ''
}

const motivationWordCount = computed(() => {
  return registrationData.value.motivation.trim().length
})
</script>

<template>
  <div class="min-h-screen bg-gray-50">
    <Navbar />
    
    <div class="pt-20 pb-16">
      <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
          <h1 class="text-3xl font-bold text-gray-900 mb-4">Aformation Registration</h1>
          <p class="text-lg text-gray-600 max-w-3xl mx-auto">
            Daftar untuk mengikuti program Aformation PUMA dan dapatkan akses ke video pembelajaran eksklusif.
          </p>
        </div>

        <!-- Success State -->
        <div v-if="isSubmitted" class="bg-white rounded-lg shadow-lg p-8 text-center">
          <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
          </div>
          
          <h2 class="text-2xl font-bold text-gray-900 mb-4">Registration Successful!</h2>
          <p class="text-gray-600 mb-6">
            Terima kasih telah mendaftar untuk program Aformation. Berikut adalah token akses Anda:
          </p>
          
          <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Your Access Token:</label>
            <div class="flex items-center space-x-2">
              <code class="flex-1 bg-white border border-gray-300 rounded px-3 py-2 text-sm font-mono">{{ generatedToken }}</code>
              <button @click="copyToken" 
                      class="px-4 py-2 bg-black text-white text-sm font-medium rounded hover:bg-gray-800">
                Copy
              </button>
            </div>
          </div>
          
          <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-start">
              <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              <div class="text-sm text-blue-800">
                <p class="font-medium mb-1">Important Instructions:</p>
                <ul class="list-disc list-inside space-y-1">
                  <li>Simpan token ini dengan aman - Anda akan membutuhkannya untuk mengakses video</li>
                  <li>Token ini bersifat unik dan hanya berlaku untuk akun Anda</li>
                  <li>Jangan bagikan token kepada orang lain</li>
                  <li>Informasi lebih lanjut akan dikirim ke email Anda</li>
                </ul>
              </div>
            </div>
          </div>
          
          <div class="flex justify-center space-x-4">
            <router-link to="/aformation/videos" 
                         class="px-6 py-3 bg-black text-white font-medium rounded-lg hover:bg-gray-800">
              Access Videos
            </router-link>
            <button @click="resetForm" 
                    class="px-6 py-3 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200">
              Register Another
            </button>
          </div>
        </div>

        <!-- Registration Form -->
        <div v-else class="bg-white rounded-lg shadow-lg p-8">
          <form @submit.prevent="submitRegistration" class="space-y-6">
            <!-- Personal Information -->
            <div class="space-y-6">
              <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2">
                Personal Information
              </h3>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                  <input v-model="registrationData.name" type="text" 
                         :class="errors.name ? 'border-red-300 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-black focus:border-black'"
                         class="w-full px-3 py-2 border rounded-md shadow-sm focus:ring-2 focus:ring-opacity-50">
                  <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</p>
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                  <input v-model="registrationData.email" type="email" 
                         :class="errors.email ? 'border-red-300 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-black focus:border-black'"
                         class="w-full px-3 py-2 border rounded-md shadow-sm focus:ring-2 focus:ring-opacity-50">
                  <p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email }}</p>
                </div>
              </div>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Student ID *</label>
                  <input v-model="registrationData.studentId" type="text" 
                         :class="errors.studentId ? 'border-red-300 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-black focus:border-black'"
                         class="w-full px-3 py-2 border rounded-md shadow-sm focus:ring-2 focus:ring-opacity-50">
                  <p v-if="errors.studentId" class="mt-1 text-sm text-red-600">{{ errors.studentId }}</p>
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                  <input v-model="registrationData.phone" type="tel" 
                         :class="errors.phone ? 'border-red-300 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-black focus:border-black'"
                         class="w-full px-3 py-2 border rounded-md shadow-sm focus:ring-2 focus:ring-opacity-50">
                  <p v-if="errors.phone" class="mt-1 text-sm text-red-600">{{ errors.phone }}</p>
                </div>
              </div>
            </div>

            <!-- Academic Information -->
            <div class="space-y-6">
              <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2">
                Academic Information
              </h3>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Batch *</label>
                <select v-model="registrationData.batch" 
                        :class="errors.batch ? 'border-red-300 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-black focus:border-black'"
                        class="w-full px-3 py-2 border rounded-md shadow-sm focus:ring-2 focus:ring-opacity-50">
                  <option value="">Select your batch</option>
                  <option v-for="batch in batches" :key="batch" :value="batch">{{ batch }}</option>
                </select>
                <p v-if="errors.batch" class="mt-1 text-sm text-red-600">{{ errors.batch }}</p>
              </div>
            </div>

            <!-- Motivation -->
            <div class="space-y-6">
              <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2">
                Motivation
              </h3>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Why do you want to join Aformation? *
                </label>
                <textarea v-model="registrationData.motivation" rows="4" 
                          :class="errors.motivation ? 'border-red-300 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-black focus:border-black'"
                          class="w-full px-3 py-2 border rounded-md shadow-sm focus:ring-2 focus:ring-opacity-50"
                          placeholder="Tell us about your motivation to join Aformation program..."></textarea>
                <div class="flex justify-between items-center mt-1">
                  <p v-if="errors.motivation" class="text-sm text-red-600">{{ errors.motivation }}</p>
                  <p class="text-sm text-gray-500 ml-auto">
                    {{ motivationWordCount }}/50 characters minimum
                  </p>
                </div>
              </div>
            </div>

            <!-- Terms and Conditions -->
            <div class="bg-gray-50 rounded-lg p-4">
              <h4 class="font-medium text-gray-900 mb-2">Terms and Conditions</h4>
              <ul class="text-sm text-gray-600 space-y-1">
                <li>• Saya bersedia mengikuti seluruh rangkaian program Aformation</li>
                <li>• Saya akan menjaga kerahasiaan materi yang diberikan</li>
                <li>• Saya tidak akan membagikan akses video kepada pihak lain</li>
                <li>• Saya memahami bahwa token akses bersifat personal dan tidak dapat dipindahtangankan</li>
              </ul>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
              <button type="submit" :disabled="isSubmitting" 
                      class="px-8 py-3 bg-black text-white font-medium rounded-lg hover:bg-gray-800 disabled:opacity-50 disabled:cursor-not-allowed flex items-center">
                <svg v-if="isSubmitting" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ isSubmitting ? 'Submitting...' : 'Submit Registration' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
    
    <Footer />
  </div>
</template>

<style scoped>
/* Custom styles for better form appearance */
.form-input:focus {
  outline: none;
  box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.1);
}
</style>