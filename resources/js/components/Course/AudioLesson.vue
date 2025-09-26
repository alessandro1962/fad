<template>
  <div class="audio-lesson">
    <!-- Audio Player -->
    <div class="bg-white rounded-xl border border-cdf-slate200 p-6 mb-6">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-cdf-deep">{{ lesson.title }}</h3>
        <span class="text-sm text-cdf-slate600">
          {{ formatDuration(lesson.duration_minutes) }}
        </span>
      </div>
      
      <!-- Audio Controls -->
      <div class="flex items-center space-x-4 mb-4">
        <button
          @click="togglePlayPause"
          class="w-12 h-12 rounded-full bg-cdf-teal text-white flex items-center justify-center hover:bg-cdf-deep transition-colors"
        >
          <svg v-if="!isPlaying" class="w-6 h-6 ml-1" fill="currentColor" viewBox="0 0 24 24">
            <path d="M8 5v14l11-7z"/>
          </svg>
          <svg v-else class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
            <path d="M6 4h4v16H6V4zm8 0h4v16h-4V4z"/>
          </svg>
        </button>
        
        <div class="flex-1">
          <!-- Progress Bar -->
          <div class="w-full bg-cdf-slate200 rounded-full h-2 mb-2">
            <div 
              class="bg-gradient-to-r from-cdf-teal to-cdf-deep h-2 rounded-full transition-all duration-300"
              :style="{ width: `${progressPercentage}%` }"
            ></div>
          </div>
          
          <!-- Time Display -->
          <div class="flex justify-between text-sm text-cdf-slate600">
            <span>{{ formatTime(currentTime) }}</span>
            <span>{{ formatTime(duration) }}</span>
          </div>
        </div>
        
        <button
          @click="toggleMute"
          class="w-10 h-10 rounded-full border border-cdf-slate200 flex items-center justify-center hover:bg-cdf-slate50 transition-colors"
        >
          <svg v-if="!isMuted" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path>
          </svg>
          <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2"></path>
          </svg>
        </button>
      </div>
      
      <!-- Audio Element -->
      <audio
        ref="audioElement"
        :src="audioUrl"
        @loadedmetadata="onAudioLoaded"
        @timeupdate="onTimeUpdate"
        @ended="onAudioEnded"
        @play="onAudioPlay"
        @pause="onAudioPause"
        preload="metadata"
      ></audio>
    </div>

    <!-- Audio Info -->
    <div class="bg-cdf-sand rounded-xl p-4 mb-6">
      <div class="flex items-center justify-between text-sm text-cdf-slate700">
        <span>Durata: {{ formatDuration(lesson.duration_minutes) }}</span>
        <span v-if="lesson.payload?.file_size">Dimensione: {{ formatFileSize(lesson.payload.file_size) }}</span>
      </div>
      <div v-if="lesson.description" class="mt-2 text-cdf-slate700">
        {{ lesson.description }}
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
            {{ isCompleted ? 'Lezione completata' : 'Ascolta l\'audio completo per completare la lezione' }}
          </span>
        </div>
        <button
          @click="markAsCompleted"
          :disabled="!canComplete"
          class="px-4 py-2 rounded-lg font-semibold transition-colors"
          :class="canComplete 
            ? 'bg-cdf-amber text-cdf-ink hover:brightness-95' 
            : 'bg-gray-300 text-gray-500 cursor-not-allowed'"
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
        Ascolta l'audio completo per continuare
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
import { ref, computed, onMounted, onUnmounted } from 'vue'
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

// Refs
const audioElement = ref(null)

// State
const isPlaying = ref(false)
const isMuted = ref(false)
const currentTime = ref(0)
const duration = ref(0)
const isCompleted = ref(false)
const hasPlayedEnough = ref(false)

// Computed
const audioUrl = computed(() => {
  return props.lesson.payload?.audio_url || props.lesson.payload?.file_url
})

const progressPercentage = computed(() => {
  if (duration.value === 0) return 0
  return Math.min(100, (currentTime.value / duration.value) * 100)
})

const canComplete = computed(() => {
  return hasPlayedEnough.value || isCompleted.value
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

const formatTime = (seconds) => {
  if (!seconds || seconds < 0) return '0:00'
  
  const minutes = Math.floor(seconds / 60)
  const secs = Math.floor(seconds % 60)
  
  return `${minutes}:${secs.toString().padStart(2, '0')}`
}

const formatFileSize = (bytes) => {
  if (!bytes) return '0 B'
  const k = 1024
  const sizes = ['B', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const togglePlayPause = () => {
  if (!audioElement.value) return
  
  if (isPlaying.value) {
    audioElement.value.pause()
  } else {
    audioElement.value.play()
  }
}

const toggleMute = () => {
  if (!audioElement.value) return
  
  isMuted.value = !isMuted.value
  audioElement.value.muted = isMuted.value
}

const onAudioLoaded = () => {
  if (audioElement.value) {
    duration.value = audioElement.value.duration
  }
}

const onTimeUpdate = () => {
  if (audioElement.value) {
    currentTime.value = audioElement.value.currentTime
    
    // Check if user has played enough (90% of the audio)
    const completionThreshold = 0.9
    if (currentTime.value >= duration.value * completionThreshold && !hasPlayedEnough.value) {
      hasPlayedEnough.value = true
    }
  }
}

const onAudioEnded = () => {
  isPlaying.value = false
  hasPlayedEnough.value = true
  currentTime.value = duration.value
}

const onAudioPlay = () => {
  isPlaying.value = true
}

const onAudioPause = () => {
  isPlaying.value = false
}

const markAsCompleted = async () => {
  try {
    isCompleted.value = true
    
    // Save progress
    const progressData = {
      completed: true,
      completed_at: new Date().toISOString(),
      time_listened: currentTime.value
    }

    await api.patch(`/v1/progress/lesson/${props.lesson.id}`, progressData)
    
    emit('progress-updated', {
      lesson_id: props.lesson.id,
      ...progressData
    })
    
    console.log('Audio lesson marked as completed')
  } catch (error) {
    console.error('Error marking audio lesson as completed:', error)
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
    if (props.userProgress.time_listened) {
      currentTime.value = props.userProgress.time_listened
    }
  }
})

onUnmounted(() => {
  // Pause audio when component is unmounted
  if (audioElement.value) {
    audioElement.value.pause()
  }
})
</script>

<style scoped>
.audio-lesson {
  @apply w-full;
}

/* Custom styling for audio controls */
.audio-controls {
  @apply flex items-center space-x-4;
}

/* Progress bar styling */
.progress-bar {
  @apply w-full h-2 bg-cdf-slate200 rounded-full overflow-hidden;
}

.progress-fill {
  @apply h-full bg-gradient-to-r from-cdf-teal to-cdf-deep transition-all duration-300;
}

/* Hover effects for buttons */
button:not(:disabled):hover {
  @apply transform scale-105 transition-transform duration-200;
}

/* Audio element styling */
audio {
  @apply hidden;
}
</style>
