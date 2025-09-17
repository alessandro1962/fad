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
            <h1 class="text-3xl font-bold mb-2">Il Tuo Progresso</h1>
            <p class="text-lg opacity-90">Continua il tuo percorso di apprendimento e sblocca nuovi traguardi</p>
          </div>
          <div class="text-right">
            <div class="text-2xl font-bold">{{ userLevel.name }}</div>
            <div class="text-sm opacity-80">Livello {{ userLevel.level }}</div>
          </div>
        </div>
      </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-8">
      <!-- Main Content -->
      <div class="lg:col-span-2 space-y-8">
        <!-- Level Progress -->
        <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6">
          <h2 class="text-xl font-bold text-cdf-deep mb-6">Progresso Livello</h2>
          
          <div class="space-y-4">
            <!-- Current Level -->
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-4">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-cdf-teal to-cdf-deep flex items-center justify-center text-white font-bold text-xl">
                  {{ userLevel.level }}
                </div>
                <div>
                  <h3 class="font-bold text-cdf-deep">{{ userLevel.name }}</h3>
                  <p class="text-sm text-cdf-slate700">{{ userLevel.description }}</p>
                </div>
              </div>
              <div class="text-right">
                <div class="text-2xl font-bold text-cdf-deep">{{ userStats.totalPoints || 0 }}</div>
                <div class="text-sm text-cdf-slate700">Punti Totali</div>
              </div>
            </div>

            <!-- Progress Bar -->
            <div class="space-y-2">
              <div class="flex justify-between text-sm text-cdf-slate700">
                <span>{{ userStats.currentLevelPoints || 0 }} / {{ userStats.nextLevelPoints || 100 }} punti</span>
                <span>{{ Math.round(levelProgress) }}%</span>
              </div>
              <div class="w-full bg-cdf-slate200 rounded-full h-3">
                <div 
                  class="bg-gradient-to-r from-cdf-teal to-cdf-deep h-3 rounded-full transition-all duration-500"
                  :style="{ width: `${levelProgress}%` }"
                ></div>
              </div>
              <p class="text-sm text-cdf-slate600">
                {{ userStats.pointsToNextLevel || 100 }} punti per raggiungere il livello {{ (userLevel.level || 1) + 1 }}
              </p>
            </div>
          </div>
        </div>

        <!-- Recent Badges -->
        <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-cdf-deep">Badge Recenti</h2>
            <router-link to="/badges" class="text-cdf-teal hover:text-cdf-deep font-semibold">
              Vedi tutti
            </router-link>
          </div>
          
          <div v-if="recentBadges.length > 0" class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <div
              v-for="badge in recentBadges"
              :key="badge.id"
              class="text-center p-4 border border-cdf-slate200 rounded-xl hover:border-cdf-teal/50 transition-colors"
            >
              <div class="w-12 h-12 mx-auto mb-3 rounded-full flex items-center justify-center text-2xl"
                   :style="{ backgroundColor: badge.color + '20', color: badge.color }">
                {{ badge.icon }}
              </div>
              <h4 class="font-semibold text-cdf-deep text-sm">{{ badge.name }}</h4>
              <p class="text-xs text-cdf-slate700 mt-1">{{ formatDate(badge.awarded_at) }}</p>
            </div>
          </div>
          
          <div v-else class="text-center py-8">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-cdf-slate200 flex items-center justify-center">
              <svg class="w-8 h-8 text-cdf-slate700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <p class="text-cdf-slate700">Nessun badge ancora. Continua a imparare!</p>
          </div>
        </div>

        <!-- Learning Streak -->
        <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6">
          <h2 class="text-xl font-bold text-cdf-deep mb-6">Streak di Apprendimento</h2>
          
          <div class="flex items-center justify-between">
            <div>
              <div class="text-3xl font-bold text-cdf-deep">{{ userStats.learningStreak }}</div>
              <div class="text-sm text-cdf-slate700">Giorni consecutivi</div>
            </div>
            <div class="text-right">
              <div class="text-lg font-semibold text-cdf-deep">{{ userStats.totalLearningDays }}</div>
              <div class="text-sm text-cdf-slate700">Giorni totali</div>
            </div>
          </div>
          
          <!-- Streak Calendar -->
          <div class="mt-6">
            <div class="grid grid-cols-7 gap-1">
              <div
                v-for="day in streakCalendar"
                :key="day.date"
                class="aspect-square rounded flex items-center justify-center text-xs"
                :class="day.hasActivity ? 'bg-cdf-teal text-white' : 'bg-cdf-slate200 text-cdf-slate700'"
                :title="day.date"
              >
                {{ day.day }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="space-y-6">
        <!-- Quick Stats -->
        <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6">
          <h3 class="font-bold text-cdf-deep mb-4">Statistiche Rapide</h3>
          <div class="space-y-4">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center">
                  <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                  </svg>
                </div>
                <span class="text-cdf-slate700">Corsi Completati</span>
              </div>
              <span class="font-semibold text-cdf-deep">{{ userStats.completedCourses }}</span>
            </div>
            
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center">
                  <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                </div>
                <span class="text-cdf-slate700">Lezioni Completate</span>
              </div>
              <span class="font-semibold text-cdf-deep">{{ userStats.completedLessons }}</span>
            </div>
            
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center">
                  <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                  </svg>
                </div>
                <span class="text-cdf-slate700">Quiz Superati</span>
              </div>
              <span class="font-semibold text-cdf-deep">{{ userStats.passedQuizzes }}</span>
            </div>
            
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center">
                  <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                </div>
                <span class="text-cdf-slate700">Ore di Apprendimento</span>
              </div>
              <span class="font-semibold text-cdf-deep">{{ userStats.learningHours }}</span>
            </div>
          </div>
        </div>

        <!-- Achievement Progress -->
        <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6">
          <h3 class="font-bold text-cdf-deep mb-4">Prossimi Traguardi</h3>
          <div v-if="upcomingAchievements.length > 0" class="space-y-4">
            <div
              v-for="achievement in upcomingAchievements"
              :key="achievement.id"
              class="flex items-center space-x-3 p-3 border border-cdf-slate200 rounded-lg"
            >
              <div class="w-10 h-10 rounded-lg flex items-center justify-center text-lg"
                   :style="{ backgroundColor: achievement.color + '20', color: achievement.color }">
                {{ achievement.icon }}
              </div>
              <div class="flex-1">
                <h4 class="font-semibold text-cdf-deep text-sm">{{ achievement.name }}</h4>
                <div class="w-full bg-cdf-slate200 rounded-full h-1.5 mt-1">
                  <div 
                    class="h-1.5 rounded-full transition-all duration-300"
                    :style="{ 
                      width: `${Math.min(100, Math.max(0, achievement.progress || 0))}%`,
                      backgroundColor: achievement.color
                    }"
                  ></div>
                </div>
                <p class="text-xs text-cdf-slate700 mt-1">
                  {{ achievement.current || 0 }} / {{ achievement.target || 1 }}
                </p>
              </div>
            </div>
          </div>
          <div v-else class="text-center py-8">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-cdf-slate200 flex items-center justify-center">
              <svg class="w-8 h-8 text-cdf-slate700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <p class="text-cdf-slate700">Continua a imparare per sbloccare nuovi traguardi!</p>
          </div>
        </div>

        <!-- Leaderboard -->
        <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6">
          <h3 class="font-bold text-cdf-deep mb-4">Classifica</h3>
          <div class="space-y-3">
            <div
              v-for="(user, index) in leaderboard"
              :key="user.id"
              class="flex items-center space-x-3 p-2 rounded-lg"
              :class="user.isCurrentUser ? 'bg-cdf-teal/10 border border-cdf-teal/20' : ''"
            >
              <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold"
                   :class="index === 0 ? 'bg-yellow-100 text-yellow-800' :
                          index === 1 ? 'bg-gray-100 text-gray-800' :
                          index === 2 ? 'bg-amber-100 text-amber-800' :
                          'bg-cdf-slate200 text-cdf-slate700'">
                {{ index + 1 }}
              </div>
              <div class="flex-1">
                <div class="font-semibold text-cdf-deep text-sm">
                  {{ user.isCurrentUser ? 'Tu' : user.name }}
                </div>
                <div class="text-xs text-cdf-slate700">{{ user.points }} punti</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import { useRouter } from 'vue-router'
