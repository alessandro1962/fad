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

            <!-- Courses Grid -->
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
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
                        <span class="text-3xl font-bold">€199</span>
                        <span class="text-lg opacity-90">/anno</span>
                    </div>
                    <button class="btn-accent mt-6">Acquista Full Vision</button>
                </div>
            </div>
        </main>
    </AppLayout>
</template>

<script setup>
import { useRouter } from 'vue-router';
import AppLayout from '@/components/Layout/AppLayout.vue';
import CourseCard from '@/components/Course/CourseCard.vue';
import { useAuthStore } from '@/stores/auth';

const router = useRouter();
const authStore = useAuthStore();

// Sample courses data
const courses = [
    {
        id: 1,
        title: 'Cybersecurity Base',
        description: 'Fondamenti di sicurezza informatica per principianti. Impara le basi della protezione dei dati e dei sistemi.',
        category: 'Cybersecurity',
        level: 'Principiante',
        duration: '3 ore',
        modules: 5,
        price: '€49',
        rating: 4.8,
        features: ['Quiz interattivi', 'Materiali scaricabili', 'Attestato finale']
    },
    {
        id: 2,
        title: 'GDPR per Incaricati',
        description: 'Formazione completa sul GDPR per incaricati del trattamento. Normative, obblighi e best practices.',
        category: 'GDPR',
        level: 'Intermedio',
        duration: '4 ore',
        modules: 6,
        price: '€79',
        rating: 4.9,
        features: ['Casi pratici', 'Template documenti', 'Certificazione']
    },
    {
        id: 3,
        title: 'NIS2 per Dipendenti',
        description: 'Direttiva NIS2: obblighi e responsabilità per i dipendenti delle organizzazioni critiche.',
        category: 'NIS2',
        level: 'Principiante',
        duration: '2 ore',
        modules: 3,
        price: '€39',
        rating: 4.7,
        features: ['Video lezioni', 'Test finale', 'Attestato']
    }
];

const handleCourseAction = (course) => {
    console.log('Course action:', course);
    // Handle course purchase or continue
};

const goToDashboard = () => {
    router.push('/dashboard');
};
</script>
