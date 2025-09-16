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
              <h1 class="text-2xl font-bold text-cdf-deep">Gestione Corsi</h1>
              <p class="text-cdf-slate700 mt-1">Crea e gestisci i corsi della piattaforma</p>
            </div>
          </div>
          <button
            @click="showCreateModal = true"
            class="bg-cdf-amber text-cdf-ink px-4 py-2 rounded-xl font-semibold hover:brightness-95 transition-all"
          >
            + Nuovo Corso
          </button>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <div class="bg-white rounded-xl shadow-sm border border-cdf-slate200 p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <!-- Search -->
          <div>
            <label class="block text-sm font-medium text-cdf-deep mb-2">Cerca</label>
            <input
              v-model="filters.search"
              type="text"
              placeholder="Titolo, descrizione..."
              class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
            />
          </div>

          <!-- Level Filter -->
          <div>
            <label class="block text-sm font-medium text-cdf-deep mb-2">Livello</label>
            <select
              v-model="filters.level"
              class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
            >
              <option value="">Tutti i livelli</option>
              <option value="beginner">Principiante</option>
              <option value="intermediate">Intermedio</option>
              <option value="expert">Esperto</option>
            </select>
          </div>

          <!-- Status Filter -->
          <div>
            <label class="block text-sm font-medium text-cdf-deep mb-2">Stato</label>
            <select
              v-model="filters.status"
              class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
            >
              <option value="">Tutti gli stati</option>
              <option value="active">Attivo</option>
              <option value="inactive">Inattivo</option>
              <option value="published">Pubblicato</option>
              <option value="draft">Bozza</option>
            </select>
          </div>

          <!-- Sort -->
          <div>
            <label class="block text-sm font-medium text-cdf-deep mb-2">Ordina per</label>
            <select
              v-model="filters.sort_by"
              class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
            >
              <option value="created_at">Data creazione</option>
              <option value="title">Titolo</option>
              <option value="enrollments_count">Iscrizioni</option>
              <option value="price_cents">Prezzo</option>
            </select>
          </div>
        </div>

        <div class="flex justify-between items-center mt-4">
          <button
            @click="applyFilters"
            class="bg-cdf-deep text-white px-4 py-2 rounded-lg hover:brightness-110 transition-all"
          >
            Applica Filtri
          </button>
          <button
            @click="resetFilters"
            class="text-cdf-slate700 px-4 py-2 rounded-lg hover:bg-cdf-slate200 transition-all"
          >
            Reset
          </button>
        </div>
      </div>

      <!-- Courses List -->
      <div class="bg-white rounded-xl shadow-sm border border-cdf-slate200">
        <div v-if="loading" class="p-8 text-center">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-cdf-teal mx-auto"></div>
          <p class="text-cdf-slate700 mt-2">Caricamento corsi...</p>
        </div>

        <div v-else-if="courses.length === 0" class="p-8 text-center">
          <div class="text-cdf-slate400 mb-4">
            <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
          </div>
          <h3 class="text-lg font-medium text-cdf-deep mb-2">Nessun corso trovato</h3>
          <p class="text-cdf-slate700">Crea il tuo primo corso per iniziare</p>
        </div>

        <div v-else class="divide-y divide-cdf-slate200">
          <div
            v-for="course in courses"
            :key="course.id"
            class="p-6 hover:bg-cdf-sand/50 transition-colors"
          >
            <div class="flex items-start justify-between">
              <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                  <h3 class="text-lg font-semibold text-cdf-deep">{{ course.title }}</h3>
                  <span
                    :class="getStatusBadgeClass(course)"
                    class="px-2 py-1 text-xs font-medium rounded-full"
                  >
                    {{ getStatusText(course) }}
                  </span>
                  <span
                    :class="getLevelBadgeClass(course.level)"
                    class="px-2 py-1 text-xs font-medium rounded-full"
                  >
                    {{ getLevelText(course.level) }}
                  </span>
                </div>
                
                <p class="text-cdf-slate700 mb-3">{{ course.summary }}</p>
                
                <div class="flex items-center gap-6 text-sm text-cdf-slate600">
                  <div class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ formatDuration(course.duration_minutes) }}
                  </div>
                  <div class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    {{ course.enrollments_count }} iscrizioni
                  </div>
                  <div class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    {{ course.modules_count }} moduli
                  </div>
                  <div class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                    €{{ (course.price_cents / 100).toFixed(2) }}
                  </div>
                </div>
              </div>

              <div class="flex items-center gap-2 ml-4">
                <button
                  @click="editCourse(course)"
                  class="p-2 text-cdf-slate600 hover:text-cdf-deep hover:bg-cdf-slate200 rounded-lg transition-all"
                  title="Modifica"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                  </svg>
                </button>
                
                <button
                  @click="toggleCourseStatus(course)"
                  :class="course.is_active ? 'text-green-600 hover:text-green-700' : 'text-cdf-slate400 hover:text-cdf-slate600'"
                  class="p-2 hover:bg-cdf-slate200 rounded-lg transition-all"
                  :title="course.is_active ? 'Disattiva' : 'Attiva'"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                </button>

                <button
                  @click="toggleCoursePublish(course)"
                  :class="course.published_at ? 'text-cdf-teal hover:text-cdf-deep' : 'text-cdf-slate400 hover:text-cdf-slate600'"
                  class="p-2 hover:bg-cdf-slate200 rounded-lg transition-all"
                  :title="course.published_at ? 'Rimuovi dalla pubblicazione' : 'Pubblica'"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                  </svg>
                </button>

                <button
                  @click="viewCourseStats(course)"
                  class="p-2 text-cdf-slate600 hover:text-cdf-deep hover:bg-cdf-slate200 rounded-lg transition-all"
                  title="Statistiche"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                  </svg>
                </button>

                <button
                  @click="manageCourseModules(course)"
                  class="p-2 text-cdf-teal hover:text-cdf-deep hover:bg-cdf-teal/10 rounded-lg transition-all"
                  title="Gestisci Moduli"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                  </svg>
                </button>

                <button
                  @click="deleteCourse(course)"
                  class="p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-all"
                  title="Elimina"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="pagination.last_page > 1" class="px-6 py-4 border-t border-cdf-slate200">
          <div class="flex items-center justify-between">
            <div class="text-sm text-cdf-slate700">
              Mostrando {{ pagination.per_page * (pagination.current_page - 1) + 1 }} - 
              {{ Math.min(pagination.per_page * pagination.current_page, pagination.total) }} 
              di {{ pagination.total }} corsi
            </div>
            <div class="flex items-center gap-2">
              <button
                @click="loadPage(pagination.current_page - 1)"
                :disabled="!pagination.prev"
                class="px-3 py-2 text-sm border border-cdf-slate200 rounded-lg hover:bg-cdf-slate200 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Precedente
              </button>
              <span class="px-3 py-2 text-sm text-cdf-slate700">
                Pagina {{ pagination.current_page }} di {{ pagination.last_page }}
              </span>
              <button
                @click="loadPage(pagination.current_page + 1)"
                :disabled="!pagination.next"
                class="px-3 py-2 text-sm border border-cdf-slate200 rounded-lg hover:bg-cdf-slate200 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Successiva
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <CourseModal
      v-if="showCreateModal || showEditModal"
      :course="editingCourse"
      :is-edit="showEditModal"
      @close="closeModal"
      @saved="onCourseSaved"
    />

    <!-- Statistics Modal -->
    <CourseStatsModal
      v-if="showStatsModal"
      :course="selectedCourse"
      @close="showStatsModal = false"
    />

    <!-- Module Management Modal -->
    <ModuleManagementModal
      v-if="showModuleModal"
      :is-open="showModuleModal"
      :course="selectedCourse"
      @close="showModuleModal = false"
      @modules-updated="onModulesUpdated"
    />
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/api'
import CourseModal from '../../components/Admin/CourseModal.vue'
import CourseStatsModal from '../../components/Admin/CourseStatsModal.vue'
import ModuleManagementModal from '../../components/Admin/ModuleManagementModal.vue'

