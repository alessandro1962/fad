<template>
    <div class="min-h-screen flex items-center justify-center bg-cdf-sand py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-cdf-teal">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1721 9z"></path>
                    </svg>
                </div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-cdf-deep">
                    Reimposta password
                </h2>
                <p class="mt-2 text-center text-sm text-cdf-slate600">
                    Inserisci la tua nuova password
                </p>
            </div>

            <form class="mt-8 space-y-6" @submit.prevent="resetPassword">
                <div>
                    <label for="email" class="block text-sm font-medium text-cdf-deep">
                        Email
                    </label>
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        required
                        readonly
                        class="mt-1 appearance-none relative block w-full px-3 py-2 border border-cdf-slate200 bg-cdf-slate50 text-cdf-slate600 rounded-lg sm:text-sm"
                    />
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-cdf-deep">
                        Nuova password
                    </label>
                    <input
                        id="password"
                        v-model="form.password"
                        type="password"
                        required
                        class="mt-1 appearance-none relative block w-full px-3 py-2 border border-cdf-slate200 placeholder-cdf-slate400 text-cdf-deep rounded-lg focus:outline-none focus:ring-cdf-teal focus:border-cdf-teal focus:z-10 sm:text-sm"
                        placeholder="Inserisci la nuova password"
                    />
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-cdf-deep">
                        Conferma password
                    </label>
                    <input
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                        required
                        class="mt-1 appearance-none relative block w-full px-3 py-2 border border-cdf-slate200 placeholder-cdf-slate400 text-cdf-deep rounded-lg focus:outline-none focus:ring-cdf-teal focus:border-cdf-teal focus:z-10 sm:text-sm"
                        placeholder="Conferma la nuova password"
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
                            Reimpostazione in corso...
                        </span>
                        <span v-else>Reimposta password</span>
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
import { ref, reactive, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import api from '@/api'

const router = useRouter()
const route = useRoute()

const isLoading = ref(false)
const successMessage = ref('')
const errorMessage = ref('')

const form = reactive({
    email: '',
    password: '',
    password_confirmation: '',
    token: ''
})

onMounted(() => {
    // Get token and email from URL parameters
    form.token = route.params.token || ''
    form.email = route.query.email || ''
    
    if (!form.token || !form.email) {
        errorMessage.value = 'Link di reset non valido. Richiedi un nuovo link.'
    }
})

const resetPassword = async () => {
    if (!form.token || !form.email) {
        errorMessage.value = 'Link di reset non valido.'
        return
    }

    if (form.password !== form.password_confirmation) {
        errorMessage.value = 'Le password non coincidono.'
        return
    }

    if (form.password.length < 8) {
        errorMessage.value = 'La password deve essere di almeno 8 caratteri.'
        return
    }

    isLoading.value = true
    successMessage.value = ''
    errorMessage.value = ''

    try {
        const response = await api.post('/v1/auth/reset-password', {
            email: form.email,
            password: form.password,
            password_confirmation: form.password_confirmation,
            token: form.token
        })

        successMessage.value = 'Password reimpostata con successo! Ora puoi accedere con la nuova password.'
        
        // Redirect to login after 3 seconds
        setTimeout(() => {
            router.push('/login')
        }, 3000)
        
    } catch (error) {
        console.error('Errore nel reset password:', error)
        
        if (error.response?.data?.message) {
            errorMessage.value = error.response.data.message
        } else if (error.response?.data?.errors) {
            // Handle validation errors
            const errors = error.response.data.errors
            if (errors.email) {
                errorMessage.value = errors.email[0]
            } else if (errors.password) {
                errorMessage.value = errors.password[0]
            } else if (errors.token) {
                errorMessage.value = 'Link di reset non valido o scaduto. Richiedi un nuovo link.'
            } else {
                errorMessage.value = 'Errore durante la reimpostazione della password.'
            }
        } else {
            errorMessage.value = 'Errore durante la reimpostazione della password. Riprova.'
        }
    } finally {
        isLoading.value = false
    }
}
</script>
