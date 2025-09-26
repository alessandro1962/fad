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

const emit = defineEmits(['downloaded'])

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


const viewPublic = () => {
  window.open(publicUrl.value, '_blank')
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
