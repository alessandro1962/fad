<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex min-h-screen items-center justify-center p-4">
      <!-- Backdrop -->
      <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="closeModal"></div>
      
      <!-- Modal -->
      <div class="relative w-full max-w-6xl bg-white rounded-2xl shadow-2xl">
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-cdf-slate200">
          <div>
            <h2 class="text-2xl font-bold text-cdf-deep">Gestione Moduli</h2>
            <p class="text-cdf-slate600 mt-1">{{ course?.title }}</p>
          </div>
          <button
            @click="closeModal"
            class="p-2 text-cdf-slate400 hover:text-cdf-deep hover:bg-cdf-slate100 rounded-lg transition-colors"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <!-- Content -->
        <div class="p-6">
          <!-- Add Module Button -->
          <div class="mb-6">
            <button
              @click="showAddModuleForm = true"
              class="flex items-center gap-2 px-4 py-2 bg-cdf-teal text-white rounded-lg hover:bg-cdf-deep transition-colors"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
              </svg>
              Aggiungi Modulo
            </button>
          </div>

          <!-- Add Module Form -->
          <div v-if="showAddModuleForm" class="mb-6 p-4 bg-cdf-sand rounded-lg border border-cdf-slate200">
            <h3 class="text-lg font-semibold text-cdf-deep mb-4">Nuovo Modulo</h3>
            <form @submit.prevent="addModule" class="space-y-4">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-cdf-deep mb-2">Titolo Modulo</label>
                  <input
                    v-model="newModule.title"
                    type="text"
                    required
                    class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
                    placeholder="Es. Introduzione alla Sicurezza"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-cdf-deep mb-2">Durata (minuti)</label>
                  <input
                    v-model="newModule.duration_minutes"
                    type="number"
                    min="1"
                    class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
                    placeholder="60"
                  />
                </div>
              </div>
              <div>
                <label class="block text-sm font-medium text-cdf-deep mb-2">Descrizione</label>
                <textarea
                  v-model="newModule.description"
                  rows="3"
                  class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
                  placeholder="Descrizione del modulo..."
                ></textarea>
              </div>
              <div class="flex gap-3">
                <button
                  type="submit"
                  :disabled="loading"
                  class="px-4 py-2 bg-cdf-teal text-white rounded-lg hover:bg-cdf-deep disabled:opacity-50 transition-colors"
                >
                  {{ loading ? 'Salvataggio...' : 'Salva Modulo' }}
                </button>
                <button
                  type="button"
                  @click="showAddModuleForm = false"
                  class="px-4 py-2 border border-cdf-slate200 text-cdf-slate600 rounded-lg hover:bg-cdf-slate100 transition-colors"
                >
                  Annulla
                </button>
              </div>
            </form>
          </div>

          <!-- Modules List -->
          <div class="space-y-4">
            <div
              v-for="module in modules"
              :key="module.id"
              class="border border-cdf-slate200 rounded-lg p-4"
            >
              <!-- Module Header -->
              <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                  <div class="p-2 bg-cdf-teal/10 rounded-lg">
                    <svg class="w-5 h-5 text-cdf-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                  </div>
                  <div>
                    <h3 class="font-semibold text-cdf-deep">{{ module.title }}</h3>
                    <p class="text-sm text-cdf-slate600">{{ module.duration_minutes }} minuti</p>
                  </div>
                </div>
                <div class="flex items-center gap-2">
                  <button
                    @click="addLesson(module)"
                    class="p-2 text-cdf-teal hover:text-cdf-deep hover:bg-cdf-teal/10 rounded-lg transition-colors"
                    title="Aggiungi Lezione"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                  </button>
                  <button
                    @click="editModule(module)"
                    class="p-2 text-cdf-slate400 hover:text-cdf-deep hover:bg-cdf-slate100 rounded-lg transition-colors"
                    title="Modifica Modulo"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                  </button>
                  <button
                    @click="deleteModule(module)"
                    class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors"
                    title="Elimina Modulo"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                  </button>
                </div>
              </div>

              <!-- Module Description -->
              <p v-if="module.description" class="text-sm text-cdf-slate600 mb-4">{{ module.description }}</p>

              <!-- Lessons List -->
              <div class="space-y-2">
                <div
                  v-for="lesson in module.lessons"
                  :key="lesson.id"
                  class="flex items-center justify-between p-3 bg-cdf-sand rounded-lg"
                >
                  <div class="flex items-center gap-3">
                    <div class="p-1.5 bg-white rounded-lg">
                      <component :is="getLessonIcon(lesson.type)" class="w-4 h-4 text-cdf-teal" />
                    </div>
                    <div>
                      <h4 class="font-medium text-cdf-deep">{{ lesson.title }}</h4>
                      <p class="text-xs text-cdf-slate600">{{ getLessonTypeLabel(lesson.type) }} • {{ lesson.duration_minutes }} min</p>
                    </div>
                  </div>
                  <div class="flex items-center gap-2">
                    <button
                      @click="editLesson(lesson)"
                      class="p-1.5 text-cdf-slate400 hover:text-cdf-deep hover:bg-white rounded transition-colors"
                      title="Modifica Lezione"
                    >
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                      </svg>
                    </button>
                    <button
                      @click="deleteLesson(lesson)"
                      class="p-1.5 text-red-500 hover:text-red-700 hover:bg-white rounded transition-colors"
                      title="Elimina Lezione"
                    >
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Lesson Modal -->
  <LessonModal
    v-if="showLessonModal"
    :is-open="showLessonModal"
    :lesson="selectedLesson"
    :module="selectedModule"
    @close="closeLessonModal"
    @saved="onLessonSaved"
  />
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '@/api'
import LessonModal from './LessonModal.vue'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  },
  course: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['close'])

