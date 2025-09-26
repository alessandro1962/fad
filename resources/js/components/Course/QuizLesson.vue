<template>
  <div class="quiz-lesson">
    <!-- Loading State -->
    <div v-if="loading" class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6 text-center">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-cdf-teal mx-auto mb-4"></div>
      <p class="text-cdf-slate700">Caricamento quiz...</p>
    </div>
    
    <!-- Quiz Content -->
    <div v-else-if="quizData.questions && quizData.questions.length > 0">
      <!-- Quiz Header -->
      <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
          <div>
            <h2 class="text-2xl font-bold text-cdf-deep">{{ quizData.quiz_title || lesson.title }}</h2>
            <p class="text-cdf-slate700 mt-1">{{ quizData.description || 'Completa il quiz per continuare' }}</p>
          </div>
          <div class="text-right">
            <div class="text-sm text-cdf-slate600">
              <span v-if="timeLimit > 0" class="block">
                Tempo rimanente: 
                <span class="font-semibold" :class="timeRemaining <= 60 ? 'text-red-600' : 'text-cdf-deep'">
                  {{ formatTime(timeRemaining) }}
                </span>
              </span>
              <span class="block mt-1">
                Punteggio minimo: <span class="font-semibold text-cdf-deep">{{ quizData.passing_score || 70 }}%</span>
              </span>
            </div>
          </div>
        </div>

        <!-- Progress Bar -->
        <div class="w-full bg-cdf-slate200 rounded-full h-2">
          <div 
            class="bg-gradient-to-r from-cdf-teal to-cdf-deep h-2 rounded-full transition-all duration-500"
            :style="{ width: `${quizProgress}%` }"
          ></div>
        </div>
        <p class="text-sm text-cdf-slate600 mt-2">
          Domanda {{ currentQuestionIndex + 1 }} di {{ quizData.questions?.length || 0 }}
        </p>
      </div>

      <!-- Quiz Content -->
      <div v-if="!quizCompleted" class="space-y-6">
        <!-- Current Question -->
        <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6">
          <div class="mb-6">
            <h3 class="text-xl font-bold text-cdf-deep mb-4">
              {{ currentQuestion.text }}
            </h3>
            
            <!-- Question Type: Single Choice -->
            <div v-if="currentQuestion.type === 'single_choice'" class="space-y-3">
              <label
                v-for="(option, index) in currentQuestion.options"
                :key="index"
                class="flex items-center p-4 border border-cdf-slate200 rounded-xl hover:border-cdf-teal/50 hover:bg-cdf-teal/5 transition-all cursor-pointer"
                :class="{ 'border-cdf-teal bg-cdf-teal/10': answers[currentQuestionIndex] === index }"
              >
                <input
                  type="radio"
                  :name="`question_${currentQuestionIndex}`"
                  :value="index"
                  v-model="answers[currentQuestionIndex]"
                  class="sr-only"
                />
                <div class="flex items-center w-full">
                  <div class="w-6 h-6 rounded-full border-2 mr-4 flex items-center justify-center"
                       :class="answers[currentQuestionIndex] === index ? 'border-cdf-teal bg-cdf-teal' : 'border-cdf-slate300'">
                    <div v-if="answers[currentQuestionIndex] === index" class="w-3 h-3 rounded-full bg-white"></div>
                  </div>
                  <span class="text-cdf-deep font-medium">{{ option }}</span>
                </div>
              </label>
            </div>

            <!-- Question Type: Multiple Choice -->
            <div v-else-if="currentQuestion.type === 'multiple_choice'" class="space-y-3">
              <label
                v-for="(option, index) in currentQuestion.options"
                :key="index"
                class="flex items-center p-4 border border-cdf-slate200 rounded-xl hover:border-cdf-teal/50 hover:bg-cdf-teal/5 transition-all cursor-pointer"
                :class="{ 'border-cdf-teal bg-cdf-teal/10': (answers[currentQuestionIndex] || []).includes(index) }"
              >
                <input
                  type="checkbox"
                  :value="index"
                  v-model="answers[currentQuestionIndex]"
                  class="sr-only"
                />
                <div class="flex items-center w-full">
                  <div class="w-6 h-6 rounded border-2 mr-4 flex items-center justify-center"
                       :class="(answers[currentQuestionIndex] || []).includes(index) ? 'border-cdf-teal bg-cdf-teal' : 'border-cdf-slate300'">
                    <svg v-if="(answers[currentQuestionIndex] || []).includes(index)" class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                  </div>
                  <span class="text-cdf-deep font-medium">{{ option }}</span>
                </div>
              </label>
            </div>

            <!-- Question Type: True/False -->
            <div v-else-if="currentQuestion.type === 'true_false'" class="space-y-3">
              <label
                v-for="(option, index) in ['Vero', 'Falso']"
                :key="index"
                class="flex items-center p-4 border border-cdf-slate200 rounded-xl hover:border-cdf-teal/50 hover:bg-cdf-teal/5 transition-all cursor-pointer"
                :class="{ 'border-cdf-teal bg-cdf-teal/10': answers[currentQuestionIndex] === index }"
              >
                <input
                  type="radio"
                  :name="`question_${currentQuestionIndex}`"
                  :value="index"
                  v-model="answers[currentQuestionIndex]"
                  class="sr-only"
                />
                <div class="flex items-center w-full">
                  <div class="w-6 h-6 rounded-full border-2 mr-4 flex items-center justify-center"
                       :class="answers[currentQuestionIndex] === index ? 'border-cdf-teal bg-cdf-teal' : 'border-cdf-slate300'">
                    <div v-if="answers[currentQuestionIndex] === index" class="w-3 h-3 rounded-full bg-white"></div>
                  </div>
                  <span class="text-cdf-deep font-medium">{{ option }}</span>
                </div>
              </label>
            </div>

            <!-- Question Type: Text Input -->
            <div v-else-if="currentQuestion.type === 'text_input'" class="space-y-3">
              <textarea
                v-model="answers[currentQuestionIndex]"
                class="w-full p-4 border border-cdf-slate200 rounded-xl focus:ring-2 focus:ring-cdf-teal focus:border-transparent resize-none"
                rows="4"
                placeholder="Inserisci la tua risposta..."
              ></textarea>
            </div>

            <!-- Question Type: Number Input -->
            <div v-else-if="currentQuestion.type === 'number_input'" class="space-y-3">
              <input
                v-model.number="answers[currentQuestionIndex]"
                type="number"
                class="w-full p-4 border border-cdf-slate200 rounded-xl focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
                placeholder="Inserisci un numero..."
              />
            </div>
          </div>

          <!-- Navigation Buttons -->
          <div class="flex justify-between">
            <button
              @click="previousQuestion"
              :disabled="currentQuestionIndex === 0"
              class="px-6 py-3 border border-cdf-slate200 text-cdf-slate700 rounded-xl font-semibold hover:bg-cdf-slate50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
              </svg>
              Precedente
            </button>

            <button
              v-if="currentQuestionIndex < (quizData.questions?.length || 0) - 1"
              @click="nextQuestion"
              :disabled="!hasAnswer"
              class="px-6 py-3 bg-cdf-teal text-white rounded-xl font-semibold hover:bg-cdf-deep transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Successiva
              <svg class="w-5 h-5 inline ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </button>

            <button
              v-else
              @click="submitQuiz"
              :disabled="!hasAnswer"
              class="px-6 py-3 bg-cdf-amber text-cdf-ink rounded-xl font-semibold hover:brightness-95 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              Completa Quiz
            </button>
          </div>
        </div>
      </div>

      <!-- Quiz Results -->
      <div v-else class="space-y-6">
        <!-- Results Summary -->
        <div class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6">
          <div class="text-center mb-6">
            <div class="w-20 h-20 mx-auto mb-4 rounded-full flex items-center justify-center"
                 :class="quizPassed ? 'bg-green-100' : 'bg-red-100'">
              <svg class="w-10 h-10" :class="quizPassed ? 'text-green-600' : 'text-red-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path v-if="quizPassed" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <h3 class="text-2xl font-bold mb-2" :class="quizPassed ? 'text-green-600' : 'text-red-600'">
              {{ quizPassed ? 'Quiz Superato!' : 'Quiz Non Superato' }}
            </h3>
            <p class="text-cdf-slate700">
              Hai ottenuto {{ finalScore }}% (minimo richiesto: {{ quizData.passing_score || 70 }}%)
            </p>
          </div>

          <!-- Score Breakdown -->
          <div class="grid md:grid-cols-3 gap-4 mb-6">
            <div class="text-center p-4 bg-cdf-sand rounded-xl">
              <div class="text-2xl font-bold text-cdf-deep">{{ correctAnswers }}</div>
              <div class="text-sm text-cdf-slate700">Risposte Corrette</div>
            </div>
            <div class="text-center p-4 bg-cdf-sand rounded-xl">
              <div class="text-2xl font-bold text-cdf-deep">{{ totalQuestions }}</div>
              <div class="text-sm text-cdf-slate700">Domande Totali</div>
            </div>
            <div class="text-center p-4 bg-cdf-sand rounded-xl">
              <div class="text-2xl font-bold text-cdf-deep">{{ attemptNumber }}</div>
              <div class="text-sm text-cdf-slate700">Tentativo</div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex gap-3">
            <button
              v-if="!quizPassed"
              @click="retryQuiz"
              class="flex-1 bg-cdf-teal text-white px-6 py-3 rounded-xl font-semibold hover:bg-cdf-deep transition-colors"
            >
              Riprova Quiz
            </button>
            <button
              v-else
              @click="proceedToNext"
              class="flex-1 bg-cdf-amber text-cdf-ink px-6 py-3 rounded-xl font-semibold hover:brightness-95 transition-colors"
            >
              {{ isLastLesson ? 'Completa Corso' : 'Continua al Prossimo' }}
            </button>
            <button
              v-if="quizPassed"
              @click="reviewAnswers"
              class="px-6 py-3 border border-cdf-slate200 text-cdf-slate700 rounded-xl font-semibold hover:bg-cdf-slate50 transition-colors"
            >
              Rivedi Risposte
            </button>
          </div>
        </div>

        <!-- Detailed Results -->
        <div v-if="showDetailedResults && quizPassed" class="bg-white rounded-2xl shadow-sm border border-cdf-slate200 p-6">
          <h4 class="text-lg font-bold text-cdf-deep mb-4">Dettaglio Risposte</h4>
          <div class="space-y-4">
            <div
              v-for="(question, index) in (quizData.questions || [])"
              :key="index"
              class="p-4 border rounded-xl"
              :class="isAnswerCorrect(index) ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50'"
            >
              <div class="flex items-start justify-between mb-2">
                <h5 class="font-semibold text-cdf-deep">{{ question.text }}</h5>
                <div class="flex items-center">
                  <svg v-if="isAnswerCorrect(index)" class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                  <svg v-else class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
                </div>
              </div>
              <div class="text-sm text-cdf-slate700">
                <p><strong>La tua risposta:</strong> {{ formatUserAnswer(index) }}</p>
                <p><strong>Risposta corretta:</strong> {{ formatCorrectAnswer(index) }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Error State -->
    <div v-else class="bg-red-50 border border-red-200 rounded-xl p-6 text-center">
      <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-red-100 flex items-center justify-center">
        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
      </div>
      <h3 class="text-lg font-semibold text-red-800 mb-2">Errore nel caricamento del quiz</h3>
      <p class="text-red-700 mb-4">
        Non è stato possibile caricare il quiz. Riprova o contatta il supporto.
      </p>
      <button
        @click="loadQuiz"
        class="px-6 py-3 bg-red-600 text-white rounded-xl font-semibold hover:bg-red-700 transition-colors"
      >
        Riprova
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import api from '@/api'

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

// Refs
const quizData = ref({})
const answers = ref([])
const currentQuestionIndex = ref(0)
const quizCompleted = ref(false)
const quizPassed = ref(false)
const finalScore = ref(0)
const correctAnswers = ref(0)
const totalQuestions = ref(0)
const attemptNumber = ref(1)
const currentAttemptId = ref(null)
const showDetailedResults = ref(false)
const timeRemaining = ref(0)
const timeLimit = ref(0)
const timerInterval = ref(null)
const quizAnswers = ref([])
const loading = ref(true)

// Computed
const currentQuestion = computed(() => {
  return quizData.value.questions?.[currentQuestionIndex.value] || {}
})

const quizProgress = computed(() => {
  return ((currentQuestionIndex.value + 1) / totalQuestions.value) * 100
})

const hasAnswer = computed(() => {
  const answer = answers.value[currentQuestionIndex.value]
  if (Array.isArray(answer)) {
    return answer.length > 0
  }
  return answer !== null && answer !== undefined && answer !== ''
})

// Methods
const formatTime = (seconds) => {
  const minutes = Math.floor(seconds / 60)
  const secs = seconds % 60
  return `${minutes}:${secs.toString().padStart(2, '0')}`
}

const loadQuiz = async () => {
  try {
    loading.value = true
    console.log('Loading quiz for lesson:', props.lesson.id)
    
    // Try to load quiz from lesson payload first
    if (props.lesson.payload && props.lesson.payload.questions) {
      quizData.value = props.lesson.payload
      totalQuestions.value = quizData.value.questions?.length || 0
      timeLimit.value = (quizData.value.time_limit || 0) * 60
      timeRemaining.value = timeLimit.value
      attemptNumber.value = 1
      
      console.log('Quiz loaded from lesson payload:', quizData.value)
    } else {
      // Fallback to API
      const response = await api.get(`/v1/lessons/${props.lesson.id}/quiz`)
      quizData.value = response.data.data || {}
      totalQuestions.value = quizData.value.questions?.length || 0
      timeLimit.value = (quizData.value.time_limit_minutes || 0) * 60
      timeRemaining.value = timeLimit.value
      attemptNumber.value = 1
      
      console.log('Quiz loaded from API:', quizData.value)
    }
    
    // Initialize answers array
    answers.value = new Array(totalQuestions.value).fill(null)
    
    // Start timer if time limit is set
    if (timeLimit.value > 0) {
      startTimer()
    }
    
  } catch (error) {
    console.error('Error loading quiz:', error)
    emit('error', error)
  } finally {
    loading.value = false
  }
}

const startTimer = () => {
  timerInterval.value = setInterval(() => {
    timeRemaining.value--
    if (timeRemaining.value <= 0) {
      submitQuiz()
    }
  }, 1000)
}

const nextQuestion = () => {
  if (currentQuestionIndex.value < totalQuestions.value - 1) {
    currentQuestionIndex.value++
  }
}

const previousQuestion = () => {
  if (currentQuestionIndex.value > 0) {
    currentQuestionIndex.value--
  }
}

const submitQuiz = async () => {
  try {
    // Calculate score locally
    let correct = 0
    const results = []
    
    quizData.value.questions.forEach((question, index) => {
      const userAnswer = answers.value[index]
      const isCorrect = checkAnswer(question, userAnswer)
      
      if (isCorrect) correct++
      
      results.push({
        question_id: question.id || index,
        answer: userAnswer,
        is_correct: isCorrect,
        correct_answer: question.correct_answer
      })
    })
    
    const percentage = Math.round((correct / totalQuestions.value) * 100)
    const passingScore = quizData.value.passing_score || 70
    const passed = percentage >= passingScore
    
    // Update state
    quizCompleted.value = true
    quizPassed.value = passed
    finalScore.value = percentage
    correctAnswers.value = correct
    quizAnswers.value = results
    
    // Save progress
    await saveProgress(passed, percentage)
    
    // Emit completion event
    emit('lesson-completed', {
      lesson: props.lesson,
      completed: passed,
      score: percentage,
      passed: passed,
      completed_at: new Date().toISOString()
    })
    
  } catch (error) {
    console.error('Error submitting quiz:', error)
    emit('error', error)
  }
}

const checkAnswer = (question, userAnswer) => {
  const correctAnswer = question.correct_answer
  
  if (question.type === 'single_choice' || question.type === 'true_false') {
    return userAnswer === correctAnswer
  } else if (question.type === 'multiple_choice') {
    if (!Array.isArray(userAnswer) || !Array.isArray(correctAnswer)) return false
    return JSON.stringify(userAnswer.sort()) === JSON.stringify(correctAnswer.sort())
  } else if (question.type === 'text_input') {
    return userAnswer?.toString().toLowerCase().trim() === correctAnswer?.toString().toLowerCase().trim()
  } else if (question.type === 'number_input') {
    return parseFloat(userAnswer) === parseFloat(correctAnswer)
  }
  
  return false
}

const saveProgress = async (passed, score) => {
  try {
    const progressData = {
      completed: passed,
      score: score,
      completed_at: new Date().toISOString()
    }

    await api.patch(`/v1/progress/lesson/${props.lesson.id}`, progressData)
    
    emit('progress-updated', {
      lesson_id: props.lesson.id,
      ...progressData
    })
    
    console.log('Quiz progress saved:', progressData)
  } catch (error) {
    console.error('Error saving quiz progress:', error)
  }
}

const retryQuiz = () => {
  // Reset state
  quizCompleted.value = false
  quizPassed.value = false
  answers.value = new Array(totalQuestions.value).fill(null)
  currentQuestionIndex.value = 0
  timeRemaining.value = timeLimit.value
  showDetailedResults.value = false
  attemptNumber.value++
  
  // Restart timer if needed
  if (timeLimit.value > 0) {
    startTimer()
  }
}

const proceedToNext = () => {
  emit('lesson-completed', {
    lesson: props.lesson,
    completed: true,
    completed_at: new Date().toISOString()
  })
}

const reviewAnswers = () => {
  showDetailedResults.value = !showDetailedResults.value
}

const isAnswerCorrect = (questionIndex) => {
  const answer = quizAnswers.value[questionIndex]
  return answer ? answer.is_correct : false
}

const formatUserAnswer = (questionIndex) => {
  const answer = quizAnswers.value[questionIndex]
  if (!answer) return 'Nessuna risposta'
  
  const question = quizData.value.questions[questionIndex]
  
  if (question.type === 'single_choice' || question.type === 'true_false') {
    return question.options[answer.answer] || 'N/A'
  } else if (question.type === 'multiple_choice') {
    if (Array.isArray(answer.answer)) {
      return answer.answer.map(index => question.options[index]).join(', ')
    }
    return 'N/A'
  } else {
    return answer.answer || 'N/A'
  }
}

const formatCorrectAnswer = (questionIndex) => {
  const question = quizData.value.questions[questionIndex]
  const correctAnswer = question.correct_answer

  if (question.type === 'single_choice' || question.type === 'true_false') {
    return question.options[correctAnswer] || 'N/A'
  } else if (question.type === 'multiple_choice') {
    if (Array.isArray(correctAnswer)) {
      return correctAnswer.map(index => question.options[index]).join(', ')
    }
    return 'N/A'
  } else {
    return correctAnswer || 'N/A'
  }
}

// Lifecycle
onMounted(() => {
  loadQuiz()
})

onUnmounted(() => {
  if (timerInterval.value) {
    clearInterval(timerInterval.value)
  }
})
</script>

<style scoped>
.quiz-lesson {
  @apply w-full max-w-4xl mx-auto;
}

/* Custom radio button styling */
input[type="radio"]:checked + div {
  @apply border-cdf-teal bg-cdf-teal/10;
}

/* Custom checkbox styling */
input[type="checkbox"]:checked + div {
  @apply border-cdf-teal bg-cdf-teal;
}

/* Animation for progress bar */
.progress-bar {
  transition: width 0.5s ease-in-out;
}

/* Hover effects for interactive elements */
.interactive:hover {
  @apply transform scale-105 transition-transform duration-200;
}
</style>
