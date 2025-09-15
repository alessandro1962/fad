<template>
    <AppLayout>
        <!-- Course Header -->
        <div class="bg-gradient-to-r from-cdf-deep to-cdf-teal rounded-3xl p-8 mb-8 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold mb-2">{{ course.title }}</h1>
                    <p class="text-lg opacity-90">{{ course.description }}</p>
                    <div class="flex items-center space-x-4 mt-4">
                        <span class="px-3 py-1 bg-white/20 rounded-full text-sm">
                            {{ course.level }}
                        </span>
                        <span class="px-3 py-1 bg-white/20 rounded-full text-sm">
                            {{ course.duration }}
                        </span>
                        <span class="px-3 py-1 bg-white/20 rounded-full text-sm">
                            {{ course.modules }} moduli
                        </span>
                    </div>
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

        <div class="grid lg:grid-cols-4 gap-8">
            <!-- Course Content -->
            <div class="lg:col-span-3">
                <!-- Progress Bar -->
                <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-bold text-cdf-deep">Progresso Corso</h2>
                        <span class="text-sm font-semibold text-cdf-deep">{{ course.progress }}%</span>
                    </div>
                    <div class="w-full bg-cdf-slate200 rounded-full h-3">
                        <div 
                            class="bg-gradient-to-r from-cdf-teal to-cdf-deep h-3 rounded-full transition-all duration-500"
                            :style="{ width: `${course.progress}%` }"
                        ></div>
                    </div>
                </div>

                <!-- Current Lesson -->
                <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6 mb-6">
                    <h2 class="text-lg font-bold text-cdf-deep mb-4">Lezione Corrente</h2>
                    <div class="aspect-video bg-cdf-slate200 rounded-xl mb-4 flex items-center justify-center">
                        <div class="text-center">
                            <svg class="w-16 h-16 text-cdf-slate700 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-cdf-slate700">Player Video</p>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-cdf-deep mb-2">{{ currentLesson.title }}</h3>
                    <p class="text-cdf-slate700 mb-4">{{ currentLesson.description }}</p>
                    <div class="flex items-center space-x-4">
                        <button class="btn-primary">
                            {{ currentLesson.completed ? 'Rivedi' : 'Inizia Lezione' }}
                        </button>
                        <button class="btn-secondary">
                            Materiali
                        </button>
                    </div>
                </div>

                <!-- Course Modules -->
                <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6">
                    <h2 class="text-lg font-bold text-cdf-deep mb-6">Moduli del Corso</h2>
                    <div class="space-y-4">
                        <div 
                            v-for="(module, index) in course.modules" 
                            :key="index"
                            class="border border-cdf-slate200 rounded-xl p-4"
                        >
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center space-x-3">
                                    <div 
                                        class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium"
                                        :class="module.completed 
                                            ? 'bg-cdf-teal text-white' 
                                            : 'bg-cdf-slate200 text-cdf-slate700'"
                                    >
                                        {{ index + 1 }}
                                    </div>
                                    <h3 class="font-semibold text-cdf-deep">{{ module.title }}</h3>
                                </div>
                                <span class="text-sm text-cdf-slate700">{{ module.duration }}</span>
                            </div>
                            
                            <div class="ml-11 space-y-2">
                                <div 
                                    v-for="lesson in module.lessons" 
                                    :key="lesson.id"
                                    class="flex items-center justify-between py-2 px-3 rounded-lg hover:bg-cdf-sand transition-colors duration-200"
                                >
                                    <div class="flex items-center space-x-3">
                                        <div 
                                            class="w-6 h-6 rounded-full flex items-center justify-center"
                                            :class="lesson.completed 
                                                ? 'bg-cdf-teal text-white' 
                                                : 'bg-cdf-slate200 text-cdf-slate700'"
                                        >
                                            <svg v-if="lesson.completed" class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                            <span v-else class="text-xs">{{ lesson.order }}</span>
                                        </div>
                                        <span class="text-sm text-cdf-slate700">{{ lesson.title }}</span>
                                    </div>
                                    <span class="text-xs text-cdf-slate700">{{ lesson.duration }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Course Info -->
                <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6">
                    <h3 class="font-bold text-cdf-deep mb-4">Informazioni Corso</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-cdf-slate700">Livello:</span>
                            <span class="font-medium text-cdf-deep">{{ course.level }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-cdf-slate700">Durata:</span>
                            <span class="font-medium text-cdf-deep">{{ course.duration }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-cdf-slate700">Moduli:</span>
                            <span class="font-medium text-cdf-deep">{{ course.modules }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-cdf-slate700">Iniziato:</span>
                            <span class="font-medium text-cdf-deep">{{ course.startedAt }}</span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6">
                    <h3 class="font-bold text-cdf-deep mb-4">Azioni Rapide</h3>
                    <div class="space-y-3">
                        <button class="w-full btn-primary">
                            Continua Corso
                        </button>
                        <button class="w-full btn-secondary">
                            Scarica Materiali
                        </button>
                        <button class="w-full btn-secondary">
                            Note Personali
                        </button>
                    </div>
                </div>

                <!-- Course Features -->
                <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6">
                    <h3 class="font-bold text-cdf-deep mb-4">Caratteristiche</h3>
                    <div class="space-y-2">
                        <div 
                            v-for="feature in course.features" 
                            :key="feature"
                            class="flex items-center text-sm text-cdf-slate700"
                        >
                            <svg class="w-4 h-4 mr-2 text-cdf-teal" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            {{ feature }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import AppLayout from '@/components/Layout/AppLayout.vue';

// Sample course data
const course = ref({
    id: 1,
    title: 'Cybersecurity Base',
    description: 'Fondamenti di sicurezza informatica per principianti. Impara le basi della protezione dei dati e dei sistemi.',
    level: 'Principiante',
    duration: '3 ore',
    modules: 5,
    progress: 75,
    startedAt: '15 Gen 2024',
    features: [
        'Quiz interattivi',
        'Materiali scaricabili',
        'Attestato finale',
        'Supporto dedicato'
    ],
    modules: [
        {
            title: 'Introduzione alla Cybersecurity',
            duration: '30 min',
            completed: true,
            lessons: [
                { id: 1, title: 'Cos\'è la Cybersecurity', duration: '10 min', completed: true, order: 1 },
                { id: 2, title: 'Minacce e Vulnerabilità', duration: '20 min', completed: true, order: 2 }
            ]
        },
        {
            title: 'Protezione dei Dati',
            duration: '45 min',
            completed: true,
            lessons: [
                { id: 3, title: 'Classificazione dei Dati', duration: '15 min', completed: true, order: 3 },
                { id: 4, title: 'Backup e Recovery', duration: '30 min', completed: true, order: 4 }
            ]
        },
        {
            title: 'Sicurezza delle Reti',
            duration: '60 min',
            completed: false,
            lessons: [
                { id: 5, title: 'Firewall e VPN', duration: '30 min', completed: false, order: 5 },
                { id: 6, title: 'Monitoraggio di Rete', duration: '30 min', completed: false, order: 6 }
            ]
        },
        {
            title: 'Best Practices',
            duration: '30 min',
            completed: false,
            lessons: [
                { id: 7, title: 'Password e Autenticazione', duration: '15 min', completed: false, order: 7 },
                { id: 8, title: 'Formazione Utenti', duration: '15 min', completed: false, order: 8 }
            ]
        },
        {
            title: 'Test Finale',
            duration: '15 min',
            completed: false,
            lessons: [
                { id: 9, title: 'Quiz di Verifica', duration: '15 min', completed: false, order: 9 }
            ]
        }
    ]
});

const currentLesson = ref({
    title: 'Firewall e VPN',
    description: 'Impara i concetti fondamentali di firewall e VPN per proteggere la tua rete aziendale.',
    completed: false
});
</script>
