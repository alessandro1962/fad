<template>
  <div class="video-lesson">
    <!-- Video Container -->
    <div class="relative bg-black rounded-xl overflow-hidden mb-6">
      <!-- Vimeo Player -->
      <div v-if="videoProvider === 'vimeo'" class="aspect-video relative">
        <iframe
          ref="vimeoPlayer"
          :src="vimeoEmbedUrl"
          frameborder="0"
          allow="autoplay; fullscreen; picture-in-picture"
          allowfullscreen
          class="w-full h-full"
          @load="onPlayerLoad"
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
                  Guarda il video completo per sbloccare il pulsante "Vai al Modulo Successivo".
                </p>
                <div class="text-xs text-cdf-teal font-semibold mb-3 space-y-1">
                  <p>▶️ Clicca sul video per iniziare la riproduzione</p>
                  <p>⏸️ Usa i controlli del video per mettere in pausa se necessario</p>
                  <p>✅ Il pulsante apparirà automaticamente alla fine del video</p>
                </div>
                <p class="text-xs text-gray-400 mb-3">
                  Non è possibile saltare il video. Guarda il video completo per continuare.
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
    </div>

    <!-- Video Controls -->
    <div class="space-y-4">
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
          v-if="!isCompleted"
          disabled
          class="flex-1 bg-gray-300 text-gray-500 px-4 py-2 rounded-lg font-semibold cursor-not-allowed"
        >
          Guarda il video completo per continuare
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
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
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
  isLastLesson: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['lesson-completed', 'progress-updated', 'error'])

// Refs
const vimeoPlayer = ref(null)
const vimeoPlayerInstance = ref(null)
const youtubePlayer = ref(null)
const videoElement = ref(null)
const loading = ref(true)
const showInstructions = ref(true)

// State
const watchedTime = ref(0)
const totalDuration = ref(0)
const isCompleted = ref(false)
const lastPosition = ref(0)

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
  
  // Carica il progresso esistente
  await loadExistingProgress()
  
  // Per Vimeo, inizializza il player con API ufficiale
  if (videoProvider.value === 'vimeo') {
    setTimeout(() => {
      initializeVimeoPlayer()
    }, 1000)
  }
}

const initializeVimeoPlayer = async () => {
  try {
    const vimeoId = extractVimeoId(videoId.value)
    if (!vimeoId) {
      throw new Error('ID Vimeo non valido')
    }
    
    console.log('Inizializzazione player Vimeo con ID:', vimeoId)
    
    // Crea il player Vimeo
    const player = new Player(vimeoPlayer.value, {
      id: vimeoId,
      width: 640,
      height: 360
    })
    
    vimeoPlayerInstance.value = player
    
    // Eventi del player
    player.on('loaded', () => {
      console.log('Player Vimeo caricato')
    })
    
    player.on('ready', () => {
      console.log('Player Vimeo pronto')
    })
    
    player.on('play', () => {
      console.log('Video in riproduzione')
      hideInstructions()
    })
    
    player.on('pause', () => {
      console.log('Video in pausa')
    })
    
    player.on('ended', () => {
      console.log('Video Vimeo terminato')
      watchedTime.value = totalDuration.value
      isCompleted.value = true
      saveProgress()
    })
    
    // Ottieni la durata
    const duration = await player.getDuration()
    totalDuration.value = duration
    console.log('Durata video Vimeo ottenuta:', duration, 'secondi')
    
  } catch (error) {
    console.error('Errore nell\'inizializzazione player Vimeo:', error)
    // Fallback: usa durata stimata
    const estimatedDuration = props.lesson.duration_minutes ? props.lesson.duration_minutes * 60 : 300
    totalDuration.value = estimatedDuration
    console.log('Fallback - durata stimata:', estimatedDuration, 'secondi')
  }
}

const extractVimeoId = (videoId) => {
  if (!videoId) return null
  
  // Se è già un ID numerico, restituiscilo
  if (/^\d+$/.test(videoId)) {
    return videoId
  }
  
  // Se è un URL, estrai l'ID
  const match = videoId.match(/(?:vimeo\.com\/|player\.vimeo\.com\/video\/)(\d+)/)
  return match ? match[1] : null
}

const hideInstructions = () => {
  showInstructions.value = false
}

const loadExistingProgress = async () => {
  try {
    // Carica il progresso dal props se disponibile
    if (props.userProgress) {
      watchedTime.value = props.userProgress.seconds_watched || 0
      lastPosition.value = props.userProgress.last_position_sec || 0
      isCompleted.value = props.userProgress.completed || false
      console.log('Progresso caricato da props:', props.userProgress)
    } else {
      // Fallback: carica dal backend
      const response = await api.get(`/v1/progress/lesson/${props.lesson.id}`)
      if (response.data.data) {
        const progress = response.data.data
        watchedTime.value = progress.seconds_watched || 0
        lastPosition.value = progress.last_position_sec || 0
        isCompleted.value = progress.completed || false
        console.log('Progresso caricato dal backend:', progress)
      }
    }
  } catch (error) {
    console.error('Errore nel caricamento progresso:', error)
    // Se non riesce a caricare, mantieni i valori di default
    watchedTime.value = 0
    isCompleted.value = false
  }
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
    
    console.log('Progresso salvato:', progressData)
  } catch (error) {
    console.error('Errore nel salvataggio progresso:', error)
    emit('error', error)
  }
}

const proceedToNext = () => {
  if (isCompleted.value) {
    emit('lesson-completed', {
      lesson: props.lesson,
      completed: true,
      completed_at: new Date().toISOString()
    })
  }
}

// Video element methods
const onVideoLoaded = () => {
  if (videoElement.value) {
    totalDuration.value = videoElement.value.duration
    loading.value = false
  }
}

const onTimeUpdate = () => {
  if (videoElement.value) {
    watchedTime.value = videoElement.value.currentTime
    lastPosition.value = videoElement.value.currentTime
    
    // Check if video is completed
    if (videoElement.value.currentTime >= videoElement.value.duration * completionThreshold.value) {
      isCompleted.value = true
      saveProgress()
    }
  }
}

const onVideoEnded = () => {
  watchedTime.value = totalDuration.value
  isCompleted.value = true
  saveProgress()
}

const onVideoPause = () => {
  saveProgress()
}

const onVideoPlay = () => {
  hideInstructions()
}

// Lifecycle
onMounted(() => {
  // Initialize based on video provider
  if (videoProvider.value === 'upload' && videoElement.value) {
    onVideoLoaded()
  } else if (videoProvider.value === 'vimeo') {
    setTimeout(() => {
      onPlayerLoad()
    }, 100)
  }
})

onUnmounted(() => {
  // Cleanup Vimeo player
  if (vimeoPlayerInstance.value) {
    vimeoPlayerInstance.value.destroy()
    vimeoPlayerInstance.value = null
  }
})
</script>

<style scoped>
.video-lesson {
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
.video-lesson iframe {
  pointer-events: auto;
}
</style>
