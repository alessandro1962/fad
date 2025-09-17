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
            <h1 class="text-3xl font-bold text-cdf-deep">I Miei Attestati</h1>
            <p class="text-cdf-slate700 mt-2">Visualizza e condividi le tue certificazioni</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-cdf-deep/10 rounded-2xl">
                        <svg class="w-6 h-6 text-cdf-deep" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-cdf-slate700">Attestati Totali</p>
                        <p class="text-2xl font-bold text-cdf-deep">{{ certificates.length }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-cdf-teal/10 rounded-2xl">
                        <svg class="w-6 h-6 text-cdf-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-cdf-slate700">Ore Formazione</p>
                        <p class="text-2xl font-bold text-cdf-deep">{{ totalHours }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-cdf-amber/10 rounded-2xl">
                        <svg class="w-6 h-6 text-cdf-amber" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-cdf-slate700">Livello Raggiunto</p>
                        <p class="text-2xl font-bold text-cdf-deep">Intermedio</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="text-center py-12">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-cdf-teal"></div>
            <p class="mt-4 text-cdf-slate700">Caricamento attestati...</p>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="text-center py-12">
            <div class="text-red-500 mb-4">{{ error }}</div>
            <button @click="loadCertificates" class="bg-cdf-teal text-white px-4 py-2 rounded-lg hover:bg-cdf-deep transition-colors">
                Riprova
            </button>
        </div>

        <!-- Empty State -->
        <div v-else-if="certificates.length === 0" class="text-center py-12">
            <div class="text-6xl mb-4">📜</div>
            <h3 class="text-xl font-semibold text-cdf-deep mb-2">Nessun attestato disponibile</h3>
            <p class="text-cdf-slate700">Completa i tuoi corsi per ottenere i primi attestati!</p>
        </div>

        <!-- Certificates Grid -->
        <div v-else class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div 
                v-for="certificate in certificates" 
                :key="certificate.id"
                class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 overflow-hidden hover:shadow-lg transition-shadow duration-200"
            >
                <!-- Certificate Preview -->
                <div class="h-48 bg-gradient-to-br from-cdf-deep/10 to-cdf-teal/10 flex items-center justify-center relative">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-white rounded-2xl shadow-lg flex items-center justify-center mx-auto mb-3">
                            <svg class="w-8 h-8 text-cdf-deep" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-cdf-deep">{{ certificate.kind === 'course' ? 'Corso' : 'Percorso' }}</p>
                    </div>
                    
                    <!-- Verification Badge -->
                    <div class="absolute top-4 right-4">
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Certificate Info -->
                <div class="p-6">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-lg font-bold text-cdf-deep">{{ certificate.title }}</h3>
                        <span 
                            class="px-2 py-1 rounded-lg text-xs font-medium"
                            :class="getTypeBadgeClass(certificate.kind)"
                        >
                            {{ certificate.kind === 'course' ? 'Corso' : 'Percorso' }}
                        </span>
                    </div>
                    
                    <p class="text-cdf-slate700 text-sm mb-4">{{ certificate.description }}</p>
                    
                    <!-- Certificate Details -->
                    <div class="space-y-2 mb-4">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-cdf-slate700">Rilasciato il:</span>
                            <span class="font-medium text-cdf-deep">{{ new Date(certificate.issued_at).toLocaleDateString('it-IT') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-cdf-slate700">Durata:</span>
                            <span class="font-medium text-cdf-deep">{{ certificate.hours_total }} ore</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-cdf-slate700">ID Certificato:</span>
                            <span class="font-mono text-xs text-cdf-slate700">{{ certificate.public_uid }}</span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex space-x-2">
                        <button 
                            @click="viewCertificate(certificate)"
                            class="flex-1 px-4 py-2 bg-cdf-deep text-white rounded-xl font-semibold hover:bg-cdf-deep/90 transition-colors duration-200 text-sm"
                        >
                            Visualizza
                        </button>
                        <button 
                            @click="downloadCertificate(certificate)"
                            class="px-4 py-2 border border-cdf-slate200 text-cdf-slate700 rounded-xl font-semibold hover:bg-cdf-sand transition-colors duration-200"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-if="certificates.length === 0" class="text-center py-12">
            <div class="w-24 h-24 bg-cdf-slate200 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-12 h-12 text-cdf-slate700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-cdf-deep mb-2">Nessun attestato disponibile</h3>
            <p class="text-cdf-slate700 mb-6">Completa i tuoi corsi per ottenere i primi attestati</p>
            <router-link to="/i-miei-corsi" class="btn-primary">
                Vai ai Miei Corsi
            </router-link>
        </div>

        <!-- Verification Modal -->
        <div v-if="showVerificationModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6 border-b border-cdf-slate200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold text-cdf-deep">Verifica Attestato</h3>
                        <button 
                            @click="showVerificationModal = false"
                            class="p-2 hover:bg-cdf-slate200 rounded-lg transition-colors duration-200"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="text-center mb-6">
                        <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h4 class="text-lg font-semibold text-cdf-deep mb-2">Attestato Verificato</h4>
                        <p class="text-cdf-slate700">Questo attestato è stato verificato e risulta autentico</p>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between py-2 border-b border-cdf-slate200">
                            <span class="text-cdf-slate700">Titolo:</span>
                            <span class="font-medium text-cdf-deep">{{ selectedCertificate?.title }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-cdf-slate200">
                            <span class="text-cdf-slate700">Rilasciato a:</span>
                            <span class="font-medium text-cdf-deep">{{ authStore.user?.first_name }} {{ authStore.user?.last_name }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-cdf-slate200">
                            <span class="text-cdf-slate700">Data rilascio:</span>
                            <span class="font-medium text-cdf-deep">{{ selectedCertificate?.issued_at ? new Date(selectedCertificate.issued_at).toLocaleDateString('it-IT') : 'Non disponibile' }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-cdf-slate200">
                            <span class="text-cdf-slate700">ID Certificato:</span>
                            <span class="font-mono text-sm text-cdf-slate700">{{ selectedCertificate?.public_uid || 'Non disponibile' }}</span>
                        </div>
                        <div class="flex justify-between py-2">
                            <span class="text-cdf-slate700">Durata corso:</span>
                            <span class="font-medium text-cdf-deep">{{ selectedCertificate?.hours_total ? selectedCertificate.hours_total + ' ore' : 'Non disponibile' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import AppLayout from '@/components/Layout/AppLayout.vue';
import { useAuthStore } from '@/stores/auth';
import api from '@/api';

const authStore = useAuthStore();
const router = useRouter();
const showVerificationModal = ref(false);
const selectedCertificate = ref(null);

// Real certificates data - loaded from API
const certificates = ref([]);
const loading = ref(true);
const error = ref(null);

const totalHours = computed(() => {
    return certificates.value.reduce((total, cert) => {
        const hours = cert.hours_total || 0;
        return total + hours;
    }, 0);
});

// Load certificates from API
const loadCertificates = async () => {
    try {
        loading.value = true;
        const response = await api.get('/v1/certificates');
        certificates.value = response.data.data || [];
    } catch (err) {
        console.error('Error loading certificates:', err);
        error.value = 'Errore nel caricamento degli attestati';
    } finally {
        loading.value = false;
    }
};

// Load certificates on component mount
onMounted(() => {
    loadCertificates();
});

const getTypeBadgeClass = (kind) => {
    return kind === 'track' 
        ? 'bg-cdf-teal/10 text-cdf-teal' 
        : 'bg-cdf-amber/10 text-cdf-amber';
};

const viewCertificate = (certificate) => {
    console.log('Certificate object:', certificate);
    selectedCertificate.value = certificate;
    showVerificationModal.value = true;
};

const downloadCertificate = async (certificate) => {
    try {
        console.log('Downloading certificate:', certificate);
        
        // Create download URL
        const downloadUrl = `/api/v1/certificates/${certificate.id}/download`;
        
        // Create a temporary link element and trigger download
        const link = document.createElement('a');
        link.href = downloadUrl;
        link.download = `${certificate.title.replace(/[^a-z0-9]/gi, '_').toLowerCase()}.pdf`;
        
        // Add authentication header by using fetch first
        const response = await fetch(downloadUrl, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${authStore.token}`,
                'Accept': 'application/pdf'
            }
        });
        
        if (response.ok) {
            // Get the blob from response
            const blob = await response.blob();
            
            // Create object URL and trigger download
            const url = window.URL.createObjectURL(blob);
            link.href = url;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            window.URL.revokeObjectURL(url);
        } else {
            console.error('Download failed:', response.status, response.statusText);
            alert('Errore nel download dell\'attestato. Riprova più tardi.');
        }
    } catch (error) {
        console.error('Error downloading certificate:', error);
        alert('Errore nel download dell\'attestato. Riprova più tardi.');
    }
};

const goToDashboard = () => {
    router.push('/dashboard');
};

</script>
