<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import Navbar from '../Navbar.vue'
import Footer from '../Footer.vue'
import { useAuth } from '@/composables/useAuth'

const router = useRouter()
const { login, register, loading } = useAuth()

const activeTab = ref('login')
const showPassword = ref(false)
const showConfirmPassword = ref(false)

// Login form data
const loginForm = ref({
    email: '',
    password: '',
    remember: false
})

// Register form data
const registerForm = ref({
    firstName: '',
    lastName: '',
    email: '',
    username: '',
    password: '',
    confirmPassword: '',
    acceptTerms: false
})

const successMessage = ref<string | null>(null)
const errorMessage = ref<string | null>(null)

// Password strength validation
const passwordStrength = computed(() => {
    const password = registerForm.value.password
    if (!password) return { score: 0, label: '', color: '' }
    
    let score = 0
    
    // Length check
    if (password.length >= 8) score++
    if (password.length >= 12) score++
    
    // Character type checks
    if (/[a-z]/.test(password)) score++
    if (/[A-Z]/.test(password)) score++
    if (/[0-9]/.test(password)) score++
    if (/[^A-Za-z0-9]/.test(password)) score++
    
    // Determine strength
    if (score <= 2) return { score: 1, label: 'WEAK', color: 'bg-red-500' }
    if (score <= 4) return { score: 2, label: 'MEDIUM', color: 'bg-yellow-500' }
    return { score: 3, label: 'STRONG', color: 'bg-green-500' }
})

const passwordsMatch = computed(() => {
    if (!registerForm.value.confirmPassword) return null
    return registerForm.value.password === registerForm.value.confirmPassword
})

const handleLogin = async () => {
    errorMessage.value = null
    successMessage.value = null

    if (!loginForm.value.email || !loginForm.value.password) {
        errorMessage.value = 'Please fill in all fields'
        return
    }

    const result = await login(loginForm.value.email, loginForm.value.password)
    
    if (result.success) {
        successMessage.value = 'Login successful! Redirecting...'
        setTimeout(() => {
            router.push('/')
        }, 1000)
    } else {
        errorMessage.value = result.message
    }
}

const handleRegister = async () => {
    errorMessage.value = null
    successMessage.value = null

    if (!registerForm.value.firstName || !registerForm.value.lastName || 
        !registerForm.value.email || !registerForm.value.password) {
        errorMessage.value = 'Please fill in all fields'
        return
    }

    if (registerForm.value.password.length < 8) {
        errorMessage.value = 'Password must be at least 8 characters long'
        return
    }

    if (registerForm.value.password !== registerForm.value.confirmPassword) {
        errorMessage.value = 'Passwords do not match'
        return
    }

    if (passwordStrength.value.score < 2) {
        errorMessage.value = 'Password is too weak. Please use a stronger password.'
        return
    }

    if (!registerForm.value.acceptTerms) {
        errorMessage.value = 'Please accept the terms of service'
        return
    }

    const fullName = `${registerForm.value.firstName} ${registerForm.value.lastName}`
    const result = await register(fullName, registerForm.value.email, registerForm.value.password)
    
    if (result.success) {
        successMessage.value = 'Registration successful! Redirecting...'
        setTimeout(() => {
            router.push('/')
        }, 1000)
    } else {
        errorMessage.value = result.message
    }
}
</script>

