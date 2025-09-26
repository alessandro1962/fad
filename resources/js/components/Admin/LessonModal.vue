<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex min-h-screen items-center justify-center p-4">
      <!-- Backdrop -->
      <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="closeModal"></div>
      
      <!-- Modal -->
      <div class="relative w-full max-w-4xl bg-white rounded-2xl shadow-2xl">
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-cdf-slate200">
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
              <h2 class="text-2xl font-bold text-cdf-deep">
                {{ isEdit ? 'Modifica Lezione' : 'Nuova Lezione' }}
              </h2>
              <p class="text-cdf-slate600 mt-1">{{ module?.title }}</p>
            </div>
          </div>
          <button
            @click="closeModal"
            class="p-2 text-cdf-slate400 hover:text-cdf-deep hover:bg-cdf-slate100 rounded-lg transition-colors"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <!-- Content -->
        <div class="p-6">
          <form @submit.prevent="saveLesson" class="space-y-6">
            <!-- Basic Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-cdf-deep mb-2">Titolo Lezione</label>
                <input
                  v-model="form.title"
                  type="text"
                  required
                  class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
                  placeholder="Es. Introduzione alla Sicurezza Informatica"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-cdf-deep mb-2">Durata (minuti)</label>
                <input
                  v-model="form.duration_minutes"
                  type="number"
                  min="1"
                  class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
                  placeholder="15"
                />
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-cdf-deep mb-2">Descrizione</label>
              <textarea
                v-model="form.description"
                rows="3"
                class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
                placeholder="Descrizione della lezione..."
              ></textarea>
            </div>

            <!-- Lesson Type Selection -->
            <div>
              <label class="block text-sm font-medium text-cdf-deep mb-2">Tipo di Contenuto</label>
              <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                <button
                  v-for="type in lessonTypes"
                  :key="type.value"
                  type="button"
                  @click="selectLessonType(type.value)"
                  :class="[
                    'p-4 border-2 rounded-lg text-center transition-all',
                    form.type === type.value
                      ? 'border-cdf-teal bg-cdf-teal/10 text-cdf-teal'
                      : 'border-cdf-slate200 hover:border-cdf-slate300'
                  ]"
                >
                  <component :is="type.icon" class="w-8 h-8 mx-auto mb-2" />
                  <div class="text-sm font-medium">{{ type.label }}</div>
                </button>
              </div>
            </div>

            <!-- Content Configuration -->
            <div v-if="form.type" class="border-t border-cdf-slate200 pt-6">
              <h3 class="text-lg font-semibold text-cdf-deep mb-4">Configurazione Contenuto</h3>
              
              <!-- Video Configuration -->
              <div v-if="form.type === 'video'" class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-cdf-deep mb-2">Provider Video</label>
                  <select
                    v-model="form.payload.provider"
                    class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
                  >
                    <option value="vimeo">Vimeo</option>
                    <option value="youtube">YouTube</option>
                    <option value="upload">Upload Diretto</option>
                  </select>
                </div>
                
                <div v-if="form.payload.provider === 'vimeo'">
                  <label class="block text-sm font-medium text-cdf-deep mb-2">ID Video Vimeo</label>
                  <input
                    v-model="form.payload.video_id"
                    type="text"
                    class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
                    placeholder="Es. 123456789"
                  />
                </div>
                
                <div v-if="form.payload.provider === 'youtube'">
                  <label class="block text-sm font-medium text-cdf-deep mb-2">URL YouTube</label>
                  <input
                    v-model="form.payload.video_url"
                    type="url"
                    class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
                    placeholder="https://www.youtube.com/watch?v=..."
                  />
                </div>

                <div class="flex items-center gap-4">
                  <label class="flex items-center gap-2">
                    <input
                      v-model="form.payload.block_progression"
                      type="checkbox"
                      class="rounded border-cdf-slate200 text-cdf-teal focus:ring-cdf-teal"
                    />
                    <span class="text-sm text-cdf-deep">Blocca progressione fino al completamento</span>
                  </label>
                </div>
              </div>

              <!-- PDF Configuration -->
              <div v-if="form.type === 'pdf'" class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-cdf-deep mb-2">File PDF</label>
                  <input
                    type="file"
                    accept=".pdf"
                    @change="handleFileUpload"
                    class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
                  />
                  <p class="text-xs text-cdf-slate600 mt-1">Carica un file PDF (max 50MB)</p>
                </div>
                
                <div v-if="form.payload.file_name">
                  <p class="text-sm text-cdf-slate600">File selezionato: {{ form.payload.file_name }}</p>
                </div>
              </div>

              <!-- Quiz Configuration -->
              <div v-if="form.type === 'quiz'" class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-cdf-deep mb-2">Titolo Quiz</label>
                  <input
                    v-model="form.payload.quiz_title"
                    type="text"
                    class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
                    placeholder="Es. Quiz di Verifica"
                  />
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-cdf-deep mb-2">Punteggio Minimo (%)</label>
                    <input
                      v-model="form.payload.passing_score"
                      type="number"
                      min="0"
                      max="100"
                      class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
                      placeholder="70"
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-cdf-deep mb-2">Tentativi Massimi</label>
                    <input
                      v-model="form.payload.max_attempts"
                      type="number"
                      min="1"
                      class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
                      placeholder="3"
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-cdf-deep mb-2">Limite Tempo (min)</label>
                    <input
                      v-model="form.payload.time_limit"
                      type="number"
                      min="0"
                      class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
                      placeholder="0 (illimitato)"
                    />
                  </div>
                </div>

                <div class="flex items-center gap-4">
                  <label class="flex items-center gap-2">
                    <input
                      v-model="form.payload.block_progression"
                      type="checkbox"
                      class="rounded border-cdf-slate200 text-cdf-teal focus:ring-cdf-teal"
                    />
                    <span class="text-sm text-cdf-deep">Blocca progressione fino al superamento</span>
                  </label>
                </div>

                <!-- Quiz Questions -->
                <div class="border-t border-cdf-slate200 pt-4">
                  <div class="flex items-center justify-between mb-4">
                    <h4 class="font-medium text-cdf-deep">Domande del Quiz</h4>
                    <button
                      type="button"
                      @click="addQuestion"
                      class="px-3 py-1.5 bg-cdf-teal text-white text-sm rounded-lg hover:bg-cdf-deep transition-colors"
                    >
                      Aggiungi Domanda
                    </button>
                  </div>
                  
                  <div class="space-y-4">
                    <div
                      v-for="(question, index) in form.payload.questions"
                      :key="index"
                      class="p-4 border border-cdf-slate200 rounded-lg"
                    >
                      <div class="flex items-center justify-between mb-3">
                        <h5 class="font-medium text-cdf-deep">Domanda {{ index + 1 }}</h5>
                        <button
                          type="button"
                          @click="removeQuestion(index)"
                          class="p-1 text-red-500 hover:text-red-700 hover:bg-red-50 rounded"
                        >
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                          </svg>
                        </button>
                      </div>
                      
                      <div class="space-y-3">
                        <div>
                          <label class="block text-sm font-medium text-cdf-deep mb-1">Testo Domanda</label>
                          <input
                            v-model="question.text"
                            type="text"
                            class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
                            placeholder="Inserisci la domanda..."
                          />
                        </div>
                        
                        <div>
                          <label class="block text-sm font-medium text-cdf-deep mb-1">Tipo Domanda</label>
                          <select
                            v-model="question.type"
                            @change="updateQuestionType(question)"
                            class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
                          >
                            <option value="single_choice">Scelta Singola</option>
                            <option value="multiple_choice">Scelta Multipla</option>
                            <option value="true_false">Vero/Falso</option>
                            <option value="text">Risposta Libera</option>
                            <option value="number">Risposta Numerica</option>
                          </select>
                        </div>
                        
                        <!-- Options for choice questions -->
                        <div v-if="['single_choice', 'multiple_choice'].includes(question.type)">
                          <label class="block text-sm font-medium text-cdf-deep mb-1">Opzioni di Risposta</label>
                          <div class="space-y-2">
                            <div
                              v-for="(option, optIndex) in question.options"
                              :key="optIndex"
                              class="flex items-center gap-2"
                            >
                              <input
                                v-model="question.options[optIndex]"
                                type="text"
                                class="flex-1 px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
                                :placeholder="`Opzione ${optIndex + 1}`"
                              />
                              <button
                                type="button"
                                @click="removeOption(question, optIndex)"
                                class="p-1 text-red-500 hover:text-red-700 hover:bg-red-50 rounded"
                              >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                              </button>
                            </div>
                            <button
                              type="button"
                              @click="addOption(question)"
                              class="px-3 py-1.5 text-sm text-cdf-teal hover:text-cdf-deep border border-cdf-teal rounded-lg hover:bg-cdf-teal/10 transition-colors"
                            >
                              Aggiungi Opzione
                            </button>
                          </div>
                        </div>
                        
                        <!-- Correct Answer -->
                        <div>
                          <label class="block text-sm font-medium text-cdf-deep mb-1">Risposta Corretta</label>
                          <div v-if="question.type === 'true_false'">
                            <select
                              v-model="question.correct_answer[0]"
                              class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
                            >
                              <option value="true">Vero</option>
                              <option value="false">Falso</option>
                            </select>
                          </div>
                          <div v-else-if="['single_choice', 'multiple_choice'].includes(question.type)">
                            <div class="space-y-2">
                              <label
                                v-for="(option, optIndex) in question.options"
                                :key="optIndex"
                                class="flex items-center gap-2"
                              >
                                <input
                                  :type="question.type === 'single_choice' ? 'radio' : 'checkbox'"
                                  :value="optIndex"
                                  v-model="question.correct_answer"
                                  :name="`correct_${index}`"
                                  class="text-cdf-teal focus:ring-cdf-teal"
                                />
                                <span class="text-sm">{{ option }}</span>
                              </label>
                            </div>
                          </div>
                          <div v-else>
                            <input
                              v-model="question.correct_answer[0]"
                              type="text"
                              class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
                              placeholder="Risposta corretta..."
                            />
                          </div>
                        </div>
                        
                        <div>
                          <label class="block text-sm font-medium text-cdf-deep mb-1">Punteggio</label>
                          <input
                            v-model="question.score"
                            type="number"
                            min="1"
                            class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
                            placeholder="1"
                          />
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Slide Configuration -->
              <div v-if="form.type === 'slide'" class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-cdf-deep mb-2">File Slide</label>
                  <input
                    type="file"
                    accept=".pdf,.pptx"
                    @change="handleFileUpload"
                    class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
                  />
                  <p class="text-xs text-cdf-slate600 mt-1">Carica file PDF o PowerPoint (max 100MB)</p>
                </div>
                
                <div v-if="form.payload.file_name">
                  <p class="text-sm text-cdf-slate600">File selezionato: {{ form.payload.file_name }}</p>
                </div>
              </div>

              <!-- Link Configuration -->
              <div v-if="form.type === 'link'" class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-cdf-deep mb-2">URL Link</label>
                  <input
                    v-model="form.payload.url"
                    type="url"
                    class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
                    placeholder="https://example.com"
                  />
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-cdf-deep mb-2">Titolo Link</label>
                  <input
                    v-model="form.payload.title"
                    type="text"
                    class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
                    placeholder="Titolo del link"
                  />
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-cdf-deep mb-2">Descrizione</label>
                  <textarea
                    v-model="form.payload.description"
                    rows="2"
                    class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
                    placeholder="Descrizione del link..."
                  ></textarea>
                </div>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-3 pt-6 border-t border-cdf-slate200">
              <button
                type="submit"
                :disabled="loading"
                class="px-6 py-3 bg-cdf-teal text-white rounded-lg hover:bg-cdf-deep disabled:opacity-50 transition-colors"
              >
                {{ loading ? 'Salvataggio...' : (isEdit ? 'Aggiorna Lezione' : 'Crea Lezione') }}
              </button>
              <button
                type="button"
                @click="closeModal"
                class="px-6 py-3 border border-cdf-slate200 text-cdf-slate600 rounded-lg hover:bg-cdf-slate100 transition-colors"
              >
                Annulla
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/api'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  },
  lesson: {
    type: Object,
    default: null
  },
  module: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['close', 'saved'])
