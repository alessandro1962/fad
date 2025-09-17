<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl max-w-6xl w-full max-h-[95vh] overflow-hidden">
      <!-- Header -->
      <div class="px-6 py-4 border-b border-cdf-slate200">
        <div class="flex items-center justify-between">
          <h2 class="text-xl font-bold text-cdf-deep">
            Editor Certificati - {{ template?.name || 'Nuovo Template' }}
          </h2>
          <button
            @click="$emit('close')"
            class="p-2 hover:bg-cdf-sand rounded-xl transition-colors"
          >
            <XMarkIcon class="w-6 h-6 text-cdf-slate700" />
          </button>
        </div>
      </div>

      <div class="flex h-[calc(95vh-80px)]">
        <!-- Sidebar -->
        <div class="w-80 border-r border-cdf-slate200 bg-cdf-sand overflow-y-auto">
          <div class="p-4">
            <!-- Template Info -->
            <div class="mb-6">
              <h3 class="text-lg font-semibold text-cdf-deep mb-3">Informazioni Template</h3>
              
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-cdf-deep mb-2">
                    Nome Template
                  </label>
                  <input
                    v-model="templateName"
                    type="text"
                    class="w-full px-3 py-2 border border-cdf-slate200 rounded-xl focus:ring-2 focus:ring-cdf-teal focus:border-transparent text-sm"
                    placeholder="Es. Certificato Corso Base"
                  />
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-cdf-deep mb-2">
                    Corso Associato <span class="text-red-500">*</span>
                  </label>
                  <select
                    v-model="selectedCourseId"
                    required
                    class="w-full px-3 py-2 border border-cdf-slate200 rounded-xl focus:ring-2 focus:ring-cdf-teal focus:border-transparent text-sm"
                    :class="{ 'border-red-500': !selectedCourseId && showValidation }"
                  >
                    <option value="">Seleziona corso</option>
                    <option v-for="course in courses" :key="course.id" :value="course.id">
                      {{ course.title }}
                    </option>
                  </select>
                  <p v-if="!selectedCourseId && showValidation" class="text-red-500 text-xs mt-1">
                    Il corso è obbligatorio per associare l'attestato
                  </p>
                </div>
              </div>
            </div>

            <!-- Upload Background -->
            <div class="mb-6">
              <h3 class="text-lg font-semibold text-cdf-deep mb-3">Sfondo Certificato</h3>
              
              <!-- File Upload -->
              <div class="mb-4">
                <label class="block text-sm font-medium text-cdf-deep mb-2">
                  Carica Immagine o PDF
                </label>
                <input
                  @change="handleFileUpload"
                  type="file"
                  accept=".jpg,.jpeg,.png,.pdf"
                  class="w-full px-3 py-2 border border-cdf-slate200 rounded-xl focus:ring-2 focus:ring-cdf-teal focus:border-transparent text-sm"
                />
                <p class="text-xs text-cdf-slate700 mt-1">
                  Formati supportati: JPG, PNG, PDF (max 10MB)
                </p>
              </div>

              <!-- Background Preview -->
              <div v-if="backgroundPreview" class="mb-4">
                <img
                  :src="backgroundPreview"
                  alt="Anteprima sfondo"
                  class="w-full h-32 object-cover rounded-xl border border-cdf-slate200"
                />
              </div>
            </div>

            <!-- Placeholder Types -->
            <div class="mb-6">
              <h3 class="text-lg font-semibold text-cdf-deep mb-3">Elementi da Inserire</h3>
              <div class="space-y-2">
                <button
                  v-for="placeholder in availablePlaceholders"
                  :key="placeholder.id"
                  @click="selectPlaceholder(placeholder)"
                  :class="[
                    'w-full p-3 text-left rounded-xl border transition-colors',
                    selectedPlaceholder?.id === placeholder.id
                      ? 'border-cdf-teal bg-cdf-teal/10 text-cdf-teal'
                      : 'border-cdf-slate200 bg-white hover:bg-cdf-sand'
                  ]"
                >
                  <div class="flex items-center space-x-3">
                    <component :is="placeholder.icon" class="w-5 h-5" />
                    <div>
                      <p class="font-medium text-sm">{{ placeholder.name }}</p>
                      <p class="text-xs text-cdf-slate700">{{ placeholder.description }}</p>
                    </div>
                  </div>
                </button>
              </div>
            </div>

            <!-- Instructions -->
            <div v-if="selectedPlaceholder" class="mb-6">
              <div class="bg-cdf-teal/10 border border-cdf-teal/20 rounded-xl p-4">
                <h4 class="font-semibold text-cdf-teal mb-2">Come Inserire</h4>
                <p class="text-sm text-cdf-slate700">
                  Clicca sulla posizione desiderata nell'immagine per inserire "{{ selectedPlaceholder.name }}"
                </p>
              </div>
            </div>

            <!-- Placed Elements -->
            <div v-if="placedElements.length > 0">
              <h3 class="text-lg font-semibold text-cdf-deep mb-3">Elementi Inseriti</h3>
              <div class="space-y-2">
                <div
                  v-for="element in placedElements"
                  :key="element.id"
                  class="p-3 bg-white rounded-xl border border-cdf-slate200"
                >
                  <div class="flex items-center justify-between">
                    <div>
                      <p class="font-medium text-sm text-cdf-deep">{{ element.name }}</p>
                      <p class="text-xs text-cdf-slate700">
                        Posizione: {{ Math.round(element.x) }}, {{ Math.round(element.y) }}
                      </p>
                    </div>
                    <button
                      @click="removeElement(element)"
                      class="p-1 text-red-600 hover:bg-red-50 rounded"
                    >
                      <TrashIcon class="w-4 h-4" />
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Canvas Area -->
        <div class="flex-1 flex flex-col">
          <!-- Toolbar -->
          <div class="p-4 border-b border-cdf-slate200 bg-white">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-4">
                <button
                  @click="zoomOut"
                  class="p-2 border border-cdf-slate200 rounded-xl hover:bg-cdf-sand transition-colors"
                >
                  <MinusIcon class="w-4 h-4" />
                </button>
                <span class="text-sm font-medium text-cdf-deep">{{ Math.round(zoom * 100) }}%</span>
                <button
                  @click="zoomIn"
                  class="p-2 border border-cdf-slate200 rounded-xl hover:bg-cdf-sand transition-colors"
                >
                  <PlusIcon class="w-4 h-4" />
                </button>
              </div>
              
              <div class="flex items-center space-x-2">
                <button
                  @click="previewMode = !previewMode"
                  :class="[
                    'px-4 py-2 rounded-xl font-medium transition-colors',
                    previewMode
                      ? 'bg-cdf-teal text-white'
                      : 'border border-cdf-slate200 text-cdf-deep hover:bg-cdf-sand'
                  ]"
                >
                  {{ previewMode ? 'Modifica' : 'Anteprima' }}
                </button>
                <button
                  @click="saveTemplate"
                  :disabled="loading"
                  class="px-4 py-2 bg-cdf-amber text-cdf-ink font-semibold rounded-xl hover:brightness-95 transition-colors disabled:opacity-50"
                >
                  {{ loading ? 'Salvataggio...' : 'Salva Template' }}
                </button>
              </div>
            </div>
          </div>

          <!-- Canvas -->
          <div class="flex-1 p-6 bg-gray-100 overflow-auto">
            <div class="flex justify-center">
              <div
                v-if="backgroundImage"
                ref="canvas"
                @click="onCanvasClick"
                class="relative cursor-crosshair"
                :style="{
                  transform: `scale(${zoom})`,
                  transformOrigin: 'top center'
                }"
              >
                <!-- Background Image -->
                <img
                  :src="backgroundImage"
                  alt="Certificato"
                  class="max-w-full h-auto shadow-lg"
                  style="max-height: 80vh;"
                />

                <!-- Placed Elements -->
                <div
                  v-for="element in placedElements"
                  :key="element.id"
                  :data-element-id="element.id"
                  :class="[
                    'absolute cursor-move select-none px-2 py-1 rounded text-sm font-medium',
                    previewMode ? 'bg-white/90 border border-cdf-teal' : 'bg-cdf-amber/90 text-cdf-ink'
                  ]"
                  :style="{
                    left: element.x + 'px',
                    top: element.y + 'px',
                    fontSize: element.fontSize + 'px',
                    color: element.color,
                    fontWeight: element.fontWeight
                  }"
                  @mousedown.stop="startDrag($event, element)"
                >
                  {{ previewMode ? element.previewText : element.name }}
                </div>

                <!-- Click Indicator -->
                <div
                  v-if="selectedPlaceholder && !previewMode"
                  class="absolute inset-0 pointer-events-none"
                >
                  <div class="absolute inset-0 bg-cdf-teal/10 border-2 border-dashed border-cdf-teal flex items-center justify-center">
                    <p class="text-cdf-teal font-medium bg-white px-4 py-2 rounded-xl">
                      Clicca per inserire "{{ selectedPlaceholder.name }}"
                    </p>
                  </div>
                </div>
              </div>

              <!-- No Background State -->
              <div v-else class="flex items-center justify-center h-96 w-full">
                <div class="text-center">
                  <DocumentTextIcon class="w-16 h-16 text-cdf-slate200 mx-auto mb-4" />
                  <h3 class="text-lg font-medium text-cdf-deep mb-2">Carica un'immagine di sfondo</h3>
                  <p class="text-cdf-slate700">Seleziona un file JPG, PNG o PDF per iniziare</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import {
  XMarkIcon,
  PlusIcon,
  MinusIcon,
  TrashIcon,
  DocumentTextIcon,
  UserIcon,
  CalendarIcon,
  AcademicCapIcon,
  ClockIcon,
  CheckBadgeIcon
} from '@heroicons/vue/24/outline'
import api from '@/api'

