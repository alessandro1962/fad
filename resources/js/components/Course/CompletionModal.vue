<template>
  <!-- Modal Overlay -->
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @click.self="$emit('close')">
    <div class="completion-modal bg-white rounded-3xl shadow-xl border border-cdf-slate200 p-8 text-center max-h-[90vh] overflow-y-auto relative">
      <!-- Close Button -->
      <button 
        @click="$emit('close')"
        class="absolute top-4 right-4 w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors"
      >
        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
      <!-- Success Icon -->
      <div class="w-32 h-32 mx-auto mb-8 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center">
        <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
      </div>

      <!-- Congratulations Message -->
      <h1 class="text-4xl font-bold text-cdf-deep mb-4">
        🎉 Congratulazioni!
      </h1>
      <h2 class="text-2xl font-semibold text-cdf-slate700 mb-6">
        Hai completato con successo il corso
      </h2>
      <p class="text-xl text-cdf-slate600 mb-8">
        <strong>{{ course.title }}</strong>
      </p>

      <!-- Certificate Info -->
      <div class="bg-cdf-sand rounded-2xl p-6 mb-8">
        <div class="flex items-center justify-center mb-4">
          <div class="w-12 h-12 rounded-full bg-cdf-teal/20 flex items-center justify-center mr-4">
            <svg class="w-6 h-6 text-cdf-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
          </div>
          <h3 class="text-lg font-semibold text-cdf-deep">Certificato di Completamento</h3>
        </div>
        <p class="text-cdf-slate700 mb-4">
          Il tuo certificato è stato generato e sarà disponibile nella sezione "I miei attestati".
        </p>
        <div class="flex items-center justify-center space-x-4">
          <button
            @click="downloadCertificate"
            :disabled="certificateLoading"
            class="px-6 py-3 bg-cdf-teal text-white rounded-xl font-semibold hover:bg-cdf-deep transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <svg v-if="certificateLoading" class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <svg v-else class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            {{ certificateLoading ? 'Generazione...' : 'Scarica Certificato' }}
          </button>
          <button
            @click="shareCertificate"
            class="px-6 py-3 border border-cdf-slate200 text-cdf-slate700 rounded-xl font-semibold hover:bg-cdf-slate50 transition-colors"
          >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
            </svg>
            Condividi
          </button>
        </div>
      </div>

      <!-- Course Stats -->
      <div class="grid md:grid-cols-3 gap-4 mb-8">
        <div class="bg-cdf-sand rounded-xl p-4">
          <div class="text-2xl font-bold text-cdf-deep">{{ completionData.total_lessons || 0 }}</div>
          <div class="text-sm text-cdf-slate700">Lezioni Completate</div>
        </div>
        <div class="bg-cdf-sand rounded-xl p-4">
          <div class="text-2xl font-bold text-cdf-deep">{{ formatDuration(completionData.total_duration || 0) }}</div>
          <div class="text-sm text-cdf-slate700">Tempo Totale</div>
        </div>
        <div class="bg-cdf-sand rounded-xl p-4">
          <div class="text-2xl font-bold text-cdf-deep">{{ completionData.completion_date || 'Oggi' }}</div>
          <div class="text-sm text-cdf-slate700">Data Completamento</div>
        </div>
      </div>

      <!-- Next Steps -->
      <div class="bg-cdf-teal/10 rounded-2xl p-6 mb-8">
        <h3 class="text-lg font-semibold text-cdf-deep mb-4">Prossimi Passi</h3>
        <div class="space-y-3 text-left">
          <div class="flex items-center">
            <div class="w-6 h-6 rounded-full bg-cdf-teal text-white flex items-center justify-center mr-3 text-sm font-bold">1</div>
            <span class="text-cdf-slate700">Scarica il tuo certificato di completamento</span>
          </div>
          <div class="flex items-center">
            <div class="w-6 h-6 rounded-full bg-cdf-teal text-white flex items-center justify-center mr-3 text-sm font-bold">2</div>
            <span class="text-cdf-slate700">Esplora altri corsi nel nostro catalogo</span>
          </div>
          <div class="flex items-center">
            <div class="w-6 h-6 rounded-full bg-cdf-teal text-white flex items-center justify-center mr-3 text-sm font-bold">3</div>
            <span class="text-cdf-slate700">Condividi il tuo successo sui social media</span>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <button
          @click="goToDashboard"
          class="px-8 py-3 bg-cdf-amber text-cdf-ink rounded-xl font-semibold hover:brightness-95 transition-colors"
        >
          <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"></path>
          </svg>
          Vai alla Dashboard
        </button>
        <button
          @click="restartCourse"
          class="px-8 py-3 border border-cdf-slate200 text-cdf-slate700 rounded-xl font-semibold hover:bg-cdf-slate50 transition-colors"
        >
          <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
          </svg>
          Ripeti il Corso
        </button>
      </div>

      <!-- Social Sharing -->
      <div class="mt-8 pt-6 border-t border-cdf-slate200">
        <p class="text-sm text-cdf-slate600 mb-4">Condividi il tuo successo:</p>
        <div class="flex justify-center space-x-4">
          <button
            @click="shareOnSocial('linkedin')"
            class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center hover:bg-blue-700 transition-colors"
          >
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
              <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
            </svg>
          </button>
          <button
            @click="shareOnSocial('twitter')"
            class="w-10 h-10 rounded-full bg-blue-400 text-white flex items-center justify-center hover:bg-blue-500 transition-colors"
          >
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
              <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
            </svg>
          </button>
          <button
            @click="shareOnSocial('facebook')"
            class="w-10 h-10 rounded-full bg-blue-800 text-white flex items-center justify-center hover:bg-blue-900 transition-colors"
          >
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
              <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import api from '@/api'