const router = useRouter()

// State
const loading = ref(false)
const form = ref({
  title: '',
  description: '',
  type: '',
  duration_minutes: 15,
  order: 1,
  payload: {}
})

// Lesson types configuration
const lessonTypes = [
  {
    value: 'video',
    label: 'Video',
    icon: 'VideoIcon'
  },
  {
    value: 'pdf',
    label: 'PDF',
    icon: 'DocumentIcon'
  },
  {
    value: 'quiz',
    label: 'Quiz',
    icon: 'QuestionMarkCircleIcon'
  },
  {
    value: 'slide',
    label: 'Slide',
    icon: 'PresentationChartBarIcon'
  },
  {
    value: 'link',
    label: 'Link',
    icon: 'LinkIcon'
  }
]

// Computed
const isOpen = computed(() => props.isOpen)
const isEdit = computed(() => !!props.lesson)

// Methods
const closeModal = () => {
  emit('close')
  resetForm()
}

const resetForm = () => {
  form.value = {
    title: '',
    description: '',
    type: '',
    duration_minutes: 15,
    order: 1,
    payload: {}
  }
}

const selectLessonType = (type) => {
  form.value.type = type
  form.value.payload = getDefaultPayload(type)
}

const getDefaultPayload = (type) => {
  const defaults = {
    video: {
      provider: 'vimeo',
      video_id: '',
      video_url: '',
      block_progression: true
    },
    pdf: {
      file_name: '',
      file_path: ''
    },
    quiz: {
      quiz_title: '',
      passing_score: 70,
      max_attempts: 3,
      time_limit: 0,
      block_progression: true,
      questions: []
    },
    slide: {
      file_name: '',
      file_path: ''
    },
    link: {
      url: '',
      title: '',
      description: ''
    }
  }
  return defaults[type] || {}
}

