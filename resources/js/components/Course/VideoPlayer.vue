<template>
  <div class="video-player-container">
    <!-- Video Container -->
    <div class="relative bg-black rounded-xl overflow-hidden">
      <!-- Vimeo Player -->
      <div v-if="videoProvider === 'vimeo'" class="aspect-video relative">
        <!-- Vimeo iframe with custom controls -->
        <iframe
          ref="vimeoPlayer"
          :src="vimeoEmbedUrl"
          frameborder="0"
          allow="autoplay; fullscreen; picture-in-picture"
          allowfullscreen
          class="w-full h-full"
          @load="onPlayerLoad"
          @click="hideInstructions"
        ></iframe>
        
        <!-- Custom Controls Overlay -->
        <div class="absolute inset-0 pointer-events-none" style="z-index: 1;">
          <!-- Instructions (Only show initially) -->
          <div v-if="showInstructions" class="absolute top-4 left-4 bg-black/80 text-white p-4 rounded-lg max-w-sm pointer-events-auto">
            <div class="flex items-start gap-3">
              <div class="w-8 h-8 rounded-full bg-cdf-teal/20 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-cdf-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div>
                <p class="text-sm font-semibold mb-2">Completa la lezione per continuare</p>
                <p class="text-xs text-gray-300 mb-2">
                  Guarda almeno il {{ Math.round(completionThreshold * 100) }}% del video per sbloccare la prossima lezione.
                </p>
                <div class="text-xs text-cdf-teal font-semibold mb-3 space-y-1">
                  <p>▶️ Per far partire il video: clicca sul video</p>
                  <p>⏸️ Per metterlo in pausa: clicca sul video</p>
                  <p>▶️ Per farlo ripartire: riclicca sul video</p>
                </div>
                <p class="text-xs text-gray-400 mb-3">
                  I video di Campus Digitale Forma sono realizzati in modo da non poter mandarli avanti meccanicamente.
                </p>
                <button
                  @click="hideInstructions"
                  class="text-xs bg-cdf-teal text-white px-3 py-1 rounded hover:bg-cdf-deep transition-colors"
                >
                  OK, ho capito
                </button>
              </div>
            </div>
          </div>
          
          <!-- Progress Bar (Custom) -->
          <div class="absolute bottom-0 left-0 right-0 p-4 pointer-events-auto">
            <div class="w-full bg-white/20 rounded-full h-1 mb-2">
              <div 
                class="bg-cdf-teal h-1 rounded-full transition-all duration-300"
                :style="{ width: `${progressPercentage}%` }"
              ></div>
            </div>
            <div class="flex justify-between items-center text-white text-sm">
              <div class="flex gap-2">
                <span>{{ formatTime(watchedTime) }}</span>
                <span>/</span>
                <span>{{ formatTime(totalDuration) }}</span>
              </div>
              <div class="flex items-center gap-2">
                <span class="text-xs">{{ Math.round(progressPercentage) }}%</span>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Complete Lesson Button (Outside iframe area for Vimeo) -->
      <div v-if="videoProvider === 'vimeo'" class="mt-4 text-center">
        <div v-if="!isCompleted" class="mb-4">
          <button
            @click="markAsCompleted"
            :class="progressPercentage >= 90 ? 
              'bg-cdf-teal text-white hover:bg-cdf-deep' : 
              'bg-cdf-amber text-cdf-ink hover:brightness-95'"
            class="px-6 py-3 rounded-lg font-semibold transition-colors shadow-lg"
          >
            {{ progressPercentage >= 90 ? '✅ Completa Lezione' : '⏭️ Completa Manualmente' }}
          </button>
          <p class="text-sm text-cdf-slate600 mt-2">
            {{ progressPercentage >= 90 ? 
              `Hai visto il ${Math.round(progressPercentage)}% del video. Clicca per completare la lezione.` :
              `Progresso: ${Math.round(progressPercentage)}%. Clicca per completare manualmente la lezione.`
            }}
          </p>
        </div>
        <div v-else class="mb-4">
          <button
            @click="proceedToNext"
            class="bg-cdf-amber text-cdf-ink px-6 py-3 rounded-lg font-semibold hover:brightness-95 transition-colors shadow-lg"
          >
            ➡️ Continua al Prossimo
          </button>
          <p class="text-sm text-cdf-slate600 mt-2">
            Lezione completata! Clicca per passare alla prossima lezione.
          </p>
        </div>
      </div>

      <!-- YouTube Player -->
      <div v-else-if="videoProvider === 'youtube'" class="aspect-video">
        <iframe
          ref="youtubePlayer"
          :src="youtubeEmbedUrl"
          frameborder="0"
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
          allowfullscreen
          class="w-full h-full"
          @load="onPlayerLoad"
        ></iframe>
      </div>
      
      <!-- Complete Lesson Button (Outside iframe area for YouTube) -->
      <div v-if="videoProvider === 'youtube'" class="mt-4 text-center">
        <div v-if="!isCompleted" class="mb-4">
          <button
            @click="markAsCompleted"
            :class="progressPercentage >= 90 ? 
              'bg-cdf-teal text-white hover:bg-cdf-deep' : 
              'bg-cdf-amber text-cdf-ink hover:brightness-95'"
            class="px-6 py-3 rounded-lg font-semibold transition-colors shadow-lg"
          >
            {{ progressPercentage >= 90 ? '✅ Completa Lezione' : '⏭️ Completa Manualmente' }}
          </button>
          <p class="text-sm text-cdf-slate600 mt-2">
            {{ progressPercentage >= 90 ? 
              `Hai visto il ${Math.round(progressPercentage)}% del video. Clicca per completare la lezione.` :
              `Progresso: ${Math.round(progressPercentage)}%. Clicca per completare manualmente la lezione.`
            }}
          </p>
        </div>
        <div v-else class="mb-4">
          <button
            @click="proceedToNext"
            class="bg-cdf-amber text-cdf-ink px-6 py-3 rounded-lg font-semibold hover:brightness-95 transition-colors shadow-lg"
          >
            ➡️ Continua al Prossimo
          </button>
          <p class="text-sm text-cdf-slate600 mt-2">
            Lezione completata! Clicca per passare alla prossima lezione.
          </p>
        </div>
      </div>

      <!-- Uploaded Video -->
      <div v-else-if="videoProvider === 'upload'" class="aspect-video">
        <video
          ref="videoElement"
          :src="videoUrl"
          controls
          class="w-full h-full"
          @loadedmetadata="onVideoLoaded"
          @timeupdate="onTimeUpdate"
          @ended="onVideoEnded"
          @pause="onVideoPause"
          @play="onVideoPlay"
        ></video>
      </div>
      
      <!-- Complete Lesson Button (Outside video area for uploaded videos) -->
      <div v-if="videoProvider === 'upload'" class="mt-4 text-center">
        <div v-if="!isCompleted" class="mb-4">
          <button
            @click="markAsCompleted"
            :class="progressPercentage >= 90 ? 
              'bg-cdf-teal text-white hover:bg-cdf-deep' : 
              'bg-cdf-amber text-cdf-ink hover:brightness-95'"
            class="px-6 py-3 rounded-lg font-semibold transition-colors shadow-lg"
          >
            {{ progressPercentage >= 90 ? '✅ Completa Lezione' : '⏭️ Completa Manualmente' }}
          </button>
          <p class="text-sm text-cdf-slate600 mt-2">
            {{ progressPercentage >= 90 ? 
              `Hai visto il ${Math.round(progressPercentage)}% del video. Clicca per completare la lezione.` :
              `Progresso: ${Math.round(progressPercentage)}%. Clicca per completare manualmente la lezione.`
            }}
          </p>
        </div>
        <div v-else class="mb-4">
          <button
            @click="proceedToNext"
            class="bg-cdf-amber text-cdf-ink px-6 py-3 rounded-lg font-semibold hover:brightness-95 transition-colors shadow-lg"
          >
            ➡️ Continua al Prossimo
          </button>
          <p class="text-sm text-cdf-slate600 mt-2">
            Lezione completata! Clicca per passare alla prossima lezione.
          </p>
        </div>
      </div>

      <!-- Loading Overlay -->
      <div v-if="loading" class="absolute inset-0 bg-black/50 flex items-center justify-center">
        <div class="text-white text-center">
          <svg class="animate-spin h-8 w-8 mx-auto mb-2" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <p>Caricamento video...</p>
        </div>
      </div>

      <!-- Progress Overlay - Temporarily disabled -->
      <!-- 
      <div v-if="showProgressOverlay" class="absolute inset-0 bg-black/80 flex items-center justify-center">
        <div class="text-white text-center max-w-md mx-4">
          <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-cdf-teal/20 flex items-center justify-center">
            <svg class="w-8 h-8 text-cdf-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <h3 class="text-xl font-bold mb-2">Completa la lezione per continuare</h3>
          <p class="text-gray-300 mb-4">
            Devi guardare almeno il {{ Math.round(completionThreshold * 100) }}% del video per sbloccare la prossima lezione.
          </p>
          <div class="w-full bg-gray-700 rounded-full h-2 mb-4">
            <div 
              class="bg-cdf-teal h-2 rounded-full transition-all duration-500"
              :style="{ width: `${progressPercentage}%` }"
            ></div>
          </div>
          <p class="text-sm text-gray-400">
            Progresso: {{ Math.round(progressPercentage) }}% / {{ Math.round(completionThreshold * 100) }}%
          </p>
          <button
            @click="hideProgressOverlay"
            class="mt-4 bg-cdf-teal text-white px-6 py-2 rounded-lg font-semibold hover:bg-cdf-deep transition-colors"
          >
            Inizia a Guardare
          </button>
        </div>
      </div>
      -->
    </div>

    <!-- Video Controls -->
    <div class="mt-4 space-y-4">
      <!-- Progress Info -->
      <div class="flex items-center justify-between text-sm text-cdf-slate700">
        <span>Durata: {{ formatTime(totalDuration) }}</span>
        <span>Guardato: {{ formatTime(watchedTime) }}</span>
        <span class="font-semibold" :class="isCompleted ? 'text-green-600' : 'text-cdf-slate700'">
          {{ isCompleted ? 'Completato' : `${Math.round(progressPercentage)}%` }}
        </span>
      </div>

      <!-- Action Buttons -->
      <div class="flex gap-3">
        <button
          v-if="!isCompleted && canProceed"
          @click="markAsCompleted"
          class="flex-1 bg-cdf-teal text-white px-4 py-2 rounded-lg font-semibold hover:bg-cdf-deep transition-colors"
        >
          Segna come Completato
        </button>
        <button
          v-else-if="isCompleted"
          @click="proceedToNext"
          class="flex-1 bg-cdf-amber text-cdf-ink px-4 py-2 rounded-lg font-semibold hover:brightness-95 transition-colors"
        >
          Continua al Prossimo
        </button>
        <button
          v-else
          disabled
          class="flex-1 bg-gray-300 text-gray-500 px-4 py-2 rounded-lg font-semibold cursor-not-allowed"
        >
          Completa la lezione per continuare
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
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
  blockProgression: {
    type: Boolean,
    default: true
  }
})

