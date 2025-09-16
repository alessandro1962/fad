<template>
  <div class="certificate-card bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6 hover:shadow-lg transition-all duration-300">
    <!-- Certificate Header -->
    <div class="flex items-start justify-between mb-4">
      <div class="flex items-center space-x-3">
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-cdf-teal to-cdf-deep flex items-center justify-center text-white">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
          </svg>
        </div>
        <div>
          <h3 class="font-bold text-cdf-deep">{{ certificate.title }}</h3>
          <p class="text-sm text-cdf-slate700">{{ certificate.description }}</p>
        </div>
      </div>
      <div class="text-right">
        <div class="text-xs text-cdf-slate600">
          {{ formatDate(certificate.issued_at) }}
        </div>
        <div class="text-xs text-cdf-slate600">
          {{ certificate.hours_total }}h
        </div>
      </div>
    </div>

    <!-- Certificate Type Badge -->
    <div class="mb-4">
      <span class="px-3 py-1 rounded-full text-xs font-semibold"
            :class="certificate.kind === 'course' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800'">
        {{ certificate.kind === 'course' ? 'Certificato Corso' : 'Certificato Percorso' }}
      </span>
    </div>

    <!-- Certificate Actions -->
    <div class="flex gap-3">
      <button
        @click="downloadCertificate"
        class="flex-1 bg-cdf-teal text-white px-4 py-2 rounded-xl font-semibold hover:bg-cdf-deep transition-colors flex items-center justify-center space-x-2"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        <span>Scarica PDF</span>
      </button>
      
      <button
        @click="shareCertificate"
        class="px-4 py-2 border border-cdf-slate200 text-cdf-slate700 rounded-xl font-semibold hover:bg-cdf-slate50 transition-colors flex items-center justify-center"
        title="Condividi certificato"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
        </svg>
      </button>
      
      <button
        @click="viewPublic"
        class="px-4 py-2 border border-cdf-slate200 text-cdf-slate700 rounded-xl font-semibold hover:bg-cdf-slate50 transition-colors flex items-center justify-center"
        title="Visualizza pubblico"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
        </svg>
      </button>
    </div>

    <!-- Certificate Metadata -->
    <div v-if="certificate.metadata" class="mt-4 pt-4 border-t border-cdf-slate200">
      <div class="grid grid-cols-2 gap-4 text-xs text-cdf-slate600">
        <div v-if="certificate.metadata.reference_title">
          <span class="font-semibold">Corso/Percorso:</span>
          <div class="mt-1">{{ certificate.metadata.reference_title }}</div>
        </div>
        <div v-if="certificate.metadata.generated_at">
          <span class="font-semibold">Generato il:</span>
          <div class="mt-1">{{ formatDate(certificate.metadata.generated_at) }}</div>
        </div>
      </div>
    </div>

    <!-- Share Modal -->
    <div v-if="showShareModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-xl shadow-xl max-w-md w-full">
        <div class="p-6">
          <h3 class="text-lg font-bold text-cdf-deep mb-4">Condividi Certificato</h3>
          
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-cdf-deep mb-2">Link Pubblico</label>
              <div class="flex">
                <input
                  :value="publicUrl"
                  readonly
                  class="flex-1 px-3 py-2 border border-cdf-slate200 rounded-l-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
                />
                <button
                  @click="copyToClipboard"
                  class="px-4 py-2 bg-cdf-teal text-white rounded-r-lg hover:bg-cdf-deep transition-colors"
                >
                  Copia
                </button>
              </div>
            </div>
            
            <div class="flex gap-3">
              <button
                @click="shareOnLinkedIn"
                class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700 transition-colors"
              >
                LinkedIn
              </button>
              <button
                @click="shareOnTwitter"
                class="flex-1 bg-blue-400 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-500 transition-colors"
              >
                Twitter
              </button>
            </div>
          </div>
          
          <div class="flex justify-end gap-3 mt-6">
            <button
              @click="showShareModal = false"
              class="px-4 py-2 text-cdf-slate600 border border-cdf-slate200 rounded-lg hover:bg-cdf-slate50 transition-colors"
            >
              Chiudi
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import api from '@/api'

const props = defineProps({
  certificate: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['downloaded', 'shared'])

// State
const showShareModal = ref(false)
const copying = ref(false)

// Computed
const publicUrl = computed(() => {
  return `${window.location.origin}/certificates/${props.certificate.public_uid}`
})

// Methods
const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('it-IT', { 
    day: 'numeric', 
    month: 'long',
    year: 'numeric'
  })
}

const downloadCertificate = async () => {
  try {
    const response = await api.get(`/v1/certificates/${props.certificate.id}/download`, {
      responseType: 'blob'
    })
    
    // Create blob and download
    const blob = new Blob([response.data], { type: 'application/pdf' })
    const url = window.URL.createObjectURL(blob)
    const link = document.createElement('a')
    link.href = url
    link.download = `${props.certificate.title}.pdf`
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
    window.URL.revokeObjectURL(url)
    
    emit('downloaded', props.certificate)
  } catch (error) {
    console.error('Errore nel download del certificato:', error)
  }
}

const shareCertificate = () => {
  showShareModal.value = true
  emit('shared', props.certificate)
}

const viewPublic = () => {
  window.open(publicUrl.value, '_blank')
}

const copyToClipboard = async () => {
  try {
    copying.value = true
    await navigator.clipboard.writeText(publicUrl.value)
    
    // Show success message
    const button = event.target
    const originalText = button.textContent
    button.textContent = 'Copiato!'
    button.classList.add('bg-green-600')
    
    setTimeout(() => {
      button.textContent = originalText
      button.classList.remove('bg-green-600')
      copying.value = false
    }, 2000)
  } catch (error) {
    console.error('Errore nella copia:', error)
  }
}

const shareOnLinkedIn = () => {
  const url = `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(publicUrl.value)}`
  window.open(url, '_blank')
}

const shareOnTwitter = () => {
  const text = `Ho completato il corso "${props.certificate.title}" su Campus Digitale Forma!`
  const url = `https://twitter.com/intent/tweet?text=${encodeURIComponent(text)}&url=${encodeURIComponent(publicUrl.value)}`
  window.open(url, '_blank')
}
</script>

<style scoped>
.certificate-card {
  @apply transition-all duration-300;
}

.certificate-card:hover {
  @apply transform -translate-y-1;
}

/* Animation for copy button */
.copy-button {
  @apply transition-all duration-200;
}

.copy-button:hover {
  @apply transform scale-105;
}
</style>
