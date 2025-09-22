<template>
  <div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl border border-cdf-slate200 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
      <!-- Header -->
      <div class="flex items-center justify-between p-6 border-b border-cdf-slate200">
        <h2 class="text-xl font-bold text-cdf-deep">
          {{ organization ? 'Modifica Azienda' : 'Nuova Azienda' }}
        </h2>
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
      <form @submit.prevent="saveOrganization" class="p-6 space-y-6">
        <!-- Basic Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-cdf-deep mb-2">Nome Azienda *</label>
            <input
              v-model="form.name"
              type="text"
              required
              class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-cdf-deep mb-2">P.IVA</label>
            <input
              v-model="form.vat_number"
              type="text"
              class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
            />
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-cdf-deep mb-2">Email</label>
            <input
              v-model="form.email"
              type="email"
              class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-cdf-deep mb-2">Telefono</label>
            <input
              v-model="form.phone"
              type="text"
              class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
            />
          </div>
        </div>

        <!-- Address -->
        <div>
          <label class="block text-sm font-medium text-cdf-deep mb-2">Indirizzo</label>
          <textarea
            v-model="form.address"
            rows="3"
            class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
          ></textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div>
            <label class="block text-sm font-medium text-cdf-deep mb-2">Città</label>
            <input
              v-model="form.city"
              type="text"
              class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-cdf-deep mb-2">CAP</label>
            <input
              v-model="form.postal_code"
              type="text"
              class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-cdf-deep mb-2">Paese</label>
            <input
              v-model="form.country"
              type="text"
              class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
            />
          </div>
        </div>

        <!-- SSO Type -->
        <div>
          <label class="block text-sm font-medium text-cdf-deep mb-2">Tipo SSO</label>
          <select
            v-model="form.sso_type"
            class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
          >
            <option value="none">Nessuno</option>
            <option value="google">Google</option>
            <option value="azuread">Azure AD</option>
          </select>
        </div>

        <!-- Status -->
        <div class="flex items-center">
          <input
            v-model="form.is_active"
            type="checkbox"
            class="h-4 w-4 text-cdf-teal focus:ring-cdf-teal border-cdf-slate300 rounded"
          />
          <label class="ml-2 block text-sm text-cdf-deep">
            Azienda attiva
          </label>
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
            <span v-if="loading">Salvataggio...</span>
            <span v-else>{{ organization ? 'Aggiorna' : 'Crea' }} Azienda</span>
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
  vat_number: '',
  email: '',
  phone: '',
  address: '',
  city: '',
  postal_code: '',
  country: '',
  sso_type: 'none',
  is_active: true
})

const resetForm = () => {
  form.value = {
    name: '',
    vat_number: '',
    email: '',
    phone: '',
    address: '',
    city: '',
    postal_code: '',
    country: '',
    sso_type: 'none',
    is_active: true
  }
}

// Watch for organization changes to populate form
watch(() => props.organization, (newOrg) => {
  if (newOrg) {
    form.value = {
      name: newOrg.name || '',
      vat_number: newOrg.vat_number || '',
      email: newOrg.email || '',
      phone: newOrg.phone || '',
      address: newOrg.address || '',
      city: newOrg.city || '',
      postal_code: newOrg.postal_code || '',
      country: newOrg.country || '',
      sso_type: newOrg.sso_type || 'none',
      is_active: newOrg.is_active !== false
    }
  } else {
    resetForm()
  }
}, { immediate: true })

const saveOrganization = async () => {
  try {
    loading.value = true
    
    let response
    if (props.organization) {
      response = await api.put(`/v1/admin/organizations/${props.organization.id}`, form.value)
    } else {
      response = await api.post('/v1/admin/organizations', form.value)
    }
    
    emit('success', response.data.data)
  } catch (error) {
    console.error('Error saving organization:', error)
    if (error.response?.data?.errors) {
      const errors = error.response.data.errors
      const errorMessages = Object.values(errors).flat().join(', ')
      alert(`Errore: ${errorMessages}`)
    } else {
      alert('Errore nel salvataggio dell\'azienda. Riprova.')
    }
  } finally {
    loading.value = false
  }
}
</script>
