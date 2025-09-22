<template>
  <div class="lesson-renderer">
    <!-- Video Lesson -->
    <VideoLesson
      v-if="lesson.type === 'video'"
      :lesson="lesson"
      :user-progress="userProgress"
      :is-last-lesson="isLastLesson"
      @lesson-completed="onLessonCompleted"
      @progress-updated="onProgressUpdated"
      @error="onError"
    />

    <!-- Quiz Lesson -->
    <QuizLesson
      v-else-if="lesson.type === 'quiz'"
      :lesson="lesson"
      :user-progress="userProgress"
      :is-last-lesson="isLastLesson"
      @lesson-completed="onLessonCompleted"
      @progress-updated="onProgressUpdated"
      @error="onError"
    />

    <!-- PDF Lesson -->
    <PdfLesson
      v-else-if="lesson.type === 'pdf'"
      :lesson="lesson"
      :user-progress="userProgress"
      :is-last-lesson="isLastLesson"
      @lesson-completed="onLessonCompleted"
      @progress-updated="onProgressUpdated"
      @error="onError"
    />

    <!-- Slide Lesson -->
    <SlideLesson
      v-else-if="lesson.type === 'slide'"
      :lesson="lesson"
      :user-progress="userProgress"
      :is-last-lesson="isLastLesson"
      @lesson-completed="onLessonCompleted"
      @progress-updated="onProgressUpdated"
      @error="onError"
    />

    <!-- Audio Lesson -->
    <AudioLesson
      v-else-if="lesson.type === 'audio'"
      :lesson="lesson"
      :user-progress="userProgress"
      :is-last-lesson="isLastLesson"
      @lesson-completed="onLessonCompleted"
      @progress-updated="onProgressUpdated"
      @error="onError"
    />

    <!-- Link Lesson -->
    <LinkLesson
      v-else-if="lesson.type === 'link'"
      :lesson="lesson"
      :user-progress="userProgress"
      :is-last-lesson="isLastLesson"
      @lesson-completed="onLessonCompleted"
      @progress-updated="onProgressUpdated"
      @error="onError"
    />

    <!-- Default/Unknown Lesson Type -->
    <div v-else class="bg-cdf-sand rounded-xl p-8 text-center">
      <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-cdf-slate200 flex items-center justify-center">
        <svg class="w-8 h-8 text-cdf-slate700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
      </div>
      <h3 class="text-lg font-semibold text-cdf-deep mb-2">Tipo di lezione non supportato</h3>
      <p class="text-cdf-slate700 mb-4">
        Il tipo di lezione "{{ lesson.type }}" non è ancora supportato.
      </p>
      <button
        @click="markAsCompleted"
        class="px-6 py-3 bg-cdf-amber text-cdf-ink rounded-xl font-semibold hover:brightness-95 transition-colors"
      >
        Segna come Completata
      </button>
    </div>
  </div>
</template>

<script setup>
import VideoLesson from './VideoLesson.vue'
import QuizLesson from './QuizLesson.vue'
import PdfLesson from './PdfLesson.vue'
import SlideLesson from './SlideLesson.vue'
import AudioLesson from './AudioLesson.vue'
import LinkLesson from './LinkLesson.vue'

const props = defineProps({
  lesson: {
    type: Object,
    required: true
  },
  userProgress: {
    type: Object,
    default: () => ({})
  },
  isLastLesson: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['lesson-completed', 'progress-updated', 'error'])

const onLessonCompleted = (data) => {
  emit('lesson-completed', data)
}

const onProgressUpdated = (data) => {
  emit('progress-updated', data)
}

const onError = (error) => {
  emit('error', error)
}

const markAsCompleted = () => {
  onLessonCompleted({
    lesson: props.lesson,
    completed: true,
    completed_at: new Date().toISOString()
  })
}
</script>

<style scoped>
.lesson-renderer {
  @apply w-full;
}
</style>
