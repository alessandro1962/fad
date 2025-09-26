<template>
  <div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-hidden">
      <!-- Header -->
      <div class="flex items-center justify-between p-6 border-b border-cdf-slate200">
        <div>
          <h3 class="text-lg font-semibold text-cdf-deep">
            Iscrizioni di {{ user?.name }}
          </h3>
          <p class="text-sm text-cdf-slate600 mt-1">
            {{ user?.email }}
          </p>
        </div>
        <button
          @click="closeModal"
          class="text-cdf-slate400 hover:text-cdf-slate600 transition-colors"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <!-- Content -->
      <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
        <!-- Loading State -->
        <div v-if="loading" class="flex justify-center py-8">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-cdf-teal"></div>
        </div>

        <!-- Enrollments List -->
        <div v-else-if="enrollments.length > 0" class="space-y-4">
          <div
            v-for="enrollment in enrollments"
            :key="enrollment.id"
            class="border border-cdf-slate200 rounded-lg p-4 hover:shadow-md transition-shadow"
          >
            <div class="flex items-start justify-between">
              <div class="flex-1">
                <h4 class="font-semibold text-cdf-deep mb-2">
                  {{ enrollment.course?.title }}
                </h4>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                  <div>
                    <span class="text-cdf-slate600">Livello:</span>
                    <span class="ml-1 font-medium">{{ enrollment.course?.level }}</span>
                  </div>
                  <div>
                    <span class="text-cdf-slate600">Durata:</span>
                    <span class="ml-1 font-medium">{{ enrollment.course?.duration_minutes }} min</span>
                  </div>
                  <div>
                    <span class="text-cdf-slate600">Iscritto il:</span>
                    <span class="ml-1 font-medium">{{ formatDate(enrollment.enrolled_at) }}</span>
                  </div>
                  <div>
                    <span class="text-cdf-slate600">Stato:</span>
                    <span 
                      class="ml-1 px-2 py-1 rounded-full text-xs font-medium"
                      :class="getStatusClass(enrollment.status)"
                    >
                      {{ getStatusText(enrollment.status) }}
                    </span>
                  </div>
                </div>
                
                <!-- Progress Bar -->
                <div class="mt-3">
                  <div class="flex justify-between text-sm text-cdf-slate600 mb-1">
                    <span>Progresso</span>
                    <span>{{ enrollment.progress_percentage }}%</span>
                  </div>
                  <div class="w-full bg-cdf-slate200 rounded-full h-2">
                    <div 
                      class="bg-cdf-teal h-2 rounded-full transition-all duration-300"
                      :style="{ width: `${enrollment.progress_percentage}%` }"
                    ></div>
                  </div>
                </div>

                <!-- Course Description -->
                <p v-if="enrollment.course?.summary" class="text-sm text-cdf-slate600 mt-2">
                  {{ enrollment.course.summary }}
                </p>
              </div>

              <!-- Actions -->
              <div class="flex gap-2 ml-4">
                <button
                  @click="viewCourse(enrollment.course)"
                  class="p-2 text-cdf-teal hover:text-cdf-deep hover:bg-cdf-teal/10 rounded-lg transition-all"
                  title="Visualizza Corso"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                  </svg>
                </button>
                <button
                  @click="updateProgress(enrollment)"
                  class="p-2 text-cdf-blue hover:text-cdf-deep hover:bg-cdf-blue/10 rounded-lg transition-all"
                  title="Aggiorna Progresso"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-8">
          <svg class="w-16 h-16 text-cdf-slate300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
          </svg>
          <h3 class="text-lg font-medium text-cdf-slate600 mb-2">
            Nessuna iscrizione trovata
          </h3>
          <p class="text-cdf-slate500">
            Questo utente non è iscritto a nessun corso.
          </p>
        </div>
      </div>

      <!-- Footer -->
      <div class="flex justify-end gap-3 p-6 border-t border-cdf-slate200">
        <button
          @click="closeModal"
          class="px-4 py-2 text-cdf-slate600 border border-cdf-slate200 rounded-lg hover:bg-cdf-slate50 transition-colors"
        >
          Chiudi
        </button>
        <button
          @click="openEnrollModal"
          class="px-4 py-2 bg-cdf-teal text-white rounded-lg hover:bg-cdf-deep transition-colors"
        >
          Iscrivi a Nuovo Corso
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import api from '@/api'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  },
  user: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['close', 'enroll-user'])

const loading = ref(false)
const enrollments = ref([])

// Load enrollments when modal opens
watch(() => props.isOpen, (isOpen) => {
  if (isOpen && props.user) {
    loadEnrollments()
  }
})

const loadEnrollments = async () => {
  if (!props.user) return
  
  loading.value = true
  try {
    const response = await api.get(`/v1/admin/users/${props.user.id}/enrollments`)
    enrollments.value = response.data.data || []
  } catch (error) {
    console.error('Errore nel caricamento iscrizioni:', error)
    enrollments.value = []
  } finally {
    loading.value = false
  }
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString('it-IT', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const getStatusClass = (status) => {
  const classes = {
    'enrolled': 'bg-cdf-blue/10 text-cdf-blue',
    'in_progress': 'bg-cdf-teal/10 text-cdf-teal',
    'completed': 'bg-cdf-green/10 text-cdf-green',
    'paused': 'bg-cdf-yellow/10 text-cdf-yellow'
  }
  return classes[status] || 'bg-cdf-slate/10 text-cdf-slate'
}

const getStatusText = (status) => {
  const texts = {
    'enrolled': 'Iscritto',
    'in_progress': 'In Corso',
    'completed': 'Completato',
    'paused': 'In Pausa'
  }
  return texts[status] || status
}

const viewCourse = (course) => {
  // Navigate to course details or open course modal
  console.log('View course:', course)
}

const updateProgress = (enrollment) => {
  // Open progress update modal
  console.log('Update progress:', enrollment)
}

const openEnrollModal = () => {
  emit('enroll-user')
  closeModal()
}

const closeModal = () => {
  enrollments.value = []
  emit('close')
}
</script>
