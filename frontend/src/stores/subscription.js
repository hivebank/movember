import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { subscriptionAPI } from '@/services/api'
import { useAuthStore } from './auth'

export const useSubscriptionStore = defineStore('subscription', () => {
  const plans = ref({})
  const currentPlan = ref(null)
  const loading = ref(false)
  const error = ref(null)

  const authStore = useAuthStore()

  const isPro = computed(() => {
    return currentPlan.value?.plan === 'pro' || currentPlan.value?.plan === 'enterprise'
  })

  const canUseFeature = (feature) => {
    if (!currentPlan.value) return false
    const plan = plans.value[currentPlan.value.plan]
    return plan?.features?.[feature] || false
  }

  const fetchPlans = async () => {
    loading.value = true
    error.value = null

    try {
      const response = await subscriptionAPI.getPlans()
      plans.value = response.data.plans
      return response.data.plans
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch plans'
      throw err
    } finally {
      loading.value = false
    }
  }

  const fetchStatus = async () => {
    loading.value = true
    error.value = null

    try {
      const response = await subscriptionAPI.getStatus()
      currentPlan.value = response.data.subscription
      return response.data.subscription
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch subscription status'
      throw err
    } finally {
      loading.value = false
    }
  }

  const subscribe = async (plan) => {
    loading.value = true
    error.value = null

    try {
      const response = await subscriptionAPI.create({ plan })
      return response.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to create subscription'
      throw err
    } finally {
      loading.value = false
    }
  }

  const cancelSubscription = async () => {
    loading.value = true
    error.value = null

    try {
      const response = await subscriptionAPI.cancel()
      await fetchStatus()
      return response.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to cancel subscription'
      throw err
    } finally {
      loading.value = false
    }
  }

  const updateSubscription = async (plan) => {
    loading.value = true
    error.value = null

    try {
      const response = await subscriptionAPI.update({ plan })
      await fetchStatus()
      return response.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to update subscription'
      throw err
    } finally {
      loading.value = false
    }
  }

  return {
    plans,
    currentPlan,
    loading,
    error,
    isPro,
    canUseFeature,
    fetchPlans,
    fetchStatus,
    subscribe,
    cancelSubscription,
    updateSubscription,
  }
})
