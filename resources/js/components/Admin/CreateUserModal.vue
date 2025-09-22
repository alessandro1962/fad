<template>
  <div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
      <!-- Header -->
      <div class="flex items-center justify-between p-6 border-b border-cdf-slate200">
        <h3 class="text-lg font-semibold text-cdf-deep">
          Nuovo Utente
        </h3>
        <button
          @click="closeModal"
          class="text-cdf-slate400 hover:text-cdf-slate600 transition-colors"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <!-- Content -->
      <div class="p-6">
        <form @submit.prevent="createUser" class="space-y-4">
          <!-- Name -->
          <div>
            <label class="block text-sm font-medium text-cdf-deep mb-2">
              Nome Completo *
            </label>
            <input
              v-model="form.name"
              type="text"
              class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
              required
            />
          </div>

          <!-- First Name -->
          <div>
            <label class="block text-sm font-medium text-cdf-deep mb-2">
              Nome *
            </label>
            <input
              v-model="form.first_name"
              type="text"
              class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
              required
            />
          </div>

          <!-- Last Name -->
          <div>
            <label class="block text-sm font-medium text-cdf-deep mb-2">
              Cognome *
            </label>
            <input
              v-model="form.last_name"
              type="text"
              class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
              required
            />
          </div>

          <!-- Email -->
          <div>
            <label class="block text-sm font-medium text-cdf-deep mb-2">
              Email *
            </label>
            <input
              v-model="form.email"
              type="email"
              class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
              required
            />
          </div>

          <!-- Password -->
          <div>
            <label class="block text-sm font-medium text-cdf-deep mb-2">
              Password *
            </label>
            <input
              v-model="form.password"
              type="password"
              class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
              required
              minlength="8"
            />
          </div>

          <!-- Password Confirmation -->
          <div>
            <label class="block text-sm font-medium text-cdf-deep mb-2">
              Conferma Password *
            </label>
            <input
              v-model="form.password_confirmation"
              type="password"
              class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
              required
              minlength="8"
            />
          </div>

          <!-- Organization -->
          <div>
            <label class="block text-sm font-medium text-cdf-deep mb-2">
              Azienda
            </label>
            <select
              v-model="form.organization_id"
              class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
            >
              <option value="">Seleziona azienda</option>
              <option
                v-for="org in organizations"
                :key="org.id"
                :value="org.id"
              >
                {{ org.name }}
              </option>
            </select>
          </div>

          <!-- Company (for display) -->
          <div>
            <label class="block text-sm font-medium text-cdf-deep mb-2">
              Nome Azienda (display)
            </label>
            <input
              v-model="form.company"
              type="text"
              class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
            />
          </div>

          <!-- Profession -->
          <div>
            <label class="block text-sm font-medium text-cdf-deep mb-2">
              Professione
            </label>
            <input
              v-model="form.profession"
              type="text"
              class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
            />
          </div>

          <!-- Admin Status -->
          <div class="flex items-center">
            <input
              v-model="form.is_admin"
              type="checkbox"
              class="h-4 w-4 text-cdf-teal focus:ring-cdf-teal border-cdf-slate300 rounded"
            />
            <label class="ml-2 block text-sm text-cdf-deep">
              Amministratore
            </label>
          </div>

          <!-- Company Manager Status -->
          <div class="flex items-center">
            <input
              v-model="form.is_company_manager"
              type="checkbox"
              class="h-4 w-4 text-cdf-teal focus:ring-cdf-teal border-cdf-slate300 rounded"
            />
            <label class="ml-2 block text-sm text-cdf-deep">
              Manager Aziendale
            </label>
          </div>

          <!-- Marketing Consent -->
          <div class="flex items-center">
            <input
              v-model="form.marketing_consent"
              type="checkbox"
              class="h-4 w-4 text-cdf-teal focus:ring-cdf-teal border-cdf-slate300 rounded"
            />
            <label class="ml-2 block text-sm text-cdf-deep">
              Consenso Marketing
            </label>
          </div>

          <!-- Privacy Consent -->
          <div class="flex items-center">
            <input
              v-model="form.privacy_consent"
              type="checkbox"
              class="h-4 w-4 text-cdf-teal focus:ring-cdf-teal border-cdf-slate300 rounded"
            />
            <label class="ml-2 block text-sm text-cdf-deep">
              Consenso Privacy
            </label>
          </div>

          <!-- Actions -->
          <div class="flex gap-3 pt-4">
            <button
              type="button"
              @click="closeModal"
              class="flex-1 px-4 py-2 text-cdf-slate600 border border-cdf-slate200 rounded-lg hover:bg-cdf-slate50 transition-colors"
            >
              Annulla
            </button>
            <button
              type="submit"
              :disabled="loading"
              class="flex-1 px-4 py-2 bg-cdf-teal text-white rounded-lg hover:bg-cdf-deep transition-colors disabled:opacity-50"
            >
              <span v-if="loading">Creando...</span>
              <span v-else>Crea Utente</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/api'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
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
  organization_id: '',
  company: '',
  profession: '',
  is_admin: false,
  is_company_manager: false,
  marketing_consent: false,
  privacy_consent: false
})

const organizations = ref([])

const loadOrganizations = async () => {
  try {
    const response = await api.get('/v1/admin/organizations?all=true')
    organizations.value = response.data.data
  } catch (error) {
    console.error('Error loading organizations:', error)
  }
}

const createUser = async () => {
  loading.value = true
  
  try {
    const response = await api.post('/v1/admin/users', form.value)
    
    emit('success', response.data.data)
    closeModal()
  } catch (error) {
    console.error('Errore nella creazione utente:', error)
    if (error.response?.data?.errors) {
      const errors = error.response.data.errors
      const errorMessages = Object.values(errors).flat().join(', ')
      alert(`Errore: ${errorMessages}`)
    } else {
      alert('Errore nella creazione dell\'utente. Riprova.')
    }
  } finally {
    loading.value = false
  }
}

const closeModal = () => {
  // Reset form
  form.value = {
    name: '',
    first_name: '',
    last_name: '',
    email: '',
    password: '',
    password_confirmation: '',
    organization_id: '',
    company: '',
    profession: '',
    is_admin: false,
    is_company_manager: false,
    marketing_consent: false,
    privacy_consent: false
  }
  emit('close')
}

onMounted(() => {
  loadOrganizations()
})
</script>
