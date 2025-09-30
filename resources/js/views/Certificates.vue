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
                class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 hover:shadow-lg transition-shadow duration-200"
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
                        <div class="relative">
                            <button 
                                @click="toggleSocialMenu(certificate.id)"
                                class="px-4 py-2 border border-cdf-slate200 text-cdf-slate700 rounded-xl font-semibold hover:bg-cdf-sand transition-colors duration-200"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                                </svg>
                            </button>
                            
                            <!-- Social Menu Dropdown -->
                            <div 
                                v-if="activeSocialMenu === certificate.id"
                                class="absolute right-0 top-full mt-2 w-48 bg-white rounded-xl shadow-lg border border-cdf-slate200 py-2 z-50"
                            >
                                <button 
                                    @click="shareOnSocial('linkedin', certificate)"
                                    class="w-full px-4 py-2 text-left hover:bg-cdf-slate50 flex items-center"
                                >
                                    <svg class="w-4 h-4 mr-3 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                    </svg>
                                    LinkedIn
                                </button>
                                <button 
                                    @click="shareOnSocial('twitter', certificate)"
                                    class="w-full px-4 py-2 text-left hover:bg-cdf-slate50 flex items-center"
                                >
                                    <svg class="w-4 h-4 mr-3 text-blue-400" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                    </svg>
                                    Twitter
                                </button>
                                <button 
                                    @click="shareOnSocial('facebook', certificate)"
                                    class="w-full px-4 py-2 text-left hover:bg-cdf-slate50 flex items-center"
                                >
                                    <svg class="w-4 h-4 mr-3 text-blue-800" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                    </svg>
                                    Facebook
                                </button>
                                <button 
                                    @click="copyCertificateLink(certificate)"
                                    class="w-full px-4 py-2 text-left hover:bg-cdf-slate50 flex items-center"
                                >
                                    <svg class="w-4 h-4 mr-3 text-cdf-slate700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                    Copia Link
                                </button>
                            </div>
                        </div>
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
const activeSocialMenu = ref(null);

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
    
    // Close social menu when clicking outside
    document.addEventListener('click', (event) => {
        if (!event.target.closest('.relative')) {
            activeSocialMenu.value = null;
        }
    });
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

// Social sharing functions
const toggleSocialMenu = (certificateId) => {
    activeSocialMenu.value = activeSocialMenu.value === certificateId ? null : certificateId;
};

const shareOnSocial = (platform, certificate) => {
    const text = `Ho appena ottenuto il certificato "${certificate.title}" su Campus Digitale Forma! 🎓`;
    const url = `${window.location.origin}/certificates/${certificate.public_uid}`;
    
    let shareUrl = '';
    
    switch (platform) {
        case 'linkedin':
            shareUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(url)}&summary=${encodeURIComponent(text)}`;
            break;
        case 'twitter':
            shareUrl = `https://twitter.com/intent/tweet?text=${encodeURIComponent(text)}&url=${encodeURIComponent(url)}`;
            break;
        case 'facebook':
            shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}&quote=${encodeURIComponent(text)}`;
            break;
    }
    
    if (shareUrl) {
        window.open(shareUrl, '_blank', 'width=600,height=400');
    }
    
    // Close the menu
    activeSocialMenu.value = null;
};

const copyCertificateLink = async (certificate) => {
    const url = `${window.location.origin}/certificates/${certificate.public_uid}`;
    
    try {
        await navigator.clipboard.writeText(url);
        alert('Link del certificato copiato negli appunti!');
    } catch (err) {
        console.error('Failed to copy link:', err);
        alert('Errore nella copia del link. Riprova.');
    }
    
    // Close the menu
    activeSocialMenu.value = null;
};

// Close social menu when clicking outside
const closeSocialMenu = () => {
    activeSocialMenu.value = null;
};

</script>
