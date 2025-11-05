<template>
  <div class="min-h-screen bg-gray-50">
    <Navbar />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Header -->
      <div class="flex justify-between items-center mb-6">
        <div>
          <input
            v-model="form.title"
            type="text"
            class="text-2xl font-bold border-none focus:outline-none focus:ring-2 focus:ring-primary-500 rounded px-2"
            placeholder="Untitled Form"
          />
          <input
            v-model="form.description"
            type="text"
            class="block mt-1 text-gray-600 border-none focus:outline-none focus:ring-2 focus:ring-primary-500 rounded px-2"
            placeholder="Add description..."
          />
        </div>
        <div class="flex space-x-2">
          <button @click="saveForm" class="btn btn-secondary">
            {{ isEditing ? 'Save' : 'Save Draft' }}
          </button>
          <button @click="publishForm" class="btn btn-primary">
            Publish
          </button>
        </div>
      </div>

      <div class="grid grid-cols-12 gap-6">
        <!-- Field Library -->
        <div class="col-span-3 card">
          <h3 class="font-semibold mb-4">Add Fields</h3>
          <div class="space-y-2">
            <button
              v-for="fieldType in fieldTypes"
              :key="fieldType.type"
              @click="addField(fieldType)"
              class="w-full text-left px-4 py-3 border border-gray-300 rounded-lg hover:border-primary-500 hover:bg-primary-50 transition"
            >
              <div class="font-medium">{{ fieldType.icon }} {{ fieldType.label }}</div>
              <div class="text-xs text-gray-500">{{ fieldType.description }}</div>
            </button>
          </div>
        </div>

        <!-- Form Canvas -->
        <div class="col-span-6 card">
          <h3 class="font-semibold mb-4">Form Fields</h3>

          <div v-if="form.fields.length === 0" class="text-center py-12 text-gray-500">
            Add fields from the left panel to build your form
          </div>

          <div v-else class="space-y-4">
            <div
              v-for="(field, index) in form.fields"
              :key="field.id"
              class="form-field-preview"
              :class="{ 'form-field-active': selectedFieldIndex === index }"
              @click="selectField(index)"
            >
              <div class="flex justify-between items-start mb-2">
                <div class="flex-1">
                  <input
                    v-model="field.label"
                    type="text"
                    class="font-medium border-none focus:outline-none focus:ring-1 focus:ring-primary-500 rounded px-1"
                    placeholder="Field Label"
                    @click.stop
                  />
                  <div class="text-xs text-gray-500 mt-1">{{ field.type }}</div>
                </div>
                <div class="flex space-x-2">
                  <button @click.stop="moveField(index, -1)" :disabled="index === 0" class="text-gray-500 hover:text-gray-700">
                    ↑
                  </button>
                  <button @click.stop="moveField(index, 1)" :disabled="index === form.fields.length - 1" class="text-gray-500 hover:text-gray-700">
                    ↓
                  </button>
                  <button @click.stop="removeField(index)" class="text-red-500 hover:text-red-700">
                    ✕
                  </button>
                </div>
              </div>

              <input
                v-if="['text', 'email', 'number'].includes(field.type)"
                type="text"
                :placeholder="field.placeholder || 'Enter your answer...'"
                class="w-full input"
                disabled
              />
              <textarea
                v-else-if="field.type === 'textarea'"
                :placeholder="field.placeholder || 'Enter your answer...'"
                class="w-full input"
                disabled
              ></textarea>
              <select v-else-if="field.type === 'select'" class="w-full input" disabled>
                <option>Select an option...</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Field Settings -->
        <div class="col-span-3 card">
          <h3 class="font-semibold mb-4">Field Settings</h3>

          <div v-if="selectedField" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Label</label>
              <input v-model="selectedField.label" type="text" class="input" />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Placeholder</label>
              <input v-model="selectedField.placeholder" type="text" class="input" />
            </div>

            <div>
              <label class="flex items-center">
                <input v-model="selectedField.required" type="checkbox" class="mr-2" />
                <span class="text-sm font-medium text-gray-700">Required field</span>
              </label>
            </div>
          </div>

          <div v-else class="text-center text-gray-500 py-8">
            Select a field to edit its settings
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useFormsStore } from '@/stores/forms'
import Navbar from '@/components/common/Navbar.vue'

const route = useRoute()
const router = useRouter()
const formsStore = useFormsStore()

const form = ref({
  title: 'Untitled Form',
  description: '',
  fields: [],
  status: 'draft',
  settings: {
    theme: 'default',
    submitText: 'Submit',
    redirectUrl: '',
  },
})

const selectedFieldIndex = ref(null)
const isEditing = computed(() => !!route.params.id)

const selectedField = computed(() => {
  if (selectedFieldIndex.value !== null) {
    return form.value.fields[selectedFieldIndex.value]
  }
  return null
})

const fieldTypes = [
  { type: 'text', label: 'Short Text', icon: '📝', description: 'Single line text' },
  { type: 'textarea', label: 'Long Text', icon: '📄', description: 'Multi-line text' },
  { type: 'email', label: 'Email', icon: '📧', description: 'Email address' },
  { type: 'number', label: 'Number', icon: '🔢', description: 'Numeric input' },
  { type: 'select', label: 'Dropdown', icon: '📋', description: 'Select from options' },
  { type: 'radio', label: 'Multiple Choice', icon: '⭕', description: 'Radio buttons' },
  { type: 'checkbox', label: 'Checkboxes', icon: '☑️', description: 'Multiple selection' },
  { type: 'date', label: 'Date', icon: '📅', description: 'Date picker' },
]

const addField = (fieldType) => {
  const newField = {
    id: `field_${Date.now()}`,
    type: fieldType.type,
    label: fieldType.label,
    placeholder: '',
    required: false,
    order: form.value.fields.length,
    options: ['select', 'radio', 'checkbox'].includes(fieldType.type) ? ['Option 1', 'Option 2'] : [],
  }
  form.value.fields.push(newField)
}

const selectField = (index) => {
  selectedFieldIndex.value = index
}

const removeField = (index) => {
  form.value.fields.splice(index, 1)
  if (selectedFieldIndex.value === index) {
    selectedFieldIndex.value = null
  }
}

const moveField = (index, direction) => {
  const newIndex = index + direction
  if (newIndex >= 0 && newIndex < form.value.fields.length) {
    const temp = form.value.fields[index]
    form.value.fields[index] = form.value.fields[newIndex]
    form.value.fields[newIndex] = temp
  }
}

const saveForm = async () => {
  try {
    if (isEditing.value) {
      await formsStore.updateForm(route.params.id, form.value)
      alert('Form saved successfully!')
    } else {
      const created = await formsStore.createForm(form.value)
      router.push(`/forms/${created.id}/edit`)
      alert('Form created successfully!')
    }
  } catch (error) {
    alert('Failed to save form')
  }
}

const publishForm = async () => {
  form.value.status = 'published'
  await saveForm()
}

onMounted(async () => {
  if (isEditing.value) {
    const loadedForm = await formsStore.getForm(route.params.id)
    form.value = { ...loadedForm }
  }
})
</script>
