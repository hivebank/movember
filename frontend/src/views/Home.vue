<template>
  <div>
    <Navbar />

    <div class="relative overflow-hidden">
      <!-- Hero Section -->
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="text-center">
          <h1 class="text-5xl font-bold text-gray-900 mb-6">
            Create Beautiful Forms in Minutes
          </h1>
          <p class="text-xl text-gray-600 mb-8 max-w-2xl mx-auto">
            Build step-by-step forms that convert. Collect payments, analyze responses,
            and grow your business with FormFlow.
          </p>
          <div class="flex justify-center space-x-4">
            <router-link to="/register" class="btn btn-primary text-lg px-8 py-3">
              Get Started Free
            </router-link>
            <router-link to="/pricing" class="btn btn-outline text-lg px-8 py-3">
              View Pricing
            </router-link>
          </div>
        </div>
      </div>

      <!-- Features Section -->
      <div class="bg-gray-50 py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <h2 class="text-3xl font-bold text-center mb-12">Why Choose FormFlow?</h2>

          <div class="grid md:grid-cols-3 gap-8">
            <div class="card text-center">
              <div class="text-4xl mb-4">🎨</div>
              <h3 class="text-xl font-semibold mb-2">Beautiful Design</h3>
              <p class="text-gray-600">
                Create stunning, professional forms with our intuitive drag-and-drop builder.
              </p>
            </div>

            <div class="card text-center">
              <div class="text-4xl mb-4">💳</div>
              <h3 class="text-xl font-semibold mb-2">Accept Payments</h3>
              <p class="text-gray-600">
                Collect payments seamlessly with integrated Stripe support.
              </p>
            </div>

            <div class="card text-center">
              <div class="text-4xl mb-4">📊</div>
              <h3 class="text-xl font-semibold mb-2">Analytics</h3>
              <p class="text-gray-600">
                Track responses and gain insights with powerful analytics tools.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import Navbar from '@/components/common/Navbar.vue'
</script>
