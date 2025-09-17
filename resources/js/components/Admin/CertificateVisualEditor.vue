<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl max-w-7xl w-full max-h-[95vh] overflow-hidden">
      <!-- Header -->
      <div class="px-6 py-4 border-b border-cdf-slate200">
        <div class="flex items-center justify-between">
          <h2 class="text-xl font-bold text-cdf-deep">
            Editor Visuale - {{ template?.name || 'Nuovo Template' }}
          </h2>
          <div class="flex items-center space-x-3">
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
              @click="$emit('close')"
              class="p-2 hover:bg-cdf-sand rounded-xl transition-colors"
            >
              <XMarkIcon class="w-6 h-6 text-cdf-slate700" />
            </button>
          </div>
        </div>
      </div>

      <div class="flex h-[calc(95vh-80px)]">
        <!-- Sidebar -->
        <div class="w-80 border-r border-cdf-slate200 bg-cdf-sand overflow-y-auto">
          <div class="p-4">
            <!-- Placeholder Library -->
            <div class="mb-6">
              <h3 class="text-lg font-semibold text-cdf-deep mb-3">Elementi Disponibili</h3>
              <div class="space-y-2">
                <div
                  v-for="placeholder in availablePlaceholders"
                  :key="placeholder.id"
                  @dragstart="onDragStart($event, placeholder)"
                  draggable="true"
                  class="p-3 bg-white rounded-xl border border-cdf-slate200 cursor-move hover:shadow-sm transition-shadow"
                >
                  <div class="flex items-center space-x-3">
                    <component :is="placeholder.icon" class="w-5 h-5 text-cdf-teal" />
                    <div>
                      <p class="font-medium text-cdf-deep text-sm">{{ placeholder.name }}</p>
                      <p class="text-xs text-cdf-slate700">{{ placeholder.description }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Properties Panel -->
            <div v-if="selectedElement" class="mb-6">
              <h3 class="text-lg font-semibold text-cdf-deep mb-3">Proprietà</h3>
              <div class="bg-white rounded-xl border border-cdf-slate200 p-4 space-y-4">
                <div>
                  <label class="block text-sm font-medium text-cdf-deep mb-2">Testo</label>
                  <input
                    v-model="selectedElement.text"
                    type="text"
                    class="w-full px-3 py-2 border border-cdf-slate200 rounded-xl focus:ring-2 focus:ring-cdf-teal focus:border-transparent text-sm"
                  />
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-cdf-deep mb-2">Font Size</label>
                  <input
                    v-model.number="selectedElement.fontSize"
                    type="number"
                    min="8"
                    max="48"
                    class="w-full px-3 py-2 border border-cdf-slate200 rounded-xl focus:ring-2 focus:ring-cdf-teal focus:border-transparent text-sm"
                  />
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-cdf-deep mb-2">Colore</label>
                  <input
                    v-model="selectedElement.color"
                    type="color"
                    class="w-full h-10 border border-cdf-slate200 rounded-xl focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
                  />
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-cdf-deep mb-2">Font Weight</label>
                  <select
                    v-model="selectedElement.fontWeight"
                    class="w-full px-3 py-2 border border-cdf-slate200 rounded-xl focus:ring-2 focus:ring-cdf-teal focus:border-transparent text-sm"
                  >
                    <option value="normal">Normal</option>
                    <option value="bold">Bold</option>
                    <option value="600">Semi Bold</option>
                    <option value="800">Extra Bold</option>
                  </select>
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-cdf-deep mb-2">Allineamento</label>
                  <select
                    v-model="selectedElement.textAlign"
                    class="w-full px-3 py-2 border border-cdf-slate200 rounded-xl focus:ring-2 focus:ring-cdf-teal focus:border-transparent text-sm"
                  >
                    <option value="left">Sinistra</option>
                    <option value="center">Centro</option>
                    <option value="right">Destra</option>
                  </select>
                </div>
                
                <button
                  @click="deleteElement(selectedElement)"
                  class="w-full px-3 py-2 bg-red-100 text-red-700 rounded-xl hover:bg-red-200 transition-colors text-sm font-medium"
                >
                  Elimina Elemento
                </button>
              </div>
            </div>

            <!-- Template Settings -->
            <div>
              <h3 class="text-lg font-semibold text-cdf-deep mb-3">Impostazioni Template</h3>
              <div class="bg-white rounded-xl border border-cdf-slate200 p-4 space-y-4">
                <div>
                  <label class="block text-sm font-medium text-cdf-deep mb-2">Dimensioni</label>
                  <div class="grid grid-cols-2 gap-2">
                    <input
                      v-model.number="templateSettings.width"
                      type="number"
                      placeholder="Larghezza"
                      class="px-3 py-2 border border-cdf-slate200 rounded-xl focus:ring-2 focus:ring-cdf-teal focus:border-transparent text-sm"
                    />
                    <input
                      v-model.number="templateSettings.height"
                      type="number"
                      placeholder="Altezza"
                      class="px-3 py-2 border border-cdf-slate200 rounded-xl focus:ring-2 focus:ring-cdf-teal focus:border-transparent text-sm"
                    />
                  </div>
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-cdf-deep mb-2">Sfondo</label>
                  <input
                    v-model="templateSettings.background"
                    type="text"
                    placeholder="background: #ffffff"
                    class="w-full px-3 py-2 border border-cdf-slate200 rounded-xl focus:ring-2 focus:ring-cdf-teal focus:border-transparent text-sm font-mono"
                  />
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
                  @click="saveTemplate"
                  :disabled="loading"
                  class="px-4 py-2 bg-cdf-teal text-white font-semibold rounded-xl hover:brightness-95 transition-colors disabled:opacity-50"
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
                ref="canvas"
                @drop="onDrop"
                @dragover="onDragOver"
                @click="selectElement(null)"
                class="bg-white shadow-lg relative"
                :style="{
                  width: templateSettings.width + 'px',
                  height: templateSettings.height + 'px',
                  transform: `scale(${zoom})`,
                  transformOrigin: 'top center',
                  background: templateSettings.background
                }"
              >
                <!-- Elements -->
                <div
                  v-for="element in elements"
                  :key="element.id"
                  @click.stop="selectElement(element)"
                  :class="[
                    'absolute cursor-move select-none',
                    selectedElement?.id === element.id ? 'ring-2 ring-cdf-teal' : ''
                  ]"
                  :style="{
                    left: element.x + 'px',
                    top: element.y + 'px',
                    fontSize: element.fontSize + 'px',
                    color: element.color,
                    fontWeight: element.fontWeight,
                    textAlign: element.textAlign,
                    fontFamily: 'Inter, sans-serif'
                  }"
                  @mousedown="startDrag($event, element)"
                >
                  {{ element.text }}
                </div>

                <!-- Drop Zone Indicator -->
                <div
                  v-if="isDragging"
                  class="absolute inset-0 border-2 border-dashed border-cdf-teal bg-cdf-teal/10 flex items-center justify-center"
                >
                  <p class="text-cdf-teal font-medium">Rilascia qui per aggiungere l'elemento</p>
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
  UserIcon,
  DocumentTextIcon,
  CalendarIcon,
  AcademicCapIcon,
  BuildingOfficeIcon,
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
const isDragging = ref(false)
const selectedElement = ref(null)
const canvas = ref(null)

