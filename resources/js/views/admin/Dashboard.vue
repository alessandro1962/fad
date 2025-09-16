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
                value="1,247"
                icon="users"
                :change="12"
                change-label="rispetto al mese scorso"
                :show-trend="true"
            />
            <StatCard
                title="Corsi Attivi"
                value="23"
                icon="courses"
                :change="8"
                change-label="rispetto al mese scorso"
            />
            <StatCard
                title="Completamenti"
                value="456"
                icon="completed"
                :change="25"
                change-label="rispetto al mese scorso"
            />
            <StatCard
                title="Ricavi Mensili"
                value="€12,450"
                icon="revenue"
                :change="18"
                change-label="rispetto al mese scorso"
            />
        </div>

        <!-- Main Content Grid -->
        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Recent Activity -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6">
                    <h2 class="text-xl font-bold text-cdf-deep mb-6">Attività Recente</h2>
                    <div class="space-y-4">
                        <div 
                            v-for="activity in recentActivities" 
                            :key="activity.id"
                            class="flex items-start space-x-4 p-4 border border-cdf-slate200 rounded-xl"
                        >
                            <div 
                                class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                                :class="activity.bgClass"
                            >
                                <component :is="activity.icon" class="w-5 h-5" :class="activity.iconClass" />
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-cdf-deep">{{ activity.title }}</h3>
                                <p class="text-sm text-cdf-slate700">{{ activity.description }}</p>
                                <p class="text-xs text-cdf-slate700 mt-1">{{ activity.timestamp }}</p>
                            </div>
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
                            to="/admin/analytics"
                            class="w-full btn-secondary block text-center"
                        >
                            Report Analytics
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
import AppLayout from '@/components/Layout/AppLayout.vue';
import StatCard from '@/components/Dashboard/StatCard.vue';

// Sample admin data
const recentActivities = [
    {
        id: 1,
        title: 'Nuovo utente registrato',
        description: 'Mario Rossi si è registrato alla piattaforma',
        timestamp: '2 minuti fa',
        icon: 'svg',
        bgClass: 'bg-cdf-teal/10',
        iconClass: 'text-cdf-teal'
    },
    {
        id: 2,
        title: 'Corso completato',
        description: 'Anna Bianchi ha completato "Cybersecurity Base"',
        timestamp: '15 minuti fa',
        icon: 'svg',
        bgClass: 'bg-cdf-amber/10',
        iconClass: 'text-cdf-amber'
    },
    {
        id: 3,
        title: 'Nuovo acquisto',
        description: 'Pacchetto Full Vision acquistato da Azienda XYZ',
        timestamp: '1 ora fa',
        icon: 'svg',
        bgClass: 'bg-green-100',
        iconClass: 'text-green-600'
    },
    {
        id: 4,
        title: 'Report generato',
        description: 'Report mensile analytics completato',
        timestamp: '2 ore fa',
        icon: 'svg',
        bgClass: 'bg-cdf-deep/10',
        iconClass: 'text-cdf-deep'
    }
];
</script>
