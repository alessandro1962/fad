<template>
  <div class="min-h-screen bg-cdf-sand">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-cdf-slate200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-6">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-cdf-teal to-cdf-deep flex items-center justify-center">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
              </svg>
            </div>
            <div>
              <h1 class="text-xl font-bold text-cdf-deep">Campus Digitale Forma</h1>
              <p class="text-sm text-cdf-slate700">Certificato Verificabile</p>
            </div>
          </div>
          <div class="text-right">
            <div class="text-sm text-cdf-slate600">
              Verificato il {{ formatDate(new Date()) }}
            </div>
            <div class="text-xs text-cdf-slate500">
              ID: {{ certificate?.public_uid }}
            </div>
          </div>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Loading State -->
      <div v-if="loading" class="text-center py-12">
        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-cdf-slate200 flex items-center justify-center">
          <svg class="animate-spin w-8 h-8 text-cdf-slate700" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
        </div>
        <p class="text-cdf-slate700">Caricamento certificato...</p>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="text-center py-12">
        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-red-100 flex items-center justify-center">
          <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
          </svg>
        </div>
        <h2 class="text-xl font-bold text-cdf-deep mb-2">Certificato Non Trovato</h2>
        <p class="text-cdf-slate700 mb-4">Il certificato richiesto non esiste o non è più disponibile.</p>
        <button
          @click="goHome"
          class="bg-cdf-teal text-white px-6 py-2 rounded-xl font-semibold hover:bg-cdf-deep transition-colors"
        >
          Torna alla Home
        </button>
      </div>

      <!-- Certificate Display -->
      <div v-else-if="certificate" class="bg-white rounded-2xl shadow-lg border border-cdf-slate200 overflow-hidden">
        <!-- Certificate Header -->
        <div class="bg-gradient-to-r from-cdf-deep to-cdf-teal p-8 text-white text-center">
          <div class="w-20 h-20 mx-auto mb-4 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center">
            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
            </svg>
          </div>
          <h1 class="text-3xl font-bold mb-2">Certificato di Completamento</h1>
          <p class="text-lg opacity-90">Campus Digitale Forma</p>
        </div>

        <!-- Certificate Content -->
        <div class="p-8">
          <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-cdf-deep mb-4">{{ certificate.title }}</h2>
            <p class="text-cdf-slate700 mb-6">{{ certificate.description }}</p>
            
            <!-- Certificate Details -->
            <div class="grid md:grid-cols-2 gap-6 mb-8">
              <div class="bg-cdf-sand rounded-xl p-6">
                <h3 class="font-semibold text-cdf-deep mb-2">Certificato a</h3>
                <p class="text-lg font-bold text-cdf-deep">{{ certificate.user_name }}</p>
                <p class="text-sm text-cdf-slate700">{{ certificate.user_email }}</p>
              </div>
              
              <div class="bg-cdf-sand rounded-xl p-6">
                <h3 class="font-semibold text-cdf-deep mb-2">Dettagli</h3>
                <div class="space-y-1 text-sm text-cdf-slate700">
                  <div>Rilasciato il: {{ formatDate(certificate.issued_at) }}</div>
                  <div>Durata: {{ certificate.hours_total }} ore</div>
                  <div>Tipo: {{ certificate.kind === 'course' ? 'Corso' : 'Percorso' }}</div>
                </div>
              </div>
            </div>

            <!-- Verification Status -->
            <div class="bg-green-50 border border-green-200 rounded-xl p-6 mb-8">
              <div class="flex items-center justify-center space-x-3">
                <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
                  <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="font-semibold text-green-800">Certificato Verificato</h3>
                  <p class="text-sm text-green-700">Questo certificato è autentico e verificabile</p>
                </div>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
              <button
                @click="downloadCertificate"
                class="bg-cdf-teal text-white px-6 py-3 rounded-xl font-semibold hover:bg-cdf-deep transition-colors flex items-center justify-center space-x-2"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span>Scarica PDF</span>
              </button>
              
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-cdf-slate200 mt-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="text-center">
          <div class="flex items-center justify-center space-x-3 mb-4">
            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-cdf-teal to-cdf-deep flex items-center justify-center">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
              </svg>
            </div>
            <span class="text-lg font-bold text-cdf-deep">Campus Digitale Forma</span>
          </div>
          <p class="text-cdf-slate700 text-sm">
            Trasforma l'apprendimento in valore
          </p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/api'

const route = useRoute()
const router = useRouter()

// State
const certificate = ref(null)
const loading = ref(true)
const error = ref(false)

// Methods
const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('it-IT', { 
    day: 'numeric', 
    month: 'long',
    year: 'numeric'
  })
}

const loadCertificate = async () => {
  try {
    loading.value = true
    const response = await api.get(`/v1/certificates/public/${route.params.uid}`)
    certificate.value = response.data.data
  } catch (err) {
    console.error('Errore nel caricamento certificato:', err)
    error.value = true
  } finally {
    loading.value = false
  }
}

const downloadCertificate = async () => {
  try {
    const response = await api.get(`/v1/certificates/public/${route.params.uid}/download`, {
      responseType: 'blob'
    })
    
    // Create blob and download
    const blob = new Blob([response.data], { type: 'application/pdf' })
    const url = window.URL.createObjectURL(blob)
    const link = document.createElement('a')
    link.href = url
    link.download = `${certificate.value.title}.pdf`
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
    window.URL.revokeObjectURL(url)
  } catch (error) {
    console.error('Errore nel download del certificato:', error)
  }
}


const goHome = () => {
  router.push('/')
}

// Lifecycle
onMounted(() => {
  loadCertificate()
})
</script>

<style scoped>
/* Custom styles for the public certificate page */
.certificate-display {
  @apply transition-all duration-300;
}

.certificate-display:hover {
  @apply shadow-xl;
}

/* Animation for verification badge */
.verification-badge {
  @apply animate-pulse;
}
</style>
