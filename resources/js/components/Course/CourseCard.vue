<template>
    <div 
        class="group bg-white rounded-2xl shadow-sm border border-cdf-slate200 hover:shadow-xl hover:border-cdf-teal/30 transition-all duration-300 overflow-hidden cursor-pointer"
        @click="goToCourse"
    >
        <!-- Course Image/Thumbnail -->
        <div class="relative h-48 overflow-hidden">
            <!-- Course Image -->
            <img 
                v-if="course.thumbnail_url" 
                :src="getImageUrl(course.thumbnail_url)" 
                :alt="course.title"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
            />
            <!-- Fallback gradient background -->
            <div v-else class="w-full h-full bg-gradient-to-br from-cdf-teal/20 via-cdf-deep/20 to-cdf-amber/20 flex items-center justify-center">
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-3 rounded-2xl bg-white/80 backdrop-blur-sm flex items-center justify-center shadow-lg">
                        <svg class="w-8 h-8 text-cdf-deep" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-cdf-deep/80">{{ course.category }}</p>
                </div>
            </div>
            
            <!-- Course Level Badge -->
            <div class="absolute top-4 right-4">
                <span 
                    class="px-3 py-1 rounded-full text-xs font-semibold shadow-sm"
                    :class="levelBadgeClass"
                >
                    {{ course.level }}
                </span>
            </div>

            <!-- Progress Bar (if enrolled) -->
            <div v-if="course.progress !== undefined" class="absolute bottom-0 left-0 right-0">
                <div class="h-1 bg-white/20">
                    <div 
                        class="h-full bg-cdf-teal transition-all duration-500"
                        :style="{ width: `${course.progress}%` }"
                    ></div>
                </div>
            </div>
        </div>

        <!-- Course Content -->
        <div class="p-6">
            <!-- Tags -->
            <div class="flex flex-wrap gap-2 mb-3">
                <span 
                    class="px-2 py-1 rounded-lg text-xs font-medium"
                    :class="categoryBadgeClass"
                >
                    {{ course.category }}
                </span>
                <span class="px-2 py-1 bg-cdf-slate200 text-cdf-slate700 rounded-lg text-xs font-medium">
                    {{ course.duration }}
                </span>
            </div>

            <!-- Title -->
            <h3 class="text-lg font-bold text-cdf-deep mb-2 line-clamp-2 group-hover:text-cdf-teal transition-colors duration-200">
                {{ course.title }}
            </h3>

            <!-- Description -->
            <p class="text-cdf-slate700 text-sm mb-4 line-clamp-3 leading-relaxed">
                {{ course.description }}
            </p>

            <!-- Course Stats -->
            <div class="flex items-center justify-between text-sm text-cdf-slate700 mb-4">
                <div class="flex items-center space-x-4">
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
                </div>
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-1 text-cdf-amber" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                    {{ course.rating }}
                </div>
            </div>

            <!-- Progress Info (if enrolled) -->
            <div v-if="course.progress !== undefined" class="mb-4">
                <div class="flex items-center justify-between text-sm mb-2">
                    <span class="text-cdf-slate700">Progresso</span>
                    <span class="font-semibold text-cdf-deep">{{ course.progress }}%</span>
                </div>
                <div class="w-full bg-cdf-slate200 rounded-full h-2">
                    <div 
                        class="bg-gradient-to-r from-cdf-teal to-cdf-deep h-2 rounded-full transition-all duration-500"
                        :style="{ width: `${course.progress}%` }"
                    ></div>
                </div>
            </div>

            <!-- Action Button -->
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <span class="text-2xl font-bold text-cdf-deep">{{ course.price }}</span>
                    <span v-if="course.originalPrice" class="text-sm text-cdf-slate700 line-through ml-2">{{ course.originalPrice }}</span>
                </div>
                
                <button 
                    @click="handleAction"
                    class="px-6 py-2 rounded-xl font-semibold text-sm transition-all duration-200 transform hover:scale-105"
                    :class="actionButtonClass"
                >
                    {{ actionButtonText }}
                </button>
            </div>

            <!-- Additional Info -->
            <div v-if="course.features" class="mt-4 pt-4 border-t border-cdf-slate200">
                <div class="flex flex-wrap gap-2">
                    <span 
                        v-for="feature in course.features" 
                        :key="feature"
                        class="flex items-center text-xs text-cdf-slate700"
                    >
                        <svg class="w-3 h-3 mr-1 text-cdf-teal" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        {{ feature }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    course: {
        type: Object,
        required: true
    }
});

const emit = defineEmits(['action']);

const levelBadgeClass = computed(() => {
    const level = props.course.level?.toLowerCase();
    switch (level) {
        case 'principiante':
            return 'bg-cdf-amber/10 text-cdf-amber border border-cdf-amber/20';
        case 'intermedio':
            return 'bg-cdf-teal/10 text-cdf-teal border border-cdf-teal/20';
        case 'esperto':
            return 'bg-cdf-deep/10 text-cdf-deep border border-cdf-deep/20';
        default:
            return 'bg-cdf-slate200 text-cdf-slate700';
    }
});

const categoryBadgeClass = computed(() => {
    const category = props.course.category?.toLowerCase();
    switch (category) {
        case 'cybersecurity':
            return 'bg-cdf-teal/10 text-cdf-teal';
        case 'gdpr':
            return 'bg-cdf-deep/10 text-cdf-deep';
        case 'nis2':
            return 'bg-cdf-amber/10 text-cdf-amber';
        default:
            return 'bg-cdf-slate200 text-cdf-slate700';
    }
});

const actionButtonClass = computed(() => {
    if (props.course.progress !== undefined) {
        return 'bg-cdf-teal text-white hover:bg-cdf-teal/90';
    }
    return 'bg-cdf-deep text-white hover:bg-cdf-deep/90';
});

const actionButtonText = computed(() => {
    if (props.course.progress !== undefined) {
        return props.course.progress === 100 ? 'Rivedi' : 'Continua';
    }
    return 'Acquista';
});

const handleAction = () => {
    emit('action', props.course);
};

const getImageUrl = (imagePath) => {
    if (!imagePath) return null;
    // If it's already a full URL, return as is
    if (imagePath.startsWith('http')) return imagePath;
    // Otherwise, construct the full URL
    return `${import.meta.env.VITE_APP_URL || 'http://127.0.0.1:8001'}/storage/${imagePath}`;
};

const goToCourse = () => {
    // Navigate to course details page using ID
    window.location.href = `/corso/${props.course.id}`;
};
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