const router = useRouter()

// State
const loading = ref(false)
const courses = ref([])
const pagination = ref({})
const showCreateModal = ref(false)
const showEditModal = ref(false)
const showStatsModal = ref(false)
const showModuleModal = ref(false)
const editingCourse = ref(null)
const selectedCourse = ref(null)

// Filters
const filters = reactive({
  search: '',
  level: '',
  status: '',
  sort_by: 'created_at',
  sort_order: 'desc'
})

// Methods
const loadCourses = async (page = 1) => {
  loading.value = true
  try {
    const params = new URLSearchParams({
      page: page.toString(),
      ...Object.fromEntries(
        Object.entries(filters).filter(([_, value]) => value !== '')
      )
    })

    const response = await api.get(`/v1/admin/courses?${params}`)
    courses.value = response.data.data
    pagination.value = response.data.meta
  } catch (error) {
    console.error('Errore:', error)
    // Handle error
  } finally {
    loading.value = false
  }
}

const applyFilters = () => {
  loadCourses(1)
}

const resetFilters = () => {
  Object.keys(filters).forEach(key => {
    filters[key] = key === 'sort_by' ? 'created_at' : ''
  })
  loadCourses(1)
}

const loadPage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    loadCourses(page)
  }
}

const editCourse = (course) => {
  editingCourse.value = course
  showEditModal.value = true
}

