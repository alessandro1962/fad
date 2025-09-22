<template>
  <div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl border border-cdf-slate200 w-full max-w-4xl max-h-[90vh] overflow-y-auto">
      <!-- Header -->
      <div class="flex items-center justify-between p-6 border-b border-cdf-slate200">
        <div>
          <h2 class="text-xl font-bold text-cdf-deep">Assegna Utenti</h2>
          <p class="text-cdf-slate600 mt-1">Seleziona gli utenti da assegnare a {{ organization?.name }}</p>
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

      <div class="p-6 space-y-6">
        <!-- Search -->
        <div>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Cerca utenti..."
            class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
            @input="searchUsers"
          />
        </div>

        <!-- Users List -->
        <div v-if="loading" class="flex items-center justify-center py-8">
          <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-cdf-teal"></div>
        </div>

        <div v-else class="space-y-4">
          <div
            v-for="user in filteredUsers"
            :key="user.id"
            class="flex items-center justify-between p-4 border border-cdf-slate200 rounded-xl hover:bg-cdf-slate50 transition-colors"
          >
            <div class="flex items-center space-x-4">
              <input
                v-model="selectedUsers"
                :value="user.id"
                type="checkbox"
                class="h-4 w-4 text-cdf-teal focus:ring-cdf-teal border-cdf-slate300 rounded"
              />
              <div class="w-10 h-10 bg-cdf-slate100 rounded-full flex items-center justify-center">
                <svg class="w-5 h-5 text-cdf-slate600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
              </div>
              <div>
                <h4 class="font-medium text-cdf-deep">{{ user.name }}</h4>
                <p class="text-sm text-cdf-slate600">{{ user.email }}</p>
                <p class="text-sm text-cdf-slate500">{{ user.company || 'N/A' }}</p>
              </div>
            </div>
          </div>

          <div v-if="filteredUsers.length === 0" class="text-center py-8 text-cdf-slate500">
            <svg class="w-12 h-12 mx-auto mb-4 text-cdf-slate300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <p>Nessun utente disponibile</p>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-between pt-6 border-t border-cdf-slate200">
          <div class="text-sm text-cdf-slate600">
            {{ selectedUsers.length }} utenti selezionati
          </div>
          <div class="flex items-center space-x-4">
            <button
              @click="$emit('close')"
              class="px-4 py-2 text-cdf-slate600 hover:text-cdf-deep transition-colors"
            >
              Annulla
            </button>
            <button
              @click="assignUsers"
              :disabled="selectedUsers.length === 0 || loading"
              class="btn-primary"
            >
              <span v-if="loading">Assegnazione...</span>
              <span v-else>Assegna {{ selectedUsers.length }} Utenti</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue'
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
const users = ref([])
const selectedUsers = ref([])
const searchQuery = ref('')

const filteredUsers = computed(() => {
  if (!searchQuery.value) return users.value
  
  const query = searchQuery.value.toLowerCase()
  return users.value.filter(user => 
    user.name.toLowerCase().includes(query) ||
    user.email.toLowerCase().includes(query) ||
    (user.company && user.company.toLowerCase().includes(query))
  )
})

// Watch for modal open to load users
watch(() => props.isOpen, (isOpen) => {
  if (isOpen) {
    loadUnassignedUsers()
  } else {
    selectedUsers.value = []
    searchQuery.value = ''
  }
})

const loadUnassignedUsers = async () => {
  try {
    loading.value = true
    const response = await api.get('/v1/admin/organizations/unassigned-users')
    users.value = response.data.data
  } catch (error) {
    console.error('Error loading unassigned users:', error)
  } finally {
    loading.value = false
  }
}

const searchUsers = () => {
  // Search is handled by computed property
}

const assignUsers = async () => {
  if (selectedUsers.value.length === 0) return

  try {
    loading.value = true
    await api.post(`/v1/admin/organizations/${props.organization.id}/assign-users`, {
      user_ids: selectedUsers.value
    })
    
    emit('success')
  } catch (error) {
    console.error('Error assigning users:', error)
    if (error.response?.data?.error) {
      alert(error.response.data.error)
    }
  } finally {
    loading.value = false
  }
}
</script>
