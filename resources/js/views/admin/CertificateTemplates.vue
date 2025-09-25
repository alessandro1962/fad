<template>
  <div class="min-h-screen bg-cdf-sand">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-cdf-slate200">
      <div class="max-w-7xl mx-auto px-6 py-4">
        <div class="flex items-center justify-between">
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
            <div>
              <h1 class="text-2xl font-bold text-cdf-deep">Template Certificati</h1>
              <p class="text-cdf-slate700 mt-1">Gestisci i template per la generazione dei certificati</p>
            </div>
          </div>
          <button
            @click="openCreateEditor"
            class="px-4 py-2 bg-cdf-amber text-cdf-ink font-semibold rounded-xl hover:brightness-95 transition-colors"
          >
            <PlusIcon class="w-5 h-5 inline mr-2" />
            Nuovo Template
          </button>
        </div>
      </div>
    </div>

    <!-- Content -->
    <div class="max-w-7xl mx-auto px-6 py-8">
      <!-- Filters -->
      <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-medium text-cdf-deep mb-2">Tipo Template</label>
            <select
              v-model="filters.type"
              @change="loadTemplates"
              class="w-full px-3 py-2 border border-cdf-slate200 rounded-xl focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
            >
              <option value="">Tutti i tipi</option>
              <option value="course">Corso</option>
              <option value="track">Percorso</option>
              <option value="custom">Personalizzato</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-cdf-deep mb-2">Stato</label>
            <select
              v-model="filters.status"
              @change="loadTemplates"
              class="w-full px-3 py-2 border border-cdf-slate200 rounded-xl focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
            >
              <option value="">Tutti</option>
              <option value="active">Attivi</option>
              <option value="inactive">Inattivi</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-cdf-deep mb-2">Cerca</label>
            <input
              v-model="filters.search"
              @input="debouncedSearch"
              type="text"
              placeholder="Nome template..."
              class="w-full px-3 py-2 border border-cdf-slate200 rounded-xl focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
            />
          </div>
          <div class="flex items-end">
            <button
              @click="resetFilters"
              class="w-full px-4 py-2 border border-cdf-slate200 text-cdf-deep rounded-xl hover:bg-cdf-sand transition-colors"
            >
              Reset Filtri
            </button>
          </div>
        </div>
      </div>

      <!-- Templates Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="template in templates"
          :key="template.id"
          class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 overflow-hidden hover:shadow-md transition-shadow"
        >
          <!-- Template Preview -->
          <div class="h-48 bg-gradient-to-br from-cdf-teal/10 to-cdf-deep/10 relative">
            <div class="absolute inset-0 flex items-center justify-center">
              <div class="text-center">
                <DocumentTextIcon class="w-12 h-12 text-cdf-teal mx-auto mb-2" />
                <p class="text-sm text-cdf-slate700">{{ template.name }}</p>
              </div>
            </div>
            <!-- Status Badge -->
            <div class="absolute top-3 right-3">
              <span
                :class="[
                  'px-2 py-1 rounded-full text-xs font-medium',
                  template.is_active
                    ? 'bg-green-100 text-green-800'
                    : 'bg-gray-100 text-gray-800'
                ]"
              >
                {{ template.is_active ? 'Attivo' : 'Inattivo' }}
              </span>
            </div>
            <!-- Default Badge -->
            <div v-if="template.is_default" class="absolute top-3 left-3">
              <span class="px-2 py-1 bg-cdf-amber text-cdf-ink rounded-full text-xs font-medium">
                Default
              </span>
            </div>
          </div>

          <!-- Template Info -->
          <div class="p-6">
            <h3 class="font-semibold text-cdf-deep mb-2">{{ template.name }}</h3>
            <p class="text-sm text-cdf-slate700 mb-4 line-clamp-2">
              {{ template.description || 'Nessuna descrizione' }}
            </p>
            
            <!-- Template Details -->
            <div class="space-y-2 mb-4">
              <div class="flex items-center text-sm text-cdf-slate700">
                <TagIcon class="w-4 h-4 mr-2" />
                <span class="capitalize">{{ template.type }}</span>
              </div>
              <div class="flex items-center text-sm text-cdf-slate700">
                <CalendarIcon class="w-4 h-4 mr-2" />
                <span>{{ formatDate(template.created_at) }}</span>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex space-x-2">
              <button
                @click="openVisualEditor(template)"
                class="flex-1 px-3 py-2 bg-cdf-teal text-white text-sm font-medium rounded-xl hover:brightness-95 transition-colors"
              >
                Modifica Template
              </button>
              <button
                @click="duplicateTemplate(template)"
                class="px-3 py-2 border border-cdf-slate200 text-cdf-deep rounded-xl hover:bg-cdf-sand transition-colors"
                title="Duplica"
              >
                <DocumentDuplicateIcon class="w-4 h-4" />
              </button>
              <button
                @click="deleteTemplate(template)"
                class="px-3 py-2 border border-red-200 text-red-600 rounded-xl hover:bg-red-50 transition-colors"
                title="Elimina"
              >
                <TrashIcon class="w-4 h-4" />
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="templates.length === 0" class="text-center py-12">
        <DocumentTextIcon class="w-16 h-16 text-cdf-slate200 mx-auto mb-4" />
        <h3 class="text-lg font-medium text-cdf-deep mb-2">Nessun template trovato</h3>
        <p class="text-cdf-slate700 mb-6">Crea il tuo primo template per i certificati</p>
        <button
          @click="openCreateEditor"
          class="px-6 py-3 bg-cdf-amber text-cdf-ink font-semibold rounded-xl hover:brightness-95 transition-colors"
        >
          Crea Template
        </button>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <CertificateTemplateModal
      v-if="showModal"
      :template="editingTemplate"
      :is-editing="isEditing"
      @close="closeModal"
      @saved="onTemplateSaved"
    />

    <!-- Simple Visual Editor Modal -->
    <SimpleCertificateEditor
      v-if="showVisualEditor"
      :template="editingTemplate"
      @close="closeVisualEditor"
      @saved="onTemplateSaved"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/api'
