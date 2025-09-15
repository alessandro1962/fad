<template>
    <AppLayout>
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-cdf-deep">I Miei Corsi</h1>
            <p class="text-cdf-slate700 mt-2">Gestisci i tuoi corsi e monitora i progressi</p>
        </div>

        <!-- Filter Tabs -->
        <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6 mb-8">
            <div class="flex flex-wrap gap-2">
                <button 
                    v-for="filter in filters" 
                    :key="filter.key"
                    @click="activeFilter = filter.key"
                    class="px-4 py-2 rounded-xl font-medium transition-all duration-200"
                    :class="activeFilter === filter.key 
                        ? 'bg-cdf-deep text-white' 
                        : 'bg-cdf-slate200 text-cdf-slate700 hover:bg-cdf-slate300'"
                >
                    {{ filter.label }}
                    <span class="ml-2 px-2 py-1 bg-white/20 rounded-full text-xs">
                        {{ getFilterCount(filter.key) }}
                    </span>
                </button>
            </div>
        </div>

        <!-- Courses List -->
        <div class="space-y-6">
            <!-- Course Item -->
            <div 
                v-for="course in filteredCourses" 
                :key="course.id"
                class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6 hover:shadow-md transition-shadow duration-200"
            >
                <div class="flex items-start space-x-6">
                    <!-- Course Thumbnail -->
                    <div class="w-24 h-24 bg-gradient-to-br from-cdf-teal/20 to-cdf-deep/20 rounded-2xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-10 h-10 text-cdf-deep" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>

                    <!-- Course Info -->
                    <div class="flex-1">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <div class="flex items-center space-x-2 mb-2">
                                    <h3 class="text-xl font-bold text-cdf-deep">{{ course.title }}</h3>
                                    <span 
                                        class="px-2 py-1 rounded-lg text-xs font-medium"
                                        :class="getStatusBadgeClass(course.status)"
                                    >
                                        {{ getStatusLabel(course.status) }}
                                    </span>
                                </div>
                                <p class="text-cdf-slate700 mb-3">{{ course.description }}</p>
                                
                                <!-- Course Stats -->
                                <div class="flex items-center space-x-6 text-sm text-cdf-slate700 mb-4">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ course.duration }}
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                        {{ course.modules }} moduli
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        Iniziato: {{ course.startedAt }}
                                    </div>
                                </div>

                                <!-- Progress Bar -->
                                <div class="mb-4">
                                    <div class="flex items-center justify-between text-sm mb-2">
                                        <span class="text-cdf-slate700">Progresso</span>
                                        <span class="font-semibold text-cdf-deep">{{ course.progress }}%</span>
                                    </div>
                                    <div class="w-full bg-cdf-slate200 rounded-full h-3">
                                        <div 
                                            class="bg-gradient-to-r from-cdf-teal to-cdf-deep h-3 rounded-full transition-all duration-500"
                                            :style="{ width: `${course.progress}%` }"
                                        ></div>
                                    </div>
                                </div>

                                <!-- Modules Progress -->
                                <div class="mb-4">
                                    <p class="text-sm font-medium text-cdf-deep mb-2">Moduli Completati</p>
                                    <div class="flex flex-wrap gap-2">
                                        <div 
                                            v-for="(module, index) in course.modulesList" 
                                            :key="index"
                                            class="flex items-center space-x-2"
                                        >
                                            <div 
                                                class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-medium"
                                                :class="module.completed 
                                                    ? 'bg-cdf-teal text-white' 
                                                    : 'bg-cdf-slate200 text-cdf-slate700'"
                                            >
                                                {{ index + 1 }}
                                            </div>
                                            <span class="text-sm text-cdf-slate700">{{ module.title }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex flex-col space-y-2">
                                <button 
                                    @click="continueCourse(course)"
                                    class="px-6 py-2 bg-cdf-teal text-white rounded-xl font-semibold hover:bg-cdf-teal/90 transition-colors duration-200"
                                >
                                    {{ course.progress === 100 ? 'Rivedi' : 'Continua' }}
                                </button>
                                <button 
                                    @click="viewCertificate(course)"
                                    v-if="course.progress === 100"
                                    class="px-6 py-2 bg-cdf-amber text-cdf-ink rounded-xl font-semibold hover:bg-cdf-amber/90 transition-colors duration-200"
                                >
                                    Attestato
                                </button>
                                <button 
                                    @click="downloadMaterials(course)"
                                    class="px-6 py-2 border border-cdf-slate200 text-cdf-slate700 rounded-xl font-semibold hover:bg-cdf-sand transition-colors duration-200"
                                >
                                    Materiali
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-if="filteredCourses.length === 0" class="text-center py-12">
            <div class="w-24 h-24 bg-cdf-slate200 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-12 h-12 text-cdf-slate700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-cdf-deep mb-2">Nessun corso trovato</h3>
            <p class="text-cdf-slate700 mb-6">Non hai ancora corsi in questa categoria</p>
            <router-link to="/catalogo" class="btn-primary">
                Sfoglia Catalogo
            </router-link>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import AppLayout from '@/components/Layout/AppLayout.vue';

const activeFilter = ref('all');

const filters = [
    { key: 'all', label: 'Tutti' },
    { key: 'active', label: 'In Corso' },
    { key: 'completed', label: 'Completati' },
    { key: 'not-started', label: 'Non Iniziati' }
];

// Sample courses data
const courses = [
    {
        id: 1,
        title: 'Cybersecurity Base',
        description: 'Fondamenti di sicurezza informatica per principianti. Impara le basi della protezione dei dati e dei sistemi.',
        status: 'active',
        progress: 75,
        duration: '3 ore',
        modules: 5,
        startedAt: '15 Gen 2024',
        modulesList: [
            { title: 'Introduzione', completed: true },
            { title: 'Minacce Informatiche', completed: true },
            { title: 'Protezione Dati', completed: true },
            { title: 'Best Practices', completed: false },
            { title: 'Test Finale', completed: false }
        ]
    },
    {
        id: 2,
        title: 'GDPR per Incaricati',
        description: 'Formazione completa sul GDPR per incaricati del trattamento. Normative, obblighi e best practices.',
        status: 'active',
        progress: 30,
        duration: '4 ore',
        modules: 6,
        startedAt: '10 Gen 2024',
        modulesList: [
            { title: 'Panoramica GDPR', completed: true },
            { title: 'Principi Fondamentali', completed: false },
            { title: 'Diritti degli Interessati', completed: false },
            { title: 'Obblighi del Titolare', completed: false },
            { title: 'Sicurezza dei Dati', completed: false },
            { title: 'Valutazione d\'Impatto', completed: false }
        ]
    },
    {
        id: 3,
        title: 'NIS2 per Dipendenti',
        description: 'Direttiva NIS2: obblighi e responsabilità per i dipendenti delle organizzazioni critiche.',
        status: 'not-started',
        progress: 0,
        duration: '2 ore',
        modules: 3,
        startedAt: 'Non iniziato',
        modulesList: [
            { title: 'Introduzione NIS2', completed: false },
            { title: 'Obblighi Organizzativi', completed: false },
            { title: 'Responsabilità Individuali', completed: false }
        ]
    },
    {
        id: 4,
        title: 'Privacy e Sicurezza Dati',
        description: 'Corso completo su privacy e sicurezza dei dati personali secondo le normative vigenti.',
        status: 'completed',
        progress: 100,
        duration: '2.5 ore',
        modules: 4,
        startedAt: '5 Dic 2023',
        modulesList: [
            { title: 'Normativa Privacy', completed: true },
            { title: 'Gestione Consensi', completed: true },
            { title: 'Sicurezza Informatica', completed: true },
            { title: 'Test Finale', completed: true }
        ]
    }
];

const filteredCourses = computed(() => {
    if (activeFilter.value === 'all') return courses;
    return courses.filter(course => course.status === activeFilter.value);
});

const getFilterCount = (filterKey) => {
    if (filterKey === 'all') return courses.length;
    return courses.filter(course => course.status === filterKey).length;
};

const getStatusBadgeClass = (status) => {
    const classes = {
        'active': 'bg-cdf-teal/10 text-cdf-teal',
        'completed': 'bg-green-100 text-green-700',
        'not-started': 'bg-cdf-slate200 text-cdf-slate700'
    };
    return classes[status] || 'bg-cdf-slate200 text-cdf-slate700';
};

const getStatusLabel = (status) => {
    const labels = {
        'active': 'In Corso',
        'completed': 'Completato',
        'not-started': 'Non Iniziato'
    };
    return labels[status] || status;
};

const continueCourse = (course) => {
    console.log('Continue course:', course);
    // Navigate to course player
};

const viewCertificate = (course) => {
    console.log('View certificate:', course);
    // Navigate to certificate view
};

const downloadMaterials = (course) => {
    console.log('Download materials:', course);
    // Download course materials
};
</script>
