<template>
  <AppLayout>
    <div class="space-y-8">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-cdf-deep">Gestione Aziende</h1>
          <p class="text-cdf-slate600 mt-2">Gestisci le aziende e i loro dipendenti</p>
        </div>
        <button
          @click="showCreateModal = true"
          class="btn-primary"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
          </svg>
          Nuova Azienda
        </button>
      </div>

      <!-- Filters -->
      <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <!-- Search -->
          <div>
            <label class="block text-sm font-medium text-cdf-deep mb-2">Cerca</label>
            <input
              v-model="filters.search"
              type="text"
              placeholder="Nome, email, P.IVA..."
              class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
              @input="searchOrganizations"
            />
          </div>

          <!-- Sort By -->
          <div>
            <label class="block text-sm font-medium text-cdf-deep mb-2">Ordina per</label>
            <select
              v-model="filters.sort_by"
              class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
              @change="loadOrganizations"
            >
              <option value="created_at">Data creazione</option>
              <option value="name">Nome</option>
              <option value="users_count">Numero dipendenti</option>
            </select>
          </div>

          <!-- Sort Order -->
          <div>
            <label class="block text-sm font-medium text-cdf-deep mb-2">Ordine</label>
            <select
              v-model="filters.sort_order"
              class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
              @change="loadOrganizations"
            >
              <option value="desc">Decrescente</option>
              <option value="asc">Crescente</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Organizations List -->
      <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200">
        <div class="p-6 border-b border-cdf-slate200">
          <h2 class="text-xl font-bold text-cdf-deep">Aziende</h2>
        </div>

        <div v-if="loading" class="flex items-center justify-center py-12">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-cdf-teal"></div>
        </div>

        <div v-else class="divide-y divide-cdf-slate200">
          <div
            v-for="organization in organizations"
            :key="organization.id"
            class="p-6 hover:bg-cdf-slate50 transition-colors"
          >
            <div class="flex items-center justify-between">
              <div class="flex-1">
                <div class="flex items-center space-x-4">
                  <div class="w-12 h-12 bg-cdf-teal/10 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-cdf-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                  </div>
                  <div>
                    <h3 class="text-lg font-semibold text-cdf-deep">{{ organization.name }}</h3>
                    <div class="flex items-center space-x-4 text-sm text-cdf-slate600 mt-1">
                      <span v-if="organization.email">{{ organization.email }}</span>
                      <span v-if="organization.vat_number">P.IVA: {{ organization.vat_number }}</span>
                      <span v-if="organization.city">{{ organization.city }}</span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="flex items-center space-x-4">
                <div class="text-right">
                  <p class="text-sm text-cdf-slate600">Dipendenti</p>
                  <p class="text-lg font-semibold text-cdf-deep">{{ organization.users_count || 0 }}</p>
                </div>
                <div class="flex items-center space-x-2">
                  <span
                    :class="{
                      'bg-green-100 text-green-800': organization.is_active,
                      'bg-red-100 text-red-800': !organization.is_active
                    }"
                    class="px-2 py-1 rounded-full text-xs font-medium"
                  >
                    {{ organization.is_active ? 'Attiva' : 'Inattiva' }}
                  </span>
                </div>
                <div class="flex items-center space-x-2">
                  <button
                    @click="viewOrganization(organization)"
                    class="p-2 text-cdf-slate600 hover:text-cdf-teal hover:bg-cdf-slate100 rounded-lg transition-colors"
                    title="Visualizza dettagli"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                  </button>
                  <button
                    @click="editOrganization(organization)"
                    class="p-2 text-cdf-slate600 hover:text-cdf-amber hover:bg-cdf-slate100 rounded-lg transition-colors"
                    title="Modifica"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                  </button>
                  <button
                    @click="deleteOrganization(organization)"
                    class="p-2 text-cdf-slate600 hover:text-red-600 hover:bg-cdf-slate100 rounded-lg transition-colors"
                    title="Elimina"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <div v-if="organizations.length === 0" class="text-center py-12 text-cdf-slate500">
            <svg class="w-16 h-16 mx-auto mb-4 text-cdf-slate300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
            <p class="text-lg">Nessuna azienda trovata</p>
            <p class="text-sm">Crea la prima azienda per iniziare</p>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="pagination.last_page > 1" class="p-6 border-t border-cdf-slate200">
          <div class="flex items-center justify-between">
            <div class="text-sm text-cdf-slate600">
              Mostrando {{ organizations.length }} di {{ pagination.total }} aziende
            </div>
            <div class="flex items-center space-x-2">
              <button
                @click="loadOrganizations(pagination.current_page - 1)"
                :disabled="pagination.current_page <= 1"
                class="px-3 py-1 text-sm border border-cdf-slate200 rounded-lg hover:bg-cdf-slate50 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Precedente
              </button>
              <span class="px-3 py-1 text-sm text-cdf-slate600">
                {{ pagination.current_page }} di {{ pagination.last_page }}
              </span>
              <button
                @click="loadOrganizations(pagination.current_page + 1)"
                :disabled="pagination.current_page >= pagination.last_page"
                class="px-3 py-1 text-sm border border-cdf-slate200 rounded-lg hover:bg-cdf-slate50 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Successivo
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Create/Edit Organization Modal -->
    <CreateOrganizationModal
      :is-open="showCreateModal"
      :organization="editingOrganization"
      @close="closeModal"
      @success="handleOrganizationSaved"
    />

    <!-- Organization Details Modal -->
    <OrganizationDetailsModal
      :is-open="showDetailsModal"
      :organization="selectedOrganization"
      @close="closeDetailsModal"
      @success="handleOrganizationUpdated"
    />
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import AppLayout from '@/components/Layout/AppLayout.vue'
import CreateOrganizationModal from '@/components/Admin/CreateOrganizationModal.vue'
import OrganizationDetailsModal from '@/components/Admin/OrganizationDetailsModal.vue'
import api from '@/api'

