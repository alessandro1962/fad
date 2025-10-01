<template>
  <div class="slide-lesson">
    <!-- Slide Viewer -->
    <div class="bg-black rounded-xl overflow-hidden mb-6">
      <div class="aspect-video relative">
        <!-- Slide Content -->
        <div v-if="currentSlide" class="w-full h-full flex items-center justify-center bg-white">
          <div class="max-w-4xl mx-auto p-8 text-center">
            <h2 class="text-2xl font-bold text-cdf-deep mb-4">{{ currentSlide.title }}</h2>
            
            <!-- PDF File Display -->
            <div v-if="currentSlide.file_path" class="space-y-6">
              <div class="w-24 h-24 mx-auto bg-red-100 rounded-full flex items-center justify-center">
                <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
              </div>
              <div class="prose prose-lg max-w-none" v-html="currentSlide.content"></div>
              <div class="space-y-3">
                <button
                  @click="openPdf"
                  class="px-6 py-3 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition-colors"
                >
                  <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                  </svg>
                  Apri PDF in Nuova Finestra
                </button>
                <button
                  @click="downloadPdf"
                  class="px-6 py-3 bg-cdf-teal text-white rounded-lg font-semibold hover:bg-cdf-deep transition-colors ml-3"
                >
                  <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                  Scarica PDF
                </button>
              </div>
            </div>
            
            <!-- Standard Slide Content -->
            <div v-else class="prose prose-lg max-w-none" v-html="currentSlide.content"></div>
          </div>
        </div>
        
        <!-- Navigation Overlay (only for multi-slide presentations) -->
        <div v-if="!lesson.payload?.file_path" class="absolute inset-0 flex items-center justify-between p-4 pointer-events-none">
          <button
            @click="previousSlide"
            :disabled="currentSlideIndex === 0"
            class="pointer-events-auto w-12 h-12 rounded-full bg-black/50 text-white flex items-center justify-center hover:bg-black/70 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
          </button>
          <button
            @click="nextSlide"
            :disabled="currentSlideIndex === slides.length - 1"
            class="pointer-events-auto w-12 h-12 rounded-full bg-black/50 text-white flex items-center justify-center hover:bg-black/70 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
          </button>
        </div>
        
        <!-- Slide Counter (only for multi-slide presentations) -->
        <div v-if="!lesson.payload?.file_path" class="absolute bottom-4 left-1/2 transform -translate-x-1/2 pointer-events-none">
          <div class="bg-black/50 text-white px-3 py-1 rounded-full text-sm">
            {{ currentSlideIndex + 1 }} / {{ slides.length }}
          </div>
        </div>
      </div>
    </div>

    <!-- Slide Navigation (only for multi-slide presentations) -->
    <div v-if="!lesson.payload?.file_path" class="bg-white rounded-xl border border-cdf-slate200 p-4 mb-6">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-cdf-deep">Navigazione Slide</h3>
        <span class="text-sm text-cdf-slate600">
          {{ Math.round(((currentSlideIndex + 1) / slides.length) * 100) }}% completato
        </span>
      </div>
      
      <!-- Progress Bar -->
      <div class="w-full bg-cdf-slate200 rounded-full h-2 mb-4">
        <div 
          class="bg-gradient-to-r from-cdf-teal to-cdf-deep h-2 rounded-full transition-all duration-500"
          :style="{ width: `${((currentSlideIndex + 1) / slides.length) * 100}%` }"
        ></div>
      </div>
      
      <!-- Slide Thumbnails -->
      <div class="grid grid-cols-6 gap-2">
        <button
          v-for="(slide, index) in slides"
          :key="index"
          @click="goToSlide(index)"
          class="aspect-video bg-cdf-slate200 rounded-lg p-2 text-center hover:bg-cdf-teal/20 transition-colors"
          :class="{ 'bg-cdf-teal text-white': index === currentSlideIndex }"
        >
          <div class="text-xs font-semibold">{{ index + 1 }}</div>
        </button>
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
            {{ isCompleted ? 'Lezione completata' : (lesson.payload?.file_path ? 'Visualizza o scarica il PDF per completare la lezione' : 'Visualizza tutte le slide per completare la lezione') }}
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
        {{ lesson.payload?.file_path ? 'Visualizza il PDF per continuare' : 'Visualizza tutte le slide per continuare' }}
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
import { ref, computed, onMounted, watch } from 'vue'
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
const currentSlideIndex = ref(0)
const isCompleted = ref(false)
const viewedSlides = ref(new Set())

