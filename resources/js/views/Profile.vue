<template>
    <AppLayout>
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-cdf-deep">Il Mio Profilo</h1>
            <p class="text-cdf-slate700 mt-2">Gestisci le tue informazioni personali e le preferenze</p>
        </div>

        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Profile Info -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Personal Information -->
                <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6">
                    <h2 class="text-xl font-bold text-cdf-deep mb-6">Informazioni Personali</h2>
                    
                    <form @submit.prevent="updateProfile" class="space-y-6">
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-cdf-deep mb-2">Nome</label>
                                <input
                                    v-model="profileForm.first_name"
                                    type="text"
                                    class="input-field"
                                    required
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-cdf-deep mb-2">Cognome</label>
                                <input
                                    v-model="profileForm.last_name"
                                    type="text"
                                    class="input-field"
                                    required
                                />
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-cdf-deep mb-2">Email</label>
                            <input
                                v-model="profileForm.email"
                                type="email"
                                class="input-field"
                                required
                            />
                        </div>
                        
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-cdf-deep mb-2">Azienda</label>
                                <input
                                    v-model="profileForm.company"
                                    type="text"
                                    class="input-field"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-cdf-deep mb-2">Professione</label>
                                <input
                                    v-model="profileForm.profession"
                                    type="text"
                                    class="input-field"
                                />
                            </div>
                        </div>
                        
                        <div class="flex justify-end">
                            <button 
                                type="submit"
                                :disabled="isUpdating"
                                class="btn-primary"
                            >
                                {{ isUpdating ? 'Salvataggio...' : 'Salva Modifiche' }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Password Change -->
                <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6">
                    <h2 class="text-xl font-bold text-cdf-deep mb-6">Cambia Password</h2>
                    
                    <form @submit.prevent="changePassword" class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-cdf-deep mb-2">Password Attuale</label>
                            <input
                                v-model="passwordForm.current_password"
                                type="password"
                                class="input-field"
                                required
                            />
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-cdf-deep mb-2">Nuova Password</label>
                            <input
                                v-model="passwordForm.password"
                                type="password"
                                class="input-field"
                                required
                            />
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-cdf-deep mb-2">Conferma Nuova Password</label>
                            <input
                                v-model="passwordForm.password_confirmation"
                                type="password"
                                class="input-field"
                                required
                            />
                        </div>
                        
                        <div class="flex justify-end">
                            <button 
                                type="submit"
                                :disabled="isChangingPassword"
                                class="btn-primary"
                            >
                                {{ isChangingPassword ? 'Aggiornamento...' : 'Cambia Password' }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Privacy Settings -->
                <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6">
                    <h2 class="text-xl font-bold text-cdf-deep mb-6">Preferenze Privacy</h2>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-medium text-cdf-deep">Consenso Privacy</h3>
                                <p class="text-sm text-cdf-slate700">Accetto l'informativa sulla privacy</p>
                            </div>
                            <div class="flex items-center">
                                <input
                                    v-model="privacyForm.privacy_consent"
                                    type="checkbox"
                                    class="h-4 w-4 text-cdf-teal focus:ring-cdf-teal border-cdf-slate200 rounded"
                                />
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-medium text-cdf-deep">Newsletter e Comunicazioni</h3>
                                <p class="text-sm text-cdf-slate700">Ricevi aggiornamenti e offerte speciali</p>
                            </div>
                            <div class="flex items-center">
                                <input
                                    v-model="privacyForm.marketing_consent"
                                    type="checkbox"
                                    class="h-4 w-4 text-cdf-teal focus:ring-cdf-teal border-cdf-slate200 rounded"
                                />
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-medium text-cdf-deep">Notifiche Push</h3>
                                <p class="text-sm text-cdf-slate700">Ricevi notifiche per nuovi corsi e scadenze</p>
                            </div>
                            <div class="flex items-center">
                                <input
                                    v-model="privacyForm.push_notifications"
                                    type="checkbox"
                                    class="h-4 w-4 text-cdf-teal focus:ring-cdf-teal border-cdf-slate200 rounded"
                                />
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end">
                        <button 
                            @click="updatePrivacySettings"
                            :disabled="isUpdatingPrivacy"
                            class="btn-primary"
                        >
                            {{ isUpdatingPrivacy ? 'Salvataggio...' : 'Salva Preferenze' }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Profile Picture -->
                <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6">
                    <h3 class="font-bold text-cdf-deep mb-4">Foto Profilo</h3>
                    <div class="text-center">
                        <div class="w-24 h-24 bg-gradient-to-br from-cdf-teal to-cdf-deep rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <span class="text-white text-2xl font-bold">
                                {{ authStore.user?.first_name?.charAt(0) }}{{ authStore.user?.last_name?.charAt(0) }}
                            </span>
                        </div>
                        <button class="btn-secondary text-sm">
                            Cambia Foto
                        </button>
                    </div>
                </div>

                <!-- Account Stats -->
                <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6">
                    <h3 class="font-bold text-cdf-deep mb-4">Statistiche Account</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-cdf-slate700">Membro dal:</span>
                            <span class="font-medium text-cdf-deep">{{ formatDate(authStore.user?.created_at) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-cdf-slate700">Ultimo accesso:</span>
                            <span class="font-medium text-cdf-deep">{{ formatDate(authStore.user?.last_login_at) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-cdf-slate700">Corsi completati:</span>
                            <span class="font-medium text-cdf-deep">1</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-cdf-slate700">Ore di formazione:</span>
                            <span class="font-medium text-cdf-deep">7 ore</span>
                        </div>
                    </div>
                </div>

                <!-- Danger Zone -->
                <div class="bg-white rounded-2xl shadow-sm border border-red-200 p-6">
                    <h3 class="font-bold text-red-600 mb-4">Zona Pericolosa</h3>
                    <p class="text-sm text-cdf-slate700 mb-4">
                        Queste azioni sono irreversibili. Procedi con cautela.
                    </p>
                    <button 
                        @click="showDeleteModal = true"
                        class="w-full px-4 py-2 bg-red-600 text-white rounded-xl font-semibold hover:bg-red-700 transition-colors duration-200"
                    >
                        Elimina Account
                    </button>
                </div>
            </div>
        </div>

        <!-- Delete Account Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-6">
                <div class="text-center">
                    <div class="w-16 h-16 bg-red-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-cdf-deep mb-2">Elimina Account</h3>
                    <p class="text-cdf-slate700 mb-6">
                        Sei sicuro di voler eliminare il tuo account? Questa azione non può essere annullata.
                    </p>
                    <div class="flex space-x-3">
                        <button 
                            @click="showDeleteModal = false"
                            class="flex-1 btn-secondary"
                        >
                            Annulla
                        </button>
                        <button 
                            @click="deleteAccount"
                            :disabled="isDeleting"
                            class="flex-1 px-4 py-2 bg-red-600 text-white rounded-xl font-semibold hover:bg-red-700 transition-colors duration-200"
                        >
                            {{ isDeleting ? 'Eliminazione...' : 'Elimina' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import AppLayout from '@/components/Layout/AppLayout.vue';
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();
const showDeleteModal = ref(false);
const isUpdating = ref(false);
const isChangingPassword = ref(false);
const isUpdatingPrivacy = ref(false);
const isDeleting = ref(false);

const profileForm = reactive({
    first_name: authStore.user?.first_name || '',
    last_name: authStore.user?.last_name || '',
    email: authStore.user?.email || '',
    company: authStore.user?.company || '',
    profession: authStore.user?.profession || ''
});

const passwordForm = reactive({
    current_password: '',
    password: '',
    password_confirmation: ''
});

const privacyForm = reactive({
    privacy_consent: authStore.user?.privacy_consent || false,
    marketing_consent: authStore.user?.marketing_consent || false,
    push_notifications: true
});

const updateProfile = async () => {
    isUpdating.value = true;
    try {
        // API call to update profile
        console.log('Updating profile:', profileForm);
        // Simulate API call
        await new Promise(resolve => setTimeout(resolve, 1000));
        alert('Profilo aggiornato con successo!');
    } catch (error) {
        console.error('Error updating profile:', error);
        alert('Errore durante l\'aggiornamento del profilo');
    } finally {
        isUpdating.value = false;
    }
};

const changePassword = async () => {
    if (passwordForm.password !== passwordForm.password_confirmation) {
        alert('Le password non coincidono');
        return;
    }
    
    isChangingPassword.value = true;
    try {
        // API call to change password
        console.log('Changing password');
        // Simulate API call
        await new Promise(resolve => setTimeout(resolve, 1000));
        alert('Password cambiata con successo!');
        passwordForm.current_password = '';
        passwordForm.password = '';
        passwordForm.password_confirmation = '';
    } catch (error) {
        console.error('Error changing password:', error);
        alert('Errore durante il cambio password');
    } finally {
        isChangingPassword.value = false;
    }
};

const updatePrivacySettings = async () => {
    isUpdatingPrivacy.value = true;
    try {
        // API call to update privacy settings
        console.log('Updating privacy settings:', privacyForm);
        // Simulate API call
        await new Promise(resolve => setTimeout(resolve, 1000));
        alert('Preferenze aggiornate con successo!');
    } catch (error) {
        console.error('Error updating privacy settings:', error);
        alert('Errore durante l\'aggiornamento delle preferenze');
    } finally {
        isUpdatingPrivacy.value = false;
    }
};

const deleteAccount = async () => {
    isDeleting.value = true;
    try {
        // API call to delete account
        console.log('Deleting account');
        // Simulate API call
        await new Promise(resolve => setTimeout(resolve, 2000));
        alert('Account eliminato con successo');
        await authStore.logout();
    } catch (error) {
        console.error('Error deleting account:', error);
        alert('Errore durante l\'eliminazione dell\'account');
    } finally {
        isDeleting.value = false;
        showDeleteModal.value = false;
    }
};

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleDateString('it-IT');
};
</script>
