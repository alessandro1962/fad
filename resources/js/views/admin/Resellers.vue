<template>
  <div class="min-h-screen bg-cdf-sand">
    <AppLayout>
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-3xl font-bold text-cdf-dark">Gestione Rivenditori</h1>
              <p class="text-cdf-gray mt-2">Gestisci i rivenditori e assegna gettoni</p>
            </div>
            <button
              @click="showCreateModal = true"
              class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors"
            >
              Crea Rivenditore
            </button>
          </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
          <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
              <div class="p-2 bg-cdf-primary rounded-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-cdf-gray">Rivenditori Totali</p>
                <p class="text-2xl font-semibold text-cdf-dark">{{ statistics.total_resellers || 0 }}</p>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
              <div class="p-2 bg-green-500 rounded-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-cdf-gray">Rivenditori Attivi</p>
                <p class="text-2xl font-semibold text-cdf-dark">{{ statistics.active_resellers || 0 }}</p>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
              <div class="p-2 bg-blue-500 rounded-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-cdf-gray">Gettoni Assegnati</p>
                <p class="text-2xl font-semibold text-cdf-dark">{{ statistics.total_tokens_assigned || 0 }}</p>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
              <div class="p-2 bg-purple-500 rounded-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-cdf-gray">Clienti Totali</p>
                <p class="text-2xl font-semibold text-cdf-dark">{{ statistics.total_clients || 0 }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Resellers Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-cdf-dark">
              Rivenditori ({{ resellers.total || 0 }})
            </h3>
          </div>
          
          <div v-if="loading" class="p-8 text-center">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-cdf-primary"></div>
            <p class="mt-2 text-gray-600">Caricamento...</p>
          </div>
          
          <div v-else-if="resellers.data && resellers.data.length === 0" class="p-8 text-center text-gray-500">
            Nessun rivenditore trovato
          </div>
          
          <div v-else class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Rivenditore
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Azienda
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Gettoni Rimanenti
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Clienti
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Stato
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Azioni
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="reseller in resellers.data" :key="reseller.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-10 w-10">
                        <div class="h-10 w-10 rounded-full bg-cdf-primary flex items-center justify-center">
                          <span class="text-white font-medium text-sm">
                            {{ reseller.user.name.charAt(0).toUpperCase() }}
                          </span>
                        </div>
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">
                          {{ reseller.user.name }}
                        </div>
                        <div class="text-sm text-gray-500">
                          {{ reseller.user.email }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ reseller.company_name }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    <span class="font-medium text-lg">{{ reseller.tokens ? reseller.tokens.available_tokens : 0 }}</span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ reseller.clients_count || 0 }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span
                      :class="[
                        'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                        reseller.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                      ]"
                    >
                      {{ reseller.is_active ? 'Attivo' : 'Inattivo' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex space-x-2">
                      <button
                        @click="viewReseller(reseller)"
                        class="text-cdf-primary hover:text-cdf-primary-dark"
                      >
                        Visualizza
                      </button>
                      <button
                        @click="editReseller(reseller)"
                        class="text-blue-600 hover:text-blue-900"
                      >
                        Modifica
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div v-if="resellers.data && resellers.data.length > 0" class="px-6 py-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
              <div class="text-sm text-gray-700">
                Mostrando {{ resellers.from }} a {{ resellers.to }} di {{ resellers.total }} risultati
              </div>
              <div class="flex space-x-2">
                <button
                  v-if="resellers.prev_page_url"
                  @click="loadResellers(resellers.current_page - 1)"
                  class="px-3 py-1 border border-gray-300 rounded-md text-sm hover:bg-gray-50"
                >
                  Precedente
                </button>
                <button
                  v-if="resellers.next_page_url"
                  @click="loadResellers(resellers.current_page + 1)"
                  class="px-3 py-1 border border-gray-300 rounded-md text-sm hover:bg-gray-50"
                >
                  Successiva
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Create Reseller Modal -->
      <div v-if="showCreateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
          <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Crea Nuovo Rivenditore</h3>
            <form @submit.prevent="createReseller" class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Nome Completo</label>
                <input
                  v-model="createForm.name"
                  type="text"
                  required
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-cdf-primary focus:border-cdf-primary"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Nome</label>
                <input
                  v-model="createForm.first_name"
                  type="text"
                  required
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-cdf-primary focus:border-cdf-primary"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Cognome</label>
                <input
                  v-model="createForm.last_name"
                  type="text"
                  required
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-cdf-primary focus:border-cdf-primary"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input
                  v-model="createForm.email"
                  type="email"
                  required
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-cdf-primary focus:border-cdf-primary"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Password</label>
                <input
                  v-model="createForm.password"
                  type="password"
                  required
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-cdf-primary focus:border-cdf-primary"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Nome Azienda</label>
                <input
                  v-model="createForm.company_name"
                  type="text"
                  required
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-cdf-primary focus:border-cdf-primary"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Email Contatto</label>
                <input
                  v-model="createForm.contact_email"
                  type="email"
                  required
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-cdf-primary focus:border-cdf-primary"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Gettoni Iniziali</label>
                <input
                  v-model="createForm.tokens_assigned"
                  type="number"
                  min="0"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-cdf-primary focus:border-cdf-primary"
                />
              </div>
              <div class="flex space-x-3 pt-4">
                <button
                  type="submit"
                  :disabled="loading"
                  class="flex-1 bg-cdf-primary text-white px-4 py-2 rounded-lg hover:bg-cdf-primary-dark disabled:opacity-50"
                >
                  {{ loading ? 'Creazione...' : 'Crea' }}
                </button>
                <button
                  type="button"
                  @click="showCreateModal = false"
                  class="flex-1 bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400"
                >
                  Annulla
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Assign Tokens Modal -->
      <div v-if="showAssignModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
          <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
              Assegna Gettoni a {{ selectedReseller?.user?.name }}
            </h3>
            <form @submit.prevent="assignTokens" class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Numero Gettoni</label>
                <input
                  v-model="assignForm.tokens"
                  type="number"
                  min="1"
                  required
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-cdf-primary focus:border-cdf-primary"
                />
              </div>
              <div class="flex space-x-3 pt-4">
                <button
                  type="submit"
                  :disabled="loading"
                  class="flex-1 bg-cdf-primary text-white px-4 py-2 rounded-lg hover:bg-cdf-primary-dark disabled:opacity-50"
                >
                  {{ loading ? 'Assegnazione...' : 'Assegna' }}
                </button>
                <button
                  type="button"
                  @click="showAssignModal = false"
                  class="flex-1 bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400"
                >
                  Annulla
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- View Reseller Modal -->
      <div v-if="showViewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
          <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Dettagli Rivenditore</h3>
            <div v-if="selectedResellerForView" class="space-y-3">
              <div>
                <label class="block text-sm font-medium text-gray-700">Nome Completo</label>
                <p class="text-sm text-gray-900">{{ selectedResellerForView.user.name }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <p class="text-sm text-gray-900">{{ selectedResellerForView.user.email }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Azienda</label>
                <p class="text-sm text-gray-900">{{ selectedResellerForView.company_name }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Email Contatto</label>
                <p class="text-sm text-gray-900">{{ selectedResellerForView.contact_email }}</p>
              </div>
              <div v-if="selectedResellerForView.contact_phone">
                <label class="block text-sm font-medium text-gray-700">Telefono</label>
                <p class="text-sm text-gray-900">{{ selectedResellerForView.contact_phone }}</p>
              </div>
              <div v-if="selectedResellerForView.tokens">
                <label class="block text-sm font-medium text-gray-700">Gettoni Assegnati</label>
                <p class="text-sm text-gray-900">{{ selectedResellerForView.tokens.tokens_assigned }}</p>
              </div>
              <div v-if="selectedResellerForView.tokens">
                <label class="block text-sm font-medium text-gray-700">Gettoni Utilizzati</label>
                <p class="text-sm text-gray-900">{{ selectedResellerForView.tokens.tokens_used }}</p>
              </div>
            </div>
            <div class="mt-6">
              <button
                @click="showViewModal = false"
                class="w-full bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600"
              >
                Chiudi
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Edit Reseller Modal -->
      <div v-if="showEditModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
          <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Modifica Rivenditore</h3>
            <form @submit.prevent="updateReseller" class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Nome Completo</label>
                <input
                  v-model="editForm.name"
                  type="text"
                  required
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-cdf-primary focus:border-cdf-primary"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Nome</label>
                <input
                  v-model="editForm.first_name"
                  type="text"
                  required
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-cdf-primary focus:border-cdf-primary"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Cognome</label>
                <input
                  v-model="editForm.last_name"
                  type="text"
                  required
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-cdf-primary focus:border-cdf-primary"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input
                  v-model="editForm.email"
                  type="email"
                  required
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-cdf-primary focus:border-cdf-primary"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Nome Azienda</label>
                <input
                  v-model="editForm.company_name"
                  type="text"
                  required
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-cdf-primary focus:border-cdf-primary"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Email Contatto</label>
                <input
                  v-model="editForm.contact_email"
                  type="email"
                  required
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-cdf-primary focus:border-cdf-primary"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Telefono</label>
                <input
                  v-model="editForm.contact_phone"
                  type="text"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-cdf-primary focus:border-cdf-primary"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Indirizzo</label>
                <textarea
                  v-model="editForm.address"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-cdf-primary focus:border-cdf-primary"
                  rows="3"
                ></textarea>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Partita IVA</label>
                <input
                  v-model="editForm.vat_number"
                  type="text"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-cdf-primary focus:border-cdf-primary"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Gettoni da Assegnare</label>
                <input
                  v-model="editForm.tokens_to_assign"
                  type="number"
                  min="0"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-cdf-primary focus:border-cdf-primary"
                  placeholder="0"
                />
                <p class="mt-1 text-xs text-gray-500">Gettoni attuali: {{ selectedResellerForEdit?.tokens?.available_tokens || 0 }}</p>
              </div>
              <div class="flex items-center">
                <input
                  v-model="editForm.is_active"
                  type="checkbox"
                  class="h-4 w-4 text-cdf-primary focus:ring-cdf-primary border-gray-300 rounded"
                />
                <label class="ml-2 block text-sm text-gray-900">Attivo</label>
              </div>
              <div class="flex space-x-3">
                <button
                  type="submit"
                  :disabled="loading"
                  class="flex-1 bg-cdf-primary text-white px-4 py-2 rounded-md hover:bg-cdf-primary-dark disabled:opacity-50"
                >
                  {{ loading ? 'Salvataggio...' : 'Salva' }}
                </button>
                <button
                  type="button"
                  @click="showEditModal = false"
                  class="flex-1 bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600"
                >
                  Annulla
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </AppLayout>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import AppLayout from '@/components/Layout/AppLayout.vue'
import api from '@/api'

// Data
const resellers = ref({})
const statistics = ref({})
const loading = ref(false)
const showCreateModal = ref(false)
const showAssignModal = ref(false)
const selectedReseller = ref(null)

// Forms
const createForm = ref({
  name: '',
  first_name: '',
  last_name: '',
  email: '',
  password: '',
  company_name: '',
  contact_email: '',
  tokens_assigned: 0
})

const assignForm = ref({
  tokens: 1
})

const showViewModal = ref(false)
const showEditModal = ref(false)
const selectedResellerForView = ref(null)
const selectedResellerForEdit = ref(null)

const editForm = ref({
  name: '',
  first_name: '',
  last_name: '',
  email: '',
  company_name: '',
  contact_email: '',
  contact_phone: '',
  address: '',
  vat_number: '',
  tokens_to_assign: 0,
  is_active: true
})

// Methods
const loadResellers = async (page = 1) => {
  loading.value = true
  try {
    console.log('Loading resellers...')
    const resellersResponse = await api.get(`/v1/admin/resellers?page=${page}&per_page=15`)
    console.log('Resellers response:', resellersResponse.data)
    
    resellers.value = resellersResponse.data.data
    console.log('Resellers set to:', resellers.value)
    
    // Try to load statistics separately
    try {
      const statsResponse = await api.get('/v1/admin/resellers/statistics')
      statistics.value = statsResponse.data.data
    } catch (statsError) {
      console.log('Statistics error (non-blocking):', statsError)
      statistics.value = {
        total_resellers: 0,
        active_resellers: 0,
        total_tokens_assigned: 0,
        total_tokens_used: 0,
        total_clients: 0
      }
    }
  } catch (error) {
    console.error('Error loading resellers:', error)
  } finally {
    loading.value = false
  }
}

const createReseller = async () => {
  loading.value = true
  try {
    await api.post('/v1/admin/resellers', createForm.value)
    
    // Reset form
    createForm.value = {
      name: '',
      first_name: '',
      last_name: '',
      email: '',
      password: '',
      company_name: '',
      contact_email: '',
      tokens_assigned: 0
    }
    showCreateModal.value = false
    
    // Reload data
    await loadResellers(1) // Force reload first page
    
    alert('Rivenditore creato con successo!')
  } catch (error) {
    console.error('Error creating reseller:', error)
    alert('Errore durante la creazione del rivenditore')
  } finally {
    loading.value = false
  }
}

const showAssignTokensModal = (reseller) => {
  selectedReseller.value = reseller
  assignForm.value.tokens = 1
  showAssignModal.value = true
}

const assignTokens = async () => {
  loading.value = true
  try {
    await api.post(`/v1/admin/resellers/${selectedReseller.value.id}/assign-tokens`, {
      tokens: parseInt(assignForm.value.tokens)
    })
    
    showAssignModal.value = false
    selectedReseller.value = null
    
    // Reload data
    await loadResellers()
    
    alert('Gettoni assegnati con successo!')
  } catch (error) {
    console.error('Error assigning tokens:', error)
    alert('Errore durante l\'assegnazione dei gettoni')
  } finally {
    loading.value = false
  }
}

const viewReseller = (reseller) => {
  selectedResellerForView.value = reseller
  showViewModal.value = true
}

const editReseller = (reseller) => {
  selectedResellerForEdit.value = reseller
  editForm.value = {
    name: reseller.user.name,
    first_name: reseller.user.first_name,
    last_name: reseller.user.last_name,
    email: reseller.user.email,
    company_name: reseller.company_name,
    contact_email: reseller.contact_email,
    contact_phone: reseller.contact_phone || '',
    address: reseller.address || '',
    vat_number: reseller.vat_number || '',
    tokens_to_assign: 0,
    is_active: reseller.is_active
  }
  showEditModal.value = true
}

const updateReseller = async () => {
  loading.value = true
  try {
    // Update reseller info
    const updateData = { ...editForm.value }
    delete updateData.tokens_to_assign
    
    await api.put(`/v1/admin/resellers/${selectedResellerForEdit.value.id}`, updateData)
    
    // Assign tokens if specified
    if (editForm.value.tokens_to_assign > 0) {
      await api.post(`/v1/admin/resellers/${selectedResellerForEdit.value.id}/assign-tokens`, {
        tokens: parseInt(editForm.value.tokens_to_assign)
      })
    }
    
    showEditModal.value = false
    await loadResellers()
    alert('Rivenditore aggiornato con successo!')
  } catch (error) {
    console.error('Error updating reseller:', error)
    alert('Errore durante l\'aggiornamento del rivenditore')
  } finally {
    loading.value = false
  }
}

// Lifecycle
onMounted(() => {
  loadResellers()
})
</script>
