<template>
    <AppLayout>
        <!-- Admin Header -->
        <div class="bg-gradient-to-r from-cdf-deep to-cdf-teal rounded-3xl p-8 mb-8 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold mb-2">Pannello Amministratore</h1>
                    <p class="text-lg opacity-90">Gestisci la piattaforma e monitora le attività</p>
                </div>
                <div class="hidden md:block">
                    <div class="w-24 h-24 bg-white/10 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Admin Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <StatCard
                title="Utenti Totali"
                :value="statistics.total_users?.value || '0'"
                icon="users"
                :change="statistics.total_users?.change || 0"
                :change-label="statistics.total_users?.change_label || 'rispetto al mese scorso'"
                :show-trend="true"
            />
            <StatCard
                title="Corsi Attivi"
                :value="statistics.active_courses?.value || '0'"
                icon="courses"
                :change="statistics.active_courses?.change || 0"
                :change-label="statistics.active_courses?.change_label || 'rispetto al mese scorso'"
            />
            <StatCard
                title="Completamenti"
                :value="statistics.completions?.value || '0'"
                icon="completed"
                :change="statistics.completions?.change || 0"
                :change-label="statistics.completions?.change_label || 'rispetto al mese scorso'"
            />
            <StatCard
                title="Ricavi Mensili"
                :value="statistics.monthly_revenue?.value || '€0'"
                icon="revenue"
                :change="statistics.monthly_revenue?.change || 0"
                :change-label="statistics.monthly_revenue?.change_label || 'rispetto al mese scorso'"
            />
        </div>

        <!-- Main Content Grid -->
        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Recent Activity -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-cdf-deep">Attività Recente</h2>
                        <button @click="loadStatistics" class="text-cdf-teal hover:text-cdf-deep transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <div v-if="loading" class="flex items-center justify-center py-8">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-cdf-teal"></div>
                    </div>
                    
                    <div v-else class="space-y-4">
                        <div 
                            v-for="activity in recentActivities" 
                            :key="activity.id"
                            class="flex items-start space-x-4 p-4 border border-cdf-slate200 rounded-xl"
                        >
                            <div 
                                class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                                :class="activity.bgClass"
                            >
                                <svg class="w-5 h-5" :class="activity.iconClass" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-cdf-deep">{{ activity.title }}</h3>
                                <p class="text-sm text-cdf-slate700">{{ activity.description }}</p>
                                <p class="text-xs text-cdf-slate700 mt-1">{{ activity.timestamp }}</p>
                            </div>
                        </div>
                        
                        <div v-if="recentActivities.length === 0" class="text-center py-8 text-cdf-slate500">
                            <p>Nessuna attività recente</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6">
                    <h3 class="font-bold text-cdf-deep mb-4">Azioni Rapide</h3>
                    <div class="space-y-3">
                        <router-link 
                            to="/admin/courses"
                            class="w-full btn-primary block text-center"
                        >
                            Gestisci Corsi
                        </router-link>
                        <router-link 
                            to="/admin/users"
                            class="w-full btn-secondary block text-center"
                        >
                            Gestisci Utenti
                        </router-link>
                        <router-link 
                            to="/admin/organizations"
                            class="w-full btn-secondary block text-center"
                        >
                            Gestisci Aziende
                        </router-link>
                        <router-link 
                            to="/admin/resellers"
                            class="w-full btn-secondary block text-center"
                        >
                            Gestisci Rivenditori
                        </router-link>
                        <router-link 
                            to="/admin/analytics"
                            class="w-full btn-secondary block text-center"
                        >
                            Report Analytics
                        </router-link>
                        <router-link 
                            to="/admin/certificate-templates"
                            class="w-full btn-secondary block text-center"
                        >
                            Template Certificati
                        </router-link>
                        <router-link 
                            to="/admin/settings"
                            class="w-full btn-secondary block text-center"
                        >
                            Impostazioni Sistema
                        </router-link>
                    </div>
                </div>

                <!-- System Status -->
                <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6">
                    <h3 class="font-bold text-cdf-deep mb-4">Stato Sistema</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-cdf-slate700">Database</span>
                            <div class="flex items-center space-x-2">
                                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                <span class="text-sm text-green-600 font-medium">Operativo</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-cdf-slate700">Storage</span>
                            <div class="flex items-center space-x-2">
                                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                <span class="text-sm text-green-600 font-medium">Operativo</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-cdf-slate700">Email</span>
                            <div class="flex items-center space-x-2">
                                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                <span class="text-sm text-green-600 font-medium">Operativo</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-cdf-slate700">CDN</span>
                            <div class="flex items-center space-x-2">
                                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                <span class="text-sm text-green-600 font-medium">Operativo</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import AppLayout from '@/components/Layout/AppLayout.vue';
import StatCard from '@/components/Dashboard/StatCard.vue';
import api from '@/api'

// Reactive data
const loading = ref(false)
const statistics = ref({})
const recentActivities = ref([])

// Load dashboard statistics
const loadStatistics = async () => {
  try {
    loading.value = true
    const response = await api.get('/v1/admin/dashboard/statistics')
    
    statistics.value = response.data.data.statistics
    recentActivities.value = response.data.data.recent_activities
    
    console.log('Dashboard statistics loaded:', response.data.data)
  } catch (error) {
    console.error('Error loading dashboard statistics:', error)
  } finally {
    loading.value = false
  }
}

// Load statistics on component mount
onMounted(() => {
  loadStatistics()
})
</script>
