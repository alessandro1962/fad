<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
      <!-- Header -->
      <div class="flex items-center justify-between p-6 border-b border-cdf-slate200">
        <h2 class="text-xl font-bold text-cdf-deep">
          {{ isEdit ? 'Modifica Corso' : 'Nuovo Corso' }}
        </h2>
        <button
          @click="$emit('close')"
          class="p-2 text-cdf-slate400 hover:text-cdf-slate600 hover:bg-cdf-slate200 rounded-lg transition-all"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <!-- Form -->
      <form @submit.prevent="saveCourse" class="p-6 space-y-6">
        <!-- Title -->
        <div>
          <label class="block text-sm font-medium text-cdf-deep mb-2">
            Titolo del corso *
          </label>
          <input
            v-model="form.title"
            type="text"
            required
            class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
            placeholder="Es. Laravel per Principianti"
          />
        </div>

        <!-- Slug -->
        <div>
          <label class="block text-sm font-medium text-cdf-deep mb-2">
            Slug (URL)
          </label>
          <input
            v-model="form.slug"
            type="text"
            class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
            placeholder="laravel-per-principianti"
          />
          <p class="text-xs text-cdf-slate600 mt-1">
            Se lasciato vuoto, verrà generato automaticamente dal titolo
          </p>
        </div>

        <!-- Summary -->
        <div>
          <label class="block text-sm font-medium text-cdf-deep mb-2">
            Riassunto *
          </label>
          <textarea
            v-model="form.summary"
            required
            rows="3"
            maxlength="500"
            class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
            placeholder="Breve descrizione del corso..."
          ></textarea>
          <p class="text-xs text-cdf-slate600 mt-1">
            {{ form.summary.length }}/500 caratteri
          </p>
        </div>

        <!-- Description -->
        <div>
          <label class="block text-sm font-medium text-cdf-deep mb-2">
            Descrizione completa *
          </label>
          <textarea
            v-model="form.description"
            required
            rows="6"
            class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
            placeholder="Descrizione dettagliata del corso, obiettivi, contenuti..."
          ></textarea>
        </div>

        <!-- Level and Duration -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-cdf-deep mb-2">
              Livello *
            </label>
            <select
              v-model="form.level"
              required
              class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
            >
              <option value="">Seleziona livello</option>
              <option value="beginner">Principiante</option>
              <option value="intermediate">Intermedio</option>
              <option value="expert">Esperto</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-cdf-deep mb-2">
              Durata (minuti) *
            </label>
            <input
              v-model.number="form.duration_minutes"
              type="number"
              required
              min="1"
              class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
              placeholder="180"
            />
          </div>
        </div>

        <!-- Price -->
        <div>
          <label class="block text-sm font-medium text-cdf-deep mb-2">
            Prezzo (€) *
          </label>
          <input
            v-model.number="form.price_euros"
            type="number"
            required
            min="0"
            step="0.01"
            class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
            placeholder="99.00"
          />
        </div>

        <!-- Tags -->
        <div>
          <label class="block text-sm font-medium text-cdf-deep mb-2">
            Tag
          </label>
          <input
            v-model="tagsInput"
            type="text"
            class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
            placeholder="laravel, php, web-development (separati da virgola)"
          />
          <p class="text-xs text-cdf-slate600 mt-1">
            Tag separati da virgola per categorizzare il corso
          </p>
        </div>

        <!-- Video Preview URL -->
        <div>
          <label class="block text-sm font-medium text-cdf-deep mb-2">
            URL Video Anteprima
          </label>
          <input
            v-model="form.video_preview_url"
            type="url"
            class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
            placeholder="https://vimeo.com/..."
          />
        </div>

        <!-- Status -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="flex items-center">
            <input
              v-model="form.is_active"
              type="checkbox"
              id="is_active"
              class="h-4 w-4 text-cdf-teal focus:ring-cdf-teal border-cdf-slate200 rounded"
            />
            <label for="is_active" class="ml-2 text-sm text-cdf-deep">
              Corso attivo
            </label>
          </div>

          <div class="flex items-center">
            <input
              v-model="form.published_at"
              type="checkbox"
              id="published_at"
              class="h-4 w-4 text-cdf-teal focus:ring-cdf-teal border-cdf-slate200 rounded"
            />
            <label for="published_at" class="ml-2 text-sm text-cdf-deep">
              Pubblicato
            </label>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end gap-3 pt-6 border-t border-cdf-slate200">
          <button
            type="button"
            @click="$emit('close')"
            class="px-4 py-2 text-cdf-slate700 border border-cdf-slate200 rounded-lg hover:bg-cdf-slate200 transition-all"
          >
            Annulla
          </button>
          <button
            type="submit"
            :disabled="saving"
            class="px-4 py-2 bg-cdf-amber text-cdf-ink rounded-lg font-semibold hover:brightness-95 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
          >
            <span v-if="saving" class="flex items-center gap-2">
              <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-cdf-ink"></div>
              Salvataggio...
            </span>
            <span v-else>
              {{ isEdit ? 'Aggiorna' : 'Crea' }} Corso
            </span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, watch } from 'vue'
import api from '@/api'

const props = defineProps({
  course: {
    type: Object,
    default: null
  },
  isEdit: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['close', 'saved'])

const saving = ref(false)

// Form data
const form = reactive({
  title: '',
  slug: '',
  summary: '',
  description: '',
  level: '',
  duration_minutes: 180,
  price_euros: 0,
  tags: [],
  video_preview_url: '',
  is_active: true,
  published_at: false
})

const tagsInput = ref('')

// Computed
const priceCents = computed(() => Math.round(form.price_euros * 100))

// Watch for course prop changes
watch(() => props.course, (newCourse) => {
  if (newCourse) {
    form.title = newCourse.title || ''
    form.slug = newCourse.slug || ''
    form.summary = newCourse.summary || ''
    form.description = newCourse.description || ''
    form.level = newCourse.level || ''
    form.duration_minutes = newCourse.duration_minutes || 180
    form.price_euros = newCourse.price_cents ? newCourse.price_cents / 100 : 0
    form.tags = newCourse.tags || []
    form.video_preview_url = newCourse.video_preview_url || ''
    form.is_active = newCourse.is_active ?? true
    form.published_at = !!newCourse.published_at
    
    tagsInput.value = form.tags.join(', ')
  }
}, { immediate: true })

// Watch tags input
watch(tagsInput, (newValue) => {
  form.tags = newValue
    .split(',')
    .map(tag => tag.trim())
    .filter(tag => tag.length > 0)
})

const saveCourse = async () => {
  saving.value = true
  
  try {
    const payload = {
      ...form,
      price_cents: priceCents.value
    }

    const response = props.isEdit
      ? await api.put(`/v1/admin/courses/${props.course.id}`, payload)
      : await api.post('/v1/admin/courses', payload)

    emit('saved', response.data.data)
  } catch (error) {
    console.error('Errore:', error)
    alert(error.message)
  } finally {
    saving.value = false
  }
}
</script>
