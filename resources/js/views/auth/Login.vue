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
                        <a href="#" class="font-medium text-cdf-teal hover:text-cdf-deep">
                            Password dimenticata?
                        </a>
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