const props = defineProps({
  template: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['close', 'saved'])

// State
const loading = ref(false)
const previewMode = ref(false)
const zoom = ref(0.8)
const backgroundImage = ref(null)
const backgroundPreview = ref(null)
const selectedPlaceholder = ref(null)
const canvas = ref(null)
const templateName = ref('')
const selectedCourseId = ref('')
const courses = ref([])
const showValidation = ref(false)

// Available placeholders
const availablePlaceholders = ref([
  {
    id: 'user_name',
    name: 'Nome Utente',
    description: 'Nome completo dell\'utente',
    icon: UserIcon,
    previewText: 'Mario Rossi',
    fontSize: 24,
    color: '#0B3B5E',
    fontWeight: 'bold'
  },
  {
    id: 'course_title',
    name: 'Titolo Corso',
    description: 'Titolo del corso completato',
    icon: AcademicCapIcon,
    previewText: 'Corso di Esempio',
    fontSize: 20,
    color: '#00A7B7',
    fontWeight: '600'
  },
  {
    id: 'certificate_date',
    name: 'Data Certificato',
    description: 'Data di rilascio del certificato',
    icon: CalendarIcon,
    previewText: '15/09/2025',
    fontSize: 14,
    color: '#14161A',
    fontWeight: 'normal'
  },
  {
    id: 'hours_total',
    name: 'Ore Totali',
    description: 'Numero di ore di formazione',
    icon: ClockIcon,
    previewText: '40 ore',
    fontSize: 16,
    color: '#FFC857',
    fontWeight: '600'
  },
  {
    id: 'certificate_uid',
    name: 'ID Certificato',
    description: 'Identificativo univoco del certificato',
    icon: CheckBadgeIcon,
    previewText: 'ID: ABC123',
    fontSize: 12,
    color: '#334155',
    fontWeight: 'normal'
  }
])

// Placed elements
const placedElements = ref([])

// File upload handler
const handleFileUpload = (event) => {
  const file = event.target.files[0]
  if (file) {
    const reader = new FileReader()
    reader.onload = (e) => {
      backgroundImage.value = e.target.result
      backgroundPreview.value = e.target.result
    }
    reader.readAsDataURL(file)
  }
}

// Placeholder selection
const selectPlaceholder = (placeholder) => {
  selectedPlaceholder.value = placeholder
}

// Canvas click handler
const onCanvasClick = (event) => {
  if (!selectedPlaceholder.value || !backgroundImage.value) return

  const rect = canvas.value.getBoundingClientRect()
  const x = (event.clientX - rect.left) / zoom.value
  const y = (event.clientY - rect.top) / zoom.value

  const element = {
    id: Date.now() + Math.random(),
    type: selectedPlaceholder.value.id,
    name: selectedPlaceholder.value.name,
    previewText: selectedPlaceholder.value.previewText,
    x: x,
    y: y,
    fontSize: selectedPlaceholder.value.fontSize,
    color: selectedPlaceholder.value.color,
    fontWeight: selectedPlaceholder.value.fontWeight
  }

  placedElements.value.push(element)
  selectedPlaceholder.value = null
}

// Remove element
const removeElement = (element) => {
  placedElements.value = placedElements.value.filter(e => e.id !== element.id)
}

// Drag element
let isDraggingElement = false
let dragOffset = { x: 0, y: 0 }
const draggedElement = ref(null)

const startDrag = (event, element) => {
  isDraggingElement = true
  draggedElement.value = element
  const rect = canvas.value.getBoundingClientRect()
  dragOffset.x = event.clientX - rect.left - element.x * zoom.value
  dragOffset.y = event.clientY - rect.top - element.y * zoom.value
  
  document.addEventListener('mousemove', onMouseMove)
  document.addEventListener('mouseup', onMouseUp)
}

const onMouseMove = (event) => {
  if (!isDraggingElement || !draggedElement.value) return
  
  const rect = canvas.value.getBoundingClientRect()
  const x = (event.clientX - rect.left - dragOffset.x) / zoom.value
  const y = (event.clientY - rect.top - dragOffset.y) / zoom.value
  
  draggedElement.value.x = Math.max(0, x)
  draggedElement.value.y = Math.max(0, y)
}

const onMouseUp = () => {
  isDraggingElement = false
  draggedElement.value = null
  document.removeEventListener('mousemove', onMouseMove)
  document.removeEventListener('mouseup', onMouseUp)
}

// Zoom controls
const zoomIn = () => {
  zoom.value = Math.min(zoom.value + 0.1, 2)
}

const zoomOut = () => {
  zoom.value = Math.max(zoom.value - 0.1, 0.3)
}

// Load courses
const loadCourses = async () => {
  try {
    const response = await api.get('/v1/admin/courses')
    courses.value = response.data.data
  } catch (error) {
    console.error('Errore nel caricamento corsi:', error)
  }
}

// Save template
const saveTemplate = async () => {
  showValidation.value = true
  
  if (!templateName.value.trim()) {
    alert('Inserisci un nome per il template')
    return
  }
  
  if (!selectedCourseId.value) {
    alert('Seleziona un corso per associare l\'attestato')
    return
  }
  
  if (!backgroundImage.value) {
    alert('Carica un\'immagine di sfondo')
    return
  }
  
  loading.value = true
  
  try {
    const templateData = {
      name: templateName.value,
      description: `Template per ${courses.value.find(c => c.id == selectedCourseId.value)?.title || 'corso'}`,
      type: 'course',
      course_id: selectedCourseId.value,
      background_type: 'image',
      background_data: backgroundImage.value,
      placeholder_positions: placedElements.value.map(el => ({
        id: el.id,
        type: el.type,
        x: el.x,
        y: el.y,
        fontSize: el.fontSize,
        color: el.color,
        fontWeight: el.fontWeight,
        name: el.name,
        previewText: el.previewText
      })),
      styling: {
        font_family: 'Inter',
        font_size: 12,
        text_color: '#14161A',
        primary_color: '#0B3B5E',
        secondary_color: '#00A7B7',
        accent_color: '#FFC857',
        background_color: '#F4F1EA'
      },
      is_active: true
    }
    
    let response
    if (props.template?.id) {
      response = await api.put(`/v1/admin/certificate-templates/${props.template.id}`, templateData)
    } else {
      response = await api.post('/v1/admin/certificate-templates', templateData)
    }
    
    emit('saved', response.data.data)
  } catch (error) {
    console.error('Errore nel salvataggio template:', error)
    alert('Errore nel salvataggio del template. Controlla la console per i dettagli.')
  } finally {
    loading.value = false
  }
}

// Load data on mount
onMounted(() => {
  loadCourses()
  
  if (props.template) {
    templateName.value = props.template.name || ''
    selectedCourseId.value = props.template.course_id || ''
    
    // Carica placeholder_positions (ora vengono restituiti direttamente dal backend)
    if (props.template.placeholder_positions) {
      placedElements.value = props.template.placeholder_positions.map(pos => ({
        id: pos.id || Date.now() + Math.random(),
        type: pos.type,
        name: pos.name,
        previewText: pos.previewText,
        x: pos.x || 0,
        y: pos.y || 0,
        fontSize: pos.fontSize || 16,
        color: pos.color || '#0B3B5E',
        fontWeight: pos.fontWeight || 'normal'
      }))
    }
    
    if (props.template.background_image) {
      backgroundImage.value = props.template.background_image
      backgroundPreview.value = props.template.background_image
    }
  }
})
</script>
