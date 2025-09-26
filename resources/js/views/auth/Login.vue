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
                    Accedi al tuo account
                </h2>
            </div>

            <!-- Login Form -->
            <form class="mt-8 space-y-6" @submit.prevent="handleLogin">
                <div class="space-y-4">
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
                            placeholder="tua@email.com"
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
                            placeholder="La tua password"
                        />
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input
                            id="remember"
                            v-model="form.remember"
                            type="checkbox"
                            class="h-4 w-4 text-cdf-teal focus:ring-cdf-teal border-cdf-slate200 rounded"
                        />
                        <label for="remember" class="ml-2 block text-sm text-cdf-slate700">
                            Ricordami
                        </label>
                    </div>

                    <div class="text-sm">
                        <router-link to="/forgot-password" class="font-medium text-cdf-teal hover:text-cdf-deep">
                            Password dimenticata?
                        </router-link>
                    </div>
                </div>

                <div>
                    <button
                        type="submit"
                        :disabled="authStore.isLoading"
                        class="btn-primary w-full"
                    >
                        <span v-if="authStore.isLoading">Accesso in corso...</span>
                        <span v-else>Accedi</span>
                    </button>
                </div>

            </form>

            <!-- Social Login -->
            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-cdf-slate200" />
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-cdf-sand text-cdf-slate500">Oppure accedi con</span>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-1 gap-3">
                    <a
                        href="https://fad.campusdigitale.online/auth/google"
                        class="w-full inline-flex justify-center py-2 px-4 border border-cdf-slate200 rounded-lg shadow-sm bg-white text-sm font-medium text-cdf-slate700 hover:bg-cdf-slate50 transition-colors"
                    >
                        <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        Entra con Google
                    </a>

                    <a
                        href="https://fad.campusdigitale.online/auth/microsoft"
                        class="w-full inline-flex justify-center py-2 px-4 border border-cdf-slate200 rounded-lg shadow-sm bg-white text-sm font-medium text-cdf-slate700 hover:bg-cdf-slate50 transition-colors"
                    >
                        <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24">
                            <path fill="#F25022" d="M1 1h10v10H1z"/>
                            <path fill="#00A4EF" d="M13 1h10v10H13z"/>
                            <path fill="#7FBA00" d="M1 13h10v10H1z"/>
                            <path fill="#FFB900" d="M13 13h10v10H13z"/>
                        </svg>
                        Entra con Microsoft 365
                    </a>
                </div>
            </div>

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
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const router = useRouter();
const authStore = useAuthStore();

const form = ref({
    email: '',
    password: '',
    remember: false,
});

const errorMessage = ref('');

// Carica le credenziali salvate al mount
onMounted(() => {
    const rememberedEmail = localStorage.getItem('rememberedEmail');
    const rememberedPassword = localStorage.getItem('rememberedPassword');
    
    if (rememberedEmail && rememberedPassword) {
        form.value.email = rememberedEmail;
        form.value.password = rememberedPassword;
        form.value.remember = true;
    }
    
});

const handleLogin = async () => {
    errorMessage.value = '';
    
    const result = await authStore.login(form.value);
    
    if (result.success) {
        // Salva le credenziali se "ricordami" è selezionato
        if (form.value.remember) {
            localStorage.setItem('rememberedEmail', form.value.email);
            localStorage.setItem('rememberedPassword', form.value.password);
        } else {
            localStorage.removeItem('rememberedEmail');
            localStorage.removeItem('rememberedPassword');
        }
        
        // Reindirizza admin alla dashboard admin, utenti normali alla dashboard utente
        const redirectPath = authStore.isAdmin ? '/admin' : '/dashboard';
        router.push(redirectPath);
    } else {
        errorMessage.value = result.message;
    }
};


</script>