// Available placeholders
const availablePlaceholders = ref([
  {
    id: 'user_name',
    name: 'Nome Utente',
    description: 'Nome completo dell\'utente',
    icon: UserIcon,
    text: '{{user_name}}',
    fontSize: 24,
    color: '#0B3B5E',
    fontWeight: 'bold',
    textAlign: 'center'
  },
  {
    id: 'course_title',
    name: 'Titolo Corso',
    description: 'Titolo del corso completato',
    icon: DocumentTextIcon,
    text: '{{course_title}}',
    fontSize: 20,
    color: '#00A7B7',
    fontWeight: '600',
    textAlign: 'center'
  },
  {
    id: 'certificate_date',
    name: 'Data Certificato',
    description: 'Data di rilascio del certificato',
    icon: CalendarIcon,
    text: '{{certificate_date}}',
    fontSize: 14,
    color: '#14161A',
    fontWeight: 'normal',
    textAlign: 'center'
  },
  {
    id: 'hours_total',
    name: 'Ore Totali',
    description: 'Numero di ore di formazione',
    icon: ClockIcon,
    text: '{{hours_total}} ore',
    fontSize: 16,
    color: '#FFC857',
    fontWeight: '600',
    textAlign: 'center'
  },
  {
    id: 'certificate_uid',
    name: 'ID Certificato',
    description: 'Identificativo univoco del certificato',
    icon: CheckBadgeIcon,
    text: 'ID: {{certificate_uid}}',
    fontSize: 12,
    color: '#334155',
    fontWeight: 'normal',
    textAlign: 'left'
  }
])