import {
  PlusIcon,
  DocumentTextIcon,
  TagIcon,
  CalendarIcon,
  DocumentDuplicateIcon,
  TrashIcon
} from '@heroicons/vue/24/outline'
import CertificateTemplateModal from '@/components/Admin/CertificateTemplateModal.vue'
import SimpleCertificateEditor from '@/components/Admin/SimpleCertificateEditor.vue'

const router = useRouter()

const goToDashboard = () => {
  router.push('/admin')
}

// State
const templates = ref([])
const loading = ref(false)
const showModal = ref(false)
const showVisualEditor = ref(false)
const editingTemplate = ref(null)
const isEditing = ref(false)

// Filters
const filters = ref({
  type: '',
  status: '',
  search: ''
})

// Load templates
const loadTemplates = async () => {
  loading.value = true
  try {
    const params = new URLSearchParams()
    if (filters.value.type) params.append('type', filters.value.type)
    if (filters.value.status) params.append('status', filters.value.status)
    if (filters.value.search) params.append('search', filters.value.search)

    const response = await api.get(`/v1/admin/certificate-templates?${params}`)
    templates.value = response.data.data
  } catch (error) {
    console.error('Errore nel caricamento template:', error)
  } finally {
    loading.value = false
  }
}

// Debounced search
let searchTimeout = null
const debouncedSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    loadTemplates()
  }, 300)
}

// Reset filters
const resetFilters = () => {
  filters.value = {
    type: '',
    status: '',
    search: ''
  }
  loadTemplates()
}

// Modal functions
const openCreateModal = () => {
  editingTemplate.value = null
  isEditing.value = false
  showModal.value = true
}

const openCreateEditor = () => {
  editingTemplate.value = null
  showVisualEditor.value = true
}

const openEditModal = (template) => {
  editingTemplate.value = template
  isEditing.value = true
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  editingTemplate.value = null
  isEditing.value = false
}

const openVisualEditor = (template) => {
  editingTemplate.value = template
  showVisualEditor.value = true
}

const closeVisualEditor = () => {
  showVisualEditor.value = false
  editingTemplate.value = null
}

// Template actions
const duplicateTemplate = async (template) => {
  try {
    const response = await api.post(`/v1/admin/certificate-templates/${template.id}/duplicate`)
    templates.value.unshift(response.data.data)
  } catch (error) {
    console.error('Errore nella duplicazione:', error)
  }
}

const deleteTemplate = async (template) => {
  if (!confirm(`Sei sicuro di voler eliminare il template "${template.name}"?`)) {
    return
  }

  try {
    await api.delete(`/v1/admin/certificate-templates/${template.id}`)
    templates.value = templates.value.filter(t => t.id !== template.id)
  } catch (error) {
    console.error('Errore nell\'eliminazione:', error)
  }
}

// Event handlers
const onTemplateSaved = (template) => {
  if (isEditing.value) {
    const index = templates.value.findIndex(t => t.id === template.id)
    if (index !== -1) {
      templates.value[index] = template
    }
  } else {
    templates.value.unshift(template)
  }
  closeModal()
  closeVisualEditor()
}

// Utility functions
const formatDate = (date) => {
  return new Date(date).toLocaleDateString('it-IT')
}

// Lifecycle
onMounted(() => {
  loadTemplates()
})
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
