<template>
  <AppLayout>
    <div class="space-y-8">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-cdf-deep">Dashboard Aziendale</h1>
          <p class="text-cdf-slate600 mt-2">Monitora i progressi dei tuoi dipendenti nei corsi assegnati</p>
        </div>
        <div class="flex items-center space-x-4">
          <div class="text-right">
            <p class="text-sm text-cdf-slate600">Organizzazione</p>
            <p class="font-semibold text-cdf-deep">{{ user?.organization?.name || 'N/A' }}</p>
          </div>
        </div>
      </div>

      <!-- Statistics Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
        <StatCard
          title="Dipendenti Totali"
          :value="statistics.total_employees?.value || '0'"
          icon="users"
          :change="statistics.total_employees?.change || 0"
          :change-label="statistics.total_employees?.change_label || 'dipendenti totali'"
          :show-trend="false"
        />
        <StatCard
          title="Nuovi Dipendenti"
          :value="statistics.new_employees?.value || '0'"
          icon="user-plus"
          :change="statistics.new_employees?.change || 0"
          :change-label="statistics.new_employees?.change_label || 'nuovi dipendenti'"
          :show-trend="false"
        />
        <StatCard
          title="Corsi Attivi"
          :value="statistics.active_courses?.value || '0'"
          icon="book-open"
          :change="statistics.active_courses?.change || 0"
          :change-label="statistics.active_courses?.change_label || 'corsi attivi'"
          :show-trend="false"
        />
        <StatCard
          title="Corsi Completati"
          :value="statistics.total_completions?.value || '0'"
          icon="check-circle"
          :change="statistics.total_completions?.change || 0"
          :change-label="statistics.total_completions?.change_label || 'corsi completati'"
          :show-trend="false"
        />
        <StatCard
          title="Progresso Medio"
          :value="statistics.average_progress?.value || '0%'"
          icon="trending-up"
          :change="statistics.average_progress?.change || 0"
          :change-label="statistics.average_progress?.change_label || 'progresso medio'"
          :show-trend="false"
        />
      </div>

      <!-- Main Content Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Employees List -->
        <div class="lg:col-span-2">
          <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6">
            <div class="flex items-center justify-between mb-6">
              <h2 class="text-xl font-bold text-cdf-deep">Dipendenti</h2>
              <div class="flex items-center space-x-4">
                <!-- Search -->
                <div class="relative">
                  <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Cerca dipendenti..."
                    class="pl-10 pr-4 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
                    @input="searchEmployees"
                  />
                  <svg class="absolute left-3 top-2.5 h-5 w-5 text-cdf-slate400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                  </svg>
                </div>
                <!-- Refresh -->
                <button @click="loadEmployees" class="text-cdf-teal hover:text-cdf-deep transition-colors">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                  </svg>
                </button>
              </div>
            </div>

            <div v-if="loading" class="flex items-center justify-center py-8">
              <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-cdf-teal"></div>
            </div>

            <div v-else class="space-y-4">
              <div
                v-for="employee in employees"
                :key="employee.id"
                class="flex items-center justify-between p-4 border border-cdf-slate200 rounded-xl hover:bg-cdf-slate50 transition-colors cursor-pointer"
                @click="viewEmployeeDetails(employee.id)"
              >
                <div class="flex items-center space-x-4">
                  <div class="w-12 h-12 bg-cdf-teal/10 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-cdf-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                  </div>
                  <div>
                    <h3 class="font-semibold text-cdf-deep">{{ employee.name }}</h3>
                    <p class="text-sm text-cdf-slate600">{{ employee.email }}</p>
                    <p class="text-sm text-cdf-slate500">{{ employee.company }}</p>
                  </div>
                </div>
                <div class="text-right">
                  <div class="flex items-center space-x-4">
                    <div class="text-sm">
                      <p class="text-cdf-slate600">Corsi: {{ employee.stats?.total_courses || 0 }}</p>
                      <p class="text-cdf-slate600">Completati: {{ employee.stats?.completed_courses || 0 }}</p>
                    </div>
                    <div class="text-right">
                      <p class="text-sm font-semibold text-cdf-deep">{{ employee.stats?.completion_rate || 0 }}%</p>
                      <p class="text-xs text-cdf-slate500">Completamento</p>
                    </div>
                    <svg class="w-5 h-5 text-cdf-slate400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                  </div>
                </div>
              </div>

              <div v-if="employees.length === 0" class="text-center py-8 text-cdf-slate500">
                <svg class="w-12 h-12 mx-auto mb-4 text-cdf-slate300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <p>Nessun dipendente trovato</p>
              </div>
            </div>

            <!-- Pagination -->
            <div v-if="pagination.last_page > 1" class="mt-6 flex items-center justify-between">
              <div class="text-sm text-cdf-slate600">
                Mostrando {{ employees.length }} di {{ pagination.total }} dipendenti
              </div>
              <div class="flex items-center space-x-2">
                <button
                  @click="loadEmployees(pagination.current_page - 1)"
                  :disabled="pagination.current_page <= 1"
                  class="px-3 py-1 text-sm border border-cdf-slate200 rounded-lg hover:bg-cdf-slate50 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  Precedente
                </button>
                <span class="px-3 py-1 text-sm text-cdf-slate600">
                  {{ pagination.current_page }} di {{ pagination.last_page }}
                </span>
                <button
                  @click="loadEmployees(pagination.current_page + 1)"
                  :disabled="pagination.current_page >= pagination.last_page"
                  class="px-3 py-1 text-sm border border-cdf-slate200 rounded-lg hover:bg-cdf-slate50 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  Successivo
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Courses Overview -->
        <div class="lg:col-span-1">
          <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6">
            <div class="flex items-center justify-between mb-6">
              <h2 class="text-xl font-bold text-cdf-deep">Corsi Assegnati</h2>
              <button @click="loadCourses" class="text-cdf-teal hover:text-cdf-deep transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
              </button>
            </div>

            <div v-if="coursesLoading" class="flex items-center justify-center py-8">
              <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-cdf-teal"></div>
            </div>

            <div v-else class="space-y-4">
              <div
                v-for="course in courses"
                :key="course.id"
                class="p-4 border border-cdf-slate200 rounded-xl"
              >
                <h3 class="font-semibold text-cdf-deep mb-2">{{ course.title }}</h3>
                <div class="space-y-2">
                  <div class="flex justify-between text-sm">
                    <span class="text-cdf-slate600">Iscritti:</span>
                    <span class="font-medium">{{ course.stats.total_enrollments }}</span>
                  </div>
                  <div class="flex justify-between text-sm">
                    <span class="text-cdf-slate600">Completati:</span>
                    <span class="font-medium">{{ course.stats.completed_enrollments }}</span>
                  </div>
                  <div class="flex justify-between text-sm">
                    <span class="text-cdf-slate600">Tasso completamento:</span>
                    <span class="font-medium text-cdf-teal">{{ course.stats.completion_rate }}%</span>
                  </div>
                </div>
              </div>

              <div v-if="courses.length === 0" class="text-center py-8 text-cdf-slate500">
                <svg class="w-12 h-12 mx-auto mb-4 text-cdf-slate300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <p>Nessun corso assegnato</p>
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
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import AppLayout from '@/components/Layout/AppLayout.vue'
import StatCard from '@/components/Dashboard/StatCard.vue'
import api from '@/api'