// Computed
const slides = computed(() => {
  // Check if it's a PDF file-based slide lesson
  if (props.lesson.payload?.file_path) {
    return [{
      title: props.lesson.title,
      content: `<p>Questa lezione contiene una presentazione PDF.</p><p>File: ${props.lesson.payload.file_name || 'Presentazione'}</p>`,
      file_path: props.lesson.payload.file_path,
      file_name: props.lesson.payload.file_name
    }]
  }
  
  // Standard slides array
  return props.lesson.payload?.slides || []
})

const currentSlide = computed(() => {
  return slides.value[currentSlideIndex.value] || null
})

const canComplete = computed(() => {
  // For PDF-based slides, allow completion immediately
  if (props.lesson.payload?.file_path) {
    return true
  }
  
  // For standard slides, require all slides to be viewed
  return viewedSlides.value.size === slides.value.length && slides.value.length > 0
})

// Methods
const nextSlide = () => {
  if (currentSlideIndex.value < slides.value.length - 1) {
    currentSlideIndex.value++
    markSlideAsViewed(currentSlideIndex.value)
  }
}

const previousSlide = () => {
  if (currentSlideIndex.value > 0) {
    currentSlideIndex.value--
  }
}

const goToSlide = (index) => {
  currentSlideIndex.value = index
  markSlideAsViewed(index)
}

const markSlideAsViewed = (index) => {
  viewedSlides.value.add(index)
  
  // Auto-complete if all slides viewed
  if (viewedSlides.value.size === slides.value.length) {
    setTimeout(() => {
      markAsCompleted()
    }, 1000)
  }
}

const markAsCompleted = async () => {
  try {
    isCompleted.value = true
    
    // Save progress
    const progressData = {
      completed: true,
      completed_at: new Date().toISOString(),
      slides_viewed: Array.from(viewedSlides.value)
    }

    await api.patch(`/v1/progress/lesson/${props.lesson.id}`, progressData)
    
    emit('progress-updated', {
      lesson_id: props.lesson.id,
      ...progressData
    })
    
    console.log('Slide lesson marked as completed')
  } catch (error) {
    console.error('Error marking slide lesson as completed:', error)
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

const openPdf = () => {
  if (currentSlide.value?.file_path) {
    const pdfUrl = `/storage/${currentSlide.value.file_path}`
    window.open(pdfUrl, '_blank')
  }
}

const downloadPdf = () => {
  if (currentSlide.value?.file_path) {
    const pdfUrl = `/storage/${currentSlide.value.file_path}`
    const link = document.createElement('a')
    link.href = pdfUrl
    link.download = currentSlide.value.file_name || 'presentazione.pdf'
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
  }
}

// Watch for slide changes
watch(currentSlideIndex, (newIndex) => {
  markSlideAsViewed(newIndex)
})

// Lifecycle
onMounted(() => {
  // Load existing progress
  if (props.userProgress) {
    isCompleted.value = props.userProgress.completed || false
    if (props.userProgress.slides_viewed) {
      viewedSlides.value = new Set(props.userProgress.slides_viewed)
    }
  }
  
  // Mark first slide as viewed
  if (slides.value.length > 0) {
    markSlideAsViewed(0)
  }
})
</script>

<style scoped>
.slide-lesson {
  @apply w-full;
}

/* Custom styling for slide content */
.prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6 {
  @apply text-cdf-deep;
}

.prose p {
  @apply text-cdf-slate700;
}

.prose ul, .prose ol {
  @apply text-cdf-slate700;
}

.prose li {
  @apply mb-2;
}

/* Slide transition effects */
.slide-content {
  transition: opacity 0.3s ease-in-out;
}

/* Hover effects for navigation buttons */
button:not(:disabled):hover {
  @apply transform scale-105 transition-transform duration-200;
}
</style>
