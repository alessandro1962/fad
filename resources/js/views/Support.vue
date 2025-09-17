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
            <h1 class="text-3xl font-bold text-cdf-deep">Supporto</h1>
            <p class="text-cdf-slate700 mt-2">Siamo qui per aiutarti. Trova le risposte che cerchi o contattaci direttamente</p>
        </div>

        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Quick Help -->
                <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6">
                    <h2 class="text-xl font-bold text-cdf-deep mb-6">Aiuto Rapido</h2>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div 
                            v-for="helpItem in quickHelp" 
                            :key="helpItem.id"
                            class="p-4 border border-cdf-slate200 rounded-xl hover:border-cdf-teal/30 transition-colors duration-200 cursor-pointer"
                            @click="selectHelpItem(helpItem)"
                        >
                            <div class="flex items-center space-x-3">
                                <div 
                                    class="w-10 h-10 rounded-xl flex items-center justify-center"
                                    :class="helpItem.bgClass"
                                >
                                    <component :is="helpItem.icon" class="w-5 h-5" :class="helpItem.iconClass" />
                                </div>
                                <div>
                                    <h3 class="font-semibold text-cdf-deep">{{ helpItem.title }}</h3>
                                    <p class="text-sm text-cdf-slate700">{{ helpItem.description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FAQ Section -->
                <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6">
                    <h2 class="text-xl font-bold text-cdf-deep mb-6">Domande Frequenti</h2>
                    <div class="space-y-4">
                        <div 
                            v-for="faq in faqs" 
                            :key="faq.id"
                            class="border border-cdf-slate200 rounded-xl"
                        >
                            <button 
                                @click="toggleFaq(faq.id)"
                                class="w-full p-4 text-left flex items-center justify-between hover:bg-cdf-sand transition-colors duration-200"
                            >
                                <span class="font-medium text-cdf-deep">{{ faq.question }}</span>
                                <svg 
                                    class="w-5 h-5 text-cdf-slate700 transition-transform duration-200"
                                    :class="{ 'rotate-180': openFaqs.includes(faq.id) }"
                                    fill="none" 
                                    stroke="currentColor" 
                                    viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div 
                                v-if="openFaqs.includes(faq.id)"
                                class="px-4 pb-4 text-cdf-slate700"
                            >
                                {{ faq.answer }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6">
                    <h2 class="text-xl font-bold text-cdf-deep mb-6">Contattaci</h2>
                    <form @submit.prevent="submitSupportRequest" class="space-y-6">
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-cdf-deep mb-2">Nome</label>
                                <input
                                    v-model="supportForm.name"
                                    type="text"
                                    class="input-field"
                                    required
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-cdf-deep mb-2">Email</label>
                                <input
                                    v-model="supportForm.email"
                                    type="email"
                                    class="input-field"
                                    required
                                />
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-cdf-deep mb-2">Oggetto</label>
                            <select v-model="supportForm.subject" class="input-field" required>
                                <option value="">Seleziona un argomento</option>
                                <option value="technical">Problema Tecnico</option>
                                <option value="billing">Fatturazione</option>
                                <option value="course">Contenuto Corso</option>
                                <option value="account">Account</option>
                                <option value="other">Altro</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-cdf-deep mb-2">Messaggio</label>
                            <textarea
                                v-model="supportForm.message"
                                rows="5"
                                class="input-field"
                                placeholder="Descrivi il tuo problema o la tua domanda..."
                                required
                            ></textarea>
                        </div>
                        
                        <div class="flex justify-end">
                            <button 
                                type="submit"
                                :disabled="isSubmitting"
                                class="btn-primary"
                            >
                                {{ isSubmitting ? 'Invio in corso...' : 'Invia Richiesta' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Contact Info -->
                <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6">
                    <h3 class="font-bold text-cdf-deep mb-4">Informazioni di Contatto</h3>
                    <div class="space-y-4">
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 bg-cdf-teal/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-cdf-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-cdf-deep">Email</p>
                                <p class="text-sm text-cdf-slate700">supporto@campusdigitale.it</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 bg-cdf-teal/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-cdf-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-cdf-deep">Telefono</p>
                                <p class="text-sm text-cdf-slate700">+39 02 1234 5678</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 bg-cdf-teal/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-cdf-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-cdf-deep">Orari</p>
                                <p class="text-sm text-cdf-slate700">Lun-Ven: 9:00-18:00</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Live Chat -->
                <div class="bg-gradient-to-br from-cdf-deep to-cdf-teal rounded-2xl p-6 text-white">
                    <h3 class="font-bold mb-4">Chat Live</h3>
                    <p class="text-sm opacity-90 mb-4">
                        Hai bisogno di aiuto immediato? Chatta con il nostro team di supporto.
                    </p>
                    <button class="w-full bg-white text-cdf-deep rounded-xl py-2 font-semibold hover:bg-white/90 transition-colors duration-200">
                        Inizia Chat
                    </button>
                </div>

                <!-- Status Page -->
                <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6">
                    <h3 class="font-bold text-cdf-deep mb-4">Stato Servizi</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-cdf-slate700">Piattaforma</span>
                            <div class="flex items-center space-x-2">
                                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                <span class="text-sm text-green-600 font-medium">Operativo</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-cdf-slate700">Video</span>
                            <div class="flex items-center space-x-2">
                                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                <span class="text-sm text-green-600 font-medium">Operativo</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-cdf-slate700">Email</span>
                            <div class="flex items-center space-x-2">
                                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                <span class="text-sm text-green-600 font-medium">Operativo</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { useRouter } from 'vue-router';
import AppLayout from '@/components/Layout/AppLayout.vue';

const router = useRouter();
const openFaqs = ref([]);
const isSubmitting = ref(false);

const supportForm = reactive({
    name: '',
    email: '',
    subject: '',
    message: ''
});

const quickHelp = [
    {
        id: 1,
        title: 'Accesso Account',
        description: 'Problemi con login o password',
        icon: 'svg',
        bgClass: 'bg-cdf-teal/10',
        iconClass: 'text-cdf-teal'
    },
    {
        id: 2,
        title: 'Corsi e Contenuti',
        description: 'Domande sui corsi e materiali',
        icon: 'svg',
        bgClass: 'bg-cdf-amber/10',
        iconClass: 'text-cdf-amber'
    },
    {
        id: 3,
        title: 'Fatturazione',
        description: 'Problemi con pagamenti e fatture',
        icon: 'svg',
        bgClass: 'bg-cdf-deep/10',
        iconClass: 'text-cdf-deep'
    },
    {
        id: 4,
        title: 'Attestati',
        description: 'Rilascio e download certificati',
        icon: 'svg',
        bgClass: 'bg-green-100',
        iconClass: 'text-green-600'
    }
];

const faqs = [
    {
        id: 1,
        question: 'Come posso accedere ai miei corsi?',
        answer: 'Dopo aver effettuato l\'acquisto, i tuoi corsi saranno disponibili nella sezione "I Miei Corsi" del tuo dashboard. Puoi accedervi in qualsiasi momento con le tue credenziali.'
    },
    {
        id: 2,
        question: 'Quanto tempo ho per completare un corso?',
        answer: 'I corsi non hanno scadenze fisse. Puoi completarli al tuo ritmo e accedere ai contenuti per sempre dopo l\'acquisto.'
    },
    {
        id: 3,
        question: 'Come posso scaricare il mio attestato?',
        answer: 'Una volta completato un corso, il tuo attestato sarà disponibile nella sezione "Attestati". Puoi scaricarlo in formato PDF o condividerlo tramite link pubblico.'
    },
    {
        id: 4,
        question: 'Posso rimborsare un corso acquistato?',
        answer: 'Offriamo un periodo di rimborso di 14 giorni dall\'acquisto. Contatta il nostro supporto per maggiori informazioni.'
    },
    {
        id: 5,
        question: 'I corsi sono compatibili con dispositivi mobili?',
        answer: 'Sì, la nostra piattaforma è completamente responsive e ottimizzata per dispositivi mobili, tablet e desktop.'
    }
];

const toggleFaq = (faqId) => {
    const index = openFaqs.value.indexOf(faqId);
    if (index > -1) {
        openFaqs.value.splice(index, 1);
    } else {
        openFaqs.value.push(faqId);
    }
};

const selectHelpItem = (helpItem) => {
    console.log('Selected help item:', helpItem);
    // Navigate to specific help section or open modal
};

const submitSupportRequest = async () => {
    isSubmitting.value = true;
    try {
        // API call to submit support request
        console.log('Submitting support request:', supportForm);
        // Simulate API call
        await new Promise(resolve => setTimeout(resolve, 2000));
        alert('Richiesta inviata con successo! Ti risponderemo entro 24 ore.');
        
        // Reset form
        supportForm.name = '';
        supportForm.email = '';
        supportForm.subject = '';
        supportForm.message = '';
    } catch (error) {
        console.error('Error submitting support request:', error);
        alert('Errore durante l\'invio della richiesta. Riprova più tardi.');
    } finally {
        isSubmitting.value = false;
    }
};

const goToDashboard = () => {
    router.push('/dashboard');
};
</script>