const router = useRouter()
const authStore = useAuthStore()

const loading = ref(false)
const coursesLoading = ref(false)
const statistics = ref({})
const employees = ref([])
const courses = ref([])
const searchQuery = ref('')
const pagination = ref({
  current_page: 1,
  last_page: 1,
  per_page: 15,
  total: 0
})

const user = ref(authStore.user)

const loadStatistics = async () => {
  try {
    loading.value = true
    const response = await api.get('/v1/company/dashboard/statistics')
    statistics.value = response.data.data.statistics
  } catch (error) {
    console.error('Error loading company statistics:', error)
  } finally {
    loading.value = false
  }
}

const loadEmployees = async (page = 1) => {
  try {
    loading.value = true
    const params = {
      page,
      per_page: pagination.value.per_page
    }
    
    if (searchQuery.value) {
      params.search = searchQuery.value
    }
    
    const response = await api.get('/v1/company/employees', { params })
    employees.value = response.data.data
    pagination.value = response.data.pagination
  } catch (error) {
    console.error('Error loading employees:', error)
  } finally {
    loading.value = false
  }
}

const loadCourses = async () => {
  try {
    coursesLoading.value = true
    const response = await api.get('/v1/company/courses')
    courses.value = response.data.data
  } catch (error) {
    console.error('Error loading courses:', error)
  } finally {
    coursesLoading.value = false
  }
}

const searchEmployees = () => {
  // Debounce search
  clearTimeout(searchEmployees.timeout)
  searchEmployees.timeout = setTimeout(() => {
    loadEmployees(1)
  }, 500)
}

const viewEmployeeDetails = (employeeId) => {
  router.push(`/company/employees/${employeeId}`)
}

onMounted(() => {
  loadStatistics()
  loadEmployees()
  loadCourses()
})
</script>
