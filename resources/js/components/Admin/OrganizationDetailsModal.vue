<template>
  <div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl border border-cdf-slate200 w-full max-w-6xl max-h-[90vh] overflow-y-auto">
      <!-- Header -->
      <div class="flex items-center justify-between p-6 border-b border-cdf-slate200">
        <div>
          <h2 class="text-xl font-bold text-cdf-deep">{{ organization?.name }}</h2>
          <p class="text-cdf-slate600 mt-1">Gestione dipendenti e company manager</p>
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

      <div v-if="organization" class="p-6 space-y-8">
        <!-- Organization Info -->
        <div class="bg-cdf-slate50 rounded-xl p-6">
          <h3 class="text-lg font-semibold text-cdf-deep mb-4">Informazioni Azienda</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <p class="text-sm text-cdf-slate600">Email</p>
              <p class="font-medium">{{ organization.email || 'N/A' }}</p>
            </div>
            <div>
              <p class="text-sm text-cdf-slate600">P.IVA</p>
              <p class="font-medium">{{ organization.vat_number || 'N/A' }}</p>
            </div>
            <div>
              <p class="text-sm text-cdf-slate600">Telefono</p>
              <p class="font-medium">{{ organization.phone || 'N/A' }}</p>
            </div>
            <div>
              <p class="text-sm text-cdf-slate600">Città</p>
              <p class="font-medium">{{ organization.city || 'N/A' }}</p>
            </div>
          </div>
        </div>

        <!-- Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
          <div class="bg-white border border-cdf-slate200 rounded-xl p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-cdf-slate600">Dipendenti</p>
                <p class="text-2xl font-bold text-cdf-deep">{{ stats.total_users || 0 }}</p>
              </div>
              <div class="w-12 h-12 bg-cdf-teal/10 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-cdf-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white border border-cdf-slate200 rounded-xl p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-cdf-slate600">Company Manager</p>
                <p class="text-2xl font-bold text-cdf-deep">{{ stats.company_managers || 0 }}</p>
              </div>
              <div class="w-12 h-12 bg-cdf-amber/10 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-cdf-amber" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white border border-cdf-slate200 rounded-xl p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-cdf-slate600">Dipendenti</p>
                <p class="text-2xl font-bold text-cdf-deep">{{ stats.employees || 0 }}</p>
              </div>
              <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white border border-cdf-slate200 rounded-xl p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-cdf-slate600">Utenti Attivi</p>
                <p class="text-2xl font-bold text-cdf-deep">{{ stats.active_users || 0 }}</p>
              </div>
              <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-between">
          <h3 class="text-lg font-semibold text-cdf-deep">Gestione Utenti</h3>
          <div class="flex items-center space-x-4">
            <button
              @click="showAssignUsersModal = true"
              class="btn-secondary"
            >
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
              </svg>
              Assegna Utenti
            </button>
            <button
              @click="showCreateManagerModal = true"
              class="btn-primary"
            >
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
              </svg>
              Crea Company Manager
            </button>
          </div>
        </div>

        <!-- Users List -->
        <div class="bg-white border border-cdf-slate200 rounded-xl">
          <div class="p-6 border-b border-cdf-slate200">
            <h4 class="text-lg font-semibold text-cdf-deep">Utenti Assegnati</h4>
          </div>
          
          <div v-if="loadingUsers" class="flex items-center justify-center py-8">
            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-cdf-teal"></div>
          </div>

          <div v-else class="divide-y divide-cdf-slate200">
            <div
              v-for="user in users"
              :key="user.id"
              class="p-6 flex items-center justify-between"
            >
              <div class="flex items-center space-x-4">
                <div class="w-10 h-10 bg-cdf-slate100 rounded-full flex items-center justify-center">
                  <svg class="w-5 h-5 text-cdf-slate600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                  </svg>
                </div>
                <div>
                  <h5 class="font-medium text-cdf-deep">{{ user.name }}</h5>
                  <p class="text-sm text-cdf-slate600">{{ user.email }}</p>
                </div>
              </div>
              <div class="flex items-center space-x-4">
                <span
                  :class="{
                    'bg-cdf-amber/10 text-cdf-amber': user.is_company_manager,
                    'bg-cdf-slate100 text-cdf-slate600': !user.is_company_manager
                  }"
                  class="px-3 py-1 rounded-full text-sm font-medium"
                >
                  {{ user.is_company_manager ? 'Company Manager' : 'Dipendente' }}
                </span>
                <button
                  @click="removeUser(user)"
                  class="p-2 text-cdf-slate600 hover:text-red-600 hover:bg-cdf-slate100 rounded-lg transition-colors"
                  title="Rimuovi dall'azienda"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                  </svg>
                </button>
              </div>
            </div>

            <div v-if="users.length === 0" class="text-center py-8 text-cdf-slate500">
              <svg class="w-12 h-12 mx-auto mb-4 text-cdf-slate300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
              </svg>
              <p>Nessun utente assegnato</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Assign Users Modal -->
      <AssignUsersModal
        :is-open="showAssignUsersModal"
        :organization="organization"
        @close="showAssignUsersModal = false"
        @success="handleUsersAssigned"
      />

      <!-- Create Company Manager Modal -->
      <CreateCompanyManagerModal
        :is-open="showCreateManagerModal"
        :organization="organization"
        @close="showCreateManagerModal = false"
        @success="handleManagerCreated"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import AssignUsersModal from './AssignUsersModal.vue'
import CreateCompanyManagerModal from './CreateCompanyManagerModal.vue'
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

const loadingUsers = ref(false)
const users = ref([])
const stats = ref({})
const showAssignUsersModal = ref(false)
const showCreateManagerModal = ref(false)

// Watch for organization changes
watch(() => props.organization, (newOrg) => {
  if (newOrg) {
    loadOrganizationData()
  }
}, { immediate: true })

const loadOrganizationData = async () => {
  if (!props.organization) return

  try {
    loadingUsers.value = true
    
    // Load organization details with users
    const [orgResponse, statsResponse] = await Promise.all([
      api.get(`/v1/admin/organizations/${props.organization.id}`),
      api.get(`/v1/admin/organizations/${props.organization.id}/statistics`)
    ])
    
    users.value = orgResponse.data.data.users || []
    stats.value = statsResponse.data.data
  } catch (error) {
    console.error('Error loading organization data:', error)
  } finally {
    loadingUsers.value = false
  }
}

const removeUser = async (user) => {
  if (!confirm(`Sei sicuro di voler rimuovere ${user.name} dall'azienda?`)) {
    return
  }

  try {
    await api.post(`/v1/admin/organizations/${props.organization.id}/remove-users`, {
      user_ids: [user.id]
    })
    
    await loadOrganizationData()
    emit('success')
  } catch (error) {
    console.error('Error removing user:', error)
  }
}

const handleUsersAssigned = () => {
  showAssignUsersModal.value = false
  loadOrganizationData()
  emit('success')
}

const handleManagerCreated = () => {
  showCreateManagerModal.value = false
  loadOrganizationData()
  emit('success')
}
</script>
