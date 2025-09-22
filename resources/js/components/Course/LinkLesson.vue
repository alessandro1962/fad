<template>
  <div class="link-lesson">
    <!-- Link Content -->
    <div class="bg-white rounded-xl border border-cdf-slate200 p-6 mb-6">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-cdf-deep">{{ lesson.title }}</h3>
        <span class="text-sm text-cdf-slate600">
          {{ formatDuration(lesson.duration_minutes) }}
        </span>
      </div>
      
      <div v-if="lesson.description" class="text-cdf-slate700 mb-4">
        {{ lesson.description }}
      </div>
      
      <!-- Link Preview -->
      <div class="bg-cdf-sand rounded-xl p-4 mb-4">
        <div class="flex items-center space-x-3">
          <div class="w-10 h-10 rounded-lg bg-cdf-teal/20 flex items-center justify-center">
            <svg class="w-5 h-5 text-cdf-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
            </svg>
          </div>
          <div class="flex-1">
            <h4 class="font-semibold text-cdf-deep">{{ linkTitle }}</h4>
            <p class="text-sm text-cdf-slate600">{{ linkUrl }}</p>
          </div>
        </div>
      </div>
      
      <!-- Link Actions -->
      <div class="flex flex-col sm:flex-row gap-3">
        <button
          @click="openLink"
          class="flex-1 px-6 py-3 bg-cdf-teal text-white rounded-xl font-semibold hover:bg-cdf-deep transition-colors"
        >
          <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
          </svg>
          Apri Link
        </button>
        <button
          @click="copyLink"
          class="px-6 py-3 border border-cdf-slate200 text-cdf-slate700 rounded-xl font-semibold hover:bg-cdf-slate50 transition-colors"
        >
          <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
          </svg>
          Copia Link
        </button>
      </div>
    </div>

    <!-- Link Info -->
    <div class="bg-cdf-sand rounded-xl p-4 mb-6">
      <div class="flex items-center justify-between text-sm text-cdf-slate700">
        <span>Durata stimata: {{ formatDuration(lesson.duration_minutes) }}</span>
        <span v-if="linkOpened">Link aperto: {{ linkOpenedAt }}</span>
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
            {{ isCompleted ? 'Lezione completata' : 'Apri il link e segna come completata dopo averlo consultato' }}
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
        Consulta il link per continuare
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
const linkOpened = ref(false)
const linkOpenedAt = ref('')

// Computed
const linkUrl = computed(() => {
  return props.lesson.payload?.link_url || props.lesson.payload?.url
})

const linkTitle = computed(() => {
  return props.lesson.payload?.link_title || props.lesson.payload?.title || 'Link Esterno'
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

const openLink = () => {
  if (linkUrl.value) {
    window.open(linkUrl.value, '_blank', 'noopener,noreferrer')
    linkOpened.value = true
    linkOpenedAt.value = new Date().toLocaleString('it-IT')
  } else {
    emit('error', new Error('URL del link non disponibile'))
  }
}

const copyLink = async () => {
  if (linkUrl.value) {
    try {
      await navigator.clipboard.writeText(linkUrl.value)
      // Show success message (you could use a toast notification here)
      alert('Link copiato negli appunti!')
    } catch (error) {
      console.error('Error copying link:', error)
      emit('error', error)
    }
  } else {
    emit('error', new Error('URL del link non disponibile'))
  }
}

const markAsCompleted = async () => {
  try {
    isCompleted.value = true
    
    // Save progress
    const progressData = {
      completed: true,
      completed_at: new Date().toISOString(),
      link_opened: linkOpened.value,
      link_opened_at: linkOpenedAt.value
    }

    await api.patch(`/v1/progress/lesson/${props.lesson.id}`, progressData)
    
    emit('progress-updated', {
      lesson_id: props.lesson.id,
      ...progressData
    })
    
    console.log('Link lesson marked as completed')
  } catch (error) {
    console.error('Error marking link lesson as completed:', error)
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
    if (props.userProgress.link_opened) {
      linkOpened.value = props.userProgress.link_opened
      linkOpenedAt.value = props.userProgress.link_opened_at || ''
    }
  }
})
</script>

<style scoped>
.link-lesson {
  @apply w-full;
}

/* Custom styling for link preview */
.link-preview {
  @apply bg-cdf-sand rounded-xl p-4 border border-cdf-slate200;
}

/* Hover effects for buttons */
button:not(:disabled):hover {
  @apply transform scale-105 transition-transform duration-200;
}

/* Link styling */
a {
  @apply text-cdf-teal hover:text-cdf-deep transition-colors;
}
</style>