const toggleCourseStatus = async (course) => {
  try {
    const response = await api.patch(`/v1/admin/courses/${course.id}/toggle-status`)
    course.is_active = !course.is_active
    // Show success message
  } catch (error) {
    console.error('Errore:', error)
    // Handle error
  }
}

const toggleCoursePublish = async (course) => {
  try {
    const response = await api.patch(`/v1/admin/courses/${course.id}/toggle-publish`)
    course.published_at = course.published_at ? null : new Date().toISOString()
    // Show success message
  } catch (error) {
    console.error('Errore:', error)
    // Handle error
  }
}

const viewCourseStats = (course) => {
  selectedCourse.value = course
  showStatsModal.value = true
}

const deleteCourse = async (course) => {
  if (!confirm(`Sei sicuro di voler eliminare il corso "${course.title}"?`)) {
    return
  }

  try {
    await api.delete(`/v1/admin/courses/${course.id}`)
    // Remove from list
    courses.value = courses.value.filter(c => c.id !== course.id)
    // Show success message
  } catch (error) {
    console.error('Errore:', error)
    alert(error.message)
  }
}

const closeModal = () => {
  showCreateModal.value = false
  showEditModal.value = false
  editingCourse.value = null
}

const onCourseSaved = () => {
  closeModal()
  loadCourses(pagination.value.current_page)
}

const manageCourseModules = (course) => {
  selectedCourse.value = course
  showModuleModal.value = true
}

const onModulesUpdated = () => {
  // Ricarica la lista dei corsi per aggiornare il conteggio moduli
  loadCourses(pagination.value.current_page)
}

const goToDashboard = () => {
  router.push('/admin')
}

// Helper methods
const getStatusText = (course) => {
  if (!course.is_active) return 'Inattivo'
  if (!course.published_at) return 'Bozza'
  return 'Pubblicato'
}

const getStatusBadgeClass = (course) => {
  if (!course.is_active) return 'bg-red-100 text-red-800'
  if (!course.published_at) return 'bg-yellow-100 text-yellow-800'
  return 'bg-green-100 text-green-800'
}

const getLevelText = (level) => {
  const levels = {
    beginner: 'Principiante',
    intermediate: 'Intermedio',
    expert: 'Esperto'
  }
  return levels[level] || level
}

const getLevelBadgeClass = (level) => {
  const classes = {
    beginner: 'bg-blue-100 text-blue-800',
    intermediate: 'bg-cdf-teal/20 text-cdf-deep',
    expert: 'bg-cdf-amber/20 text-cdf-ink'
  }
  return classes[level] || 'bg-cdf-slate200 text-cdf-slate700'
}

const formatDuration = (minutes) => {
  const hours = Math.floor(minutes / 60)
  const mins = minutes % 60
  if (hours > 0) {
    return mins > 0 ? `${hours}h ${mins}m` : `${hours}h`
  }
  return `${mins}m`
}

// Lifecycle
onMounted(() => {
  loadCourses()
})
</script>
