<template>
  <div>
    <Navbar />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Header -->
      <div class="flex justify-between items-center mb-8">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Form Responses</h1>
          <p v-if="form" class="text-gray-600 mt-1">{{ form.title }}</p>
        </div>
        <router-link to="/dashboard" class="btn btn-secondary">
          ← Back to Dashboard
        </router-link>
      </div>

      <!-- Analytics -->
      <div v-if="analytics" class="grid md:grid-cols-3 gap-6 mb-8">
        <div class="card">
          <div class="text-gray-600 text-sm mb-1">Total Responses</div>
          <div class="text-3xl font-bold text-primary-600">
            {{ analytics.totalResponses }}
          </div>
        </div>
        <div class="card">
          <div class="text-gray-600 text-sm mb-1">Form Views</div>
          <div class="text-3xl font-bold text-primary-600">
            {{ form?.views || 0 }}
          </div>
        </div>
        <div class="card">
          <div class="text-gray-600 text-sm mb-1">Avg. Completion Time</div>
          <div class="text-3xl font-bold text-primary-600">
            {{ analytics.avgCompletionTime }}s
          </div>
        </div>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="flex justify-center py-12">
        <div class="spinner"></div>
      </div>

      <!-- Empty State -->
      <div v-else-if="responses.length === 0" class="text-center py-12 card">
        <div class="text-6xl mb-4">📭</div>
        <h2 class="text-2xl font-semibold text-gray-900 mb-2">
          No responses yet
        </h2>
        <p class="text-gray-600">
          Share your form to start collecting responses
        </p>
      </div>

      <!-- Responses Table -->
      <div v-else class="card overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Submitted
                </th>
                <th
                  v-for="field in form?.fields || []"
                  :key="field.id"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  {{ field.label }}
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="response in responses" :key="response.id">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formatDate(response.submittedAt) }}
                </td>
                <td
                  v-for="field in form?.fields || []"
                  :key="field.id"
                  class="px-6 py-4 text-sm text-gray-900"
                >
                  {{ getAnswerValue(response, field.id) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                  <button
                    @click="viewResponse(response)"
                    class="text-primary-600 hover:text-primary-900 mr-4"
                  >
                    View
                  </button>
                  <button
                    @click="deleteResponse(response)"
                    class="text-red-600 hover:text-red-900"
                  >
                    Delete
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Response Detail Modal -->
      <Modal v-model="showDetailModal" :title="'Response Details'">
        <div v-if="selectedResponse" class="space-y-4">
          <div v-for="answer in selectedResponse.answers" :key="answer.fieldId">
            <div class="text-sm font-medium text-gray-700">{{ answer.label }}</div>
            <div class="text-gray-900 mt-1">{{ answer.value }}</div>
          </div>
          <div class="pt-4 border-t">
            <div class="text-sm text-gray-600">
              Submitted: {{ formatDate(selectedResponse.submittedAt) }}
            </div>
            <div class="text-sm text-gray-600">
              Completion time: {{ selectedResponse.metadata?.completionTime || 0 }}s
            </div>
          </div>
        </div>
      </Modal>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { formsAPI, responsesAPI } from '@/services/api'
import Navbar from '@/components/common/Navbar.vue'
import Modal from '@/components/common/Modal.vue'

const route = useRoute()

const form = ref(null)
const responses = ref([])
const analytics = ref(null)
const loading = ref(true)
const showDetailModal = ref(false)
const selectedResponse = ref(null)

onMounted(async () => {
  await loadData()
})

const loadData = async () => {
  loading.value = true
  try {
    const [formRes, responsesRes, analyticsRes] = await Promise.all([
      formsAPI.get(route.params.id),
      responsesAPI.list(route.params.id),
      responsesAPI.getAnalytics(route.params.id),
    ])

    form.value = formRes.data.form
    responses.value = responsesRes.data.responses
    analytics.value = analyticsRes.data.analytics
  } catch (error) {
    console.error('Failed to load data:', error)
  } finally {
    loading.value = false
  }
}

const getAnswerValue = (response, fieldId) => {
  const answer = response.answers.find(a => a.fieldId === fieldId)
  return answer?.value || '-'
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleString()
}

const viewResponse = (response) => {
  selectedResponse.value = response
  showDetailModal.value = true
}

const deleteResponse = async (response) => {
  if (confirm('Are you sure you want to delete this response?')) {
    try {
      await responsesAPI.delete(response.id)
      responses.value = responses.value.filter(r => r.id !== response.id)
    } catch (error) {
      alert('Failed to delete response')
    }
  }
}
</script>
