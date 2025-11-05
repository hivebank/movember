<template>
  <nav class="bg-white shadow-sm border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <div class="flex items-center">
          <router-link to="/" class="flex items-center">
            <span class="text-2xl font-bold text-primary-600">FormFlow</span>
          </router-link>
        </div>

        <div class="flex items-center space-x-4">
          <template v-if="authStore.isAuthenticated">
            <router-link to="/dashboard" class="text-gray-700 hover:text-primary-600">
              Dashboard
            </router-link>
            <router-link to="/pricing" class="text-gray-700 hover:text-primary-600">
              Pricing
            </router-link>
            <div class="relative" ref="profileDropdown">
              <button
                @click="showProfile = !showProfile"
                class="flex items-center space-x-2 text-gray-700 hover:text-primary-600"
              >
                <span>{{ authStore.user?.name }}</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </button>

              <div
                v-if="showProfile"
                class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-10"
              >
                <router-link
                  to="/settings"
                  class="block px-4 py-2 text-gray-700 hover:bg-gray-100"
                  @click="showProfile = false"
                >
                  Settings
                </router-link>
                <button
                  @click="handleLogout"
                  class="block w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100"
                >
                  Logout
                </button>
              </div>
            </div>
          </template>

          <template v-else>
            <router-link to="/pricing" class="text-gray-700 hover:text-primary-600">
              Pricing
            </router-link>
            <router-link to="/login" class="text-gray-700 hover:text-primary-600">
              Login
            </router-link>
            <router-link to="/register" class="btn btn-primary">
              Get Started
            </router-link>
          </template>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()
const showProfile = ref(false)
const profileDropdown = ref(null)

const handleLogout = () => {
  authStore.logout()
  showProfile.value = false
  router.push('/login')
}

const handleClickOutside = (event) => {
  if (profileDropdown.value && !profileDropdown.value.contains(event.target)) {
    showProfile.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>
