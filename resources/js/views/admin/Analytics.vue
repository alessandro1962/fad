<template>
  <div class="min-h-screen bg-cdf-sand">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-cdf-slate200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-6">
          <div class="flex items-center gap-4">
            <button
              @click="goToDashboard"
              class="flex items-center gap-2 px-3 py-2 text-cdf-slate600 hover:text-cdf-deep hover:bg-cdf-slate100 rounded-lg transition-colors"
              title="Torna alla Dashboard"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
              </svg>
              Dashboard
            </button>
            <div>
              <h1 class="text-2xl font-bold text-cdf-deep">Report Analytics</h1>
              <p class="text-cdf-slate700 mt-1">Monitora le performance e le metriche della piattaforma</p>
            </div>
          </div>
          <div class="flex gap-3">
            <button
              @click="exportData"
              :disabled="loading"
              class="bg-cdf-teal text-white px-4 py-2 rounded-xl font-semibold hover:bg-cdf-deep transition-all disabled:opacity-50"
            >
              📊 Esporta Dati
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <div class="bg-white rounded-xl shadow-sm border border-cdf-slate200 p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-medium text-cdf-deep mb-2">
              Periodo
            </label>
            <select
              v-model="filters.period"
              @change="loadAnalytics"
              class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
            >
              <option value="7">Ultimi 7 giorni</option>
              <option value="30">Ultimi 30 giorni</option>
              <option value="90">Ultimi 90 giorni</option>
              <option value="365">Ultimo anno</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-cdf-deep mb-2">
              Organizzazione
            </label>
            <select
              v-model="filters.organization_id"
              @change="loadAnalytics"
              class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
            >
              <option value="">Tutte le organizzazioni</option>
              <option
                v-for="org in organizations"
                :key="org.id"
                :value="org.id"
              >
                {{ org.name }}
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-cdf-deep mb-2">
              Tipo Report
            </label>
            <select
              v-model="filters.report_type"
              @change="loadAnalytics"
              class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
            >
              <option value="overview">Panoramica</option>
              <option value="users">Utenti</option>
              <option value="courses">Corsi</option>
              <option value="enrollments">Iscrizioni</option>
            </select>
          </div>
          <div class="flex items-end">
            <button
              @click="loadAnalytics"
              :disabled="loading"
              class="w-full px-4 py-2 bg-cdf-teal text-white rounded-lg hover:bg-cdf-deep transition-colors disabled:opacity-50"
            >
              Aggiorna
            </button>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="flex justify-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-cdf-teal"></div>
      </div>

      <!-- Analytics Content -->
      <div v-else class="space-y-6">
        <!-- KPI Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <div
            v-for="kpi in kpis"
            :key="kpi.title"
            class="bg-white rounded-xl shadow-sm border border-cdf-slate200 p-6"
          >
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-cdf-slate600">{{ kpi.title }}</p>
                <p class="text-2xl font-bold text-cdf-deep">{{ kpi.value }}</p>
                <p
                  v-if="kpi.change !== undefined"
                  class="text-sm flex items-center mt-1"
                  :class="kpi.change >= 0 ? 'text-cdf-green' : 'text-cdf-red'"
                >
                  <svg
                    v-if="kpi.change >= 0"
                    class="w-4 h-4 mr-1"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17l9.2-9.2M17 17V7H7"></path>
                  </svg>
                  <svg
                    v-else
                    class="w-4 h-4 mr-1"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 7l-9.2 9.2M7 7v10h10"></path>
                  </svg>
                  {{ Math.abs(kpi.change) }}%
                </p>
              </div>
              <div
                class="w-12 h-12 rounded-xl flex items-center justify-center"
                :class="kpi.bgClass"
              >
                <component :is="kpi.icon" class="w-6 h-6" :class="kpi.iconClass" />
              </div>
            </div>
          </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- User Growth Chart -->
          <div class="bg-white rounded-xl shadow-sm border border-cdf-slate200 p-6">
            <h3 class="text-lg font-semibold text-cdf-deep mb-4">Crescita Utenti</h3>
            <div class="h-64 flex items-center justify-center text-cdf-slate500">
              <div class="text-center">
                <svg class="w-16 h-16 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <p>Grafico in sviluppo</p>
              </div>
            </div>
          </div>

          <!-- Course Completion Chart -->
          <div class="bg-white rounded-xl shadow-sm border border-cdf-slate200 p-6">
            <h3 class="text-lg font-semibold text-cdf-deep mb-4">Completamento Corsi</h3>
            <div class="h-64 flex items-center justify-center text-cdf-slate500">
              <div class="text-center">
                <svg class="w-16 h-16 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                </svg>
                <p>Grafico in sviluppo</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Top Courses Table -->
        <div class="bg-white rounded-xl shadow-sm border border-cdf-slate200 p-6">
          <h3 class="text-lg font-semibold text-cdf-deep mb-4">Top Corsi per Iscrizioni</h3>
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="border-b border-cdf-slate200">
                  <th class="text-left py-3 px-4 font-medium text-cdf-slate600">Corso</th>
                  <th class="text-left py-3 px-4 font-medium text-cdf-slate600">Iscrizioni</th>
                  <th class="text-left py-3 px-4 font-medium text-cdf-slate600">Completamenti</th>
                  <th class="text-left py-3 px-4 font-medium text-cdf-slate600">Tasso Completamento</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="course in topCourses"
                  :key="course.id"
                  class="border-b border-cdf-slate100 hover:bg-cdf-slate50"
                >
                  <td class="py-3 px-4">
                    <div>
                      <p class="font-medium text-cdf-deep">{{ course.title }}</p>
                      <p class="text-sm text-cdf-slate600">{{ course.level }}</p>
                    </div>
                  </td>
                  <td class="py-3 px-4 text-cdf-slate700">{{ course.enrollments_count }}</td>
                  <td class="py-3 px-4 text-cdf-slate700">{{ course.completed_count }}</td>
                  <td class="py-3 px-4">
                    <div class="flex items-center">
                      <div class="w-16 bg-cdf-slate200 rounded-full h-2 mr-2">
                        <div
                          class="bg-cdf-teal h-2 rounded-full"
                          :style="{ width: `${course.completion_rate}%` }"
                        ></div>
                      </div>
                      <span class="text-sm text-cdf-slate700">{{ course.completion_rate }}%</span>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Recent Users Table -->
        <div class="bg-white rounded-xl shadow-sm border border-cdf-slate200 p-6">
          <h3 class="text-lg font-semibold text-cdf-deep mb-4">Utenti Recenti</h3>
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="border-b border-cdf-slate200">
                  <th class="text-left py-3 px-4 font-medium text-cdf-slate600">Utente</th>
                  <th class="text-left py-3 px-4 font-medium text-cdf-slate600">Email</th>
                  <th class="text-left py-3 px-4 font-medium text-cdf-slate600">Data Registrazione</th>
                  <th class="text-left py-3 px-4 font-medium text-cdf-slate600">Corsi</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="user in recentUsers"
                  :key="user.id"
                  class="border-b border-cdf-slate100 hover:bg-cdf-slate50"
                >
                  <td class="py-3 px-4">
                    <div class="flex items-center">
                      <div class="w-8 h-8 bg-cdf-teal rounded-full flex items-center justify-center text-white text-sm font-medium mr-3">
                        {{ user.name.charAt(0).toUpperCase() }}
                      </div>
                      <span class="font-medium text-cdf-deep">{{ user.name }}</span>
                    </div>
                  </td>
                  <td class="py-3 px-4 text-cdf-slate700">{{ user.email }}</td>
                  <td class="py-3 px-4 text-cdf-slate700">{{ formatDate(user.created_at) }}</td>
                  <td class="py-3 px-4 text-cdf-slate700">{{ user.enrollments_count }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/api'

const router = useRouter()

// State
const loading = ref(false)
const kpis = ref([])
const topCourses = ref([])
const recentUsers = ref([])
const organizations = ref([])

const filters = reactive({
  period: '30',
  organization_id: '',
  report_type: 'overview'
})

// Methods
const goToDashboard = () => {
  router.push('/admin')
}

const loadAnalytics = async () => {
  loading.value = true
  try {
    const params = new URLSearchParams({
      period: filters.period,
      report_type: filters.report_type
    })
    
    if (filters.organization_id) {
      params.append('organization_id', filters.organization_id)
    }

    const response = await api.get(`/v1/admin/analytics?${params}`)
    const data = response.data.data

    // Update KPIs
    kpis.value = [
      {
        title: 'Utenti Totali',
        value: data.total_users || 0,
        change: data.users_growth || 0,
        icon: 'UsersIcon',
        bgClass: 'bg-cdf-blue/10',
        iconClass: 'text-cdf-blue'
      },
      {
        title: 'Corsi Attivi',
        value: data.total_courses || 0,
        change: data.courses_growth || 0,
        icon: 'BookOpenIcon',
        bgClass: 'bg-cdf-teal/10',
        iconClass: 'text-cdf-teal'
      },
      {
        title: 'Iscrizioni',
        value: data.total_enrollments || 0,
        change: data.enrollments_growth || 0,
        icon: 'UserPlusIcon',
        bgClass: 'bg-cdf-green/10',
        iconClass: 'text-cdf-green'
      },
      {
        title: 'Completamenti',
        value: data.completed_enrollments || 0,
        change: data.completions_growth || 0,
        icon: 'CheckCircleIcon',
        bgClass: 'bg-cdf-amber/10',
        iconClass: 'text-cdf-amber'
      }
    ]

    topCourses.value = data.top_courses || []
    recentUsers.value = data.recent_users || []
  } catch (error) {
    console.error('Errore nel caricamento analytics:', error)
  } finally {
    loading.value = false
  }
}

const exportData = async () => {
  try {
    const params = new URLSearchParams({
      type: filters.report_type,
      period: filters.period
    })
    
    if (filters.organization_id) {
      params.append('organization_id', filters.organization_id)
    }

    const response = await api.get(`/v1/admin/analytics/export?${params}`, {
      responseType: 'blob'
    })

    // Create download link
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `analytics_${filters.report_type}_${new Date().toISOString().split('T')[0]}.xlsx`)
    document.body.appendChild(link)
    link.click()
    link.remove()
    window.URL.revokeObjectURL(url)
  } catch (error) {
    console.error('Errore nell\'export:', error)
    alert('Errore nell\'esportazione dei dati')
  }
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString('it-IT', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

// Lifecycle
onMounted(() => {
  loadAnalytics()
})
</script>