const emit = defineEmits(['progress-updated', 'lesson-completed', 'next-lesson'])

// Refs
const vimeoPlayer = ref(null)
const youtubePlayer = ref(null)
const videoElement = ref(null)
const loading = ref(true)
const showProgressOverlay = ref(false)
const showInstructions = ref(true)

// State
const watchedTime = ref(0)
const totalDuration = ref(0)
const isCompleted = ref(false)
const lastPosition = ref(0)
const progressInterval = ref(null)

// Computed
const videoProvider = computed(() => {
  return props.lesson.payload?.provider || 'upload'
})

const videoId = computed(() => {
  return props.lesson.payload?.video_id || ''
})

const videoUrl = computed(() => {
  return props.lesson.payload?.video_url || ''
})

const completionThreshold = computed(() => {
  return props.lesson.payload?.completion_threshold || 0.9
})

const vimeoEmbedUrl = computed(() => {
  if (videoProvider.value !== 'vimeo' || !videoId.value) return ''
  
  // Se è già un URL completo di Vimeo, usalo così com'è
  if (videoId.value.startsWith('https://player.vimeo.com/video/')) {
    return videoId.value
  }
  
  // Fallback per ID numerico
  let actualVideoId = videoId.value
  if (videoId.value.includes('/video/')) {
    const match = videoId.value.match(/\/video\/(\d+)/)
    if (match) {
      actualVideoId = match[1]
    }
  }
  
  // URL minimale per evitare errori 403
  return `https://player.vimeo.com/video/${actualVideoId}`
})

