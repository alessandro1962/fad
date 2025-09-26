<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
      <!-- Header -->
      <div class="px-6 py-4 border-b border-cdf-slate200">
        <div class="flex items-center justify-between">
          <h2 class="text-xl font-bold text-cdf-deep">
            {{ isEditing ? 'Modifica Template' : 'Nuovo Template' }}
          </h2>
          <button
            @click="$emit('close')"
            class="p-2 hover:bg-cdf-sand rounded-xl transition-colors"
          >
            <XMarkIcon class="w-6 h-6 text-cdf-slate700" />
          </button>
        </div>
      </div>

      <!-- Content -->
      <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
        <form @submit.prevent="saveTemplate" class="space-y-6">
          <!-- Basic Info -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-cdf-deep mb-2">
                Nome Template *
              </label>
              <input
                v-model="form.name"
                type="text"
                required
                class="w-full px-3 py-2 border border-cdf-slate200 rounded-xl focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
                placeholder="Es. Certificato Corso Base"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-cdf-deep mb-2">
                Tipo Template *
              </label>
              <select
                v-model="form.type"
                required
                class="w-full px-3 py-2 border border-cdf-slate200 rounded-xl focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
              >
                <option value="course">Corso</option>
                <option value="track">Percorso</option>
                <option value="custom">Personalizzato</option>
              </select>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-cdf-deep mb-2">
              Descrizione
            </label>
            <textarea
              v-model="form.description"
              rows="3"
              class="w-full px-3 py-2 border border-cdf-slate200 rounded-xl focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
              placeholder="Descrizione del template..."
            ></textarea>
          </div>

          <!-- Background Settings -->
          <div class="border-t border-cdf-slate200 pt-6">
            <h3 class="text-lg font-semibold text-cdf-deep mb-4">Impostazioni Sfondo</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-cdf-deep mb-2">
                  Tipo Sfondo *
                </label>
                <select
                  v-model="form.background_type"
                  required
                  class="w-full px-3 py-2 border border-cdf-slate200 rounded-xl focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
                >
                  <option value="html">HTML/CSS</option>
                  <option value="image">Immagine</option>
                  <option value="pdf">PDF Template</option>
                </select>
              </div>
              
              <div v-if="form.background_type === 'image'">
                <label class="block text-sm font-medium text-cdf-deep mb-2">
                  Carica Immagine
                </label>
                <input
                  @change="handleFileUpload"
                  type="file"
                  accept="image/*"
                  class="w-full px-3 py-2 border border-cdf-slate200 rounded-xl focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
                />
              </div>
            </div>

            <!-- Background Preview -->
            <div v-if="form.background_type === 'html'" class="mt-4">
              <label class="block text-sm font-medium text-cdf-deep mb-2">
                CSS Sfondo
              </label>
              <textarea
                v-model="form.background_data"
                rows="4"
                class="w-full px-3 py-2 border border-cdf-slate200 rounded-xl focus:ring-2 focus:ring-cdf-teal focus:border-transparent font-mono text-sm"
                placeholder="background: linear-gradient(135deg, #0B3B5E 0%, #00A7B7 100%);"
              ></textarea>
            </div>
          </div>

          <!-- Styling Settings -->
          <div class="border-t border-cdf-slate200 pt-6">
            <h3 class="text-lg font-semibold text-cdf-deep mb-4">Stile e Colori</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
              <div>
                <label class="block text-sm font-medium text-cdf-deep mb-2">
                  Font Family
                </label>
                <select
                  v-model="form.styling.font_family"
                  class="w-full px-3 py-2 border border-cdf-slate200 rounded-xl focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
                >
                  <option value="Inter">Inter</option>
                  <option value="Arial">Arial</option>
                  <option value="Helvetica">Helvetica</option>
                  <option value="Times New Roman">Times New Roman</option>
                  <option value="Georgia">Georgia</option>
                </select>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-cdf-deep mb-2">
                  Font Size (px)
                </label>
                <input
                  v-model.number="form.styling.font_size"
                  type="number"
                  min="8"
                  max="24"
                  class="w-full px-3 py-2 border border-cdf-slate200 rounded-xl focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
                />
              </div>
              
              <div>
                <label class="block text-sm font-medium text-cdf-deep mb-2">
                  Colore Testo
                </label>
                <input
                  v-model="form.styling.text_color"
                  type="color"
                  class="w-full h-10 border border-cdf-slate200 rounded-xl focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
                />
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
              <div>
                <label class="block text-sm font-medium text-cdf-deep mb-2">
                  Colore Primario
                </label>
                <input
                  v-model="form.styling.primary_color"
                  type="color"
                  class="w-full h-10 border border-cdf-slate200 rounded-xl focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
                />
              </div>
              
              <div>
                <label class="block text-sm font-medium text-cdf-deep mb-2">
                  Colore Secondario
                </label>
                <input
                  v-model="form.styling.secondary_color"
                  type="color"
                  class="w-full h-10 border border-cdf-slate200 rounded-xl focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
                />
              </div>
              
              <div>
                <label class="block text-sm font-medium text-cdf-deep mb-2">
                  Colore Accento
                </label>
                <input
                  v-model="form.styling.accent_color"
                  type="color"
                  class="w-full h-10 border border-cdf-slate200 rounded-xl focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
                />
              </div>
            </div>
          </div>

          <!-- Placeholder Positions -->
          <div class="border-t border-cdf-slate200 pt-6">
            <h3 class="text-lg font-semibold text-cdf-deep mb-4">Posizioni Placeholder</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-cdf-deep mb-2">
                  Posizione Logo
                </label>
                <select
                  v-model="form.placeholder_positions.logo_position"
                  class="w-full px-3 py-2 border border-cdf-slate200 rounded-xl focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
                >
                  <option value="top-left">In alto a sinistra</option>
                  <option value="top-center">In alto al centro</option>
                  <option value="top-right">In alto a destra</option>
                  <option value="center">Centro</option>
                </select>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-cdf-deep mb-2">
                  Posizione Firma
                </label>
                <select
                  v-model="form.placeholder_positions.signature_position"
                  class="w-full px-3 py-2 border border-cdf-slate200 rounded-xl focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
                >
                  <option value="bottom-left">In basso a sinistra</option>
                  <option value="bottom-center">In basso al centro</option>
                  <option value="bottom-right">In basso a destra</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Status Settings -->
          <div class="border-t border-cdf-slate200 pt-6">
            <h3 class="text-lg font-semibold text-cdf-deep mb-4">Impostazioni</h3>
            
            <div class="flex space-x-6">
              <label class="flex items-center">
                <input
                  v-model="form.is_default"
                  type="checkbox"
                  class="w-4 h-4 text-cdf-teal border-cdf-slate200 rounded focus:ring-cdf-teal"
                />
                <span class="ml-2 text-sm text-cdf-deep">Template predefinito</span>
              </label>
              
              <label class="flex items-center">
                <input
                  v-model="form.is_active"
                  type="checkbox"
                  class="w-4 h-4 text-cdf-teal border-cdf-slate200 rounded focus:ring-cdf-teal"
                />
                <span class="ml-2 text-sm text-cdf-deep">Attivo</span>
              </label>
            </div>
          </div>
        </form>
      </div>

      <!-- Footer -->
      <div class="px-6 py-4 border-t border-cdf-slate200 bg-cdf-sand">
        <div class="flex justify-end space-x-3">
          <button
            @click="$emit('close')"
            class="px-4 py-2 border border-cdf-slate200 text-cdf-deep rounded-xl hover:bg-white transition-colors"
          >
            Annulla
          </button>
          <button
            @click="saveTemplate"
            :disabled="loading"
            class="px-6 py-2 bg-cdf-teal text-white font-semibold rounded-xl hover:brightness-95 transition-colors disabled:opacity-50"
          >
            {{ loading ? 'Salvataggio...' : (isEditing ? 'Aggiorna' : 'Crea') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, watch, onMounted } from 'vue'
import { XMarkIcon } from '@heroicons/vue/24/outline'
import api from '@/api'

const props = defineProps({
  template: {
    type: Object,
    default: null
  },
  isEditing: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['close', 'saved'])

// State
const loading = ref(false)

// Form data
const form = reactive({
  name: '',
  description: '',
  type: 'course',
  background_type: 'html',
  background_data: '',
  background_file: null,
  placeholder_positions: {
    logo_position: 'top-left',
    signature_position: 'bottom-right'
  },
  styling: {
    font_family: 'Inter',
    font_size: 12,
    text_color: '#14161A',
    primary_color: '#0B3B5E',
    secondary_color: '#00A7B7',
    accent_color: '#FFC857',
    background_color: '#F4F1EA'
  },
  is_default: false,
  is_active: true
})

// Watch for template changes
watch(() => props.template, (newTemplate) => {
  if (newTemplate) {
    Object.assign(form, {
      name: newTemplate.name || '',
      description: newTemplate.description || '',
      type: newTemplate.type || 'course',
      background_type: newTemplate.background_type || 'html',
      background_data: newTemplate.background_data || '',
      placeholder_positions: {
        logo_position: newTemplate.placeholder_positions?.logo_position || 'top-left',
        signature_position: newTemplate.placeholder_positions?.signature_position || 'bottom-right'
      },
      styling: {
        font_family: newTemplate.styling?.font_family || 'Inter',
        font_size: newTemplate.styling?.font_size || 12,
        text_color: newTemplate.styling?.text_color || '#14161A',
        primary_color: newTemplate.styling?.primary_color || '#0B3B5E',
        secondary_color: newTemplate.styling?.secondary_color || '#00A7B7',
        accent_color: newTemplate.styling?.accent_color || '#FFC857',
        background_color: newTemplate.styling?.background_color || '#F4F1EA'
      },
      is_default: newTemplate.is_default || false,
      is_active: newTemplate.is_active !== false
    })
  }
}, { immediate: true })

// File upload handler
const handleFileUpload = (event) => {
  const file = event.target.files[0]
  if (file) {
    form.background_file = file
  }
}

// Save template
const saveTemplate = async () => {
  loading.value = true
  
  try {
    const formData = new FormData()
    
    // Add form fields
    Object.keys(form).forEach(key => {
      if (key === 'placeholder_positions' || key === 'styling') {
        formData.append(key, JSON.stringify(form[key]))
      } else if (key !== 'background_file') {
        formData.append(key, form[key])
      }
    })
    
    // Add file if present
    if (form.background_file) {
      formData.append('background_file', form.background_file)
    }
    
    let response
    if (props.isEditing) {
      response = await api.put(`/v1/admin/certificate-templates/${props.template.id}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
    } else {
      response = await api.post('/v1/admin/certificate-templates', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
    }
    
    emit('saved', response.data.data)
  } catch (error) {
    console.error('Errore nel salvataggio template:', error)
  } finally {
    loading.value = false
  }
}
</script>
