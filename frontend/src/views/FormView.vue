<template>
  <div class="min-h-screen bg-gradient-to-br from-primary-50 to-blue-50 py-12">
    <div class="max-w-2xl mx-auto px-4">
      <!-- Loading -->
      <div v-if="loading" class="flex justify-center py-20">
        <div class="spinner"></div>
      </div>

      <!-- Form -->
      <div v-else-if="form" class="card">
        <div v-if="!submitted">
          <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ form.title }}</h1>
          <p v-if="form.description" class="text-gray-600 mb-8">{{ form.description }}</p>

          <form @submit.prevent="submitForm" class="space-y-6">
            <!-- Render fields -->
            <div v-for="field in form.fields" :key="field.id" class="space-y-2">
              <label class="block text-sm font-medium text-gray-900">
                {{ field.label }}
                <span v-if="field.required" class="text-red-500">*</span>
              </label>

              <!-- Text input -->
              <input
                v-if="field.type === 'text'"
                v-model="answers[field.id]"
                type="text"
                :required="field.required"
                :placeholder="field.placeholder"
                class="input"
              />

              <!-- Email input -->
              <input
                v-else-if="field.type === 'email'"
                v-model="answers[field.id]"
                type="email"
                :required="field.required"
                :placeholder="field.placeholder"
                class="input"
              />

              <!-- Number input -->
              <input
                v-else-if="field.type === 'number'"
                v-model="answers[field.id]"
                type="number"
                :required="field.required"
                :placeholder="field.placeholder"
                class="input"
              />

              <!-- Textarea -->
              <textarea
                v-else-if="field.type === 'textarea'"
                v-model="answers[field.id]"
                :required="field.required"
                :placeholder="field.placeholder"
                class="input"
                rows="4"
              ></textarea>

              <!-- Select -->
              <select
                v-else-if="field.type === 'select'"
                v-model="answers[field.id]"
                :required="field.required"
                class="input"
              >
                <option value="">Select an option...</option>
                <option v-for="option in field.options" :key="option" :value="option">
                  {{ option }}
                </option>
              </select>

              <!-- Date -->
              <input
                v-else-if="field.type === 'date'"
                v-model="answers[field.id]"
                type="date"
                :required="field.required"
                class="input"
              />
            </div>

            <!-- Submit button -->
            <div class="pt-4">
              <button
                type="submit"
                :disabled="submitting"
                class="w-full btn btn-primary py-3 text-lg"
              >
                {{ submitting ? 'Submitting...' : (form.settings?.submitText || 'Submit') }}
              </button>
            </div>
          </form>
        </div>

        <!-- Success message -->
        <div v-else class="text-center py-12">
          <div class="text-6xl mb-4">✅</div>
          <h2 class="text-2xl font-bold text-gray-900 mb-2">
            Thank you!
          </h2>
          <p class="text-gray-600">
            Your response has been submitted successfully.
          </p>
        </div>
      </div>

      <!-- Error state -->
      <div v-else class="card text-center py-12">
        <div class="text-6xl mb-4">❌</div>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">
          Form not found
        </h2>
        <p class="text-gray-600">
          This form may have been deleted or is no longer available.
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { formsAPI } from '@/services/api'

const route = useRoute()

const form = ref(null)
const answers = ref({})
const loading = ref(true)
const submitting = ref(false)
const submitted = ref(false)
const startTime = ref(Date.now())

onMounted(async () => {
  try {
    const response = await formsAPI.getPublic(route.params.slug)
    form.value = response.data.form
    startTime.value = Date.now()
  } catch (error) {
    console.error('Failed to load form:', error)
  } finally {
    loading.value = false
  }
})

const submitForm = async () => {
  submitting.value = true

  try {
    const completionTime = Math.floor((Date.now() - startTime.value) / 1000)

    const formattedAnswers = Object.entries(answers.value).map(([fieldId, value]) => {
      const field = form.value.fields.find(f => f.id === fieldId)
      return {
        fieldId,
        value,
        label: field?.label || '',
      }
    })

    await formsAPI.submit(route.params.slug, {
      answers: formattedAnswers,
      completionTime,
    })

    submitted.value = true

    // Redirect if configured
    if (form.value.settings?.redirectUrl) {
      setTimeout(() => {
        window.location.href = form.value.settings.redirectUrl
      }, 2000)
    }
  } catch (error) {
    console.error('Failed to submit form:', error)
    alert('Failed to submit form. Please try again.')
  } finally {
    submitting.value = false
  }
}
</script>
