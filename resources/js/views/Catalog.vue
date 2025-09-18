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
                        <select v-model="filters.level" @change="applyFilters" class="input-field">
                            <option value="">Tutti i livelli</option>
                            <option value="beginner">Principiante</option>
                            <option value="intermediate">Intermedio</option>
                            <option value="expert">Esperto</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-cdf-deep mb-2">Categoria</label>
                        <select v-model="filters.category" @change="applyFilters" class="input-field">
                            <option value="">Tutte le categorie</option>
                            <option value="Cybersecurity">Cybersecurity</option>
                            <option value="GDPR">GDPR</option>
                            <option value="NIS2">NIS2</option>
                            <option value="Privacy">Privacy</option>
                            <option value="Sicurezza">Sicurezza</option>
                            <option value="Email">Email</option>
                            <option value="Social Media">Social Media</option>
                            <option value="Educazione">Educazione</option>
                            <option value="Accessibilità">Accessibilità</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-cdf-deep mb-2">Durata</label>
                        <select v-model="filters.duration" @change="applyFilters" class="input-field">
                            <option value="">Qualsiasi durata</option>
                            <option value="0-2">0-2 ore</option>
                            <option value="2-5">2-5 ore</option>
                            <option value="5+">5+ ore</option>
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button @click="applyFilters" class="btn-primary flex-1">Filtra</button>
                        <button @click="clearFilters" class="btn-secondary">Reset</button>
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

            <!-- Results Count -->
            <div v-if="!loading && !error && courses.length > 0" class="mb-6">
                <p class="text-cdf-slate700">
                    <span class="font-semibold">{{ courses.length }}</span> 
                    {{ courses.length === 1 ? 'corso trovato' : 'corsi trovati' }}
                    <span v-if="courses.length !== allCourses.length" class="text-cdf-teal">
                        (filtrati da {{ allCourses.length }} totali)
                    </span>
                </p>
            </div>

            <!-- No Results -->
            <div v-else-if="!loading && !error && courses.length === 0" class="text-center py-12">
                <div class="text-cdf-slate400 mb-4">
                    <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.29-1.009-5.824-2.709M15 6.291A7.962 7.962 0 0012 5c-2.34 0-4.29 1.009-5.824 2.709"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-cdf-slate700 mb-2">Nessun corso trovato</h3>
                <p class="text-cdf-slate600 mb-4">Prova a modificare i filtri o resettarli per vedere tutti i corsi disponibili.</p>
                <button @click="clearFilters" class="btn-primary">Reset Filtri</button>
            </div>

            <!-- Courses Grid -->
            <div v-if="!loading && !error && courses.length > 0" class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
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
const allCourses = ref([]); // Store all courses for client-side filtering
const loading = ref(true);
const error = ref(null);

// Filters
const filters = ref({
    level: '',
    category: '',
    duration: ''
});

// Load courses from API
const loadCourses = async () => {
    try {
        loading.value = true;
        const response = await axios.get('/api/v1/courses?per_page=100');
        
        // Transform API data to match CourseCard component expectations
        const transformedCourses = response.data.data.map(course => ({
            id: course.id,
            title: course.title,
            slug: course.slug,
            description: course.summary || course.description,
            category: getCategoryFromTitle(course.title),
            level: course.level || 'beginner', // Use real level from database
            duration: course.duration_hours ? `${course.duration_hours} ore` : 'N/A',
            durationHours: course.duration_hours || 0, // For filtering
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
        
        allCourses.value = transformedCourses;
        courses.value = transformedCourses;
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

// Filter functions
const applyFilters = () => {
    let filteredCourses = [...allCourses.value];
    
    // Filter by level
    if (filters.value.level) {
        filteredCourses = filteredCourses.filter(course => 
            course.level === filters.value.level
        );
    }
    
    // Filter by category
    if (filters.value.category) {
        filteredCourses = filteredCourses.filter(course => 
            course.category === filters.value.category
        );
    }
    
    // Filter by duration
    if (filters.value.duration) {
        filteredCourses = filteredCourses.filter(course => {
            const hours = course.durationHours;
            switch (filters.value.duration) {
                case '0-2':
                    return hours >= 0 && hours <= 2;
                case '2-5':
                    return hours > 2 && hours <= 5;
                case '5+':
                    return hours > 5;
                default:
                    return true;
            }
        });
    }
    
    courses.value = filteredCourses;
};

const clearFilters = () => {
    filters.value = {
        level: '',
        category: '',
        duration: ''
    };
    courses.value = [...allCourses.value];
};

// Load courses on component mount
onMounted(() => {
    loadCourses();
});
</script>