const router = useRouter()

const loading = ref(false)
const organizations = ref([])
const showCreateModal = ref(false)
const showDetailsModal = ref(false)
const editingOrganization = ref(null)
const selectedOrganization = ref(null)

const filters = ref({
  search: '',
  sort_by: 'created_at',
  sort_order: 'desc'
})

const pagination = ref({
  current_page: 1,
  last_page: 1,
  per_page: 15,
  total: 0
})

const loadOrganizations = async (page = 1) => {
  try {
    loading.value = true
    const params = {
      page,
      per_page: pagination.value.per_page,
      ...filters.value
    }
    
    const response = await api.get('/v1/admin/organizations', { params })
    organizations.value = response.data.data
    pagination.value = response.data.pagination
  } catch (error) {
    console.error('Error loading organizations:', error)
  } finally {
    loading.value = false
  }
}

const searchOrganizations = () => {
  clearTimeout(searchOrganizations.timeout)
  searchOrganizations.timeout = setTimeout(() => {
    loadOrganizations(1)
  }, 500)
}

const viewOrganization = (organization) => {
  selectedOrganization.value = organization
  showDetailsModal.value = true
}

const editOrganization = (organization) => {
  editingOrganization.value = organization
  showCreateModal.value = true
}

const deleteOrganization = async (organization) => {
  if (!confirm(`Sei sicuro di voler eliminare l'azienda "${organization.name}"?`)) {
    return
  }

  try {
    await api.delete(`/v1/admin/organizations/${organization.id}`)
    await loadOrganizations()
  } catch (error) {
    console.error('Error deleting organization:', error)
    if (error.response?.data?.error) {
      alert(error.response.data.error)
    }
  }
}

const closeModal = () => {
  showCreateModal.value = false
  editingOrganization.value = null
}

const closeDetailsModal = () => {
  showDetailsModal.value = false
  selectedOrganization.value = null
}

const handleOrganizationSaved = () => {
  closeModal()
  loadOrganizations()
}

const handleOrganizationUpdated = () => {
  closeDetailsModal()
  loadOrganizations()
}

onMounted(() => {
  loadOrganizations()
})
</script>
