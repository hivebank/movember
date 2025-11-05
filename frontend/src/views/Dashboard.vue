<template>
  <div>
    <Navbar />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Header -->
      <div class="flex justify-between items-center mb-8">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">My Forms</h1>
          <p class="text-gray-600 mt-1">
            Create and manage your forms
          </p>
        </div>
        <router-link to="/forms/new" class="btn btn-primary">
          + Create New Form
        </router-link>
      </div>

      <!-- Plan Info -->
      <div v-if="authStore.user" class="bg-primary-50 border border-primary-200 rounded-lg p-4 mb-6">
        <div class="flex justify-between items-center">
          <div>
            <span class="font-semibold text-primary-900">
              {{ authStore.user.subscription.plan.toUpperCase() }} Plan
            </span>
            <span class="text-primary-700 ml-2">
              {{ forms.length }} forms created
            </span>
          </div>
          <router-link
            v-if="authStore.user.subscription.plan === 'free'"
            to="/pricing"
            class="btn btn-primary text-sm"
          >
            Upgrade Plan
          </router-link>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="flex justify-center py-12">
        <div class="spinner"></div>
      </div>

      <!-- Empty State -->
      <div v-else-if="forms.length === 0" class="text-center py-12">
        <div class="text-6xl mb-4">📝</div>
        <h2 class="text-2xl font-semibold text-gray-900 mb-2">
          No forms yet
        </h2>
        <p class="text-gray-600 mb-6">
          Create your first form to get started
        </p>
        <router-link to="/forms/new" class="btn btn-primary">
          Create Your First Form
        </router-link>
      </div>

      <!-- Forms Grid -->
      <div v-else class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="form in forms"
          :key="form.id"
          class="card hover:shadow-lg transition-shadow cursor-pointer"
        >
          <div class="flex justify-between items-start mb-3">
            <div class="flex-1">
              <h3 class="text-lg font-semibold text-gray-900 mb-1">
                {{ form.title }}
              </h3>
              <p class="text-sm text-gray-600 line-clamp-2">
                {{ form.description || 'No description' }}
              </p>
            </div>
            <span
              class="px-2 py-1 text-xs rounded-full"
              :class="{
                'bg-green-100 text-green-800': form.status === 'published',
                'bg-gray-100 text-gray-800': form.status === 'draft',
                'bg-yellow-100 text-yellow-800': form.status === 'archived'
              }"
            >
              {{ form.status }}
            </span>
          </div>

          <div class="flex items-center text-sm text-gray-500 mb-4">
            <span class="mr-4">👁️ {{ form.views }} views</span>
            <span>📬 {{ form.submissions }} responses</span>
          </div>

          <div class="flex space-x-2">
            <router-link
              :to="`/forms/${form.id}/edit`"
              class="flex-1 btn btn-outline text-sm py-2"
            >
              Edit
            </router-link>
            <router-link
              :to="`/forms/${form.id}/responses`"
              class="flex-1 btn btn-secondary text-sm py-2"
            >
              Responses
            </router-link>
            <button
              @click="handleDelete(form)"
              class="btn btn-secondary text-sm py-2"
            >
              🗑️
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <Modal v-model="showDeleteModal" title="Delete Form">
      <p class="text-gray-600">
        Are you sure you want to delete "{{ formToDelete?.title }}"?
        This action cannot be undone.
      </p>
      <template #footer>
        <button @click="confirmDelete" class="btn btn-primary mr-2">
          Delete
        </button>
        <button @click="showDeleteModal = false" class="btn btn-secondary">
          Cancel
        </button>
      </template>
    </Modal>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useFormsStore } from '@/stores/forms'
import Navbar from '@/components/common/Navbar.vue'
import Modal from '@/components/common/Modal.vue'

const authStore = useAuthStore()
const formsStore = useFormsStore()

const forms = ref([])
const loading = ref(false)
const showDeleteModal = ref(false)
const formToDelete = ref(null)

const loadForms = async () => {
  loading.value = true
  try {
    forms.value = await formsStore.fetchForms()
  } catch (error) {
    console.error('Failed to load forms:', error)
  } finally {
    loading.value = false
  }
}

const handleDelete = (form) => {
  formToDelete.value = form
  showDeleteModal.value = true
}

const confirmDelete = async () => {
  try {
    await formsStore.deleteForm(formToDelete.value.id)
    forms.value = forms.value.filter(f => f.id !== formToDelete.value.id)
    showDeleteModal.value = false
  } catch (error) {
    console.error('Failed to delete form:', error)
  }
}

onMounted(() => {
  loadForms()
})
</script>
