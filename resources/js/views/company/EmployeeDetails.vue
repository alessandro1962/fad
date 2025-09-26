<template>
  <AppLayout>
    <div class="space-y-8">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <button
            @click="goBack"
            class="p-2 text-cdf-slate600 hover:text-cdf-deep hover:bg-cdf-slate100 rounded-lg transition-colors"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
          </button>
          <div>
            <h1 class="text-3xl font-bold text-cdf-deep">{{ employee?.name || 'Caricamento...' }}</h1>
            <p class="text-cdf-slate600 mt-2">Dettagli dipendente e progressi nei corsi</p>
          </div>
        </div>
      </div>

      <div v-if="loading" class="flex items-center justify-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-cdf-teal"></div>
      </div>

      <div v-else-if="employee" class="space-y-8">
        <!-- Employee Info Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6">
          <div class="flex items-start space-x-6">
            <div class="w-20 h-20 bg-cdf-teal/10 rounded-full flex items-center justify-center">
              <svg class="w-10 h-10 text-cdf-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
              </svg>
            </div>
            <div class="flex-1">
              <h2 class="text-2xl font-bold text-cdf-deep">{{ employee.name }}</h2>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                  <p class="text-sm text-cdf-slate600">Email</p>
                  <p class="font-medium text-cdf-deep">{{ employee.email }}</p>
                </div>
                <div>
                  <p class="text-sm text-cdf-slate600">Azienda</p>
                  <p class="font-medium text-cdf-deep">{{ employee.company }}</p>
                </div>
                <div>
                  <p class="text-sm text-cdf-slate600">Professione</p>
                  <p class="font-medium text-cdf-deep">{{ employee.profession || 'N/A' }}</p>
                </div>
                <div>
                  <p class="text-sm text-cdf-slate600">Ultimo accesso</p>
                  <p class="font-medium text-cdf-deep">
                    {{ employee.last_login_at ? formatDate(employee.last_login_at) : 'Mai' }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-cdf-slate600">Corsi Totali</p>
                <p class="text-2xl font-bold text-cdf-deep">{{ stats.total_courses }}</p>
              </div>
              <div class="w-12 h-12 bg-cdf-teal/10 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-cdf-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-cdf-slate600">Corsi Completati</p>
                <p class="text-2xl font-bold text-green-600">{{ stats.completed_courses }}</p>
              </div>
              <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-cdf-slate600">In Corso</p>
                <p class="text-2xl font-bold text-cdf-amber">{{ stats.in_progress_courses }}</p>
              </div>
              <div class="w-12 h-12 bg-cdf-amber/10 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-cdf-amber" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-cdf-slate600">Progresso Medio</p>
                <p class="text-2xl font-bold text-cdf-teal">{{ stats.average_progress.toFixed(1) }}%</p>
              </div>
              <div class="w-12 h-12 bg-cdf-teal/10 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-cdf-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Course Progress -->
        <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6">
          <h3 class="text-xl font-bold text-cdf-deep mb-6">Progressi nei Corsi</h3>
          
          <div v-if="enrollments.length === 0" class="text-center py-12 text-cdf-slate500">
            <svg class="w-16 h-16 mx-auto mb-4 text-cdf-slate300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
            <p class="text-lg">Nessun corso assegnato</p>
            <p class="text-sm">Questo dipendente non ha ancora corsi assegnati.</p>
          </div>

          <div v-else class="space-y-6">
            <div
              v-for="enrollment in enrollments"
              :key="enrollment.id"
              class="border border-cdf-slate200 rounded-xl p-6"
            >
              <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                  <h4 class="text-lg font-semibold text-cdf-deep">{{ enrollment.course.title }}</h4>
                  <p class="text-cdf-slate600 mt-1">{{ enrollment.course.description }}</p>
                </div>
                <div class="text-right">
                  <span
                    :class="{
                      'bg-green-100 text-green-800': enrollment.status === 'completed',
                      'bg-cdf-amber/10 text-cdf-amber': enrollment.status === 'in_progress',
                      'bg-cdf-slate100 text-cdf-slate600': enrollment.status === 'not_started'
                    }"
                    class="px-3 py-1 rounded-full text-sm font-medium"
                  >
                    {{ getStatusLabel(enrollment.status) }}
                  </span>
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                  <p class="text-sm text-cdf-slate600">Iscritto il</p>
                  <p class="font-medium">{{ formatDate(enrollment.enrolled_at) }}</p>
                </div>
                <div v-if="enrollment.completed_at">
                  <p class="text-sm text-cdf-slate600">Completato il</p>
                  <p class="font-medium">{{ formatDate(enrollment.completed_at) }}</p>
                </div>
                <div>
                  <p class="text-sm text-cdf-slate600">Ultima attività</p>
                  <p class="font-medium">{{ formatDate(enrollment.last_activity) }}</p>
                </div>
              </div>

              <!-- Progress Bar -->
              <div class="space-y-2">
                <div class="flex justify-between text-sm">
                  <span class="text-cdf-slate600">Progresso</span>
                  <span class="font-medium text-cdf-deep">{{ enrollment.progress_percentage.toFixed(1) }}%</span>
                </div>
                <div class="w-full bg-cdf-slate200 rounded-full h-2">
                  <div
                    class="bg-gradient-to-r from-cdf-teal to-cdf-deep h-2 rounded-full transition-all duration-500"
                    :style="{ width: `${enrollment.progress_percentage}%` }"
                  ></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import AppLayout from '@/components/Layout/AppLayout.vue'
import api from '@/api'

const route = useRoute()
const router = useRouter()

const loading = ref(false)
const employee = ref(null)
const enrollments = ref([])
const stats = ref({
  total_courses: 0,
  completed_courses: 0,
  in_progress_courses: 0,
  average_progress: 0,
  total_time_spent: 0
})

const loadEmployeeDetails = async () => {
  try {
    loading.value = true
    const employeeId = route.params.employeeId
    const response = await api.get(`/v1/company/employees/${employeeId}`)
    
    employee.value = response.data.data.employee
    enrollments.value = response.data.data.enrollments
    stats.value = response.data.data.stats
  } catch (error) {
    console.error('Error loading employee details:', error)
    if (error.response?.status === 404) {
      router.push('/company/dashboard')
    }
  } finally {
    loading.value = false
  }
}

const goBack = () => {
  router.push('/company/dashboard')
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString('it-IT', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const getStatusLabel = (status) => {
  const labels = {
    'completed': 'Completato',
    'in_progress': 'In Corso',
    'not_started': 'Non Iniziato'
  }
  return labels[status] || status
}

onMounted(() => {
  loadEmployeeDetails()
})
</script>
