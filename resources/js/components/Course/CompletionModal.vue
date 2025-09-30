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
          Non dimenticarti di andare nella sezione "I miei certificati" e scaricare il tuo attestato e di condividerlo.
        </p>
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
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

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

const emit = defineEmits(['go-to-dashboard', 'close'])

const goToDashboard = () => {
  emit('go-to-dashboard')
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