import AppLayout from '@/components/Layout/AppLayout.vue'
import api from '@/api'

// Router
const router = useRouter()

// State
const userStats = ref({
  totalPoints: 0,
  currentLevelPoints: 0,
  nextLevelPoints: 100,
  pointsToNextLevel: 100,
  completedCourses: 0,
  completedLessons: 0,
  passedQuizzes: 0,
  learningHours: 0,
  learningStreak: 0,
  totalLearningDays: 0
})

const userLevel = ref({
  level: 1,
  name: 'Principiante',
  description: 'Hai appena iniziato il tuo percorso di apprendimento'
})

const recentBadges = ref([])
const upcomingAchievements = ref([])
const leaderboard = ref([])
const streakCalendar = ref([])

// Computed
const levelProgress = computed(() => {
  const current = userStats.value.currentLevelPoints || 0
  const next = userStats.value.nextLevelPoints || 100
  
  if (next === 0) return 100
  return Math.min(100, (current / next) * 100)
})

// Methods
const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('it-IT', { 
    day: 'numeric', 
    month: 'short' 
  })
}

const loadUserStats = async () => {
  try {
    const response = await api.get('/v1/gamification/stats')
    
    // Map API data (snake_case) to frontend format (camelCase)
    const apiStats = response.data.data.stats
    const apiLevel = response.data.data.level
    
    userStats.value = {
      totalPoints: apiStats.total_points || 0,
      currentLevelPoints: apiStats.current_level_points || 0,
      nextLevelPoints: apiStats.next_level_points || 100,
      pointsToNextLevel: apiStats.points_to_next_level || 100,
      completedCourses: apiStats.completed_courses || 0,
      completedLessons: apiStats.completed_lessons || 0,
      passedQuizzes: apiStats.passed_quizzes || 0,
      learningHours: apiStats.learning_hours || 0,
      learningStreak: apiStats.learning_streak || 0,
      totalLearningDays: apiStats.total_learning_days || 0
    }
    
    userLevel.value = {
      level: apiLevel.level || 1,
      name: apiLevel.name || 'Principiante',
      description: apiLevel.description || 'Hai appena iniziato il tuo percorso di apprendimento'
    }
  } catch (error) {
    console.error('Errore nel caricamento statistiche:', error)
  }
}

