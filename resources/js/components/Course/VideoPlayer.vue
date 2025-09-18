<template>
  <div class="video-player-container">
    <!-- Video Container -->
    <div class="relative bg-black rounded-xl overflow-hidden">
      <!-- Vimeo Player -->
      <div v-if="videoProvider === 'vimeo'" class="aspect-video relative">
        <!-- Vimeo player con API (fallback a iframe se API non funziona) -->
        <div
          v-if="!useVimeoIframe"
          ref="vimeoPlayer"
          class="w-full h-full"
          @click="hideInstructions"
        ></div>
        <iframe
          v-else
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
                <p class="text-sm font-semibold mb-2">Guarda il video per completare la lezione</p>
                <p class="text-xs text-gray-300 mb-2">
                  Guarda almeno il {{ Math.round(completionThreshold * 100) }}% del video per sbloccare la prossima lezione.
                </p>
                <div class="text-xs text-cdf-teal font-semibold mb-3 space-y-1">
                  <p v-if="!useVimeoIframe">▶️ Clicca sul video per iniziare la riproduzione</p>
                  <p v-if="!useVimeoIframe">⏸️ Usa i controlli del video per mettere in pausa se necessario</p>
                  <p v-if="!useVimeoIframe">📊 Il progresso viene tracciato automaticamente</p>
                  <p v-if="useVimeoIframe">▶️ Clicca "Inizia a guardare il video" per avviare il timer</p>
                  <p v-if="useVimeoIframe">⏱️ Il timer simula la durata del video</p>
                  <p v-if="useVimeoIframe" class="text-amber-400">⚠️ Modalità compatibilità attiva</p>
                </div>
                <p class="text-xs text-gray-400 mb-3">
                  <span v-if="!useVimeoIframe">Il sistema traccia automaticamente il tuo progresso. Non è possibile saltare il video.</span>
                  <span v-if="useVimeoIframe">In modalità compatibilità, il timer simula la durata del video. Guarda il video completo.</span>
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
      <!-- Progress Bar -->
      <div class="w-full bg-cdf-slate200 rounded-full h-2">
        <div 
          class="bg-gradient-to-r from-cdf-teal to-cdf-deep h-2 rounded-full transition-all duration-500"
          :style="{ width: `${progressPercentage}%` }"
        ></div>
      </div>
      
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
          v-if="!isCompleted && !useVimeoIframe"
          disabled
          class="flex-1 bg-gray-300 text-gray-500 px-4 py-2 rounded-lg font-semibold cursor-not-allowed"
        >
          Guarda il {{ Math.round(completionThreshold * 100) }}% del video per continuare
        </button>
        <div v-else-if="!isCompleted && useVimeoIframe && watchedTime === 0" class="flex gap-2">
          <button
            @click="startFallbackTimer"
            class="flex-1 bg-cdf-teal text-white px-4 py-2 rounded-lg font-semibold hover:bg-cdf-deep transition-colors"
          >
            Inizia a guardare il video
          </button>
        </div>
        <button
          v-else-if="!isCompleted && useVimeoIframe && watchedTime > 0"
          disabled
          class="flex-1 bg-gray-300 text-gray-500 px-4 py-2 rounded-lg font-semibold cursor-not-allowed"
        >
          Guarda il {{ Math.round(completionThreshold * 100) }}% del video per continuare
        </button>
        <button
          v-else
          @click="proceedToNext"
          class="flex-1 bg-cdf-amber text-cdf-ink px-4 py-2 rounded-lg font-semibold hover:brightness-95 transition-colors"
        >
          Continua al Prossimo
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import api from '@/api'
import Player from '@vimeo/player'

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
const vimeoPlayerInstance = ref(null)
const youtubePlayer = ref(null)
const videoElement = ref(null)
const loading = ref(true)
const showProgressOverlay = ref(false)
const showInstructions = ref(true)
const useVimeoIframe = ref(false)

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

const onPlayerLoad = async () => {
  loading.value = false
  
  // Reset automatico del progresso ogni volta che si carica il video
  resetProgress()
  
  initializeProgressTracking()
  
  // Per Vimeo, prova prima l'API, poi fallback a iframe
  if (videoProvider.value === 'vimeo' && vimeoPlayer.value) {
    try {
      // Inizializza il player Vimeo con l'API
      vimeoPlayerInstance.value = new Player(vimeoPlayer.value, {
        id: extractVimeoId(props.lesson.video_url),
        width: '100%',
        height: '100%',
        responsive: true
      })
      
      // Imposta la durata reale del video
      const duration = await vimeoPlayerInstance.value.getDuration()
      totalDuration.value = duration
      
      // Configura gli eventi del player
      setupVimeoEvents()
      
      console.log('Player Vimeo API inizializzato con successo')
      
    } catch (error) {
      console.error('Errore nell\'inizializzazione del player Vimeo API:', error)
      console.log('Fallback a iframe Vimeo')
      
      // Fallback a iframe
      useVimeoIframe.value = true
      const estimatedDuration = props.lesson.duration_minutes ? props.lesson.duration_minutes * 60 : 300
      totalDuration.value = estimatedDuration
      
      // Non avviare automaticamente il timer - aspetta che l'utente clicchi play
    }
  }
}