// State
const modules = ref([])
const loading = ref(false)
const showAddModuleForm = ref(false)
const showLessonModal = ref(false)
const selectedLesson = ref(null)
const selectedModule = ref(null)

const newModule = ref({
  title: '',
  description: '',
  duration_minutes: 60,
  order: 1
})

// Computed
const isOpen = computed(() => props.isOpen)

// Methods
const closeModal = () => {
  emit('close')
  showAddModuleForm.value = false
  showLessonModal.value = false
  selectedLesson.value = null
  selectedModule.value = null
}

const loadModules = async () => {
  if (!props.course?.id) return
  
  try {
    loading.value = true
    const response = await api.get(`/v1/admin/courses/${props.course.id}/modules`)
    modules.value = response.data.data || []
  } catch (error) {
    console.error('Errore nel caricamento dei moduli:', error)
  } finally {
    loading.value = false
  }
}

const addModule = async () => {
  try {
    loading.value = true
    const response = await api.post(`/v1/admin/courses/${props.course.id}/modules`, {
      ...newModule.value,
      course_id: props.course.id
    })
    
    modules.value.push(response.data.data)
    showAddModuleForm.value = false
    resetNewModule()
  } catch (error) {
    console.error('Errore nella creazione del modulo:', error)
  } finally {
    loading.value = false
  }
}

const editModule = (module) => {
  // TODO: Implement edit module
  console.log('Edit module:', module)
}

const deleteModule = async (module) => {
  if (!confirm(`Sei sicuro di voler eliminare il modulo "${module.title}"?`)) return
  
  try {
    await api.delete(`/v1/admin/modules/${module.id}`)
    modules.value = modules.value.filter(m => m.id !== module.id)
  } catch (error) {
    console.error('Errore nell\'eliminazione del modulo:', error)
  }
}

const addLesson = (module) => {
  selectedModule.value = module
  selectedLesson.value = null
  showLessonModal.value = true
}

const editLesson = (lesson) => {
  selectedLesson.value = lesson
  selectedModule.value = lesson.module
  showLessonModal.value = true
}

const deleteLesson = async (lesson) => {
  if (!confirm(`Sei sicuro di voler eliminare la lezione "${lesson.title}"?`)) return
  
  try {
    await api.delete(`/v1/admin/lessons/${lesson.id}`)
    // Reload modules to update lessons
    await loadModules()
  } catch (error) {
    console.error('Errore nell\'eliminazione della lezione:', error)
  }
}

const closeLessonModal = () => {
  showLessonModal.value = false
  selectedLesson.value = null
  selectedModule.value = null
}

const onLessonSaved = () => {
  loadModules() // Reload to show new/updated lesson
}

const resetNewModule = () => {
  newModule.value = {
    title: '',
    description: '',
    duration_minutes: 60,
    order: 1
  }
}

const getLessonIcon = (type) => {
  const icons = {
    video: 'VideoIcon',
    pdf: 'DocumentIcon',
    quiz: 'QuestionMarkCircleIcon',
    slide: 'PresentationChartBarIcon',
    link: 'LinkIcon'
  }
  return icons[type] || 'DocumentIcon'
}

const getLessonTypeLabel = (type) => {
  const labels = {
    video: 'Video',
    pdf: 'PDF',
    quiz: 'Quiz',
    slide: 'Slide',
    link: 'Link'
  }
  return labels[type] || 'Contenuto'
}

// Lifecycle
onMounted(() => {
  if (props.isOpen && props.course) {
    loadModules()
  }
})

// Watch for course changes
import { watch } from 'vue'
watch(() => props.course, (newCourse) => {
  if (newCourse && props.isOpen) {
    loadModules()
  }
})

watch(() => props.isOpen, (isOpen) => {
  if (isOpen && props.course) {
    loadModules()
  }
})
</script>
