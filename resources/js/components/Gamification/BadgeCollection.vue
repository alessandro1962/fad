<template>
  <AppLayout>
    <!-- Header -->
    <div class="mb-8">
      <div class="flex items-center gap-4 mb-4">
        <button
          @click="goToDashboard"
          class="flex items-center gap-2 px-3 py-2 text-cdf-slate600 hover:text-cdf-deep hover:bg-cdf-slate100 rounded-lg transition-colors"
          title="Torna alla Dashboard"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
          </svg>
          Dashboard
        </button>
      </div>
      <div class="bg-gradient-to-r from-cdf-deep to-cdf-teal rounded-3xl p-8 text-white">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold mb-2">La Tua Collezione Badge</h1>
            <p class="text-lg opacity-90">Mostra i tuoi traguardi e conquiste</p>
          </div>
          <div class="text-right">
            <div class="text-2xl font-bold">{{ earnedBadges.length }} / {{ allBadges.length }}</div>
            <div class="text-sm opacity-80">Badge Ottenuti</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6 mb-8">
      <div class="flex flex-wrap gap-4">
        <button
          v-for="category in categories"
          :key="category.value"
          @click="selectedCategory = category.value"
          class="px-4 py-2 rounded-xl font-semibold transition-colors"
          :class="selectedCategory === category.value 
            ? 'bg-cdf-teal text-white' 
            : 'bg-cdf-slate200 text-cdf-slate700 hover:bg-cdf-slate300'"
        >
          {{ category.label }}
        </button>
      </div>
    </div>

    <!-- Badge Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <div
        v-for="badge in filteredBadges"
        :key="badge.id"
        class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6 text-center hover:shadow-lg transition-all duration-300"
        :class="badge.earned ? 'ring-2 ring-cdf-teal/20' : 'opacity-60'"
      >
        <!-- Badge Icon -->
        <div class="w-20 h-20 mx-auto mb-4 rounded-2xl flex items-center justify-center text-3xl relative"
             :style="{ 
               backgroundColor: badge.earned ? badge.color + '20' : '#f3f4f6',
               color: badge.earned ? badge.color : '#9ca3af'
             }">
          {{ badge.icon }}
          
          <!-- Earned indicator -->
          <div v-if="badge.earned" class="absolute -top-2 -right-2 w-6 h-6 bg-cdf-teal rounded-full flex items-center justify-center">
            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
            </svg>
          </div>
        </div>

        <!-- Badge Info -->
        <h3 class="font-bold text-cdf-deep mb-2">{{ badge.name }}</h3>
        <p class="text-sm text-cdf-slate700 mb-4">{{ badge.description }}</p>

        <!-- Badge Status -->
        <div v-if="badge.earned" class="space-y-2">
          <div class="text-xs text-cdf-slate600">
            Ottenuto il {{ formatDate(badge.awarded_at) }}
          </div>
          <div v-if="badge.reason" class="text-xs text-cdf-slate600 italic">
            "{{ badge.reason }}"
          </div>
        </div>
        
        <div v-else class="space-y-2">
          <div class="text-xs text-cdf-slate600">
            {{ badge.criteria_description }}
          </div>
          <div v-if="badge.progress" class="w-full bg-cdf-slate200 rounded-full h-2">
            <div 
              class="bg-cdf-teal h-2 rounded-full transition-all duration-500"
              :style="{ width: `${badge.progress}%` }"
            ></div>
          </div>
          <div v-if="badge.progress" class="text-xs text-cdf-slate600">
            {{ badge.progress_text }}
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="filteredBadges.length === 0" class="text-center py-12">
      <div class="w-24 h-24 mx-auto mb-6 rounded-full bg-cdf-slate200 flex items-center justify-center">
        <svg class="w-12 h-12 text-cdf-slate700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
      </div>
      <h3 class="text-xl font-bold text-cdf-deep mb-2">Nessun badge trovato</h3>
      <p class="text-cdf-slate700">Prova a cambiare filtro o categoria</p>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import AppLayout from '@/components/Layout/AppLayout.vue'