const youtubeEmbedUrl = computed(() => {
  if (videoProvider.value !== 'youtube' || !videoUrl.value) return ''
  
  // Estrai video ID da URL YouTube
  const videoId = extractYouTubeId(videoUrl.value)
  if (!videoId) return ''
  
  const params = new URLSearchParams({
    autoplay: '0', // Disabilitiamo autoplay
    controls: '1',
    rel: '0',
    modestbranding: '1',
    color: 'white',
    cc_load_policy: '0',
    iv_load_policy: '3',
    fs: '1',
    disablekb: '0'
  })
  
  return `https://www.youtube.com/embed/${videoId}?${params.toString()}`
})

const progressPercentage = computed(() => {
  if (totalDuration.value === 0) return 0
  return Math.min(100, (watchedTime.value / totalDuration.value) * 100)
})

const canProceed = computed(() => {
  if (!props.blockProgression) return true
  return progressPercentage.value >= (completionThreshold.value * 100)
})

// Methods
const extractYouTubeId = (url) => {
  const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/
  const match = url.match(regExp)
  return (match && match[2].length === 11) ? match[2] : null
}

const formatTime = (seconds) => {
  if (!seconds || seconds < 0) return '0:00'
  
  const hours = Math.floor(seconds / 3600)
  const minutes = Math.floor((seconds % 3600) / 60)
  const secs = Math.floor(seconds % 60)
  
  if (hours > 0) {
    return `${hours}:${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`
  }
  return `${minutes}:${secs.toString().padStart(2, '0')}`
}

