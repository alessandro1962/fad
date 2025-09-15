<template>
    <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6 hover:shadow-md transition-shadow duration-200">
        <div class="flex items-center">
            <!-- Icon -->
            <div 
                class="flex-shrink-0 p-3 rounded-2xl"
                :class="iconBgClass"
            >
                <!-- Courses Icon -->
                <svg v-if="icon === 'courses'" class="w-6 h-6 text-cdf-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                
                <!-- Completed Icon -->
                <svg v-else-if="icon === 'completed'" class="w-6 h-6 text-cdf-amber" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
                
                <!-- Certificates Icon -->
                <svg v-else-if="icon === 'certificates'" class="w-6 h-6 text-cdf-deep" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                
                <!-- Level Icon -->
                <svg v-else-if="icon === 'level'" class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
                
                <!-- Time Icon -->
                <svg v-else-if="icon === 'time'" class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                
                <!-- Progress Icon -->
                <svg v-else-if="icon === 'progress'" class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                
                <!-- Users Icon -->
                <svg v-else-if="icon === 'users'" class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                </svg>
                
                <!-- Revenue Icon -->
                <svg v-else-if="icon === 'revenue'" class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
                
                <!-- Default Icon -->
                <svg v-else class="w-6 h-6 text-cdf-slate700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            
            <!-- Content -->
            <div class="ml-4 flex-1">
                <p class="text-sm font-medium text-cdf-slate700 mb-1">{{ title }}</p>
                <p class="text-2xl font-bold text-cdf-deep">{{ value }}</p>
                <div v-if="change !== undefined" class="flex items-center mt-1">
                    <!-- Positive Change -->
                    <svg v-if="change >= 0" class="w-4 h-4 mr-1 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17l9.2-9.2M17 17V7H7"></path>
                    </svg>
                    <!-- Negative Change -->
                    <svg v-else class="w-4 h-4 mr-1 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 7l-9.2 9.2M7 7v10h10"></path>
                    </svg>
                    <span class="text-sm font-medium" :class="change >= 0 ? 'text-green-600' : 'text-red-600'">
                        {{ Math.abs(change) }}%
                    </span>
                    <span class="text-sm text-cdf-slate700 ml-1">{{ changeLabel }}</span>
                </div>
            </div>
            
            <!-- Trend Chart (optional) -->
            <div v-if="showTrend" class="ml-4">
                <div class="w-16 h-10 flex items-end space-x-1">
                    <div 
                        v-for="(point, index) in trendData" 
                        :key="index"
                        class="bg-cdf-teal/20 rounded-sm"
                        :style="{ height: `${point}%`, width: '4px' }"
                    ></div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    title: {
        type: String,
        required: true
    },
    value: {
        type: [String, Number],
        required: true
    },
    icon: {
        type: String,
        required: true
    },
    change: {
        type: Number,
        default: undefined
    },
    changeLabel: {
        type: String,
        default: 'rispetto al mese scorso'
    },
    showTrend: {
        type: Boolean,
        default: false
    },
    trendData: {
        type: Array,
        default: () => [20, 40, 30, 60, 45, 80, 70]
    }
});

const iconBgClass = computed(() => {
    const classes = {
        courses: 'bg-cdf-teal/10',
        completed: 'bg-cdf-amber/10',
        certificates: 'bg-cdf-deep/10',
        level: 'bg-green-100',
        time: 'bg-blue-100',
        progress: 'bg-purple-100',
        users: 'bg-indigo-100',
        revenue: 'bg-emerald-100'
    };
    return classes[props.icon] || 'bg-cdf-slate200';
});
</script>