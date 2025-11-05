import axios from 'axios'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000/api',
  headers: {
    'Content-Type': 'application/json',
  },
})

// Request interceptor to add auth token
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

// Response interceptor to handle errors
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      // Unauthorized - clear token and redirect to login
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      window.location.href = '/login'
    }
    return Promise.reject(error)
  }
)

// Auth API
export const authAPI = {
  register: (data) => api.post('/auth/register', data),
  login: (data) => api.post('/auth/login', data),
  me: () => api.get('/auth/me'),
  refresh: () => api.post('/auth/refresh'),
}

// Forms API
export const formsAPI = {
  list: (params) => api.get('/forms', { params }),
  create: (data) => api.post('/forms', data),
  get: (id) => api.get(`/forms/${id}`),
  update: (id, data) => api.put(`/forms/${id}`, data),
  delete: (id) => api.delete(`/forms/${id}`),
  getPublic: (slug) => api.get(`/forms/${slug}/public`),
  submit: (slug, data) => api.post(`/forms/${slug}/submit`, data),
}

// Responses API
export const responsesAPI = {
  list: (formId, params) => api.get(`/forms/${formId}/responses`, { params }),
  get: (id) => api.get(`/responses/${id}`),
  delete: (id) => api.delete(`/responses/${id}`),
  getAnalytics: (formId) => api.get(`/forms/${formId}/analytics`),
}

// Subscription API
export const subscriptionAPI = {
  getPlans: () => api.get('/subscription/plans'),
  getStatus: () => api.get('/subscription/status'),
  create: (data) => api.post('/subscription/create', data),
  cancel: () => api.post('/subscription/cancel'),
  update: (data) => api.post('/subscription/update', data),
}

export default api
