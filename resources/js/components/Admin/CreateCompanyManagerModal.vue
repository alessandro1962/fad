<template>
  <div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl border border-cdf-slate200 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
      <!-- Header -->
      <div class="flex items-center justify-between p-6 border-b border-cdf-slate200">
        <div>
          <h2 class="text-xl font-bold text-cdf-deep">Crea Company Manager</h2>
          <p class="text-cdf-slate600 mt-1">Crea un manager per {{ organization?.name }}</p>
        </div>
        <button
          @click="$emit('close')"
          class="p-2 text-cdf-slate600 hover:text-cdf-deep hover:bg-cdf-slate100 rounded-lg transition-colors"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <!-- Form -->
      <form @submit.prevent="createManager" class="p-6 space-y-6">
        <!-- Basic Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-cdf-deep mb-2">Nome Completo *</label>
            <input
              v-model="form.name"
              type="text"
              required
              class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-cdf-deep mb-2">Email *</label>
            <input
              v-model="form.email"
              type="email"
              required
              class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
            />
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-cdf-deep mb-2">Nome *</label>
            <input
              v-model="form.first_name"
              type="text"
              required
              class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-cdf-deep mb-2">Cognome *</label>
            <input
              v-model="form.last_name"
              type="text"
              required
              class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
            />
          </div>
        </div>

        <!-- Password -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-cdf-deep mb-2">Password *</label>
            <input
              v-model="form.password"
              type="password"
              required
              minlength="8"
              class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-cdf-deep mb-2">Conferma Password *</label>
            <input
              v-model="form.password_confirmation"
              type="password"
              required
              minlength="8"
              class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
            />
          </div>
        </div>

        <!-- Additional Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-cdf-deep mb-2">Azienda</label>
            <input
              v-model="form.company"
              type="text"
              readonly
              class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg bg-cdf-slate50 text-cdf-slate600"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-cdf-deep mb-2">Professione</label>
            <input
              v-model="form.profession"
              type="text"
              class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
            />
          </div>
        </div>

        <!-- Info Box -->
        <div class="bg-cdf-amber/10 border border-cdf-amber/20 rounded-xl p-4">
          <div class="flex items-start space-x-3">
            <svg class="w-5 h-5 text-cdf-amber mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
              <h4 class="text-sm font-medium text-cdf-amber">Informazioni importanti</h4>
              <p class="text-sm text-cdf-amber/80 mt-1">
                Il company manager avrà accesso alla dashboard aziendale per monitorare i progressi dei dipendenti.
                Riceverà un'email di benvenuto con le credenziali di accesso.
              </p>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end space-x-4 pt-6 border-t border-cdf-slate200">
          <button
            type="button"
            @click="$emit('close')"
            class="px-4 py-2 text-cdf-slate600 hover:text-cdf-deep transition-colors"
          >
            Annulla
          </button>
          <button
            type="submit"
            :disabled="loading"
            class="btn-primary"
          >
            <span v-if="loading">Creazione...</span>
            <span v-else>Crea Company Manager</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import api from '@/api'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  },
  organization: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['close', 'success'])

const loading = ref(false)

const form = ref({
  name: '',
  first_name: '',
  last_name: '',
  email: '',
  password: '',
  password_confirmation: '',
  company: '',
  profession: ''
})

// Watch for organization changes
watch(() => props.organization, (newOrg) => {
  if (newOrg) {
    form.value.company = newOrg.name
  }
}, { immediate: true })

// Watch for modal close to reset form
watch(() => props.isOpen, (isOpen) => {
  if (!isOpen) {
    resetForm()
  }
})

const resetForm = () => {
  form.value = {
    name: '',
    first_name: '',
    last_name: '',
    email: '',
    password: '',
    password_confirmation: '',
    company: props.organization?.name || '',
    profession: ''
  }
}

const createManager = async () => {
  try {
    loading.value = true
    
    const response = await api.post(`/v1/admin/organizations/${props.organization.id}/create-company-manager`, form.value)
    
    emit('success', response.data.data)
  } catch (error) {
    console.error('Error creating company manager:', error)
    if (error.response?.data?.errors) {
      const errors = error.response.data.errors
      const errorMessages = Object.values(errors).flat().join(', ')
      alert(`Errore: ${errorMessages}`)
    } else if (error.response?.data?.error) {
      alert(error.response.data.error)
    } else {
      alert('Errore nella creazione del company manager. Riprova.')
    }
  } finally {
    loading.value = false
  }
}
</script>
