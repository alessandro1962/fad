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
