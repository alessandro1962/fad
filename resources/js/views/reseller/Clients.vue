<template>
  <div class="min-h-screen bg-cdf-sand">
    <AppLayout>
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
          <h1 class="text-3xl font-bold text-cdf-dark">I Miei Clienti</h1>
          <p class="text-cdf-gray mt-2">Gestisci tutti i clienti che hai creato</p>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
          <div class="flex flex-wrap gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Tipo Cliente</label>
              <select
                v-model="filters.type"
                @change="loadClients"
                class="border-gray-300 rounded-md shadow-sm focus:ring-cdf-primary focus:border-cdf-primary"
              >
                <option value="">Tutti</option>
                <option value="standalone">Standalone</option>
                <option value="organization">Organizzazione</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Cerca</label>
              <input
                v-model="filters.search"
                @input="debounceSearch"
                type="text"
                placeholder="Nome o email..."
                class="border-gray-300 rounded-md shadow-sm focus:ring-cdf-primary focus:border-cdf-primary"
              />
            </div>
          </div>
        </div>

        <!-- Clients Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-cdf-dark">
              Clienti ({{ clients.total || 0 }})
            </h3>
          </div>
          
          <div v-if="loading" class="p-8 text-center">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-cdf-primary"></div>
            <p class="mt-2 text-gray-600">Caricamento...</p>
          </div>
          
          <div v-else-if="clients.data && clients.data.length === 0" class="p-8 text-center text-gray-500">
            Nessun cliente trovato
          </div>
          
          <div v-else class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Cliente
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Tipo
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Organizzazione
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Gettoni Usati
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Data Creazione
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Azioni
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="client in clients.data" :key="client.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-10 w-10">
                        <div class="h-10 w-10 rounded-full bg-cdf-primary flex items-center justify-center">
                          <span class="text-white font-medium text-sm">
                            {{ client.user.name.charAt(0).toUpperCase() }}
                          </span>
                        </div>
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">
                          {{ client.user.name }}
                        </div>
                        <div class="text-sm text-gray-500">
                          {{ client.user.email }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span
                      :class="[
                        'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                        client.organization ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'
                      ]"
                    >
                      {{ client.organization ? 'Organizzazione' : 'Standalone' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ client.organization ? client.organization.name : '-' }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ client.tokens_used }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ formatDate(client.created_at) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex space-x-2">
                      <button
                        @click="editClient(client)"
                        class="text-cdf-primary hover:text-cdf-primary-dark"
                      >
                        Modifica
                      </button>
                      <button
                        v-if="client.organization"
                        @click="viewOrganization(client.organization)"
                        class="text-blue-600 hover:text-blue-800"
                      >
                        Azienda
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div v-if="clients.data && clients.data.length > 0" class="px-6 py-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
              <div class="text-sm text-gray-700">
                Mostrando {{ clients.from }} a {{ clients.to }} di {{ clients.total }} risultati
              </div>
              <div class="flex space-x-2">
                <button
                  v-if="clients.prev_page_url"
                  @click="loadClients(clients.current_page - 1)"
                  class="px-3 py-1 border border-gray-300 rounded-md text-sm hover:bg-gray-50"
                >
                  Precedente
                </button>
                <button
                  v-if="clients.next_page_url"
                  @click="loadClients(clients.current_page + 1)"
                  class="px-3 py-1 border border-gray-300 rounded-md text-sm hover:bg-gray-50"
                >
                  Successiva
                </button>
              </div>
            </div>
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
const clients = ref({})
const loading = ref(false)
const filters = ref({
  type: '',
  search: ''
})

let searchTimeout = null

// Methods
const loadClients = async (page = 1) => {
  loading.value = true
  try {
    const params = new URLSearchParams({
      page: page.toString(),
      per_page: '15'
    })
    
    if (filters.value.type) {
      params.append('type', filters.value.type)
    }
    
    if (filters.value.search) {
      params.append('search', filters.value.search)
    }
    
    const response = await api.get(`/v1/reseller/clients?${params}`)
    clients.value = response.data.data
  } catch (error) {
    console.error('Error loading clients:', error)
  } finally {
    loading.value = false
  }
}

const debounceSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    loadClients()
  }, 500)
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('it-IT', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

// Lifecycle
onMounted(() => {
  loadClients()
})
</script>