// Funzione per estrarre l'ID Vimeo dall'URL
const extractVimeoId = (url) => {
  const match = url.match(/(?:vimeo\.com\/|player\.vimeo\.com\/video\/)(\d+)/)
  return match ? match[1] : null
}

// Configura gli eventi del player Vimeo
const setupVimeoEvents = () => {
  if (!vimeoPlayerInstance.value) return
  
  // Evento quando il video inizia a riprodursi
  vimeoPlayerInstance.value.on('play', () => {
    console.log('Video in riproduzione')
  })
  
  // Evento quando il video viene messo in pausa
  vimeoPlayerInstance.value.on('pause', () => {
    console.log('Video in pausa')
  })
  
  // Evento per tracciare il progresso del video
  vimeoPlayerInstance.value.on('timeupdate', (data) => {
    watchedTime.value = Math.floor(data.seconds)
    
    // Salva il progresso ogni 10 secondi
    if (watchedTime.value % 10 === 0) {
      saveProgress()
    }
    
    // Controlla se il video è completato (90% della durata)
    if (watchedTime.value >= totalDuration.value * 0.9 && !isCompleted.value) {
      checkCompletion()
    }
  })
  
  // Evento quando il video finisce
  vimeoPlayerInstance.value.on('ended', () => {
    console.log('Video completato')
    watchedTime.value = totalDuration.value
    checkCompletion()
  })
  
  // Evento per errori
  vimeoPlayerInstance.value.on('error', (error) => {
    console.error('Errore nel player Vimeo:', error)
  })
}

const hideProgressOverlay = () => {
  showProgressOverlay.value = false
}

const hideInstructions = () => {
  showInstructions.value = false
}

// Timer di fallback per iframe Vimeo
let fallbackTimer = null

const startFallbackTimer = () => {
  if (fallbackTimer) return
  
  console.log('Timer di fallback avviato')
  
  fallbackTimer = setInterval(() => {
    // Simula il progresso per il fallback iframe
    // Questo è un sistema di base per quando l'API non funziona
    if (watchedTime.value < totalDuration.value) {
      watchedTime.value = Math.min(watchedTime.value + 1, totalDuration.value)
      
      // Salva il progresso ogni 10 secondi
      if (watchedTime.value % 10 === 0) {
        saveProgress()
      }
      
      // Controlla se il video è completato (90% della durata)
      if (watchedTime.value >= totalDuration.value * 0.9 && !isCompleted.value) {
        checkCompletion()
      }
    }
  }, 1000)
}

const stopFallbackTimer = () => {
  if (fallbackTimer) {
    clearInterval(fallbackTimer)
    fallbackTimer = null
    console.log('Timer di fallback fermato')
  }
}

const resetProgress = () => {
  watchedTime.value = 0
  isCompleted.value = false
  // Non fermare il timer se non è attivo
  if (fallbackTimer) {
    stopFallbackTimer()
  }
  console.log('Progresso resettato automaticamente')
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

  // Set up progress tracking interval
  progressInterval.value = setInterval(saveProgress, 10000) // Save every 10 seconds

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
  }
}

const checkCompletion = () => {
  const shouldComplete = progressPercentage.value >= (completionThreshold.value * 100)
  
  if (shouldComplete && !isCompleted.value) {
    isCompleted.value = true
    showProgressOverlay.value = false
    saveProgress() // Salva il progresso finale
    emit('lesson-completed', props.lesson)
  }
}

// La funzione markAsCompleted è stata rimossa - il completamento avviene automaticamente

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
  } else if (videoProvider.value === 'vimeo') {
    // Inizializza il player Vimeo quando il componente è montato
    setTimeout(() => {
      onPlayerLoad()
    }, 100)
  }
})

onUnmounted(() => {
  if (progressInterval.value) {
    clearInterval(progressInterval.value)
  }
  
  // Cleanup Vimeo player
  if (vimeoPlayerInstance.value) {
    vimeoPlayerInstance.value.destroy()
    vimeoPlayerInstance.value = null
  }
  
  // Cleanup fallback timer
  stopFallbackTimer()
  
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
