<template>
  <div>
    <Navbar />

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <h1 class="text-3xl font-bold text-gray-900 mb-8">Settings</h1>

      <div class="space-y-6">
        <!-- Profile Settings -->
        <div class="card">
          <h2 class="text-xl font-semibold mb-4">Profile</h2>
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
              <input
                v-model="profile.name"
                type="text"
                class="input"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
              <input
                v-model="profile.email"
                type="email"
                class="input"
                disabled
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Company</label>
              <input
                v-model="profile.company"
                type="text"
                class="input"
              />
            </div>
            <button @click="saveProfile" class="btn btn-primary">
              Save Changes
            </button>
          </div>
        </div>

        <!-- Subscription -->
        <div class="card">
          <h2 class="text-xl font-semibold mb-4">Subscription</h2>
          <div v-if="authStore.user">
            <div class="bg-gray-50 rounded-lg p-4 mb-4">
              <div class="flex justify-between items-center">
                <div>
                  <div class="font-semibold text-lg">
                    {{ authStore.user.subscription.plan.toUpperCase() }} Plan
                  </div>
                  <div class="text-sm text-gray-600 mt-1">
                    Status: {{ authStore.user.subscription.status }}
                  </div>
                </div>
                <router-link
                  v-if="authStore.user.subscription.plan === 'free'"
                  to="/pricing"
                  class="btn btn-primary"
                >
                  Upgrade
                </router-link>
                <button
                  v-else
                  @click="cancelSubscription"
                  class="btn btn-secondary"
                >
                  Cancel Subscription
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Danger Zone -->
        <div class="card border-2 border-red-200">
          <h2 class="text-xl font-semibold text-red-600 mb-4">Danger Zone</h2>
          <p class="text-gray-600 mb-4">
            Once you delete your account, there is no going back. Please be certain.
          </p>
          <button class="btn bg-red-600 text-white hover:bg-red-700">
            Delete Account
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useSubscriptionStore } from '@/stores/subscription'
import Navbar from '@/components/common/Navbar.vue'

const authStore = useAuthStore()
const subscriptionStore = useSubscriptionStore()

const profile = ref({
  name: '',
  email: '',
  company: '',
})

onMounted(() => {
  if (authStore.user) {
    profile.value = {
      name: authStore.user.name,
      email: authStore.user.email,
      company: authStore.user.company,
    }
  }
})

const saveProfile = () => {
  authStore.updateUser(profile.value)
  alert('Profile updated successfully!')
}

const cancelSubscription = async () => {
  if (confirm('Are you sure you want to cancel your subscription?')) {
    try {
      await subscriptionStore.cancelSubscription()
      alert('Subscription will be cancelled at the end of the billing period.')
      await authStore.checkAuth()
    } catch (error) {
      alert('Failed to cancel subscription. Please try again.')
    }
  }
}
</script>