const handleFileUpload = (event) => {
  const file = event.target.files[0]
  if (file) {
    form.value.payload.file_name = file.name
    form.value.payload.file_path = file.name // In a real app, you'd upload this
  }
}

const addQuestion = () => {
  form.value.payload.questions.push({
    text: '',
    type: 'single_choice',
    options: ['', ''],
    correct_answer: [0],
    score: 1
  })
}

const removeQuestion = (index) => {
  form.value.payload.questions.splice(index, 1)
}

const addOption = (question) => {
  question.options.push('')
}

const removeOption = (question, index) => {
  question.options.splice(index, 1)
}

const updateQuestionType = (question) => {
  // Reset options and correct answer based on type
  if (['single_choice', 'multiple_choice'].includes(question.type)) {
    question.options = ['', '']
    question.correct_answer = [0]
  } else if (question.type === 'true_false') {
    question.options = []
    question.correct_answer = ['true']
  } else {
    question.options = []
    question.correct_answer = ['']
  }
}

const saveLesson = async () => {
  try {
    loading.value = true
    
    const payload = {
      ...form.value,
      module_id: props.module.id
    }
    
    let response
    if (isEdit.value) {
      response = await api.put(`/v1/admin/lessons/${props.lesson.id}`, payload)
    } else {
      response = await api.post('/v1/admin/lessons', payload)
    }
    
    emit('saved', response.data.data)
    closeModal()
  } catch (error) {
    console.error('Errore nel salvataggio della lezione:', error)
  } finally {
    loading.value = false
  }
}

const goToDashboard = () => {
  router.push('/admin')
}

// Watch for lesson changes
watch(() => props.lesson, (newLesson) => {
  if (newLesson) {
    form.value = {
      title: newLesson.title || '',
      description: newLesson.description || '',
      type: newLesson.type || '',
      duration_minutes: newLesson.duration_minutes || 15,
      order: newLesson.order || 1,
      payload: newLesson.payload || {}
    }
  }
}, { immediate: true })

// Watch for modal open
watch(() => props.isOpen, (isOpen) => {
  if (isOpen && !props.lesson) {
    resetForm()
  }
})
</script>
