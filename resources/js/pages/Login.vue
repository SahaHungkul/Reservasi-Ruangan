<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useUserStore } from '../stores/user'

const email = ref('')
const password = ref('')
const loading = ref(false)
const errorMessage = ref('')

const router = useRouter()
const userStore = useUserStore()

async function handleLogin() {
  loading.value = true
  errorMessage.value = ''
  try {
    const role = await userStore.login(email.value, password.value)

    if (role === 'admin') {
      router.push('/admin')
    } else if (role === 'karyawan') {
      router.push('/karyawan')
    } else {
      router.push('/home')
    }
  } catch (err) {
    errorMessage.value = err.response?.data?.message || 'Login gagal'
  } finally {
    loading.value = false
  }
}
</script>

<template>
    <div class="flex justify-center items-center min-h-screen bg-gray-100">
        <div class="w-full max-w-sm bg-white p-6 rounded shadow">
            <div class="flex justify-center mb-4">
                <img src="@/assets/yuzu.jpg" alt="Logo" class="w-16 h-16 rounded-full shadow" />
            </div>
            <h2 class="text-2xl font-bold text-center mb-6 text-gray-700">Login</h2>
            <form @submit.prevent="handleLogin" class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input v-model="email" type="email" class="w-full border p-2 rounded sm:text-sm" placeholder="email"
                        required />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Password</label>
                    <div class="relative">
                        <input v-model="password" :type="showPassword ? 'text' : 'password'"
                            class="w-full border p-2 rounded sm:text-sm" placeholder="password" required />
                        <button type="button" @click="showPassword = !showPassword"
                            class="absolute right-2 top-2 text-gray-500 hover:text-gray-700">
                            {{ showPassword ? '🙈' : '👁️' }}
                        </button>
                    </div>
                </div>
                <button :disabled="loading" class="w-full bg-blue-500 text-white p-2 rounded cursor-pointer">
                    {{ loading ? "Loading..." : "Login" }}
                </button>
                <p v-if="errorMessage" class="text-red-500">{{ errorMessage }}</p>
            </form>
            <p class="text-sm text-center mt-4 text-gray-600">
                Belum punya akun?
                <router-link to="/register" class="text-blue-500 hover:underline">
                    Buat Akun
                </router-link>
            </p>
        </div>
    </div>
</template>