<template>
    <Navbar/>
    <div class="relative flex items-center justify-center min-h-screen p-4 bg-white">
        <div class="absolute inset-0 overflow-hidden">
            <div class="dot-pattern"></div>
        </div>
        
        <div class="relative w-full max-w-md py-24">
            <div class="absolute top-0 w-64 h-64 rounded-full -z-10 bg-purple-500/10 blur-3xl -left-32"></div>
            <div class="absolute bottom-0 w-64 h-64 rounded-full -z-10 bg-blue-500/10 blur-3xl -right-32"></div>

            <div class="overflow-hidden bg-white border shadow-2xl backdrop-blur-lg border-black/10 rounded-2xl">
                <div class="flex border-b border-black/10">
                    <button @click="activeTab = 'login'" :class="[
                        'flex-1 py-4 px-6 font-mono text-sm uppercase transition-all duration-300',
                        activeTab === 'login'
                            ? 'text-black bg-black/5'
                            : 'text-black/60 hover:text-black hover:bg-black/5'
                    ]">
                        <div class="flex items-center justify-center">
                            <span class="mr-2 text-xs px-1.5 py-0.5 rounded-full bg-black/10">01</span>
                            Login
                        </div>
                    </button>
                    <button @click="activeTab = 'register'" :class="[
                        'flex-1 py-4 px-6 font-mono text-sm uppercase transition-all duration-300',
                        activeTab === 'register'
                            ? 'text-black bg-black/5'
                            : 'text-black/60 hover:text-black hover:bg-black/5'
                    ]">
                        <div class="flex items-center justify-center">
                            <span class="mr-2 text-xs px-1.5 py-0.5 rounded-full bg-black/10">02</span>
                            Register
                        </div>
                    </button>
                </div>

                <div v-if="activeTab === 'login'" class="p-8 space-y-6">
                    <h2 class="font-mono text-2xl font-bold text-center text-black">
                        <span class="text-black">
                            Login
                        </span>
                    </h2>

                    <!-- Error Message -->
                    <div v-if="errorMessage" class="p-3 text-sm text-red-700 border border-red-300 rounded-lg bg-red-50">
                        {{ errorMessage }}
                    </div>

                    <!-- Success Message -->
                    <div v-if="successMessage" class="p-3 text-sm text-green-700 border border-green-300 rounded-lg bg-green-50">
                        {{ successMessage }}
                    </div>

                    <form @submit.prevent="handleLogin" class="space-y-6">
                        <div class="space-y-2">
                            <label
                                class="block font-mono text-xs tracking-wider uppercase text-black/70">Email</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <span class="text-xs text-black/40">[E]</span>
                                </div>
                                <input v-model="loginForm.email" type="email" required
                                    class="w-full py-3 pl-12 pr-4 font-mono text-black transition-all border rounded-lg bg-black/5 border-black/10 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-transparent"
                                    placeholder="Enter email" />
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label
                                class="block font-mono text-xs tracking-wider uppercase text-black/70">Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <span class="text-xs text-black/40">[PW]</span>
                                </div>
                                <input v-model="loginForm.password" :type="showPassword ? 'text' : 'password'" required
                                    class="w-full py-3 pl-12 pr-10 font-mono text-black transition-all border rounded-lg bg-black/5 border-black/10 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-transparent"
                                    placeholder="Enter password" />
                                <button type="button" @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-black/40 hover:text-black/70">
                                    <span class="text-xs">{{ showPassword ? '[HIDE]' : '[SHOW]' }}</span>
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <input v-model="loginForm.remember" id="remember-me" type="checkbox"
                                    class="w-4 h-4 text-blue-500 border rounded bg-black/5 border-black/10 focus:ring-blue-500/50" />
                                <label for="remember-me" class="ml-2 font-mono text-sm text-black/70">Remember
                                    session</label>
                            </div>
                            <button type="button" class="font-mono text-sm text-blue-600 hover:text-blue-800">Reset
                                access</button>
                        </div>

                        <button type="submit" :disabled="loading"
                            class="w-full px-4 py-3 font-mono font-bold tracking-wider text-white uppercase transition-all duration-300 bg-black rounded-lg disabled:opacity-50 disabled:cursor-not-allowed">
                            <span class="flex items-center justify-center">
                                <span class="mr-2">{{ loading ? 'Loading...' : 'Login' }}</span>
                                <span v-if="!loading" class="text-xs">[→]</span>
                            </span>
                        </button>
                    </form>
                </div>

                <div v-if="activeTab === 'register'" class="p-8 space-y-6">
                    <h2 class="font-mono text-2xl font-bold text-center text-black">
                        <span class="text-black">
                            Create Account
                        </span>
                    </h2>

                    <!-- Error Message -->
                    <div v-if="errorMessage" class="p-3 text-sm text-red-700 border border-red-300 rounded-lg bg-red-50">
                        {{ errorMessage }}
                    </div>

                    <!-- Success Message -->
                    <div v-if="successMessage" class="p-3 text-sm text-green-700 border border-green-300 rounded-lg bg-green-50">
                        {{ successMessage }}
                    </div>

                    <form @submit.prevent="handleRegister" class="space-y-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="block font-mono text-xs tracking-wider uppercase text-black/70">First
                                    Name</label>
                                <input v-model="registerForm.firstName" type="text" required
                                    class="w-full px-4 py-3 font-mono text-black transition-all border rounded-lg bg-black/5 border-black/10 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-transparent"
                                    placeholder="First name" />
                            </div>
                            <div class="space-y-2">
                                <label class="block font-mono text-xs tracking-wider uppercase text-black/70">Last
                                    Name</label>
                                <input v-model="registerForm.lastName" type="text" required
                                    class="w-full px-4 py-3 font-mono text-black transition-all border rounded-lg bg-black/5 border-black/10 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-transparent"
                                    placeholder="Last name" />
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block font-mono text-xs tracking-wider uppercase text-black/70">Email</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <span class="text-xs text-black/40">[E]</span>
                                </div>
                                <input v-model="registerForm.email" type="email" required
                                    class="w-full py-3 pl-12 pr-4 font-mono text-black transition-all border rounded-lg bg-black/5 border-black/10 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-transparent"
                                    placeholder="Enter email address" />
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label
                                class="block font-mono text-xs tracking-wider uppercase text-black/70">Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <span class="text-xs text-black/40">[PW]</span>
                                </div>
                                <input v-model="registerForm.password" :type="showPassword ? 'text' : 'password'" required minlength="8"
                                    class="w-full py-3 pl-12 pr-10 font-mono text-black transition-all border rounded-lg bg-black/5 border-black/10 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-transparent"
                                    placeholder="Create password (min 8 characters)" />
                                <button type="button" @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-black/40 hover:text-black/70">
                                    <span class="text-xs">{{ showPassword ? '[HIDE]' : '[SHOW]' }}</span>
                                </button>
                            </div>
                            <!-- Password Strength Indicator -->
                            <div v-if="registerForm.password" class="space-y-1">
                                <div class="flex gap-1">
                                    <div :class="['flex-1 h-1 rounded-full transition-all duration-300', passwordStrength.score >= 1 ? passwordStrength.color : 'bg-black/10']"></div>
                                    <div :class="['flex-1 h-1 rounded-full transition-all duration-300', passwordStrength.score >= 2 ? passwordStrength.color : 'bg-black/10']"></div>
                                    <div :class="['flex-1 h-1 rounded-full transition-all duration-300', passwordStrength.score >= 3 ? passwordStrength.color : 'bg-black/10']"></div>
                                </div>
                                <p class="font-mono text-xs" :class="{
                                    'text-red-600': passwordStrength.score === 1,
                                    'text-yellow-600': passwordStrength.score === 2,
                                    'text-green-600': passwordStrength.score === 3
                                }">
                                    Strength: {{ passwordStrength.label }}
                                </p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label
                                class="block font-mono text-xs tracking-wider uppercase text-black/70">Confirm Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <span class="text-xs text-black/40">[CPW]</span>
                                </div>
                                <input v-model="registerForm.confirmPassword" :type="showConfirmPassword ? 'text' : 'password'" required
                                    class="w-full py-3 pl-12 pr-10 font-mono text-black transition-all border rounded-lg bg-black/5 border-black/10 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-transparent"
                                    placeholder="Confirm your password" />
                                <button type="button" @click="showConfirmPassword = !showConfirmPassword"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-black/40 hover:text-black/70">
                                    <span class="text-xs">{{ showConfirmPassword ? '[HIDE]' : '[SHOW]' }}</span>
                                </button>
                            </div>
                            <!-- Password Match Indicator -->
                            <p v-if="passwordsMatch !== null" class="font-mono text-xs" :class="passwordsMatch ? 'text-green-600' : 'text-red-600'">
                                {{ passwordsMatch ? '✓ Passwords match' : '✗ Passwords do not match' }}
                            </p>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input v-model="registerForm.acceptTerms" id="terms" type="checkbox"
                                    class="w-4 h-4 text-blue-500 border rounded bg-black/5 border-black/10 focus:ring-blue-500/50" />
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="terms" class="font-mono text-black/70">
                                    I accept the <a href="#" class="text-blue-600 hover:text-blue-800">terms of
                                        service</a> and <a href="#" class="text-blue-600 hover:text-blue-800">privacy
                                        policy</a>
                                </label>
                            </div>
                        </div>

                        <button type="submit" :disabled="loading"
                            class="w-full px-4 py-3 font-mono font-bold tracking-wider text-white uppercase transition-all duration-300 bg-black rounded-lg disabled:opacity-50 disabled:cursor-not-allowed">
                            <span class="flex items-center justify-center">
                                <span class="mr-2">{{ loading ? 'Loading...' : 'Register' }}</span>
                                <span v-if="!loading" class="text-xs">[→]</span>
                            </span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <Footer/>
</template>

<style scoped>
input[type="checkbox"] {
    appearance: none;
    background-color: rgba(0, 0, 0, 0.05);
    border: 1px solid rgba(0, 0, 0, 0.1);
    width: 16px;
    height: 16px;
    border-radius: 3px;
    display: inline-block;
    position: relative;
    cursor: pointer;
}

input[type="checkbox"]:checked {
    background-color: rgb(59, 130, 246);
    border-color: rgb(59, 130, 246);
}

input[type="checkbox"]:checked::after {
    content: "";
    position: absolute;
    left: 5px;
    top: 2px;
    width: 5px;
    height: 9px;
    border: solid white;
    border-width: 0 2px 2px 0;
    transform: rotate(45deg);
}

/* Dot pattern background */
.dot-pattern {
    position: absolute;
    width: 100%;
    height: 100%;
    background-image: radial-gradient(rgba(0, 0, 0, 0.15) 1px, transparent 1px);
    background-size: 24px 24px;
    background-position: 0 0;
    animation: moveBackground 60s linear infinite;
}

@keyframes moveBackground {
    0% {
        transform: translate(0, 0);
    }
    50% {
        transform: translate(12px, 12px);
    }
    100% {
        transform: translate(0, 0);
    }
}
</style>