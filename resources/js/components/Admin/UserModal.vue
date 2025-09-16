<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
      <!-- Header -->
      <div class="flex items-center justify-between p-6 border-b border-cdf-slate200">
        <div class="flex items-center gap-4">
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
          <h2 class="text-xl font-bold text-cdf-deep">
            {{ isEdit ? 'Modifica Utente' : 'Nuovo Utente' }}
          </h2>
        </div>
        <button
          @click="$emit('close')"
          class="p-2 text-cdf-slate400 hover:text-cdf-slate600 hover:bg-cdf-slate200 rounded-lg transition-all"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <!-- Form -->
      <form @submit.prevent="saveUser" class="p-6 space-y-6">
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
        <div v-if="!isEdit" class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-cdf-deep mb-2">Password *</label>
            <input
              v-model="form.password"
              type="password"
              :required="!isEdit"
              class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-cdf-deep mb-2">Conferma Password *</label>
            <input
              v-model="form.password_confirmation"
              type="password"
              :required="!isEdit"
              class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
            />
          </div>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-cdf-deep mb-2">Nuova Password</label>
            <input
              v-model="form.password"
              type="password"
              class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
              placeholder="Lascia vuoto per non cambiare"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-cdf-deep mb-2">Conferma Nuova Password</label>
            <input
              v-model="form.password_confirmation"
              type="password"
              class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
              placeholder="Lascia vuoto per non cambiare"
            />
          </div>
        </div>

        <!-- Company & Profession -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-cdf-deep mb-2">Azienda</label>
            <input
              v-model="form.company"
              type="text"
              class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
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

        <!-- Permissions -->
        <div class="space-y-4">
          <h3 class="text-lg font-semibold text-cdf-deep">Permessi</h3>
          
          <div class="flex items-center">
            <input
              v-model="form.is_admin"
              type="checkbox"
              id="is_admin"
              class="h-4 w-4 text-cdf-teal focus:ring-cdf-teal border-cdf-slate300 rounded"
            />
            <label for="is_admin" class="ml-2 block text-sm text-cdf-deep">
              Amministratore
            </label>
          </div>

          <div class="flex items-center">
            <input
              v-model="form.marketing_consent"
              type="checkbox"
              id="marketing_consent"
              class="h-4 w-4 text-cdf-teal focus:ring-cdf-teal border-cdf-slate300 rounded"
            />
            <label for="marketing_consent" class="ml-2 block text-sm text-cdf-deep">
              Consenso Marketing
            </label>
          </div>

          <div class="flex items-center">
            <input
              v-model="form.privacy_consent"
              type="checkbox"
              id="privacy_consent"
              class="h-4 w-4 text-cdf-teal focus:ring-cdf-teal border-cdf-slate300 rounded"
            />
            <label for="privacy_consent" class="ml-2 block text-sm text-cdf-deep">
              Consenso Privacy
            </label>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-end gap-4 pt-6 border-t border-cdf-slate200">
          <button
            type="button"
            @click="$emit('close')"
            class="px-4 py-2 text-cdf-slate600 border border-cdf-slate200 rounded-lg hover:bg-cdf-slate50 transition-colors"
          >
            Annulla
          </button>
          <button
            type="submit"
            :disabled="saving"
            class="px-4 py-2 bg-cdf-teal text-white rounded-lg hover:bg-cdf-deep transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="saving" class="flex items-center">
              <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Salvataggio...
            </span>
            <span v-else>{{ isEdit ? 'Aggiorna' : 'Crea' }} Utente</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, watch } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/api'

const router = useRouter()

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  },
  user: {
    type: Object,
    default: null
  },
  isEdit: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['close', 'saved'])

const saving = ref(false)

// Form data
const form = reactive({
  name: '',
  first_name: '',
  last_name: '',
  email: '',
  password: '',
  password_confirmation: '',
  company: '',
  profession: '',
  is_admin: false,
  marketing_consent: false,
  privacy_consent: false
})

// Watch for user changes
watch(() => props.user, (newUser) => {
  if (newUser) {
    form.name = newUser.name || ''
    form.first_name = newUser.first_name || ''
    form.last_name = newUser.last_name || ''
    form.email = newUser.email || ''
    form.company = newUser.company || ''
    form.profession = newUser.profession || ''
    form.is_admin = newUser.is_admin || false
    form.marketing_consent = newUser.marketing_consent || false
    form.privacy_consent = newUser.privacy_consent || false
    form.password = ''
    form.password_confirmation = ''
  } else {
    // Reset form for new user
    Object.keys(form).forEach(key => {
      if (typeof form[key] === 'boolean') {
        form[key] = false
      } else {
        form[key] = ''
      }
    })
  }
}, { immediate: true })

const goToDashboard = () => {
  router.push('/admin')
}

const saveUser = async () => {
  try {
    saving.value = true
    
    // Prepare payload
    const payload = { ...form }
    
    // Remove empty password fields for edit
    if (props.isEdit && !payload.password) {
      delete payload.password
      delete payload.password_confirmation
    }
    
    let response
    if (props.isEdit) {
      response = await api.put(`/v1/admin/users/${props.user.id}`, payload)
    } else {
      response = await api.post('/v1/admin/users', payload)
    }
    
    emit('saved', response.data.data)
  } catch (error) {
    console.error('Errore:', error)
    alert(error.response?.data?.message || 'Errore nel salvataggio dell\'utente')
  } finally {
    saving.value = false
  }
}
</script>
