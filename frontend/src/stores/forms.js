import { defineStore } from 'pinia'
import { ref } from 'vue'
import { formsAPI } from '@/services/api'

export const useFormsStore = defineStore('forms', () => {
  const forms = ref([])
  const currentForm = ref(null)
  const loading = ref(false)
  const error = ref(null)

  const fetchForms = async (filters = {}) => {
    loading.value = true
    error.value = null

    try {
      const response = await formsAPI.list(filters)
      forms.value = response.data.forms
      return response.data.forms
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch forms'
      throw err
    } finally {
      loading.value = false
    }
  }

  const createForm = async (data) => {
    loading.value = true
    error.value = null

    try {
      const response = await formsAPI.create(data)
      forms.value.unshift(response.data.form)
      return response.data.form
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to create form'
      throw err
    } finally {
      loading.value = false
    }
  }

  const getForm = async (id) => {
    loading.value = true
    error.value = null

    try {
      const response = await formsAPI.get(id)
      currentForm.value = response.data.form
      return response.data.form
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch form'
      throw err
    } finally {
      loading.value = false
    }
  }

  const updateForm = async (id, data) => {
    loading.value = true
    error.value = null

    try {
      const response = await formsAPI.update(id, data)
      const index = forms.value.findIndex((f) => f.id === id)
      if (index !== -1) {
        forms.value[index] = response.data.form
      }
      currentForm.value = response.data.form
      return response.data.form
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to update form'
      throw err
    } finally {
      loading.value = false
    }
  }

  const deleteForm = async (id) => {
    loading.value = true
    error.value = null

    try {
      await formsAPI.delete(id)
      forms.value = forms.value.filter((f) => f.id !== id)
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to delete form'
      throw err
    } finally {
      loading.value = false
    }
  }

  const duplicateForm = async (form) => {
    const newForm = {
      title: `${form.title} (Copy)`,
      description: form.description,
      fields: form.fields,
      settings: form.settings,
    }
    return await createForm(newForm)
  }

  return {
    forms,
    currentForm,
    loading,
    error,
    fetchForms,
    createForm,
    getForm,
    updateForm,
    deleteForm,
    duplicateForm,
  }
})
