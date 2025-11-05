import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

// Lazy load views
const Home = () => import('@/views/Home.vue')
const Login = () => import('@/views/Login.vue')
const Register = () => import('@/views/Register.vue')
const Dashboard = () => import('@/views/Dashboard.vue')
const FormBuilder = () => import('@/views/FormBuilder.vue')
const FormView = () => import('@/views/FormView.vue')
const Responses = () => import('@/views/Responses.vue')
const Settings = () => import('@/views/Settings.vue')
const Pricing = () => import('@/views/Pricing.vue')

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home,
    meta: { requiresAuth: false },
  },
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: { requiresAuth: false, guestOnly: true },
  },
  {
    path: '/register',
    name: 'Register',
    component: Register,
    meta: { requiresAuth: false, guestOnly: true },
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: Dashboard,
    meta: { requiresAuth: true },
  },
  {
    path: '/forms/new',
    name: 'CreateForm',
    component: FormBuilder,
    meta: { requiresAuth: true },
  },
  {
    path: '/forms/:id/edit',
    name: 'EditForm',
    component: FormBuilder,
    meta: { requiresAuth: true },
  },
  {
    path: '/forms/:id/responses',
    name: 'Responses',
    component: Responses,
    meta: { requiresAuth: true },
  },
  {
    path: '/f/:slug',
    name: 'FormView',
    component: FormView,
    meta: { requiresAuth: false },
  },
  {
    path: '/settings',
    name: 'Settings',
    component: Settings,
    meta: { requiresAuth: true },
  },
  {
    path: '/pricing',
    name: 'Pricing',
    component: Pricing,
    meta: { requiresAuth: false },
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

// Navigation guard
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  const requiresAuth = to.meta.requiresAuth
  const guestOnly = to.meta.guestOnly

  if (requiresAuth && !authStore.isAuthenticated) {
    next('/login')
  } else if (guestOnly && authStore.isAuthenticated) {
    next('/dashboard')
  } else {
    next()
  }
})

export default router
