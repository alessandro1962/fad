<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-xl shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
      <!-- Header -->
      <div class="flex items-center justify-between p-6 border-b border-cdf-slate200">
        <div>
          <h2 class="text-xl font-bold text-cdf-deep">Statistiche Corso</h2>
          <p class="text-cdf-slate700 mt-1">{{ course?.title }}</p>
        </div>
        <button
          @click="$emit('close')"
          class="p-2 text-cdf-slate400 hover:text-cdf-slate600 hover:bg-cdf-slate200 rounded-lg transition-all"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <!-- Content -->
      <div v-if="loading" class="p-8 text-center">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-cdf-teal mx-auto"></div>
        <p class="text-cdf-slate700 mt-2">Caricamento statistiche...</p>
      </div>

      <div v-else-if="stats" class="p-6">
        <!-- Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <div class="bg-cdf-sand rounded-xl p-6 border border-cdf-slate200">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-cdf-slate700">Iscrizioni Totali</p>
                <p class="text-2xl font-bold text-cdf-deep">{{ stats.total_enrollments }}</p>
              </div>
              <div class="p-3 bg-cdf-teal/20 rounded-lg">
                <svg class="w-6 h-6 text-cdf-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-cdf-sand rounded-xl p-6 border border-cdf-slate200">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-cdf-slate700">Iscrizioni Attive</p>
                <p class="text-2xl font-bold text-cdf-deep">{{ stats.active_enrollments }}</p>
              </div>
              <div class="p-3 bg-green-100 rounded-lg">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-cdf-sand rounded-xl p-6 border border-cdf-slate200">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-cdf-slate700">Completati</p>
                <p class="text-2xl font-bold text-cdf-deep">{{ stats.completed_enrollments }}</p>
              </div>
              <div class="p-3 bg-cdf-amber/20 rounded-lg">
                <svg class="w-6 h-6 text-cdf-amber" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-cdf-sand rounded-xl p-6 border border-cdf-slate200">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-cdf-slate700">Progresso Medio</p>
                <p class="text-2xl font-bold text-cdf-deep">{{ Math.round(stats.average_progress) }}%</p>
              </div>
              <div class="p-3 bg-blue-100 rounded-lg">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Course Structure -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
          <div class="bg-white border border-cdf-slate200 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-cdf-deep mb-4">Struttura Corso</h3>
            <div class="space-y-4">
              <div class="flex items-center justify-between">
                <span class="text-cdf-slate700">Moduli</span>
                <span class="font-semibold text-cdf-deep">{{ stats.total_modules }}</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-cdf-slate700">Lezioni</span>
                <span class="font-semibold text-cdf-deep">{{ stats.total_lessons }}</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-cdf-slate700">Durata Totale</span>
                <span class="font-semibold text-cdf-deep">{{ formatDuration(stats.total_duration) }}</span>
              </div>
            </div>
          </div>

          <div class="bg-white border border-cdf-slate200 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-cdf-deep mb-4">Completion Rate</h3>
            <div class="space-y-4">
              <div>
                <div class="flex items-center justify-between mb-2">
                  <span class="text-sm text-cdf-slate700">Tasso di completamento</span>
                  <span class="text-sm font-semibold text-cdf-deep">
                    {{ stats.total_enrollments > 0 ? Math.round((stats.completed_enrollments / stats.total_enrollments) * 100) : 0 }}%
                  </span>
                </div>
                <div class="w-full bg-cdf-slate200 rounded-full h-2">
                  <div 
                    class="bg-cdf-teal h-2 rounded-full transition-all duration-300"
                    :style="{ width: stats.total_enrollments > 0 ? (stats.completed_enrollments / stats.total_enrollments) * 100 + '%' : '0%' }"
                  ></div>
                </div>
              </div>
              
              <div>
                <div class="flex items-center justify-between mb-2">
                  <span class="text-sm text-cdf-slate700">Progresso medio</span>
                  <span class="text-sm font-semibold text-cdf-deep">{{ Math.round(stats.average_progress) }}%</span>
                </div>
                <div class="w-full bg-cdf-slate200 rounded-full h-2">
                  <div 
                    class="bg-cdf-amber h-2 rounded-full transition-all duration-300"
                    :style="{ width: stats.average_progress + '%' }"
                  ></div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Enrollment Status Breakdown -->
        <div class="bg-white border border-cdf-slate200 rounded-xl p-6">
          <h3 class="text-lg font-semibold text-cdf-deep mb-4">Stato Iscrizioni</h3>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center">
              <div class="text-3xl font-bold text-green-600 mb-2">{{ stats.active_enrollments }}</div>
              <div class="text-sm text-cdf-slate700">Attive</div>
              <div class="text-xs text-cdf-slate600 mt-1">
                {{ stats.total_enrollments > 0 ? Math.round((stats.active_enrollments / stats.total_enrollments) * 100) : 0 }}% del totale
              </div>
            </div>
            <div class="text-center">
              <div class="text-3xl font-bold text-cdf-amber mb-2">{{ stats.completed_enrollments }}</div>
              <div class="text-sm text-cdf-slate700">Completate</div>
              <div class="text-xs text-cdf-slate600 mt-1">
                {{ stats.total_enrollments > 0 ? Math.round((stats.completed_enrollments / stats.total_enrollments) * 100) : 0 }}% del totale
              </div>
            </div>
            <div class="text-center">
              <div class="text-3xl font-bold text-cdf-slate400 mb-2">
                {{ stats.total_enrollments - stats.active_enrollments - stats.completed_enrollments }}
              </div>
              <div class="text-sm text-cdf-slate700">Altre</div>
              <div class="text-xs text-cdf-slate600 mt-1">
                Pausate/Cancellate
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Error State -->
      <div v-else class="p-8 text-center">
        <div class="text-red-400 mb-4">
          <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
        </div>
        <h3 class="text-lg font-medium text-cdf-deep mb-2">Errore nel caricamento</h3>
        <p class="text-cdf-slate700">Impossibile caricare le statistiche del corso</p>
      </div>

      <!-- Footer -->
      <div class="flex items-center justify-end p-6 border-t border-cdf-slate200">
        <button
          @click="$emit('close')"
          class="px-4 py-2 bg-cdf-deep text-white rounded-lg hover:brightness-110 transition-all"
        >
          Chiudi
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const props = defineProps({
  course: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['close'])

const loading = ref(false)
const stats = ref(null)

const loadStats = async () => {
  loading.value = true
  try {
    const response = await fetch(`/api/admin/courses/${props.course.id}/statistics`, {
      headers: {
        'Accept': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('token')}`
      }
    })

    if (!response.ok) throw new Error('Errore nel caricamento delle statistiche')

    const data = await response.json()
    stats.value = data.data
  } catch (error) {
    console.error('Errore:', error)
  } finally {
    loading.value = false
  }
}

const formatDuration = (minutes) => {
  const hours = Math.floor(minutes / 60)
  const mins = minutes % 60
  if (hours > 0) {
    return mins > 0 ? `${hours}h ${mins}m` : `${hours}h`
  }
  return `${mins}m`
}

onMounted(() => {
  loadStats()
})
</script>
