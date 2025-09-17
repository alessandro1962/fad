<template>
    <AppLayout>
        <!-- Loading State -->
        <div v-if="loading" class="flex justify-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-cdf-teal"></div>
        </div>

        <!-- Course Details -->
        <div v-else-if="course" class="max-w-6xl mx-auto py-8">
            <!-- Course Header -->
            <div class="bg-gradient-to-r from-cdf-deep to-cdf-teal rounded-3xl p-8 mb-8 text-white">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <h1 class="text-4xl font-bold mb-4">{{ course.title }}</h1>
                        <div class="flex items-center space-x-6 text-lg opacity-90">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                                {{ course.duration_hours || 0 }} ore
                            </div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                                {{ course.modules_count || 0 }} moduli
                            </div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ course.level || 'Principiante' }}
                            </div>
                        </div>
                    </div>
                    <div class="ml-8">
                        <div class="text-right">
                            <div class="text-3xl font-bold">{{ course.price_euros ? `€${course.price_euros}` : 'Gratuito' }}</div>
                            <div class="text-sm opacity-75">Prezzo</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2">
                    <!-- Course Description -->
                    <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-8 mb-8">
                        <h2 class="text-2xl font-bold text-cdf-deep mb-6">Descrizione del Corso</h2>
                        <div class="prose prose-lg max-w-none" v-html="formatDescription(course.description)"></div>
                    </div>

                    <!-- Course Modules -->
                    <div v-if="course.modules && course.modules.length > 0" class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-8">
                        <h2 class="text-2xl font-bold text-cdf-deep mb-6">Moduli del Corso</h2>
                        <div class="space-y-4">
                            <div 
                                v-for="(module, index) in course.modules" 
                                :key="module.id"
                                class="border border-cdf-slate200 rounded-xl p-6 hover:border-cdf-teal/30 transition-colors"
                            >
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-cdf-deep">{{ module.title }}</h3>
                                    <span class="text-sm text-cdf-slate600">{{ module.lessons_count || 0 }} lezioni</span>
                                </div>
                                <p v-if="module.description" class="text-cdf-slate700 mb-3">{{ module.description }}</p>
                                <div class="flex items-center text-sm text-cdf-slate600">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ formatDuration(module.duration_minutes) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <!-- Course Info -->
                    <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6 mb-6">
                        <h3 class="text-lg font-bold text-cdf-deep mb-4">Informazioni Corso</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-cdf-slate600">Livello:</span>
                                <span class="font-medium text-cdf-deep">{{ course.level || 'Principiante' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-cdf-slate600">Durata:</span>
                                <span class="font-medium text-cdf-deep">{{ course.duration_hours || 0 }} ore</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-cdf-slate600">Moduli:</span>
                                <span class="font-medium text-cdf-deep">{{ course.modules_count || 0 }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-cdf-slate600">Prezzo:</span>
                                <span class="font-medium text-cdf-deep">{{ course.price_euros ? `€${course.price_euros}` : 'Gratuito' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Purchase Button -->
                    <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6">
                        <h3 class="text-lg font-bold text-cdf-deep mb-4">Acquista il Corso</h3>
                        <p class="text-cdf-slate700 mb-6">
                            Accedi a tutti i contenuti del corso, materiali scaricabili e attestato finale.
                        </p>
                        <button
                            @click="goToPurchase"
                            class="w-full bg-cdf-teal text-white py-3 px-6 rounded-xl font-semibold hover:bg-cdf-deep transition-colors"
                        >
                            Acquista su sicurezzadigitale.shop
                        </button>
                    </div>

                    <!-- Course Features -->
                    <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6 mt-6">
                        <h3 class="text-lg font-bold text-cdf-deep mb-4">Cosa Include</h3>
                        <div class="space-y-3">
                            <div class="flex items-center text-sm text-cdf-slate700">
                                <svg class="w-4 h-4 mr-3 text-cdf-teal" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Video lezioni
                            </div>
                            <div class="flex items-center text-sm text-cdf-slate700">
                                <svg class="w-4 h-4 mr-3 text-cdf-teal" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Materiali scaricabili
                            </div>
                            <div class="flex items-center text-sm text-cdf-slate700">
                                <svg class="w-4 h-4 mr-3 text-cdf-teal" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Attestato finale
                            </div>
                            <div class="flex items-center text-sm text-cdf-slate700">
                                <svg class="w-4 h-4 mr-3 text-cdf-teal" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Accesso illimitato
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Error State -->
        <div v-else class="max-w-4xl mx-auto py-12 text-center">
            <div class="bg-white rounded-3xl shadow-xl border border-cdf-slate200 p-8">
                <h1 class="text-2xl font-bold text-cdf-deep mb-4">Corso non trovato</h1>
                <p class="text-cdf-slate700 mb-6">Il corso che stai cercando non esiste o non è più disponibile.</p>
                <button
                    @click="goToCatalog"
                    class="bg-cdf-teal text-white px-6 py-3 rounded-xl font-semibold hover:bg-cdf-deep transition-colors"
                >
                    Torna al Catalogo
                </button>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import AppLayout from '@/components/Layout/AppLayout.vue';
import axios from 'axios';

const route = useRoute();
const router = useRouter();

// State
const course = ref(null);
const loading = ref(true);

// Computed
const courseId = computed(() => route.params.id);

// Methods
const formatDuration = (minutes) => {
    if (!minutes) return '0 min';
    const hours = Math.floor(minutes / 60);
    const mins = minutes % 60;
    if (hours > 0) {
        return mins > 0 ? `${hours}h ${mins}min` : `${hours}h`;
    }
    return `${mins}min`;
};

const formatDescription = (description) => {
    if (!description) return '';
    // Clean up the HTML and format it properly
    return description
        .replace(/<span style="font-weight: 400;">/g, '')
        .replace(/<\/span>/g, '')
        .replace(/<b>/g, '<strong>')
        .replace(/<\/b>/g, '</strong>')
        .replace(/<h3>/g, '<h3 class="text-xl font-bold text-cdf-deep mt-6 mb-3">')
        .replace(/<ul>/g, '<ul class="list-disc list-inside space-y-2 mt-4">')
        .replace(/<li style="font-weight: 400;" aria-level="1">/g, '<li class="text-cdf-slate700">')
        .replace(/<p>/g, '<p class="text-cdf-slate700 mb-4">');
};

const goToPurchase = () => {
    // Redirect to sicurezzadigitale.shop with the course
    const courseSlug = course.value?.slug || course.value?.title?.toLowerCase().replace(/\s+/g, '-');
    window.open(`https://sicurezzadigitale.shop/prodotto/${courseSlug}/`, '_blank');
};

const goToCatalog = () => {
    router.push('/catalogo');
};

const loadCourse = async () => {
    try {
        loading.value = true;
        console.log('Loading course with ID:', courseId.value);
        const response = await axios.get(`/api/v1/courses/${courseId.value}`);
        console.log('Course response:', response.data);
        course.value = response.data.data;
        console.log('Course loaded:', course.value);
    } catch (error) {
        console.error('Errore nel caricamento corso:', error);
        course.value = null;
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    loadCourse();
});
</script>

<style scoped>
.prose h3 {
    @apply text-xl font-bold text-cdf-deep mt-6 mb-3;
}

.prose p {
    @apply text-cdf-slate700 mb-4;
}

.prose ul {
    @apply list-disc list-inside space-y-2 mt-4;
}

.prose li {
    @apply text-cdf-slate700;
}

.prose strong {
    @apply font-semibold text-cdf-deep;
}
</style>
