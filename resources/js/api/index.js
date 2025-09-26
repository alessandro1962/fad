import axios from 'axios'

// Create axios instance
const api = axios.create({
  baseURL: '/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
  }
})

// Add CSRF token to all requests
const token = document.head.querySelector('meta[name="csrf-token"]')
if (token) {
  api.defaults.headers.common['X-CSRF-TOKEN'] = token.content
}

// Request interceptor to add auth token
api.interceptors.request.use(
  (config) => {
    const authToken = localStorage.getItem('token')
    if (authToken) {
      config.headers.Authorization = `Bearer ${authToken}`
    }
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

// Response interceptor to handle errors
api.interceptors.response.use(
  (response) => {
    return response
  },
  (error) => {
    if (error.response?.status === 401) {
      // Token expired or invalid - but don't redirect automatically
      console.log('Token expired or invalid, but not redirecting automatically')
      // localStorage.removeItem('token')
      // localStorage.removeItem('user')
      // window.location.href = '/login'
    }
    return Promise.reject(error)
  }
)

export default api