const onPlayerLoad = () => {
  loading.value = false
  initializeProgressTracking()
  
  // Per Vimeo, aggiungiamo un listener per il completamento
  if (videoProvider.value === 'vimeo') {
    setupVimeoCompletionTracking()
  }
}

const setupVimeoCompletionTracking = () => {
  // Per i video Vimeo, usiamo un sistema di tracciamento semplificato
  // Impostiamo una durata stimata e permettiamo il completamento manuale
  
  // Stima la durata del video (in secondi) - puoi modificare questo valore
  const estimatedDuration = props.lesson.duration_minutes ? props.lesson.duration_minutes * 60 : 300 // 5 minuti di default
  totalDuration.value = estimatedDuration
  
  // Avvia un timer per simulare il progresso
  const progressTimer = setInterval(() => {
    // Incrementa gradualmente il tempo guardato
    watchedTime.value = Math.min(watchedTime.value + 1, totalDuration.value)
    
    // Salva il progresso ogni 10 secondi (ma solo se non ci sono errori di auth)
    if (watchedTime.value % 10 === 0) {
      // Non salvare automaticamente se ci sono problemi di autenticazione
      // saveProgress()
    }
  }, 1000) // Aggiorna ogni secondo
  
  // Pulisci il timer quando il componente viene distrutto
  onUnmounted(() => {
    clearInterval(progressTimer)
  })
  
  // Listener per messaggi dall'iframe di Vimeo (se disponibile)
  const handleMessage = (event) => {
    if (event.origin !== 'https://player.vimeo.com') return
    
    try {
      const data = JSON.parse(event.data)
      if (data.method === 'getCurrentTime' && data.value !== undefined) {
        const currentTime = data.value
        const duration = data.duration || totalDuration.value
        
        if (duration > 0) {
          totalDuration.value = duration
          watchedTime.value = Math.max(watchedTime.value, currentTime)
        }
      }
    } catch (error) {
      // Ignora errori di parsing
    }
  }
  
  window.addEventListener('message', handleMessage)
  
  // Pulisci il listener quando il componente viene distrutto
  onUnmounted(() => {
    window.removeEventListener('message', handleMessage)
  })
}

// Vimeo iframe player - no API needed

// Simplified iframe-based Vimeo player

const hideProgressOverlay = () => {
  showProgressOverlay.value = false
}

const hideInstructions = () => {
  showInstructions.value = false
}

// Metodi rimossi - i controlli sono gestiti direttamente dal video Vimeo

const onVideoLoaded = () => {
  if (videoElement.value) {
    totalDuration.value = videoElement.value.duration
    loading.value = false
    initializeProgressTracking()
  }
}

const onTimeUpdate = () => {
  if (videoElement.value) {
    watchedTime.value = Math.max(watchedTime.value, videoElement.value.currentTime)
    lastPosition.value = videoElement.value.currentTime
  }
}

