<template>
    <AppLayout>
        <!-- Loading State -->
        <div v-if="loading" class="flex justify-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-cdf-teal"></div>
        </div>

        <!-- Course Completion Screen -->
        <div v-else-if="courseCompleted" class="max-w-4xl mx-auto py-12">
            <div class="bg-white rounded-3xl shadow-xl border border-cdf-slate200 p-8 text-center">
                <!-- Success Icon -->
                <div class="w-32 h-32 mx-auto mb-8 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center">
                    <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>

                <!-- Congratulations Message -->
                <h1 class="text-4xl font-bold text-cdf-deep mb-4">
                    🎉 Congratulazioni!
                </h1>
                <h2 class="text-2xl font-semibold text-cdf-slate700 mb-6">
                    Hai completato con successo il corso
                </h2>
                <p class="text-xl text-cdf-slate600 mb-8">
                    <strong>{{ course.title }}</strong>
                </p>

                <!-- Certificate Info -->
                <div class="bg-cdf-sand rounded-2xl p-6 mb-8">
                    <div class="flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-cdf-teal mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="text-lg font-semibold text-cdf-deep">Attestato di Partecipazione</h3>
                    </div>
                    <p class="text-cdf-slate700 mb-4">
                        Nella sezione <strong>"Attestati"</strong> troverai un bellissimo PDF con il tuo attestato di partecipazione al corso.
                    </p>
                    <p class="text-sm text-cdf-slate600">
                        L'attestato è personalizzato con il tuo nome e include tutti i dettagli del corso completato.
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <button
                        @click="goToDashboard"
                        class="px-8 py-3 bg-cdf-teal text-white rounded-xl font-semibold hover:bg-cdf-deep transition-colors"
                    >
                        🏠 Torna alla Dashboard
                    </button>
                    <button
                        @click="goToMyCourses"
                        class="px-8 py-3 bg-cdf-amber text-cdf-ink rounded-xl font-semibold hover:brightness-95 transition-colors"
                    >
                        📚 I Miei Corsi
                    </button>
                </div>

                <!-- Course Stats -->
                <div class="mt-8 pt-8 border-t border-cdf-slate200">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-cdf-teal">{{ course.modules_count || 0 }}</div>
                            <div class="text-sm text-cdf-slate600">Moduli Completati</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-cdf-teal">{{ formatDuration(course.duration_minutes) }}</div>
                            <div class="text-sm text-cdf-slate600">Durata Totale</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-cdf-teal">{{ course.level || 'N/A' }}</div>
                            <div class="text-sm text-cdf-slate600">Livello</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Course Content -->
        <div v-else>
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
                            {{ course.modules_count }} moduli
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
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-bold text-cdf-deep">Lezione Corrente</h2>
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold"
                                  :class="currentLesson.type === 'video' ? 'bg-blue-100 text-blue-800' : 
                                         currentLesson.type === 'quiz' ? 'bg-purple-100 text-purple-800' :
                                         currentLesson.type === 'pdf' ? 'bg-red-100 text-red-800' :
                                         'bg-gray-100 text-gray-800'">
                                {{ getLessonTypeLabel(currentLesson.type) }}
                            </span>
                        </div>
                    </div>

                    <!-- Video Player -->
                    <VideoPlayer
                        v-if="currentLesson.type === 'video'"
                        :lesson="currentLesson"
                        :user-progress="currentLessonProgress"
                        :block-progression="currentLesson.payload?.block_progression !== false"
                        @progress-updated="onProgressUpdated"
                        @lesson-completed="onLessonCompleted"
                        @next-lesson="onNextLesson"
                    />

                    <!-- Quiz Player -->
                    <QuizPlayer
                        v-else-if="currentLesson.type === 'quiz'"
                        :lesson="currentLesson"
                        :user-attempts="currentLessonAttempts"
                        @quiz-completed="onQuizCompleted"
                        @next-lesson="onNextLesson"
                    />

                    <!-- PDF Viewer -->
                    <div v-else-if="currentLesson.type === 'pdf'" class="space-y-4">
                        <div class="aspect-video bg-cdf-slate200 rounded-xl flex items-center justify-center">
                            <div class="text-center">
                                <svg class="w-16 h-16 text-cdf-slate700 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-cdf-slate700 mb-4">Visualizzatore PDF</p>
                                <button class="btn-primary">
                                    Apri PDF
                                </button>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <button @click="markLessonCompleted" class="btn-primary">
                                Segna come Completato
                            </button>
                            <button class="btn-secondary">
                                Scarica PDF
                            </button>
                        </div>
                    </div>

                    <!-- Default Content -->
                    <div v-else class="space-y-4">
                        <div class="aspect-video bg-cdf-slate200 rounded-xl flex items-center justify-center">
                            <div class="text-center">
                                <svg class="w-16 h-16 text-cdf-slate700 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-cdf-slate700">{{ getLessonTypeLabel(currentLesson.type) }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <button @click="markLessonCompleted" class="btn-primary">
                                {{ currentLesson.completed ? 'Rivedi' : 'Inizia Lezione' }}
                            </button>
                            <button class="btn-secondary">
                                Materiali
                            </button>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h3 class="text-xl font-bold text-cdf-deep mb-2">{{ currentLesson.title }}</h3>
                        <p class="text-cdf-slate700">{{ currentLesson.description }}</p>
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
                            <span class="font-medium text-cdf-deep">{{ course.modules_count }}</span>
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
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import AppLayout from '@/components/Layout/AppLayout.vue';
import VideoPlayer from '@/components/Course/VideoPlayer.vue';
import QuizPlayer from '@/components/Course/QuizPlayer.vue';
import api from '@/api';

const route = useRoute();
const router = useRouter();

// State
const course = ref({});
const currentLesson = ref({});
const currentLessonProgress = ref({});
const currentLessonAttempts = ref([]);
const loading = ref(true);
const courseCompleted = ref(false);

// Computed
const courseId = computed(() => route.params.id);

// Methods
const getLessonTypeLabel = (type) => {
    const labels = {
        video: 'Video',
        quiz: 'Quiz',
        pdf: 'PDF',
        slide: 'Slide',
        link: 'Link'
    };
    return labels[type] || 'Contenuto';
};

const loadCourse = async () => {
    try {
        loading.value = true;
        const response = await api.get(`/v1/courses/${courseId.value}`);
        course.value = response.data.data;
        
        // Load current lesson (first incomplete lesson or last lesson)
        await loadCurrentLesson();
    } catch (error) {
        console.error('Errore nel caricamento corso:', error);
    } finally {
        loading.value = false;
    }
};

const loadCurrentLesson = async () => {
    try {
        // Find first incomplete lesson
        let targetLesson = null;
        for (const module of course.value.modules || []) {
            for (const lesson of module.lessons || []) {
                if (!lesson.completed) {
                    targetLesson = lesson;
                    break;
                }
            }
            if (targetLesson) break;
        }
        
        // If all lessons completed, use last lesson
        if (!targetLesson && course.value && course.value.modules && course.value.modules.length > 0) {
            const lastModule = course.value.modules[course.value.modules.length - 1];
            if (lastModule && lastModule.lessons && lastModule.lessons.length > 0) {
                targetLesson = lastModule.lessons[lastModule.lessons.length - 1];
            }
        }
        
        if (targetLesson) {
            currentLesson.value = targetLesson;
            await loadLessonProgress();
            await loadLessonAttempts();
        }
    } catch (error) {
        console.error('Errore nel caricamento lezione corrente:', error);
    }
};

const loadLessonProgress = async () => {
    try {
        const response = await api.get(`/v1/progress/lesson/${currentLesson.value.id}`);
        currentLessonProgress.value = response.data.data;
    } catch (error) {
        console.error('Errore nel caricamento progresso lezione:', error);
        currentLessonProgress.value = {};
    }
};

const loadLessonAttempts = async () => {
    try {
        if (currentLesson.value.type === 'quiz') {
            const response = await api.get(`/v1/quiz-attempts`, {
                params: { lesson_id: currentLesson.value.id }
            });
            currentLessonAttempts.value = response.data.data;
        }
    } catch (error) {
        console.error('Errore nel caricamento tentativi quiz:', error);
        currentLessonAttempts.value = [];
    }
};

const onProgressUpdated = (progressData) => {
    currentLessonProgress.value = progressData;
    // Update course progress
    updateCourseProgress();
};

const onLessonCompleted = (lesson) => {
    // Mark lesson as completed
    lesson.completed = true;
    currentLessonProgress.value.completed = true;
    
    // Update course progress
    updateCourseProgress();
    
    // Load next lesson
    loadNextLesson();
};

const onQuizCompleted = (result) => {
    if (result.result.passed) {
        onLessonCompleted(result.lesson);
    }
};

const onNextLesson = () => {
    loadNextLesson();
};

const loadNextLesson = async () => {
    // Find next lesson in sequence (regardless of completion status)
    let nextLesson = null;
    let foundCurrent = false;
    
    for (const module of course.value.modules || []) {
        for (const lesson of module.lessons || []) {
            if (foundCurrent) {
                nextLesson = lesson;
                break;
            }
            if (lesson.id === currentLesson.value.id) {
                foundCurrent = true;
            }
        }
        if (nextLesson) break;
    }
    
    if (nextLesson) {
        currentLesson.value = nextLesson;
        await loadLessonProgress();
        await loadLessonAttempts();
    } else {
        // No more lessons - course completed
        courseCompleted.value = true;
        console.log('Course completed!');
    }
};

const markLessonCompleted = async () => {
    try {
        await api.patch(`/v1/progress/lesson/${currentLesson.value.id}`, {
            completed: true,
            seconds_watched: currentLesson.value.duration_minutes * 60 || 0
        });
        
        onLessonCompleted(currentLesson.value);
    } catch (error) {
        console.error('Errore nel completamento lezione:', error);
    }
};

const updateCourseProgress = () => {
    // Calculate course progress based on completed lessons
    let totalLessons = 0;
    let completedLessons = 0;
    
    for (const module of course.value.modules || []) {
        for (const lesson of module.lessons || []) {
            totalLessons++;
            if (lesson.completed) {
                completedLessons++;
            }
        }
    }
    
    if (totalLessons > 0 && course.value) {
        course.value.progress = Math.round((completedLessons / totalLessons) * 100);
    }
};

const goToDashboard = () => {
    router.push('/dashboard');
};

const goToMyCourses = () => {
    router.push('/my-courses');
};

const formatDuration = (minutes) => {
    if (!minutes || minutes === 0) return 'N/A';
    
    const hours = Math.floor(minutes / 60);
    const remainingMinutes = minutes % 60;
    
    if (hours > 0) {
        return remainingMinutes > 0 ? `${hours}h ${remainingMinutes}m` : `${hours}h`;
    } else {
        return `${minutes}m`;
    }
};

// Lifecycle
onMounted(() => {
    loadCourse();
});
</script>
