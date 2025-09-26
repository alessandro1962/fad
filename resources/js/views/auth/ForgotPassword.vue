<template>
    <div class="min-h-screen flex items-center justify-center bg-cdf-sand py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-cdf-teal">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                    </svg>
                </div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-cdf-deep">
                    Password dimenticata?
                </h2>
                <p class="mt-2 text-center text-sm text-cdf-slate600">
                    Inserisci la tua email e ti invieremo un link per reimpostare la password
                </p>
            </div>

            <form class="mt-8 space-y-6" @submit.prevent="sendResetLink">
                <div>
                    <label for="email" class="block text-sm font-medium text-cdf-deep">
                        Indirizzo email
                    </label>
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        required
                        class="mt-1 appearance-none relative block w-full px-3 py-2 border border-cdf-slate200 placeholder-cdf-slate400 text-cdf-deep rounded-lg focus:outline-none focus:ring-cdf-teal focus:border-cdf-teal focus:z-10 sm:text-sm"
                        placeholder="Inserisci la tua email"
                    />
                </div>

                <div>
                    <button
                        type="submit"
                        :disabled="isLoading"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-cdf-teal hover:bg-cdf-deep focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cdf-teal disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span v-if="isLoading" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Invio in corso...
                        </span>
                        <span v-else>Invia link di reset</span>
                    </button>
                </div>

                <div class="text-center">
                    <router-link to="/login" class="font-medium text-cdf-teal hover:text-cdf-deep">
                        Torna al login
                    </router-link>
                </div>

                <!-- Success Message -->
                <div v-if="successMessage" class="rounded-lg bg-green-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">
                                {{ successMessage }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Error Message -->
                <div v-if="errorMessage" class="rounded-lg bg-red-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800">
                                {{ errorMessage }}
                            </p>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/api'

const router = useRouter()

const isLoading = ref(false)
const successMessage = ref('')
const errorMessage = ref('')

const form = reactive({
    email: ''
})

const sendResetLink = async () => {
    isLoading.value = true
    successMessage.value = ''
    errorMessage.value = ''

    try {
        const response = await api.post('/v1/auth/forgot-password', {
            email: form.email
        })

        successMessage.value = 'Link di reset inviato! Controlla la tua email.'
        
        // Clear form
        form.email = ''
        
    } catch (error) {
        console.error('Errore nel reset password:', error)
        
        if (error.response?.data?.message) {
            errorMessage.value = error.response.data.message
        } else {
            errorMessage.value = 'Errore durante l\'invio del link di reset. Riprova.'
        }
    } finally {
        isLoading.value = false
    }
}
</script>
