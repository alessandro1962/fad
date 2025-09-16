<template>
  <div class="analytics-dashboard">
    <!-- Header -->
    <div class="bg-gradient-to-r from-cdf-deep to-cdf-teal rounded-3xl p-8 mb-8 text-white">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold mb-2">Analytics & Reportistica</h1>
          <p class="text-lg opacity-90">Monitora le performance e i progressi della piattaforma</p>
        </div>
        <div class="text-right">
          <div class="text-sm opacity-80">Ultimo aggiornamento</div>
          <div class="text-lg font-semibold">{{ formatDate(new Date()) }}</div>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6 mb-8">
      <div class="flex flex-wrap gap-4 items-center">
        <div>
          <label class="block text-sm font-medium text-cdf-deep mb-2">Periodo</label>
          <select
            v-model="selectedPeriod"
            @change="loadAnalytics"
            class="px-4 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
          >
            <option value="7">Ultimi 7 giorni</option>
            <option value="30">Ultimi 30 giorni</option>
            <option value="90">Ultimi 90 giorni</option>
            <option value="365">Ultimo anno</option>
          </select>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-cdf-deep mb-2">Organizzazione</label>
          <select
            v-model="selectedOrganization"
            @change="loadAnalytics"
            class="px-4 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
          >
            <option value="">Tutte le organizzazioni</option>
            <option v-for="org in organizations" :key="org.id" :value="org.id">
              {{ org.name }}
            </option>
          </select>
        </div>
        
        <div class="ml-auto">
          <button
            @click="exportData"
            class="bg-cdf-amber text-cdf-ink px-6 py-2 rounded-xl font-semibold hover:brightness-95 transition-colors flex items-center space-x-2"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span>Esporta Dati</span>
          </button>
        </div>
      </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-cdf-slate700">Utenti Attivi</p>
            <p class="text-2xl font-bold text-cdf-deep">{{ analytics.activeUsers }}</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
            </svg>
          </div>
        </div>
        <div class="mt-4 flex items-center">
          <span class="text-sm" :class="analytics.activeUsersChange >= 0 ? 'text-green-600' : 'text-red-600'">
            {{ analytics.activeUsersChange >= 0 ? '+' : '' }}{{ analytics.activeUsersChange }}%
          </span>
          <span class="text-sm text-cdf-slate700 ml-2">vs periodo precedente</span>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-cdf-slate700">Corsi Completati</p>
            <p class="text-2xl font-bold text-cdf-deep">{{ analytics.completedCourses }}</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
        </div>
        <div class="mt-4 flex items-center">
          <span class="text-sm" :class="analytics.completedCoursesChange >= 0 ? 'text-green-600' : 'text-red-600'">
            {{ analytics.completedCoursesChange >= 0 ? '+' : '' }}{{ analytics.completedCoursesChange }}%
          </span>
          <span class="text-sm text-cdf-slate700 ml-2">vs periodo precedente</span>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-cdf-slate700">Tasso Completamento</p>
            <p class="text-2xl font-bold text-cdf-deep">{{ analytics.completionRate }}%</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center">
            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
          </div>
        </div>
        <div class="mt-4 flex items-center">
          <span class="text-sm" :class="analytics.completionRateChange >= 0 ? 'text-green-600' : 'text-red-600'">
            {{ analytics.completionRateChange >= 0 ? '+' : '' }}{{ analytics.completionRateChange }}%
          </span>
          <span class="text-sm text-cdf-slate700 ml-2">vs periodo precedente</span>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-cdf-slate700">Ore di Apprendimento</p>
            <p class="text-2xl font-bold text-cdf-deep">{{ analytics.learningHours }}</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center">
            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
        </div>
        <div class="mt-4 flex items-center">
          <span class="text-sm" :class="analytics.learningHoursChange >= 0 ? 'text-green-600' : 'text-red-600'">
            {{ analytics.learningHoursChange >= 0 ? '+' : '' }}{{ analytics.learningHoursChange }}%
          </span>
          <span class="text-sm text-cdf-slate700 ml-2">vs periodo precedente</span>
        </div>
      </div>
    </div>

    <!-- Charts Section -->
    <div class="grid lg:grid-cols-2 gap-8 mb-8">
      <!-- User Activity Chart -->
      <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6">
        <h3 class="text-lg font-bold text-cdf-deep mb-6">Attività Utenti</h3>
        <div class="h-64 flex items-center justify-center bg-cdf-sand rounded-xl">
          <div class="text-center">
            <svg class="w-16 h-16 text-cdf-slate700 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            <p class="text-cdf-slate700">Grafico Attività Utenti</p>
            <p class="text-sm text-cdf-slate600">Integrazione Chart.js</p>
          </div>
        </div>
      </div>

      <!-- Course Completion Chart -->
      <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6">
        <h3 class="text-lg font-bold text-cdf-deep mb-6">Completamento Corsi</h3>
        <div class="h-64 flex items-center justify-center bg-cdf-sand rounded-xl">
          <div class="text-center">
            <svg class="w-16 h-16 text-cdf-slate700 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
            </svg>
            <p class="text-cdf-slate700">Grafico Completamento</p>
            <p class="text-sm text-cdf-slate600">Integrazione Chart.js</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Detailed Tables -->
    <div class="grid lg:grid-cols-2 gap-8">
      <!-- Top Courses -->
      <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6">
        <h3 class="text-lg font-bold text-cdf-deep mb-6">Corsi Più Popolari</h3>
        <div class="space-y-4">
          <div
            v-for="(course, index) in topCourses"
            :key="course.id"
            class="flex items-center justify-between p-4 border border-cdf-slate200 rounded-xl hover:border-cdf-teal/50 transition-colors"
          >
            <div class="flex items-center space-x-4">
              <div class="w-8 h-8 rounded-full bg-cdf-teal text-white flex items-center justify-center text-sm font-bold">
                {{ index + 1 }}
              </div>
              <div>
                <h4 class="font-semibold text-cdf-deep">{{ course.title }}</h4>
                <p class="text-sm text-cdf-slate700">{{ course.enrollments }} iscrizioni</p>
              </div>
            </div>
            <div class="text-right">
              <div class="text-sm font-semibold text-cdf-deep">{{ course.completion_rate }}%</div>
              <div class="text-xs text-cdf-slate700">completamento</div>
            </div>
          </div>
        </div>
      </div>

      <!-- User Engagement -->
      <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6">
        <h3 class="text-lg font-bold text-cdf-deep mb-6">Engagement Utenti</h3>
        <div class="space-y-4">
          <div class="flex items-center justify-between p-4 border border-cdf-slate200 rounded-xl">
            <div class="flex items-center space-x-4">
              <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                </svg>
              </div>
              <div>
                <h4 class="font-semibold text-cdf-deep">Nuovi Utenti</h4>
                <p class="text-sm text-cdf-slate700">Questo mese</p>
              </div>
            </div>
            <div class="text-right">
              <div class="text-lg font-bold text-cdf-deep">{{ analytics.newUsers }}</div>
            </div>
          </div>

          <div class="flex items-center justify-between p-4 border border-cdf-slate200 rounded-xl">
            <div class="flex items-center space-x-4">
              <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div>
                <h4 class="font-semibold text-cdf-deep">Utenti Attivi</h4>
                <p class="text-sm text-cdf-slate700">Ultimi 30 giorni</p>
              </div>
            </div>
            <div class="text-right">
              <div class="text-lg font-bold text-cdf-deep">{{ analytics.activeUsers }}</div>
            </div>
          </div>

          <div class="flex items-center justify-between p-4 border border-cdf-slate200 rounded-xl">
            <div class="flex items-center space-x-4">
              <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div>
                <h4 class="font-semibold text-cdf-deep">Tempo Medio</h4>
                <p class="text-sm text-cdf-slate700">Per sessione</p>
              </div>
            </div>
            <div class="text-right">
              <div class="text-lg font-bold text-cdf-deep">{{ analytics.avgSessionTime }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/api'

// State
const selectedPeriod = ref('30')
const selectedOrganization = ref('')
const organizations = ref([])
const analytics = ref({
  activeUsers: 0,
  activeUsersChange: 0,
  completedCourses: 0,
  completedCoursesChange: 0,
  completionRate: 0,
  completionRateChange: 0,
  learningHours: 0,
  learningHoursChange: 0,
  newUsers: 0,
  avgSessionTime: '0m'
})
const topCourses = ref([])

// Methods
const formatDate = (date) => {
  return date.toLocaleDateString('it-IT', { 
    day: 'numeric', 
    month: 'long',
    year: 'numeric'
  })
}

const loadAnalytics = async () => {
  try {
    const response = await api.get('/v1/admin/analytics', {
      params: {
        period: selectedPeriod.value,
        organization_id: selectedOrganization.value
      }
    })
    
    analytics.value = response.data.data.analytics
    topCourses.value = response.data.data.top_courses
  } catch (error) {
    console.error('Errore nel caricamento analytics:', error)
  }
}

const loadOrganizations = async () => {
  try {
    const response = await api.get('/v1/admin/organizations')
    organizations.value = response.data.data
  } catch (error) {
    console.error('Errore nel caricamento organizzazioni:', error)
  }
}

const exportData = async () => {
  try {
    const response = await api.get('/v1/admin/analytics/export', {
      params: {
        period: selectedPeriod.value,
        organization_id: selectedOrganization.value
      },
      responseType: 'blob'
    })
    
    // Create blob and download
    const blob = new Blob([response.data], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' })
    const url = window.URL.createObjectURL(blob)
    const link = document.createElement('a')
    link.href = url
    link.download = `analytics_${selectedPeriod.value}days.xlsx`
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
    window.URL.revokeObjectURL(url)
  } catch (error) {
    console.error('Errore nell\'export dati:', error)
  }
}

// Lifecycle
onMounted(() => {
  loadOrganizations()
  loadAnalytics()
})
</script>

<style scoped>
.analytics-dashboard {
  @apply max-w-7xl mx-auto px-6 py-8;
}

/* KPI card hover effects */
.kpi-card {
  @apply transition-all duration-300;
}

.kpi-card:hover {
  @apply transform -translate-y-1 shadow-lg;
}

/* Chart placeholder styling */
.chart-placeholder {
  @apply transition-all duration-300;
}

.chart-placeholder:hover {
  @apply bg-cdf-slate200;
}
</style>