const props = defineProps({
  course: {
    type: Object,
    required: true
  },
  completionData: {
    type: Object,
    default: () => ({})
  }
})

const emit = defineEmits(['restart-course', 'go-to-dashboard', 'close'])

// State
const certificateLoading = ref(false)

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

const downloadCertificate = async () => {
  try {
    certificateLoading.value = true
    
    // Generate and download certificate
    const response = await api.get(`/v1/certificates/course/${props.course.id}/download`, {
      responseType: 'blob'
    })
    
    // Check if response is valid and is actually a PDF
    if (!response.data || response.data.size === 0) {
      throw new Error('Il certificato non è stato generato correttamente')
    }
    
    // Check if the response is HTML (error page) instead of PDF
    const contentType = response.headers['content-type']
    if (contentType && contentType.includes('text/html')) {
      throw new Error('Il servizio di certificati non è ancora disponibile')
    }
    
    // Create download link
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `certificato-${props.course.title.replace(/\s+/g, '-').toLowerCase()}.pdf`)
    document.body.appendChild(link)
    link.click()
    link.remove()
    window.URL.revokeObjectURL(url)
    
    // Show success message
    alert('Certificato scaricato con successo!')
    
  } catch (error) {
    console.error('Error downloading certificate:', error)
    
    // Show user-friendly error message
    if (error.response?.status === 404) {
      alert('Certificato non ancora disponibile. Riprova tra qualche minuto.')
    } else if (error.response?.status === 500) {
      alert('Errore del server durante la generazione del certificato. Riprova più tardi.')
    } else if (error.message.includes('servizio di certificati')) {
      alert('Il servizio di certificati non è ancora disponibile. Il certificato sarà disponibile nella sezione "I miei attestati" della dashboard.')
    } else {
      alert('Errore durante il download del certificato. Il certificato sarà disponibile nella sezione "I miei attestati" della dashboard.')
    }
  } finally {
    certificateLoading.value = false
  }
}

const shareCertificate = () => {
  if (navigator.share) {
    navigator.share({
      title: `Ho completato il corso: ${props.course.title}`,
      text: `Ho appena completato con successo il corso "${props.course.title}" su Campus Digitale Forma!`,
      url: window.location.origin
    })
  } else {
    // Fallback: copy to clipboard
    const text = `Ho appena completato con successo il corso "${props.course.title}" su Campus Digitale Forma!`
    navigator.clipboard.writeText(text).then(() => {
      // Show success message
      alert('Testo copiato negli appunti!')
    })
  }
}

const shareOnSocial = (platform) => {
  const text = `Ho appena completato con successo il corso "${props.course.title}" su Campus Digitale Forma!`
  const url = window.location.origin
  
  let shareUrl = ''
  
  switch (platform) {
    case 'linkedin':
      shareUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(url)}&summary=${encodeURIComponent(text)}`
      break
    case 'twitter':
      shareUrl = `https://twitter.com/intent/tweet?text=${encodeURIComponent(text)}&url=${encodeURIComponent(url)}`
      break
    case 'facebook':
      shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}&quote=${encodeURIComponent(text)}`
      break
  }
  
  if (shareUrl) {
    window.open(shareUrl, '_blank', 'width=600,height=400')
  }
}

const goToDashboard = () => {
  emit('go-to-dashboard')
}

const restartCourse = () => {
  emit('restart-course')
}
</script>

<style scoped>
.completion-modal {
  @apply w-full max-w-4xl;
}

/* Animation for success icon */
@keyframes bounce {
  0%, 20%, 53%, 80%, 100% {
    transform: translate3d(0,0,0);
  }
  40%, 43% {
    transform: translate3d(0, -30px, 0);
  }
  70% {
    transform: translate3d(0, -15px, 0);
  }
  90% {
    transform: translate3d(0, -4px, 0);
  }
}

.animate-bounce {
  animation: bounce 2s infinite;
}

/* Hover effects for social buttons */
.social-button:hover {
  @apply transform scale-110 transition-transform duration-200;
}
</style>