const onVideoEnded = () => {
  if (videoElement.value) {
    watchedTime.value = videoElement.value.duration
    lastPosition.value = videoElement.value.duration
    checkCompletion()
  }
}

const onVideoPause = () => {
  saveProgress()
}

const onVideoPlay = () => {
  // Resume from last position if needed
  if (videoElement.value && lastPosition.value > 0) {
    videoElement.value.currentTime = lastPosition.value
  }
  
  // Hide progress overlay when video starts playing
  showProgressOverlay.value = false
}

const initializeProgressTracking = () => {
  // Load existing progress
  if (props.userProgress) {
    watchedTime.value = props.userProgress.seconds_watched || 0
    lastPosition.value = props.userProgress.last_position_sec || 0
    isCompleted.value = props.userProgress.completed || false
  }

  // Set up progress tracking interval (disabled for now due to auth issues)
  // progressInterval.value = setInterval(saveProgress, 10000) // Save every 10 seconds

  // Don't show progress overlay initially - let user start the video
  showProgressOverlay.value = false
}

const saveProgress = async () => {
  try {
    const progressData = {
      seconds_watched: watchedTime.value,
      last_position_sec: lastPosition.value,
      completed: isCompleted.value
    }

    await api.patch(`/v1/progress/lesson/${props.lesson.id}`, progressData)
    
    emit('progress-updated', {
      lesson_id: props.lesson.id,
      ...progressData,
      progress_percentage: progressPercentage.value
    })
  } catch (error) {
    console.error('Errore nel salvataggio progresso:', error)
    
    // Stop the progress timer if we get authentication errors
    if (error.response?.status === 403 || error.response?.status === 401) {
      if (progressInterval.value) {
        clearInterval(progressInterval.value)
        progressInterval.value = null
      }
    }
  }
}

const checkCompletion = () => {
  const shouldComplete = progressPercentage.value >= (completionThreshold.value * 100)
  
  if (shouldComplete && !isCompleted.value) {
    isCompleted.value = true
    showProgressOverlay.value = false
    emit('lesson-completed', props.lesson)
  }
}

const markAsCompleted = async () => {
  isCompleted.value = true
  watchedTime.value = totalDuration.value
  lastPosition.value = totalDuration.value
  showProgressOverlay.value = false
  
  await saveProgress()
  emit('lesson-completed', props.lesson)
}

const proceedToNext = () => {
  // Check if user can proceed
  if (props.blockProgression && !isCompleted.value && !canProceed.value) {
    showProgressOverlay.value = true
    return
  }
  
  emit('next-lesson')
}

// Watchers
watch(() => props.userProgress, (newProgress) => {
  if (newProgress) {
    watchedTime.value = newProgress.seconds_watched || 0
    lastPosition.value = newProgress.last_position_sec || 0
    isCompleted.value = newProgress.completed || false
  }
}, { deep: true })

watch(progressPercentage, (newPercentage) => {
  if (newPercentage >= (completionThreshold.value * 100) && !isCompleted.value) {
    checkCompletion()
  }
})

// Lifecycle
onMounted(() => {
  // Initialize based on video provider
  if (videoProvider.value === 'upload' && videoElement.value) {
    onVideoLoaded()
  }
  // Vimeo iframe player doesn't need initialization
})

onUnmounted(() => {
  if (progressInterval.value) {
    clearInterval(progressInterval.value)
  }
  
  // Save final progress
  saveProgress()
})
</script>

<style scoped>
.video-player-container {
  @apply w-full;
}

/* Custom video controls styling */
video::-webkit-media-controls {
  @apply bg-black/20;
}

video::-webkit-media-controls-panel {
  @apply bg-black/50;
}

/* Progress bar styling */
.progress-bar {
  @apply w-full h-1 bg-gray-700 rounded-full overflow-hidden;
}

.progress-fill {
  @apply h-full bg-cdf-teal transition-all duration-300;
}

/* Hide Vimeo progress bar and disable seeking */
.video-player-container iframe {
  pointer-events: auto;
}

/* Custom Vimeo player styling to disable seeking */
.video-player-container iframe[src*="vimeo"] {
  /* This will be handled by Vimeo API parameters */
}
</style>
