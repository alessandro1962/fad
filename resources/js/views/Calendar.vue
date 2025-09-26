<template>
    <AppLayout>
        <!-- Header -->
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
            <h1 class="text-3xl font-bold text-cdf-deep">Calendario Eventi</h1>
            <p class="text-cdf-slate700 mt-2">Gestisci i tuoi appuntamenti e scadenze formative</p>
        </div>

        <!-- Calendar View -->
        <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-cdf-deep">{{ currentMonth }} {{ currentYear }}</h2>
                <div class="flex items-center space-x-2">
                    <button @click="previousMonth" class="p-2 hover:bg-cdf-sand rounded-lg transition-colors duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                    <button @click="nextMonth" class="p-2 hover:bg-cdf-sand rounded-lg transition-colors duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Calendar Grid -->
            <div class="grid grid-cols-7 gap-1 mb-4">
                <div 
                    v-for="day in weekDays" 
                    :key="day"
                    class="p-3 text-center text-sm font-medium text-cdf-slate700"
                >
                    {{ day }}
                </div>
            </div>

            <div class="grid grid-cols-7 gap-1">
                <div 
                    v-for="day in calendarDays" 
                    :key="day.date"
                    class="min-h-[100px] p-2 border border-cdf-slate200 rounded-lg"
                    :class="{
                        'bg-cdf-slate200': !day.isCurrentMonth,
                        'bg-white': day.isCurrentMonth,
                        'bg-cdf-teal/10': day.hasEvents
                    }"
                >
                    <div class="flex items-center justify-between mb-1">
                        <span 
                            class="text-sm font-medium"
                            :class="{
                                'text-cdf-slate700': !day.isCurrentMonth,
                                'text-cdf-deep': day.isCurrentMonth,
                                'text-cdf-teal font-bold': day.isToday
                            }"
                        >
                            {{ day.day }}
                        </span>
                        <div v-if="day.hasEvents" class="w-2 h-2 bg-cdf-teal rounded-full"></div>
                    </div>
                    
                    <div class="space-y-1">
                        <div 
                            v-for="event in day.events" 
                            :key="event.id"
                            class="text-xs p-1 rounded truncate cursor-pointer hover:bg-white/50 transition-colors duration-200"
                            :class="getEventClass(event.type)"
                            @click="selectEvent(event)"
                        >
                            {{ event.title }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Events List -->
        <div class="mt-8 grid md:grid-cols-2 gap-6">
            <!-- Upcoming Events -->
            <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6">
                <h3 class="text-lg font-bold text-cdf-deep mb-4">Prossimi Eventi</h3>
                <div class="space-y-4">
                    <div 
                        v-for="event in upcomingEvents" 
                        :key="event.id"
                        class="flex items-start space-x-3 p-3 rounded-xl hover:bg-cdf-sand transition-colors duration-200"
                    >
                        <div 
                            class="w-3 h-3 rounded-full mt-2 flex-shrink-0"
                            :class="getEventDotClass(event.type)"
                        ></div>
                        <div class="flex-1">
                            <h4 class="font-medium text-cdf-deep">{{ event.title }}</h4>
                            <p class="text-sm text-cdf-slate700">{{ event.description }}</p>
                            <p class="text-xs text-cdf-slate700 mt-1">{{ formatEventDate(event.date) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Course Deadlines -->
            <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6">
                <h3 class="text-lg font-bold text-cdf-deep mb-4">Scadenze Corsi</h3>
                <div class="space-y-4">
                    <div 
                        v-for="deadline in courseDeadlines" 
                        :key="deadline.id"
                        class="flex items-start space-x-3 p-3 rounded-xl hover:bg-cdf-sand transition-colors duration-200"
                    >
                        <div 
                            class="w-3 h-3 rounded-full mt-2 flex-shrink-0"
                            :class="getDeadlineClass(deadline.daysLeft)"
                        ></div>
                        <div class="flex-1">
                            <h4 class="font-medium text-cdf-deep">{{ deadline.courseTitle }}</h4>
                            <p class="text-sm text-cdf-slate700">{{ deadline.description }}</p>
                            <p class="text-xs text-cdf-slate700 mt-1">
                                Scadenza: {{ formatEventDate(deadline.deadline) }}
                                <span 
                                    class="ml-2 px-2 py-1 rounded-full text-xs"
                                    :class="getDeadlineBadgeClass(deadline.daysLeft)"
                                >
                                    {{ deadline.daysLeft }} giorni
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Event Modal -->
        <div v-if="selectedEvent" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-cdf-deep">{{ selectedEvent.title }}</h3>
                    <button 
                        @click="selectedEvent = null"
                        class="p-2 hover:bg-cdf-slate200 rounded-lg transition-colors duration-200"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <h4 class="font-medium text-cdf-deep mb-2">Descrizione</h4>
                        <p class="text-cdf-slate700">{{ selectedEvent.description }}</p>
                    </div>
                    
                    <div>
                        <h4 class="font-medium text-cdf-deep mb-2">Data e Ora</h4>
                        <p class="text-cdf-slate700">{{ formatEventDate(selectedEvent.date) }}</p>
                    </div>
                    
                    <div v-if="selectedEvent.location">
                        <h4 class="font-medium text-cdf-deep mb-2">Luogo</h4>
                        <p class="text-cdf-slate700">{{ selectedEvent.location }}</p>
                    </div>
                </div>
                
                <div class="mt-6 flex space-x-3">
                    <button class="flex-1 btn-primary">
                        Partecipa
                    </button>
                    <button class="flex-1 btn-secondary">
                        Aggiungi al Calendario
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import AppLayout from '@/components/Layout/AppLayout.vue';

const router = useRouter();
const currentDate = ref(new Date());
const selectedEvent = ref(null);

const weekDays = ['Lun', 'Mar', 'Mer', 'Gio', 'Ven', 'Sab', 'Dom'];

const currentMonth = computed(() => {
    return currentDate.value.toLocaleDateString('it-IT', { month: 'long' });
});

const currentYear = computed(() => {
    return currentDate.value.getFullYear();
});

// Sample events data
const events = [
    {
        id: 1,
        title: 'Webinar Cybersecurity',
        description: 'Sessione live su nuove minacce informatiche',
        date: new Date(2024, 0, 20),
        type: 'webinar',
        location: 'Online'
    },
    {
        id: 2,
        title: 'Scadenza Quiz GDPR',
        description: 'Ultimo giorno per completare il quiz finale',
        date: new Date(2024, 0, 25),
        type: 'deadline'
    },
    {
        id: 3,
        title: 'Sessione Q&A NIS2',
        description: 'Domande e risposte sulla direttiva NIS2',
        date: new Date(2024, 0, 28),
        type: 'qa',
        location: 'Online'
    }
];

const upcomingEvents = computed(() => {
    const today = new Date();
    return events
        .filter(event => event.date >= today)
        .sort((a, b) => a.date - b.date)
        .slice(0, 5);
});

const courseDeadlines = [
    {
        id: 1,
        courseTitle: 'Cybersecurity Base',
        description: 'Completamento modulo 3',
        deadline: new Date(2024, 0, 22),
        daysLeft: 7
    },
    {
        id: 2,
        courseTitle: 'GDPR per Incaricati',
        description: 'Quiz finale',
        deadline: new Date(2024, 0, 30),
        daysLeft: 15
    }
];

const calendarDays = computed(() => {
    const year = currentDate.value.getFullYear();
    const month = currentDate.value.getMonth();
    
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const startDate = new Date(firstDay);
    startDate.setDate(startDate.getDate() - firstDay.getDay() + 1);
    
    const days = [];
    const today = new Date();
    
    for (let i = 0; i < 42; i++) {
        const date = new Date(startDate);
        date.setDate(startDate.getDate() + i);
        
        const dayEvents = events.filter(event => 
            event.date.toDateString() === date.toDateString()
        );
        
        days.push({
            date: date.toISOString(),
            day: date.getDate(),
            isCurrentMonth: date.getMonth() === month,
            isToday: date.toDateString() === today.toDateString(),
            hasEvents: dayEvents.length > 0,
            events: dayEvents
        });
    }
    
    return days;
});

const previousMonth = () => {
    currentDate.value = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() - 1, 1);
};

const nextMonth = () => {
    currentDate.value = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() + 1, 1);
};

const selectEvent = (event) => {
    selectedEvent.value = event;
};

const getEventClass = (type) => {
    const classes = {
        'webinar': 'bg-cdf-teal text-white',
        'deadline': 'bg-cdf-amber text-cdf-ink',
        'qa': 'bg-cdf-deep text-white'
    };
    return classes[type] || 'bg-cdf-slate200 text-cdf-slate700';
};

const getEventDotClass = (type) => {
    const classes = {
        'webinar': 'bg-cdf-teal',
        'deadline': 'bg-cdf-amber',
        'qa': 'bg-cdf-deep'
    };
    return classes[type] || 'bg-cdf-slate200';
};

const getDeadlineClass = (daysLeft) => {
    if (daysLeft <= 3) return 'bg-red-500';
    if (daysLeft <= 7) return 'bg-cdf-amber';
    return 'bg-cdf-teal';
};

const getDeadlineBadgeClass = (daysLeft) => {
    if (daysLeft <= 3) return 'bg-red-100 text-red-700';
    if (daysLeft <= 7) return 'bg-cdf-amber/10 text-cdf-amber';
    return 'bg-cdf-teal/10 text-cdf-teal';
};

const formatEventDate = (date) => {
    return date.toLocaleDateString('it-IT', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const goToDashboard = () => {
    router.push('/dashboard');
};
</script>