const loadRecentBadges = async () => {
  try {
    const response = await api.get('/v1/badges/recent')
    recentBadges.value = response.data.data
  } catch (error) {
    console.error('Errore nel caricamento badge recenti:', error)
  }
}

const loadUpcomingAchievements = async () => {
  try {
    const response = await api.get('/v1/gamification/achievements')
    upcomingAchievements.value = response.data.data
  } catch (error) {
    console.error('Errore nel caricamento traguardi:', error)
  }
}

const loadLeaderboard = async () => {
  try {
    const response = await api.get('/v1/gamification/leaderboard')
    leaderboard.value = response.data.data
  } catch (error) {
    console.error('Errore nel caricamento classifica:', error)
  }
}

const generateStreakCalendar = () => {
  const calendar = []
  const today = new Date()
  
  // Generate last 30 days
  for (let i = 29; i >= 0; i--) {
    const date = new Date(today)
    date.setDate(date.getDate() - i)
    
    calendar.push({
      date: date.toISOString().split('T')[0],
      day: date.getDate(),
      hasActivity: Math.random() > 0.3 // Simulate activity
    })
  }
  
  streakCalendar.value = calendar
}

// Methods
const goToDashboard = () => {
  router.push('/dashboard')
}

// Lifecycle
onMounted(() => {
  loadUserStats()
  loadRecentBadges()
  loadUpcomingAchievements()
  loadLeaderboard()
  generateStreakCalendar()
})
</script>

<style scoped>
.gamification-dashboard {
  @apply max-w-7xl mx-auto px-6 py-8;
}

/* Animation for progress bars */
.progress-bar {
  transition: width 0.5s ease-in-out;
}

/* Hover effects */
.achievement-item:hover {
  @apply transform scale-105 transition-transform duration-200;
}

/* Streak calendar styling */
.streak-day {
  @apply transition-all duration-200;
}

.streak-day:hover {
  @apply transform scale-110;
}
</style>
