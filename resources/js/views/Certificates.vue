<template>
    <AppLayout>
        <!-- Header -->
        <div class="mb-8">
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

        <!-- Certificates Grid -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
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
                        <p class="text-sm font-medium text-cdf-deep">{{ certificate.type }}</p>
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
                            :class="getTypeBadgeClass(certificate.type)"
                        >
                            {{ certificate.type }}
                        </span>
                    </div>
                    
                    <p class="text-cdf-slate700 text-sm mb-4">{{ certificate.description }}</p>
                    
                    <!-- Certificate Details -->
                    <div class="space-y-2 mb-4">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-cdf-slate700">Rilasciato il:</span>
                            <span class="font-medium text-cdf-deep">{{ certificate.issuedAt }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-cdf-slate700">Durata:</span>
                            <span class="font-medium text-cdf-deep">{{ certificate.duration }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-cdf-slate700">ID Certificato:</span>
                            <span class="font-mono text-xs text-cdf-slate700">{{ certificate.certificateId }}</span>
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
                        <button 
                            @click="shareCertificate(certificate)"
                            class="px-4 py-2 border border-cdf-slate200 text-cdf-slate700 rounded-xl font-semibold hover:bg-cdf-sand transition-colors duration-200"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
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
                            <span class="font-medium text-cdf-deep">{{ selectedCertificate?.issuedAt }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-cdf-slate200">
                            <span class="text-cdf-slate700">ID Certificato:</span>
                            <span class="font-mono text-sm text-cdf-slate700">{{ selectedCertificate?.certificateId }}</span>
                        </div>
                        <div class="flex justify-between py-2">
                            <span class="text-cdf-slate700">Durata corso:</span>
                            <span class="font-medium text-cdf-deep">{{ selectedCertificate?.duration }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import AppLayout from '@/components/Layout/AppLayout.vue';
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();
const showVerificationModal = ref(false);
const selectedCertificate = ref(null);

// Sample certificates data
const certificates = [
    {
        id: 1,
        title: 'Cybersecurity Base',
        description: 'Certificazione in fondamenti di sicurezza informatica',
        type: 'Corso',
        issuedAt: '15 Gen 2024',
        duration: '3 ore',
        certificateId: 'CDF-CYB-2024-001',
        publicUrl: 'https://campusdigitale.it/certs/CDF-CYB-2024-001'
    },
    {
        id: 2,
        title: 'GDPR per Incaricati',
        description: 'Certificazione in gestione dati personali secondo GDPR',
        type: 'Corso',
        issuedAt: '10 Gen 2024',
        duration: '4 ore',
        certificateId: 'CDF-GDPR-2024-002',
        publicUrl: 'https://campusdigitale.it/certs/CDF-GDPR-2024-002'
    },
    {
        id: 3,
        title: 'Percorso Privacy Completo',
        description: 'Certificazione completa in privacy e protezione dati',
        type: 'Percorso',
        issuedAt: '5 Dic 2023',
        duration: '12 ore',
        certificateId: 'CDF-PATH-2023-001',
        publicUrl: 'https://campusdigitale.it/certs/CDF-PATH-2023-001'
    }
];

const totalHours = computed(() => {
    return certificates.reduce((total, cert) => {
        const hours = parseInt(cert.duration);
        return total + hours;
    }, 0);
});

const getTypeBadgeClass = (type) => {
    return type === 'Percorso' 
        ? 'bg-cdf-teal/10 text-cdf-teal' 
        : 'bg-cdf-amber/10 text-cdf-amber';
};

const viewCertificate = (certificate) => {
    selectedCertificate.value = certificate;
    showVerificationModal.value = true;
};

const downloadCertificate = (certificate) => {
    console.log('Download certificate:', certificate);
    // Implement download functionality
};

const shareCertificate = (certificate) => {
    console.log('Share certificate:', certificate);
    // Implement sharing functionality
    if (navigator.share) {
        navigator.share({
            title: certificate.title,
            text: `Ho completato il corso "${certificate.title}" su Campus Digitale Forma!`,
            url: certificate.publicUrl
        });
    } else {
        // Fallback to copy link
        navigator.clipboard.writeText(certificate.publicUrl);
        alert('Link copiato negli appunti!');
    }
};
</script>