import api from '@/api'

// Router
const router = useRouter()

// State
const allBadges = ref([])
const earnedBadges = ref([])
const selectedCategory = ref('all')
const loading = ref(true)

// Computed
const categories = computed(() => [
  { value: 'all', label: 'Tutti' },
  { value: 'learning', label: 'Apprendimento' },
  { value: 'quiz', label: 'Quiz' },
  { value: 'achievement', label: 'Traguardi' },
  { value: 'milestone', label: 'Pietre Miliari' }
])

const filteredBadges = computed(() => {
  let badges = allBadges.value.map(badge => {
    const earned = earnedBadges.value.find(eb => eb.badge_id === badge.id)
    return {
      ...badge,
      earned: !!earned,
      awarded_at: earned?.awarded_at,
      reason: earned?.reason,
      progress: earned ? 100 : calculateProgress(badge),
      progress_text: earned ? null : getProgressText(badge),
      criteria_description: getCriteriaDescription(badge)
    }
  })

  if (selectedCategory.value !== 'all') {
    badges = badges.filter(badge => badge.category === selectedCategory.value)
  }

  return badges
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

const calculateProgress = (badge) => {
  // This would be calculated based on user's current progress
  // For now, return a random progress for demonstration
  return Math.floor(Math.random() * 100)
}

const getProgressText = (badge) => {
  const progress = calculateProgress(badge)
  const criteria = badge.criteria
  
  if (criteria.courses_completed) {
    const current = Math.floor((progress / 100) * criteria.courses_completed)
    return `${current} / ${criteria.courses_completed} corsi completati`
  }
  
  if (criteria.lessons_completed) {
    const current = Math.floor((progress / 100) * criteria.lessons_completed)
    return `${current} / ${criteria.lessons_completed} lezioni completate`
  }
  
  if (criteria.hours_watched) {
    const current = Math.floor((progress / 100) * criteria.hours_watched)
    return `${current} / ${criteria.hours_watched} ore di apprendimento`
  }
  
  return `${progress}% completato`
}

const getCriteriaDescription = (badge) => {
  const criteria = badge.criteria
  
  if (criteria.courses_completed) {
    return `Completa ${criteria.courses_completed} corsi`
  }
  
  if (criteria.lessons_completed) {
    return `Completa ${criteria.lessons_completed} lezioni`
  }
  
  if (criteria.hours_watched) {
    return `Completa ${criteria.hours_watched} ore di apprendimento`
  }
  
  if (criteria.quizzes_passed) {
    return `Supera ${criteria.quizzes_passed} quiz`
  }
  
  if (criteria.perfect_scores) {
    return `Ottieni ${criteria.perfect_scores} punteggi perfetti`
  }
  
  return 'Completa i requisiti per sbloccare questo badge'
}

const loadAllBadges = async () => {
  try {
    const response = await api.get('/v1/badges')
    allBadges.value = response.data.data
  } catch (error) {
    console.error('Errore nel caricamento badge:', error)
  }
}

const loadEarnedBadges = async () => {
  try {
    const response = await api.get('/v1/badges/mine')
    earnedBadges.value = response.data.data
  } catch (error) {
    console.error('Errore nel caricamento badge ottenuti:', error)
  }
}

// Methods
const goToDashboard = () => {
  router.push('/dashboard')
}

// Lifecycle
onMounted(async () => {
  loading.value = true
  await Promise.all([
    loadAllBadges(),
    loadEarnedBadges()
  ])
  loading.value = false
})
</script>

<style scoped>
.badge-collection {
  @apply max-w-7xl mx-auto px-6 py-8;
}

/* Badge hover effects */
.badge-card {
  @apply transition-all duration-300;
}

.badge-card:hover {
  @apply transform -translate-y-1;
}

/* Progress bar animation */
.progress-bar {
  transition: width 0.5s ease-in-out;
}

/* Category filter animation */
.category-filter {
  @apply transition-all duration-200;
}

.category-filter:hover {
  @apply transform scale-105;
}
</style>
