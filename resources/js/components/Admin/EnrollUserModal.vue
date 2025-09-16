<template>
  <div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
      <!-- Header -->
      <div class="flex items-center justify-between p-6 border-b border-cdf-slate200">
        <h3 class="text-lg font-semibold text-cdf-deep">
          Iscrivi Utente al Corso
        </h3>
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
      <div class="p-6">
        <form @submit.prevent="enrollUser" class="space-y-4">
          <!-- User Selection -->
          <div>
            <label class="block text-sm font-medium text-cdf-deep mb-2">
              Seleziona Utente
            </label>
            <select
              v-model="form.user_id"
              class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
              required
            >
              <option value="">Seleziona un utente...</option>
              <option
                v-for="user in users"
                :key="user.id"
                :value="user.id"
              >
                {{ user.name }} ({{ user.email }})
              </option>
            </select>
          </div>

          <!-- Course Selection -->
          <div>
            <label class="block text-sm font-medium text-cdf-deep mb-2">
              Seleziona Corso
            </label>
            <select
              v-model="form.course_id"
              class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
              required
            >
              <option value="">Seleziona un corso...</option>
              <option
                v-for="course in courses"
                :key="course.id"
                :value="course.id"
              >
                {{ course.title }} ({{ course.level }})
              </option>
            </select>
          </div>

          <!-- Enrollment Date -->
          <div>
            <label class="block text-sm font-medium text-cdf-deep mb-2">
              Data di Iscrizione
            </label>
            <input
              v-model="form.enrolled_at"
              type="datetime-local"
              class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
              required
            />
          </div>

          <!-- Status -->
          <div>
            <label class="block text-sm font-medium text-cdf-deep mb-2">
              Stato
            </label>
            <select
              v-model="form.status"
              class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
            >
              <option value="enrolled">Iscritto</option>
              <option value="in_progress">In Corso</option>
              <option value="completed">Completato</option>
              <option value="paused">In Pausa</option>
            </select>
          </div>

          <!-- Progress -->
          <div>
            <label class="block text-sm font-medium text-cdf-deep mb-2">
              Progresso (%)
            </label>
            <input
              v-model.number="form.progress_percentage"
              type="number"
              min="0"
              max="100"
              class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
            />
          </div>

          <!-- Actions -->
          <div class="flex gap-3 pt-4">
            <button
              type="button"
              @click="closeModal"
              class="flex-1 px-4 py-2 text-cdf-slate600 border border-cdf-slate200 rounded-lg hover:bg-cdf-slate50 transition-colors"
            >
              Annulla
            </button>
            <button
              type="submit"
              :disabled="loading"
              class="flex-1 px-4 py-2 bg-cdf-teal text-white rounded-lg hover:bg-cdf-deep transition-colors disabled:opacity-50"
            >
              <span v-if="loading">Iscrivendo...</span>
              <span v-else>Iscrivi Utente</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import api from '@/api'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['close', 'success'])

const loading = ref(false)
const users = ref([])
const courses = ref([])

const form = ref({
  user_id: '',
  course_id: '',
  enrolled_at: '',
  status: 'enrolled',
  progress_percentage: 0
})

// Load users and courses when modal opens
watch(() => props.isOpen, (isOpen) => {
  if (isOpen) {
    loadUsers()
    loadCourses()
    // Set default enrollment date to now
    form.value.enrolled_at = new Date().toISOString().slice(0, 16)
  }
})

const loadUsers = async () => {
  try {
    const response = await api.get('/v1/admin/users')
    users.value = response.data.data || []
  } catch (error) {
    console.error('Errore nel caricamento utenti:', error)
  }
}

const loadCourses = async () => {
  try {
    const response = await api.get('/v1/admin/courses')
    courses.value = response.data.data || []
  } catch (error) {
    console.error('Errore nel caricamento corsi:', error)
  }
}

const enrollUser = async () => {
  loading.value = true
  
  try {
    const payload = {
      user_id: form.value.user_id,
      course_id: form.value.course_id,
      enrolled_at: form.value.enrolled_at,
      status: form.value.status,
      progress_percentage: form.value.progress_percentage
    }

    await api.post('/v1/admin/enrollments', payload)
    
    emit('success')
    closeModal()
  } catch (error) {
    console.error('Errore nell\'iscrizione utente:', error)
    alert('Errore nell\'iscrizione dell\'utente. Riprova.')
  } finally {
    loading.value = false
  }
}

const closeModal = () => {
  // Reset form
  form.value = {
    user_id: '',
    course_id: '',
    enrolled_at: '',
    status: 'enrolled',
    progress_percentage: 0
  }
  emit('close')
}
</script>
