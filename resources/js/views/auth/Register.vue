<template>
    <div class="min-h-screen bg-cdf-sand flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <div class="flex justify-center">
                    <div class="h-12 w-12 rounded-xl bg-white shadow ring-1 ring-cdf-slate200 grid place-items-center">
                        <svg viewBox="0 0 64 64" class="h-8 w-8" aria-hidden="true">
                            <rect x="0" y="0" width="64" height="64" rx="12" fill="none"/>
                            <path d="M46,16 A18,18 0 1 0 46,48" fill="none" stroke="#00A7B7" stroke-width="4" stroke-linecap="round"/>
                            <polygon points="30,24 40,32 30,40" fill="#FFC857"/>
                        </svg>
                    </div>
                </div>
                <h2 class="mt-6 text-3xl font-extrabold text-cdf-deep">
                    Crea il tuo account
                </h2>
                <p class="mt-2 text-sm text-cdf-slate700">
                    Oppure
                    <router-link to="/login" class="font-medium text-cdf-teal hover:text-cdf-deep">
                        accedi al tuo account esistente
                    </router-link>
                </p>
            </div>

            <!-- Registration Form -->
            <form class="mt-8 space-y-6" @submit.prevent="handleRegister">
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-cdf-deep">
                                Nome
                            </label>
                            <input
                                id="first_name"
                                v-model="form.first_name"
                                type="text"
                                required
                                class="input-field mt-1"
                                placeholder="Mario"
                            />
                        </div>
                        
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-cdf-deep">
                                Cognome
                            </label>
                            <input
                                id="last_name"
                                v-model="form.last_name"
                                type="text"
                                required
                                class="input-field mt-1"
                                placeholder="Rossi"
                            />
                        </div>
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-cdf-deep">
                            Email
                        </label>
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            required
                            class="input-field mt-1"
                            placeholder="mario.rossi@email.com"
                        />
                    </div>
                    
                    <div>
                        <label for="password" class="block text-sm font-medium text-cdf-deep">
                            Password
                        </label>
                        <input
                            id="password"
                            v-model="form.password"
                            type="password"
                            required
                            class="input-field mt-1"
                            placeholder="Minimo 8 caratteri"
                        />
                    </div>
                    
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-cdf-deep">
                            Conferma Password
                        </label>
                        <input
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            type="password"
                            required
                            class="input-field mt-1"
                            placeholder="Ripeti la password"
                        />
                    </div>
                    
                    <div>
                        <label for="company" class="block text-sm font-medium text-cdf-deep">
                            Azienda (opzionale)
                        </label>
                        <input
                            id="company"
                            v-model="form.company"
                            type="text"
                            class="input-field mt-1"
                            placeholder="Nome azienda"
                        />
                    </div>
                    
                    <div>
                        <label for="profession" class="block text-sm font-medium text-cdf-deep">
                            Professione (opzionale)
                        </label>
                        <input
                            id="profession"
                            v-model="form.profession"
                            type="text"
                            class="input-field mt-1"
                            placeholder="Es. Sviluppatore, Manager, etc."
                        />
                    </div>
                </div>

                <!-- Privacy and Marketing Consents -->
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input
                                id="privacy_consent"
                                v-model="form.privacy_consent"
                                type="checkbox"
                                required
                                class="h-4 w-4 text-cdf-teal focus:ring-cdf-teal border-cdf-slate200 rounded"
                            />
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="privacy_consent" class="text-cdf-slate700">
                                Accetto l'informativa sulla 
                                <a href="#" class="font-medium text-cdf-teal hover:text-cdf-deep">privacy</a>
                                <span class="text-red-500">*</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input
                                id="marketing_consent"
                                v-model="form.marketing_consent"
                                type="checkbox"
                                class="h-4 w-4 text-cdf-teal focus:ring-cdf-teal border-cdf-slate200 rounded"
                            />
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="marketing_consent" class="text-cdf-slate700">
                                Desidero ricevere comunicazioni promozionali e newsletter
                            </label>
                        </div>
                    </div>
                </div>

                <div>
                    <button
                        type="submit"
                        :disabled="authStore.isLoading"
                        class="btn-primary w-full"
                    >
                        <span v-if="authStore.isLoading">Registrazione in corso...</span>
                        <span v-else>Crea account</span>
                    </button>
                </div>

                <!-- Social Registration -->
                <div class="mt-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-cdf-slate200" />
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-cdf-sand text-cdf-slate700">Oppure registrati con</span>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-2 gap-3">
                        <button
                            type="button"
                            @click="authStore.socialLogin('google')"
                            class="w-full inline-flex justify-center py-2 px-4 border border-cdf-slate200 rounded-xl shadow-sm bg-white text-sm font-medium text-cdf-slate700 hover:bg-gray-50"
                        >
                            <svg class="w-5 h-5" viewBox="0 0 24 24">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                            </svg>
                            <span class="ml-2">Google</span>
                        </button>

                        <button
                            type="button"
                            @click="authStore.socialLogin('microsoft')"
                            class="w-full inline-flex justify-center py-2 px-4 border border-cdf-slate200 rounded-xl shadow-sm bg-white text-sm font-medium text-cdf-slate700 hover:bg-gray-50"
                        >
                            <svg class="w-5 h-5" viewBox="0 0 24 24">
                                <path fill="#F25022" d="M1 1h10v10H1z"/>
                                <path fill="#00A4EF" d="M13 1h10v10H13z"/>
                                <path fill="#7FBA00" d="M1 13h10v10H1z"/>
                                <path fill="#FFB900" d="M13 13h10v10H13z"/>
                            </svg>
                            <span class="ml-2">Microsoft</span>
                        </button>
                    </div>
                </div>
            </form>

            <!-- Error Message -->
            <div v-if="errorMessage" class="rounded-xl bg-red-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-800">{{ errorMessage }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const router = useRouter();
const authStore = useAuthStore();

const form = ref({
    first_name: '',
    last_name: '',
    email: '',
    password: '',
    password_confirmation: '',
    company: '',
    profession: '',
    privacy_consent: false,
    marketing_consent: false,
});

const errorMessage = ref('');

const handleRegister = async () => {
    errorMessage.value = '';
    
    if (form.value.password !== form.value.password_confirmation) {
        errorMessage.value = 'Le password non coincidono';
        return;
    }
    
    const result = await authStore.register(form.value);
    
    if (result.success) {
        router.push('/dashboard');
    } else {
        errorMessage.value = result.message;
    }
};
</script>
