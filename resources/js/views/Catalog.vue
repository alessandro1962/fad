<template>
    <AppLayout>

        <!-- Main Content -->
        <main class="mx-auto max-w-7xl px-6 py-8">
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
                <h1 class="text-3xl font-bold text-cdf-deep">Catalogo Corsi</h1>
                <p class="text-cdf-slate700 mt-2">Scegli il corso più adatto alle tue esigenze</p>
            </div>

            <!-- Filters -->
            <div class="card mb-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-cdf-deep mb-2">Livello</label>
                        <select class="input-field">
                            <option>Tutti i livelli</option>
                            <option>Principiante</option>
                            <option>Intermedio</option>
                            <option>Esperto</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-cdf-deep mb-2">Categoria</label>
                        <select class="input-field">
                            <option>Tutte le categorie</option>
                            <option>Cybersecurity</option>
                            <option>GDPR</option>
                            <option>NIS2</option>
                            <option>Privacy</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-cdf-deep mb-2">Durata</label>
                        <select class="input-field">
                            <option>Qualsiasi durata</option>
                            <option>0-2 ore</option>
                            <option>2-5 ore</option>
                            <option>5+ ore</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button class="btn-primary w-full">Filtra</button>
                    </div>
                </div>
            </div>

            <!-- Loading State -->
            <div v-if="loading" class="text-center py-12">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-cdf-teal"></div>
                <p class="mt-4 text-cdf-slate700">Caricamento corsi...</p>
            </div>

            <!-- Error State -->
            <div v-else-if="error" class="text-center py-12">
                <div class="text-red-500 mb-4">
                    <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <p class="text-red-600 mb-4">{{ error }}</p>
                <button @click="loadCourses" class="btn-primary">Riprova</button>
            </div>

            <!-- Courses Grid -->
            <div v-else class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <CourseCard
                    v-for="course in courses"
                    :key="course.id"
                    :course="course"
                    @action="handleCourseAction"
                />
            </div>

            <!-- Full Vision CTA -->
            <div class="mt-12 card bg-gradient-to-r from-cdf-deep to-cdf-teal text-white">
                <div class="text-center">
                    <h2 class="text-2xl font-bold mb-4">Accesso Completo</h2>
                    <p class="text-lg mb-6">Sblocca tutti i corsi con l'account Full Vision</p>
                    <div class="flex items-center justify-center gap-4">
                        <span class="text-3xl font-bold">€49.99</span>
                        <span class="text-lg opacity-90">/anno</span>
                    </div>
                    <button 
                        @click="goToFullVision"
                        class="btn-accent mt-6"
                    >
                        Acquista Full Vision
                    </button>
                </div>
            </div>
        </main>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import AppLayout from '@/components/Layout/AppLayout.vue';
import CourseCard from '@/components/Course/CourseCard.vue';
import { useAuthStore } from '@/stores/auth';
import axios from 'axios';

const router = useRouter();
const authStore = useAuthStore();

// Reactive data
const courses = ref([]);
const loading = ref(true);
const error = ref(null);

// Load courses from API
const loadCourses = async () => {
    try {
        loading.value = true;
        const response = await axios.get('/api/v1/courses?per_page=100');
        
        // Transform API data to match CourseCard component expectations
        courses.value = response.data.data.map(course => ({
            id: course.id,
            title: course.title,
            slug: course.slug,
            description: course.summary || course.description,
            category: getCategoryFromTitle(course.title),
            level: getLevelFromTitle(course.title),
            duration: course.duration_hours ? `${course.duration_hours} ore` : 'N/A',
            modules: course.modules_count || 0,
            price: course.price_euros ? `€${course.price_euros}` : 'Gratuito',
            rating: 4.5, // Default rating
            features: ['Video lezioni', 'Materiali scaricabili', 'Attestato finale'],
            // Add image data
            thumbnail_url: course.thumbnail_url,
            gallery: course.gallery,
            // Keep original data for other uses
            original: course
        }));
    } catch (err) {
        error.value = 'Errore nel caricamento dei corsi';
        console.error('Error loading courses:', err);
    } finally {
        loading.value = false;
    }
};

// Helper functions to extract category and level from title
const getCategoryFromTitle = (title) => {
    if (title.toLowerCase().includes('cybersecurity')) return 'Cybersecurity';
    if (title.toLowerCase().includes('gdpr')) return 'GDPR';
    if (title.toLowerCase().includes('nis2')) return 'NIS2';
    if (title.toLowerCase().includes('privacy')) return 'Privacy';
    if (title.toLowerCase().includes('whatsapp')) return 'Sicurezza';
    if (title.toLowerCase().includes('gmail')) return 'Email';
    if (title.toLowerCase().includes('instagram')) return 'Social Media';
    if (title.toLowerCase().includes('facebook')) return 'Social Media';
    if (title.toLowerCase().includes('password')) return 'Sicurezza';
    if (title.toLowerCase().includes('cookie')) return 'Privacy';
    if (title.toLowerCase().includes('dark web')) return 'Cybersecurity';
    if (title.toLowerCase().includes('scuola')) return 'Educazione';
    if (title.toLowerCase().includes('accessibilità')) return 'Accessibilità';
    return 'Generale';
};

const getLevelFromTitle = (title) => {
    if (title.toLowerCase().includes('base') || title.toLowerCase().includes('principiante')) return 'Principiante';
    if (title.toLowerCase().includes('avanzato') || title.toLowerCase().includes('esperto')) return 'Esperto';
    if (title.toLowerCase().includes('intermedio')) return 'Intermedio';
    if (title.toLowerCase().includes('formazione') || title.toLowerCase().includes('incaricati')) return 'Intermedio';
    return 'Principiante';
};

const handleCourseAction = (course) => {
    console.log('Course action:', course);
    // Handle course purchase or continue
};

const goToFullVision = () => {
    window.open('https://sicurezzadigitale.shop/prodotto/campus-digitale-account-full-vision-tutti-i-corsi-con-un-solo-accesso/', '_blank');
};

const goToDashboard = () => {
    router.push('/dashboard');
};

// Load courses on component mount
onMounted(() => {
    loadCourses();
});
</script>