// Template settings
const templateSettings = reactive({
  width: 800,
  height: 600,
  background: '#ffffff'
})

// Elements on canvas
const elements = ref([])

// Drag and drop handlers
const onDragStart = (event, placeholder) => {
  event.dataTransfer.setData('application/json', JSON.stringify(placeholder))
  isDragging.value = true
}

const onDragOver = (event) => {
  event.preventDefault()
}

const onDrop = (event) => {
  event.preventDefault()
  isDragging.value = false
  
  const placeholder = JSON.parse(event.dataTransfer.getData('application/json'))
  const rect = canvas.value.getBoundingClientRect()
  const x = (event.clientX - rect.left) / zoom.value
  const y = (event.clientY - rect.top) / zoom.value
  
  const element = {
    id: Date.now() + Math.random(),
    ...placeholder,
    x: Math.max(0, Math.min(x, templateSettings.width - 100)),
    y: Math.max(0, Math.min(y, templateSettings.height - 50))
  }
  
  elements.value.push(element)
  selectElement(element)
}

// Element selection and manipulation
const selectElement = (element) => {
  selectedElement.value = element
}

const deleteElement = (element) => {
  elements.value = elements.value.filter(e => e.id !== element.id)
  selectedElement.value = null
}

// Drag element on canvas
let isDraggingElement = false
let dragOffset = { x: 0, y: 0 }

const startDrag = (event, element) => {
  isDraggingElement = true
  const rect = canvas.value.getBoundingClientRect()
  dragOffset.x = event.clientX - rect.left - element.x * zoom.value
  dragOffset.y = event.clientY - rect.top - element.y * zoom.value
  
  document.addEventListener('mousemove', onMouseMove)
  document.addEventListener('mouseup', onMouseUp)
}

const onMouseMove = (event) => {
  if (!isDraggingElement || !selectedElement.value) return
  
  const rect = canvas.value.getBoundingClientRect()
  const x = (event.clientX - rect.left - dragOffset.x) / zoom.value
  const y = (event.clientY - rect.top - dragOffset.y) / zoom.value
  
  selectedElement.value.x = Math.max(0, Math.min(x, templateSettings.width - 100))
  selectedElement.value.y = Math.max(0, Math.min(y, templateSettings.height - 50))
}

const onMouseUp = () => {
  isDraggingElement = false
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

// Save template
const saveTemplate = async () => {
  loading.value = true
  
  try {
    const templateData = {
      name: props.template?.name || 'Template Personalizzato',
      description: props.template?.description || '',
      type: props.template?.type || 'custom',
      background_type: 'html',
      background_data: templateSettings.background,
      placeholder_positions: elements.value.map(el => ({
        id: el.id,
        type: el.id,
        x: el.x,
        y: el.y,
        fontSize: el.fontSize,
        color: el.color,
        fontWeight: el.fontWeight,
        textAlign: el.textAlign,
        text: el.text
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
  } finally {
    loading.value = false
  }
}

// Load existing template data
onMounted(() => {
  if (props.template?.placeholder_positions) {
    elements.value = props.template.placeholder_positions.map(pos => ({
      id: pos.id || Date.now() + Math.random(),
      text: pos.text || `{{${pos.type}}}`,
      x: pos.x || 0,
      y: pos.y || 0,
      fontSize: pos.fontSize || 16,
      color: pos.color || '#0B3B5E',
      fontWeight: pos.fontWeight || 'normal',
      textAlign: pos.textAlign || 'left'
    }))
  }
  
  if (props.template?.styling) {
    Object.assign(templateSettings, {
      width: 800,
      height: 600,
      background: props.template.styling.background_color || '#ffffff'
    })
  }
})
</script>
