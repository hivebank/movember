<template>
  <div>
    <Navbar />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
      <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">
          Simple, Transparent Pricing
        </h1>
        <p class="text-xl text-gray-600">
          Choose the plan that's right for you
        </p>
      </div>

      <div class="grid md:grid-cols-4 gap-8">
        <!-- Free Plan -->
        <div class="card border-2 border-gray-200">
          <h3 class="text-2xl font-bold mb-2">Free</h3>
          <div class="text-4xl font-bold mb-4">$0<span class="text-lg text-gray-600">/mo</span></div>
          <ul class="space-y-3 mb-6">
            <li class="flex items-start">
              <span class="text-green-500 mr-2">✓</span>
              <span>3 forms</span>
            </li>
            <li class="flex items-start">
              <span class="text-green-500 mr-2">✓</span>
              <span>100 responses/month</span>
            </li>
            <li class="flex items-start">
              <span class="text-gray-400 mr-2">✗</span>
              <span class="text-gray-500">File uploads</span>
            </li>
            <li class="flex items-start">
              <span class="text-gray-400 mr-2">✗</span>
              <span class="text-gray-500">Payment collection</span>
            </li>
          </ul>
          <router-link
            v-if="!authStore.isAuthenticated"
            to="/register"
            class="block btn btn-outline w-full text-center"
          >
            Get Started
          </router-link>
          <button v-else disabled class="btn btn-secondary w-full">
            Current Plan
          </button>
        </div>

        <!-- Starter Plan -->
        <div class="card border-2 border-primary-500">
          <h3 class="text-2xl font-bold mb-2">Starter</h3>
          <div class="text-4xl font-bold mb-4">$19<span class="text-lg text-gray-600">/mo</span></div>
          <ul class="space-y-3 mb-6">
            <li class="flex items-start">
              <span class="text-green-500 mr-2">✓</span>
              <span>25 forms</span>
            </li>
            <li class="flex items-start">
              <span class="text-green-500 mr-2">✓</span>
              <span>1,000 responses/month</span>
            </li>
            <li class="flex items-start">
              <span class="text-green-500 mr-2">✓</span>
              <span>File uploads</span>
            </li>
            <li class="flex items-start">
              <span class="text-green-500 mr-2">✓</span>
              <span>Payment collection</span>
            </li>
          </ul>
          <button
            @click="handleSubscribe('starter')"
            :disabled="loading"
            class="btn btn-primary w-full"
          >
            {{ loading ? 'Processing...' : 'Subscribe' }}
          </button>
        </div>

        <!-- Pro Plan -->
        <div class="card border-2 border-primary-600 bg-primary-50">
          <div class="bg-primary-600 text-white text-sm font-semibold px-3 py-1 rounded-full inline-block mb-2">
            Popular
          </div>
          <h3 class="text-2xl font-bold mb-2">Pro</h3>
          <div class="text-4xl font-bold mb-4">$49<span class="text-lg text-gray-600">/mo</span></div>
          <ul class="space-y-3 mb-6">
            <li class="flex items-start">
              <span class="text-green-500 mr-2">✓</span>
              <span>Unlimited forms</span>
            </li>
            <li class="flex items-start">
              <span class="text-green-500 mr-2">✓</span>
              <span>10,000 responses/month</span>
            </li>
            <li class="flex items-start">
              <span class="text-green-500 mr-2">✓</span>
              <span>Custom branding</span>
            </li>
            <li class="flex items-start">
              <span class="text-green-500 mr-2">✓</span>
              <span>Advanced analytics</span>
            </li>
            <li class="flex items-start">
              <span class="text-green-500 mr-2">✓</span>
              <span>API access</span>
            </li>
          </ul>
          <button
            @click="handleSubscribe('pro')"
            :disabled="loading"
            class="btn btn-primary w-full"
          >
            {{ loading ? 'Processing...' : 'Subscribe' }}
          </button>
        </div>

        <!-- Enterprise Plan -->
        <div class="card border-2 border-gray-200">
          <h3 class="text-2xl font-bold mb-2">Enterprise</h3>
          <div class="text-4xl font-bold mb-4">$199<span class="text-lg text-gray-600">/mo</span></div>
          <ul class="space-y-3 mb-6">
            <li class="flex items-start">
              <span class="text-green-500 mr-2">✓</span>
              <span>Everything in Pro</span>
            </li>
            <li class="flex items-start">
              <span class="text-green-500 mr-2">✓</span>
              <span>Unlimited responses</span>
            </li>
            <li class="flex items-start">
              <span class="text-green-500 mr-2">✓</span>
              <span>Team collaboration</span>
            </li>
            <li class="flex items-start">
              <span class="text-green-500 mr-2">✓</span>
              <span>Priority support</span>
            </li>
          </ul>
          <button
            @click="handleSubscribe('enterprise')"
            :disabled="loading"
            class="btn btn-primary w-full"
          >
            {{ loading ? 'Processing...' : 'Subscribe' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useSubscriptionStore } from '@/stores/subscription'
import Navbar from '@/components/common/Navbar.vue'

const router = useRouter()
const authStore = useAuthStore()
const subscriptionStore = useSubscriptionStore()

const loading = ref(false)

const handleSubscribe = async (plan) => {
  if (!authStore.isAuthenticated) {
    router.push('/register')
    return
  }

  loading.value = true

  try {
    const result = await subscriptionStore.subscribe(plan)
    if (result.checkoutUrl) {
      window.location.href = result.checkoutUrl
    }
  } catch (error) {
    alert('Failed to create subscription. Please try again.')
  } finally {
    loading.value = false
  }
}
</script>
