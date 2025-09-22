<template>
  <div class="pdf-lesson">
    <!-- PDF Viewer -->
    <div class="bg-cdf-slate200 rounded-xl p-8 text-center mb-6">
      <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-cdf-slate300 flex items-center justify-center">
        <svg class="w-8 h-8 text-cdf-slate700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
      </div>
      <h3 class="text-lg font-semibold text-cdf-deep mb-2">{{ lesson.title }}</h3>
      <p class="text-cdf-slate700 mb-4">{{ lesson.description || 'Visualizza il documento PDF per completare questa lezione.' }}</p>
      
      <!-- PDF Actions -->
      <div class="flex flex-col sm:flex-row gap-3 justify-center">
        <button
          @click="openPdf"
          class="px-6 py-3 bg-cdf-teal text-white rounded-xl font-semibold hover:bg-cdf-deep transition-colors"
        >
          <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
          </svg>
          Visualizza PDF
        </button>
        <button
          @click="downloadPdf"
          class="px-6 py-3 border border-cdf-slate200 text-cdf-slate700 rounded-xl font-semibold hover:bg-cdf-slate50 transition-colors"
        >
          <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
          </svg>
          Scarica PDF
        </button>
      </div>
    </div>

    <!-- PDF Info -->
    <div class="bg-cdf-sand rounded-xl p-4 mb-6">
      <div class="flex items-center justify-between text-sm text-cdf-slate700">
        <span>Durata stimata: {{ formatDuration(lesson.duration_minutes) }}</span>
        <span v-if="lesson.payload?.file_size">Dimensione: {{ formatFileSize(lesson.payload.file_size) }}</span>
      </div>
    </div>

    <!-- Completion Check -->
    <div class="bg-white rounded-xl border border-cdf-slate200 p-6">
      <div class="flex items-center justify-between">
        <div class="flex items-center">
          <div class="w-6 h-6 rounded-full border-2 mr-3 flex items-center justify-center"
               :class="isCompleted ? 'border-green-500 bg-green-500' : 'border-cdf-slate300'">
            <svg v-if="isCompleted" class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
            </svg>
          </div>
          <span class="text-cdf-slate700">
            {{ isCompleted ? 'Lezione completata' : 'Segna come completata dopo aver letto il documento' }}
          </span>
        </div>
        <button
          @click="markAsCompleted"
          :disabled="isCompleted"
          class="px-4 py-2 rounded-lg font-semibold transition-colors"
          :class="isCompleted 
            ? 'bg-green-100 text-green-700 cursor-not-allowed' 
            : 'bg-cdf-amber text-cdf-ink hover:brightness-95'"
        >
          {{ isCompleted ? 'Completata' : 'Segna Completata' }}
        </button>
      </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex gap-3 mt-6">
      <button
        v-if="!isCompleted"
        disabled
        class="flex-1 bg-gray-300 text-gray-500 px-4 py-2 rounded-lg font-semibold cursor-not-allowed"
      >
        Leggi il documento per continuare
      </button>
      <button
        v-else
        @click="proceedToNext"
        class="flex-1 bg-cdf-amber text-cdf-ink px-4 py-2 rounded-lg font-semibold hover:brightness-95 transition-colors"
      >
        {{ isLastLesson ? 'Completa Corso' : 'Vai al Modulo Successivo' }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '@/api'

const props = defineProps({
  lesson: {
    type: Object,
    required: true
  },
  userProgress: {
    type: Object,
    default: () => ({})
  },
  isLastLesson: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['lesson-completed', 'progress-updated', 'error'])

// State
const isCompleted = ref(false)

// Computed
const pdfUrl = computed(() => {
  return props.lesson.payload?.pdf_url || props.lesson.payload?.file_url
})

const pdfTitle = computed(() => {
  return props.lesson.payload?.file_name || props.lesson.title
})

// Methods
const formatDuration = (minutes) => {
  if (!minutes) return '0 min'
  const hours = Math.floor(minutes / 60)
  const mins = minutes % 60
  if (hours > 0) {
    return mins > 0 ? `${hours}h ${mins}min` : `${hours}h`
  }
  return `${mins}min`
}

const formatFileSize = (bytes) => {
  if (!bytes) return '0 B'
  const k = 1024
  const sizes = ['B', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const openPdf = () => {
  if (pdfUrl.value) {
    window.open(pdfUrl.value, '_blank')
  } else {
    emit('error', new Error('URL del PDF non disponibile'))
  }
}

const downloadPdf = () => {
  if (pdfUrl.value) {
    const link = document.createElement('a')
    link.href = pdfUrl.value
    link.download = pdfTitle.value
    link.target = '_blank'
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
  } else {
    emit('error', new Error('URL del PDF non disponibile'))
  }
}

const markAsCompleted = async () => {
  try {
    isCompleted.value = true
    
    // Save progress
    const progressData = {
      completed: true,
      completed_at: new Date().toISOString()
    }

    await api.patch(`/v1/progress/lesson/${props.lesson.id}`, progressData)
    
    emit('progress-updated', {
      lesson_id: props.lesson.id,
      ...progressData
    })
    
    console.log('PDF lesson marked as completed')
  } catch (error) {
    console.error('Error marking PDF lesson as completed:', error)
    emit('error', error)
  }
}

const proceedToNext = () => {
  emit('lesson-completed', {
    lesson: props.lesson,
    completed: true,
    completed_at: new Date().toISOString()
  })
}

// Lifecycle
onMounted(() => {
  // Load existing progress
  if (props.userProgress) {
    isCompleted.value = props.userProgress.completed || false
  }
})
</script>

<style scoped>
.pdf-lesson {
  @apply w-full;
}

/* Custom styling for PDF viewer */
.pdf-viewer {
  @apply w-full h-96 border border-cdf-slate200 rounded-xl;
}

/* Hover effects for buttons */
button:not(:disabled):hover {
  @apply transform scale-105 transition-transform duration-200;
}
</style>
