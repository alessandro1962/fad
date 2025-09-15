<template>
    <AppLayout>
        <!-- Welcome Section -->
        <div class="bg-gradient-to-r from-cdf-deep to-cdf-teal rounded-3xl p-8 mb-8 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold mb-2">
                        Benvenuto, {{ authStore.user?.first_name || 'Utente' }}! 👋
                    </h1>
                    <p class="text-lg opacity-90">
                        Continua il tuo percorso di formazione e raggiungi i tuoi obiettivi
                    </p>
                </div>
                <div class="hidden md:block">
                    <div class="w-24 h-24 bg-white/10 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <StatCard
                title="Corsi Attivi"
                value="3"
                icon="courses"
                :change="12"
                change-label="rispetto al mese scorso"
            />
            <StatCard
                title="Completati"
                value="1"
                icon="completed"
                :change="25"
                change-label="rispetto al mese scorso"
            />
            <StatCard
                title="Attestati"
                value="1"
                icon="certificates"
                :change="0"
                change-label="rispetto al mese scorso"
            />
            <StatCard
                title="Livello Attuale"
                value="Principiante"
                icon="level"
                :change="5"
                change-label="punti esperienza"
            />
        </div>

        <!-- Main Content Grid -->
        <div class="grid lg:grid-cols-3 gap-8">
            <!-- My Courses -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-cdf-deep">I Miei Corsi</h2>
                        <router-link to="/i-miei-corsi" class="text-cdf-teal hover:text-cdf-deep font-medium text-sm">
                            Vedi tutti →
                        </router-link>
                    </div>
                    
                    <div class="space-y-4">
                        <!-- Course Progress Item -->
                        <div class="border border-cdf-slate200 rounded-xl p-4 hover:border-cdf-teal/30 transition-colors duration-200">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-gradient-to-br from-cdf-teal/20 to-cdf-deep/20 rounded-xl flex items-center justify-center">
                                        <svg class="w-6 h-6 text-cdf-deep" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-cdf-deep">Cybersecurity Base</h3>
                                        <p class="text-sm text-cdf-slate700">Ultimo accesso: 2 giorni fa</p>
                                    </div>
                                </div>
                                <span class="text-sm font-semibold text-cdf-deep">75%</span>
                            </div>
                            <div class="w-full bg-cdf-slate200 rounded-full h-2 mb-3">
                                <div class="bg-gradient-to-r from-cdf-teal to-cdf-deep h-2 rounded-full transition-all duration-500" style="width: 75%"></div>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4 text-sm text-cdf-slate700">
                                    <span>⏱️ 3 ore</span>
                                    <span>📚 5 moduli</span>
                                </div>
                                <button class="px-4 py-2 bg-cdf-teal text-white rounded-lg text-sm font-medium hover:bg-cdf-teal/90 transition-colors duration-200">
                                    Continua
                                </button>
                            </div>
                        </div>

                        <!-- Course Progress Item -->
                        <div class="border border-cdf-slate200 rounded-xl p-4 hover:border-cdf-teal/30 transition-colors duration-200">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-gradient-to-br from-cdf-amber/20 to-cdf-deep/20 rounded-xl flex items-center justify-center">
                                        <svg class="w-6 h-6 text-cdf-deep" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-cdf-deep">GDPR per Incaricati</h3>
                                        <p class="text-sm text-cdf-slate700">Ultimo accesso: 1 settimana fa</p>
                                    </div>
                                </div>
                                <span class="text-sm font-semibold text-cdf-deep">30%</span>
                            </div>
                            <div class="w-full bg-cdf-slate200 rounded-full h-2 mb-3">
                                <div class="bg-gradient-to-r from-cdf-teal to-cdf-deep h-2 rounded-full transition-all duration-500" style="width: 30%"></div>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4 text-sm text-cdf-slate700">
                                    <span>⏱️ 4 ore</span>
                                    <span>📚 6 moduli</span>
                                </div>
                                <button class="px-4 py-2 bg-cdf-teal text-white rounded-lg text-sm font-medium hover:bg-cdf-teal/90 transition-colors duration-200">
                                    Continua
                                </button>
                            </div>
                        </div>

                        <!-- Course Progress Item -->
                        <div class="border border-cdf-slate200 rounded-xl p-4 hover:border-cdf-teal/30 transition-colors duration-200">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-gradient-to-br from-cdf-deep/20 to-cdf-teal/20 rounded-xl flex items-center justify-center">
                                        <svg class="w-6 h-6 text-cdf-deep" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-cdf-deep">NIS2 per Dipendenti</h3>
                                        <p class="text-sm text-cdf-slate700">Non ancora iniziato</p>
                                    </div>
                                </div>
                                <span class="text-sm font-semibold text-cdf-deep">0%</span>
                            </div>
                            <div class="w-full bg-cdf-slate200 rounded-full h-2 mb-3">
                                <div class="bg-gradient-to-r from-cdf-teal to-cdf-deep h-2 rounded-full transition-all duration-500" style="width: 0%"></div>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4 text-sm text-cdf-slate700">
                                    <span>⏱️ 2 ore</span>
                                    <span>📚 3 moduli</span>
                                </div>
                                <button class="px-4 py-2 bg-cdf-deep text-white rounded-lg text-sm font-medium hover:bg-cdf-deep/90 transition-colors duration-200">
                                    Inizia
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6">
                    <h3 class="font-bold text-cdf-deep mb-4">Azioni Rapide</h3>
                    <div class="space-y-3">
                        <router-link to="/catalogo" class="flex items-center p-3 rounded-xl hover:bg-cdf-sand transition-colors duration-200 group">
                            <div class="w-10 h-10 bg-cdf-teal/10 rounded-lg flex items-center justify-center mr-3 group-hover:bg-cdf-teal/20 transition-colors duration-200">
                                <svg class="w-5 h-5 text-cdf-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-cdf-deep">Sfoglia Catalogo</p>
                                <p class="text-sm text-cdf-slate700">Scopri nuovi corsi</p>
                            </div>
                        </router-link>
                        
                        <router-link to="/attestati" class="flex items-center p-3 rounded-xl hover:bg-cdf-sand transition-colors duration-200 group">
                            <div class="w-10 h-10 bg-cdf-amber/10 rounded-lg flex items-center justify-center mr-3 group-hover:bg-cdf-amber/20 transition-colors duration-200">
                                <svg class="w-5 h-5 text-cdf-amber" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-cdf-deep">I Miei Attestati</p>
                                <p class="text-sm text-cdf-slate700">Visualizza certificazioni</p>
                            </div>
                        </router-link>
                        
                        <router-link to="/calendario" class="flex items-center p-3 rounded-xl hover:bg-cdf-sand transition-colors duration-200 group">
                            <div class="w-10 h-10 bg-cdf-deep/10 rounded-lg flex items-center justify-center mr-3 group-hover:bg-cdf-deep/20 transition-colors duration-200">
                                <svg class="w-5 h-5 text-cdf-deep" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-cdf-deep">Calendario Eventi</p>
                                <p class="text-sm text-cdf-slate700">Prossimi appuntamenti</p>
                            </div>
                        </router-link>
                        
                        <router-link to="/supporto" class="flex items-center p-3 rounded-xl hover:bg-cdf-sand transition-colors duration-200 group">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-green-200 transition-colors duration-200">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 2.25a9.75 9.75 0 100 19.5 9.75 9.75 0 000-19.5z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-cdf-deep">Supporto</p>
                                <p class="text-sm text-cdf-slate700">Hai bisogno di aiuto?</p>
                            </div>
                        </router-link>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6">
                    <h3 class="font-bold text-cdf-deep mb-4">Attività Recente</h3>
                    <div class="space-y-4">
                        <div class="flex items-start space-x-3">
                            <div class="w-2 h-2 bg-cdf-teal rounded-full mt-2 flex-shrink-0"></div>
                            <div>
                                <p class="text-sm font-medium text-cdf-deep">Completato modulo 3</p>
                                <p class="text-xs text-cdf-slate700">Cybersecurity Base - 2 giorni fa</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-3">
                            <div class="w-2 h-2 bg-cdf-amber rounded-full mt-2 flex-shrink-0"></div>
                            <div>
                                <p class="text-sm font-medium text-cdf-deep">Badge sbloccato</p>
                                <p class="text-xs text-cdf-slate700">Primo Quiz - 3 giorni fa</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-3">
                            <div class="w-2 h-2 bg-cdf-deep rounded-full mt-2 flex-shrink-0"></div>
                            <div>
                                <p class="text-sm font-medium text-cdf-deep">Attestato rilasciato</p>
                                <p class="text-xs text-cdf-slate700">GDPR Base - 1 settimana fa</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Achievement Badges -->
                <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6">
                    <h3 class="font-bold text-cdf-deep mb-4">Badge Raggiunti</h3>
                    <div class="grid grid-cols-3 gap-3">
                        <div class="text-center">
                            <div class="w-12 h-12 bg-cdf-amber/10 rounded-xl flex items-center justify-center mx-auto mb-2">
                                <svg class="w-6 h-6 text-cdf-amber" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </div>
                            <p class="text-xs text-cdf-slate700">Primo Quiz</p>
                        </div>
                        
                        <div class="text-center">
                            <div class="w-12 h-12 bg-cdf-teal/10 rounded-xl flex items-center justify-center mx-auto mb-2">
                                <svg class="w-6 h-6 text-cdf-teal" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <p class="text-xs text-cdf-slate700">Completato</p>
                        </div>
                        
                        <div class="text-center">
                            <div class="w-12 h-12 bg-cdf-deep/10 rounded-xl flex items-center justify-center mx-auto mb-2">
                                <svg class="w-6 h-6 text-cdf-deep" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <p class="text-xs text-cdf-slate700">Esperto</p>
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
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();
</script>