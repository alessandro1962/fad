<template>
  <AppLayout>
    <div class="min-h-screen bg-gray-50 py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Course Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
          <div class="flex items-start justify-between mb-6">
            <div class="flex-1 mr-6">
              <h1 class="text-3xl font-bold text-cdf-deep mb-3">{{ course.title }}</h1>
              <div class="text-cdf-slate600 leading-relaxed">
                <p v-if="!showFullDescription" class="mb-2">{{ cleanDescription(course.description) }}</p>
                <div v-else class="mb-2" v-html="course.description"></div>
                <button 
                  @click="showFullDescription = !showFullDescription"
                  class="text-cdf-teal hover:text-cdf-deep font-medium text-sm transition-colors"
                >
                  {{ showFullDescription ? 'Mostra meno' : 'Leggi tutto' }}
                </button>
              </div>
            </div>
            <div class="text-right flex-shrink-0">
              <div class="text-2xl font-bold text-cdf-teal">{{ Math.round(overallProgress) }}%</div>
              <div class="text-sm text-cdf-slate600">Completato</div>
            </div>
          </div>
        </div>

        <!-- Course Completion Modal -->
        <CompletionModal 
          v-if="showCompletionModal"
          :course="course"
          @close="showCompletionModal = false"
          @go-to-dashboard="goToDashboard"
        />

        <!-- Course Completed Message -->
        <div v-if="courseCompleted" class="bg-green-50 border border-green-200 rounded-2xl p-6 mb-6">
          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center mr-4">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div>
                <h3 class="text-lg font-semibold text-green-800">🎉 Corso Completato!</h3>
                <p class="text-green-700">Hai completato con successo questo corso. Ora puoi navigare liberamente tra tutti i moduli.</p>
              </div>
            </div>
            <button
              @click="showCompletionModal = true"
              class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
            >
              Vedi Certificato
            </button>
          </div>
        </div>

        <!-- Modules List -->
        <div class="space-y-4">
          <div 
            v-for="(module, moduleIndex) in course.modules" 
            :key="module.id"
            class="bg-white rounded-lg shadow-sm overflow-hidden"
          >
            <!-- Module Header -->
            <div 
              class="p-6 cursor-pointer transition-colors"
              :class="{
                'bg-cdf-teal text-white': currentModuleIndex === moduleIndex,
                'bg-gray-50 hover:bg-gray-100': currentModuleIndex !== moduleIndex && isModuleClickable(moduleIndex),
                'bg-gray-200 text-gray-500 cursor-not-allowed': !isModuleClickable(moduleIndex)
              }"
              @click="openModule(moduleIndex)"
            >
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                  <!-- Module Icon -->
                  <div class="flex-shrink-0">
                    <div 
                      class="w-12 h-12 rounded-full flex items-center justify-center text-xl font-bold"
                      :class="{
                        'bg-white text-cdf-teal': currentModuleIndex === moduleIndex,
                        'bg-cdf-teal text-white': isModuleCompleted(moduleIndex),
                        'bg-gray-400 text-white': !isModuleClickable(moduleIndex) && !isModuleCompleted(moduleIndex),
                        'bg-cdf-deep text-white': isModuleClickable(moduleIndex) && !isModuleCompleted(moduleIndex)
                      }"
                    >
                      <i v-if="isModuleCompleted(moduleIndex)" class="fas fa-check"></i>
                      <i v-else-if="!isModuleClickable(moduleIndex)" class="fas fa-lock"></i>
                      <span v-else>{{ moduleIndex + 1 }}</span>
                    </div>
                  </div>
                  
                  <!-- Module Info -->
                  <div>
                    <h3 class="text-lg font-semibold">{{ module.title }}</h3>
                    <p class="text-sm opacity-90">
                      {{ module.lessons?.length || 0 }} lezioni
                      <span v-if="isModuleCompleted(moduleIndex)" class="ml-2">
                        <i class="fas fa-check-circle"></i> Completato
                      </span>
                    </p>
                  </div>
                </div>
                
                <!-- Progress Bar -->
                <div class="flex-shrink-0 w-32">
                  <div class="w-full bg-gray-200 rounded-full h-2">
                    <div 
                      class="h-2 rounded-full transition-all duration-300"
                      :class="{
                        'bg-white': currentModuleIndex === moduleIndex,
                        'bg-cdf-teal': isModuleCompleted(moduleIndex),
                        'bg-gray-400': !isModuleClickable(moduleIndex) && !isModuleCompleted(moduleIndex),
                        'bg-cdf-deep': isModuleClickable(moduleIndex) && !isModuleCompleted(moduleIndex)
                      }"
                      :style="{ width: getModuleProgress(moduleIndex) + '%' }"
                    ></div>
                  </div>
                  <div class="text-xs mt-1 text-center">
                    {{ Math.round(getModuleProgress(moduleIndex)) }}%
                  </div>
                </div>
              </div>
            </div>

            <!-- Module Content (when selected) -->
            <div v-if="currentModuleIndex === moduleIndex" class="border-t">
              <div class="p-6">
                <!-- Lessons List -->
                <div class="space-y-4">
                  <div 
                    v-for="(lesson, lessonIndex) in module.lessons" 
                    :key="lesson.id"
                    class="flex items-center justify-between p-4 bg-gray-50 rounded-lg"
                  >
                    <div class="flex items-center space-x-3">
                      <div 
                        class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold"
                        :class="{
                          'bg-cdf-teal text-white': lesson.completed || lessonProgress[lesson.id]?.completed,
                          'bg-gray-300 text-gray-600': !(lesson.completed || lessonProgress[lesson.id]?.completed)
                        }"
                      >
                        <i v-if="lesson.completed || lessonProgress[lesson.id]?.completed" class="fas fa-check text-xs"></i>
                        <span v-else>{{ lessonIndex + 1 }}</span>
                      </div>
                      <div>
                        <h4 class="font-medium">{{ lesson.title }}</h4>
                        <p class="text-sm text-gray-600 capitalize">{{ lesson.type }}</p>
                      </div>
                    </div>
                    
                    <div class="flex items-center space-x-2">
                      <span 
                        v-if="lesson.completed || lessonProgress[lesson.id]?.completed"
                        class="text-sm text-cdf-teal font-medium"
                      >
                        <i class="fas fa-check-circle mr-1"></i>Completato
                      </span>
                      <button 
                        v-else
                        class="px-4 py-2 bg-cdf-teal text-white rounded-lg hover:bg-cdf-deep transition-colors"
                        @click="startLesson(lesson, lessonIndex)"
                      >
                        Inizia
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Current Lesson Content -->
                <div v-if="currentLesson" class="mt-6">
                  <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-xl font-bold mb-4">{{ currentLesson.title }}</h3>
                    
                    <!-- Video Lesson -->
                    <VideoPlayer 
                      v-if="currentLesson.type === 'video'"
                      :lesson="currentLesson"
                      :userProgress="getLessonProgress(currentLesson.id)"
                      @lesson-completed="onLessonCompleted"
                    />
                    
                    <!-- Quiz Lesson -->
                    <QuizPlayer 
                      v-else-if="currentLesson.type === 'quiz'"
                      :lesson="currentLesson"
                      @quiz-completed="onQuizCompleted"
                    />
                    
                    <!-- PDF Lesson -->
                    <div v-else-if="currentLesson.type === 'pdf'" class="text-center py-8">
                      <i class="fas fa-file-pdf text-6xl text-red-500 mb-4"></i>
                      <p class="text-gray-600 mb-4">Contenuto PDF</p>
                      <button 
                        class="px-6 py-3 bg-cdf-teal text-white rounded-lg hover:bg-cdf-deep transition-colors"
                        @click="markLessonCompleted"
                      >
                        Marca come completato
                      </button>
                    </div>
                    
                    <!-- Audio Lesson -->
                    <div v-else-if="currentLesson.type === 'audio'" class="text-center py-8">
                      <i class="fas fa-volume-up text-6xl text-cdf-teal mb-4"></i>
                      <p class="text-gray-600 mb-4">Contenuto Audio</p>
                      <button 
                        class="px-6 py-3 bg-cdf-teal text-white rounded-lg hover:bg-cdf-deep transition-colors"
                        @click="markLessonCompleted"
                      >
                        Marca come completato
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import AppLayout from '@/components/Layout/AppLayout.vue'
import VideoPlayer from '@/components/Course/VideoPlayer.vue'
import QuizPlayer from '@/components/Course/QuizPlayer.vue'
import CompletionModal from '@/components/Course/CompletionModal.vue'
import api from '@/api'

const route = useRoute()
const router = useRouter()
const courseId = route.params.id

// Reactive data
const course = ref({})
const loading = ref(true)
const courseCompleted = ref(false)
const showCompletionModal = ref(false)
const currentModuleIndex = ref(0)
const currentLessonIndex = ref(0)
const currentLesson = ref(null)
const lessonProgress = ref({})
const showFullDescription = ref(false)

// Helper function to get lesson progress
const getLessonProgress = (lessonId) => {
  // If lesson is marked as completed locally
  if (lessonProgress.value[lessonId]) {
    return lessonProgress.value[lessonId]
  }
  
  // If course is completed, return completed progress
  if (courseCompleted.value) {
    // Find the lesson to get its type
    let lessonType = 'video'
    course.value.modules.forEach(module => {
      if (module.lessons) {
        const lesson = module.lessons.find(l => l.id === lessonId)
        if (lesson) {
          lessonType = lesson.type
        }
      }
    })
    
    return {
      completed: true,
      completed_at: new Date().toISOString(),
      seconds_watched: lessonType === 'video' ? 100 : 0,
      last_position_sec: lessonType === 'video' ? 100 : 0,
      watched_time: lessonType === 'video' ? 100 : 0,
      total_duration: lessonType === 'video' ? 100 : 0
    }
  }
  
  // Return empty progress
  return {}
}

// Computed
const overallProgress = computed(() => {
  if (!course.value.modules) return 0
  
  let totalLessons = 0
  let completedLessons = 0
  
  course.value.modules.forEach(module => {
    module.lessons?.forEach(lesson => {
      totalLessons++
      if (lesson.completed || lessonProgress.value[lesson.id]?.completed) {
        completedLessons++
      }
    })
  })
  
  return totalLessons > 0 ? (completedLessons / totalLessons) * 100 : 0
})

// Methods
const cleanDescription = (html) => {
  if (!html) return ''
  
  const temp = document.createElement('div')
  temp.innerHTML = html
  
  let text = temp.textContent || temp.innerText || ''
  text = text.replace(/\s+/g, ' ').trim()
  
  if (text.length > 300) {
    text = text.substring(0, 300) + '...'
  }
  
  return text
}

const isModuleCompleted = (moduleIndex) => {
  const module = course.value.modules[moduleIndex]
  if (!module.lessons) return false
  
  return module.lessons.every(lesson => 
    lesson.completed || lessonProgress.value[lesson.id]?.completed
  )
}

const isModuleClickable = (moduleIndex) => {
  // If course is completed, all modules are clickable
  if (courseCompleted.value) return true
  
  // First module is always clickable
  if (moduleIndex === 0) return true
  
  // Other modules are clickable only if previous module is completed
  return isModuleCompleted(moduleIndex - 1)
}

const getModuleProgress = (moduleIndex) => {
  const module = course.value.modules[moduleIndex]
  if (!module.lessons) return 0
  
  let completed = 0
  module.lessons.forEach(lesson => {
    if (lesson.completed || lessonProgress.value[lesson.id]?.completed) {
      completed++
    }
  })
  
  return module.lessons.length > 0 ? (completed / module.lessons.length) * 100 : 0
}

const openModule = (moduleIndex) => {
  if (!isModuleClickable(moduleIndex)) return
  
  currentModuleIndex.value = moduleIndex
  currentLessonIndex.value = 0
  
  const module = course.value.modules[moduleIndex]
  if (module.lessons && module.lessons.length > 0) {
    currentLesson.value = module.lessons[0]
  }
}

const startLesson = (lesson, lessonIndex) => {
  console.log('🚀 startLesson called:', { lesson: lesson.title, lessonIndex, lessonId: lesson.id })
  
  // If course is completed, allow free navigation
  if (courseCompleted.value) {
    console.log('✅ Course completed, allowing free navigation')
    currentLessonIndex.value = lessonIndex
    currentLesson.value = lesson
    return
  }
  
  // Normal sequential navigation
  console.log('📚 Setting current lesson:', lesson.title)
  currentLessonIndex.value = lessonIndex
  currentLesson.value = lesson
}

const markLessonCompleted = () => {
  if (currentLesson.value) {
    currentLesson.value.completed = true
    lessonProgress.value[currentLesson.value.id] = {
      completed: true,
      completed_at: new Date().toISOString()
    }
    
    // Auto-advance to next lesson
    setTimeout(() => {
      advanceToNextLesson()
    }, 1000)
  }
}

const onLessonCompleted = async (lesson) => {
  console.log('🎉 LESSON COMPLETED EVENT RECEIVED:', lesson)
  
  lesson.completed = true
  lessonProgress.value[lesson.id] = {
    completed: true,
    completed_at: new Date().toISOString()
  }
  
  console.log('📊 Updated lesson progress:', lessonProgress.value)
  
  // Save progress to server
  try {
    console.log('💾 Saving progress to server for lesson:', lesson.id)
    const response = await api.patch(`/v1/progress/lesson/${lesson.id}`, {
      completed: true,
      completed_at: new Date().toISOString()
    })
    console.log('✅ Progress saved to server for lesson:', lesson.id, 'Response:', response.data)
  } catch (error) {
    console.error('❌ Error saving progress:', error)
    console.error('❌ Error details:', error.response?.data)
  }
  
  // Auto-advance to next lesson
  setTimeout(() => {
    console.log('⏭️ Advancing to next lesson...')
    advanceToNextLesson()
  }, 1500)
}

const onQuizCompleted = async (result) => {
  console.log('🎉 QUIZ COMPLETED EVENT RECEIVED:', result)
  
  if (result.lesson) {
    result.lesson.completed = true
  }
  if (currentLesson.value) {
    currentLesson.value.completed = true
  }
  
  lessonProgress.value[currentLesson.value.id] = {
    completed: true,
    completed_at: new Date().toISOString()
  }
  
  console.log('📊 Updated lesson progress:', lessonProgress.value)
  
  // Save progress to server
  try {
    console.log('💾 Saving quiz progress to server for lesson:', currentLesson.value.id)
    const response = await api.patch(`/v1/progress/lesson/${currentLesson.value.id}`, {
      completed: true,
      completed_at: new Date().toISOString()
    })
    console.log('✅ Quiz progress saved to server for lesson:', currentLesson.value.id, 'Response:', response.data)
  } catch (error) {
    console.error('❌ Error saving quiz progress:', error)
    console.error('❌ Error details:', error.response?.data)
  }
  
  // Auto-advance to next lesson
  setTimeout(() => {
    console.log('⏭️ Advancing to next lesson...')
    advanceToNextLesson()
  }, 1500)
}

const advanceToNextLesson = () => {
  console.log('🚀 ADVANCE TO NEXT LESSON CALLED')
  console.log('Current module index:', currentModuleIndex.value)
  console.log('Current lesson index:', currentLessonIndex.value)
  
  const currentModule = course.value.modules[currentModuleIndex.value]
  console.log('Current module:', currentModule.title, 'Lessons:', currentModule.lessons.length)
  
  // Check if there's a next lesson in current module
  if (currentLessonIndex.value + 1 < currentModule.lessons.length) {
    currentLessonIndex.value++
    currentLesson.value = currentModule.lessons[currentLessonIndex.value]
    console.log('✅ Moving to next lesson in same module:', currentLesson.value.title)
    return
  }
  
  // Move to next module
  const nextModuleIndex = currentModuleIndex.value + 1
  console.log('Moving to next module:', nextModuleIndex, 'of', course.value.modules.length)
  
  if (nextModuleIndex < course.value.modules.length) {
    const nextModule = course.value.modules[nextModuleIndex]
    console.log('Next module:', nextModule.title, 'Lessons:', nextModule.lessons?.length)
    
    if (nextModule.lessons && nextModule.lessons.length > 0) {
      currentModuleIndex.value = nextModuleIndex
      currentLessonIndex.value = 0
      currentLesson.value = nextModule.lessons[0]
      console.log('✅ Moving to first lesson of next module:', currentLesson.value.title)
      return
    }
  }
  
  // No more lessons, complete the course
  console.log('🎓 No more lessons, completing course')
  courseCompleted.value = true
  showCompletionModal.value = true
}

const loadCourse = async () => {
  try {
    loading.value = true
    
    // Load course data
    const response = await api.get(`/v1/courses/${courseId}`)
    course.value = response.data.data
    
    // Sort modules by order
    if (course.value.modules) {
      course.value.modules.sort((a, b) => (a.order || 0) - (b.order || 0))
    }
    
    // Load user progress
    await loadUserProgress()
    
    // Find first incomplete lesson (or first module if completed)
    findFirstIncompleteLesson()
    
  } catch (error) {
    console.error('Error loading course:', error)
  } finally {
    loading.value = false
  }
}

const loadUserProgress = async () => {
  try {
    console.log('🚀 DEBUG: loadUserProgress called for course:', courseId)
    console.log('📥 LOADING USER PROGRESS for course:', courseId)
    const response = await api.get(`/v1/progress/course/${courseId}`)
    console.log('📥 Progress API response:', response.data)
    const progressData = response.data.data || {}
    
    console.log('Progress data from API:', progressData)
    
    // Check if this is a course summary (with course_progress_percentage)
    if (progressData.course_progress_percentage !== undefined) {
      console.log('📊 Course summary received:', {
        course_id: progressData.course_id,
        total_lessons: progressData.total_lessons,
        completed_lessons: progressData.completed_lessons,
        progress_percentage: progressData.course_progress_percentage
      })
      
      // Process individual lesson progress from the API response
      if (progressData.lessons && Array.isArray(progressData.lessons)) {
        console.log('📚 Processing individual lesson progress:', progressData.lessons.length, 'lessons')
        progressData.lessons.forEach(lessonProgressItem => {
          lessonProgress.value[lessonProgressItem.lesson_id] = {
            completed: lessonProgressItem.completed,
            completed_at: lessonProgressItem.completed ? new Date().toISOString() : null,
            seconds_watched: lessonProgressItem.seconds_watched || 0,
            last_position_sec: lessonProgressItem.seconds_watched || 0,
            watched_time: lessonProgressItem.seconds_watched || 0,
            total_duration: lessonProgressItem.duration_minutes * 60 || 0,
            progress_percentage: lessonProgressItem.progress_percentage || 0
          }
          console.log(`Set progress for lesson ${lessonProgressItem.lesson_id} (${lessonProgressItem.lesson_title}): completed=${lessonProgressItem.completed}`)
        })
      }
      
      // If course is 100% completed, mark course as completed
      if (progressData.course_progress_percentage === 100) {
        console.log('🎓 Course is 100% completed')
        courseCompleted.value = true
      }
    } else {
      // Handle individual lesson progress data
      if (Array.isArray(progressData)) {
        progressData.forEach(progress => {
          lessonProgress.value[progress.lesson_id] = progress
          console.log(`Set progress for lesson ${progress.lesson_id}:`, progress.completed)
        })
      } else if (typeof progressData === 'object') {
        // If it's an object, iterate through its values
        Object.values(progressData).forEach(progress => {
          if (progress && progress.lesson_id) {
            lessonProgress.value[progress.lesson_id] = progress
            console.log(`Set progress for lesson ${progress.lesson_id}:`, progress.completed)
          }
        })
      }
    }
    
    console.log('Final lesson progress:', lessonProgress.value)
    
    // Check if course is completed
    checkCourseCompletion()
    
  } catch (error) {
    console.error('Error loading user progress:', error)
  }
}

const findFirstIncompleteLesson = () => {
  console.log('🔍 FINDING FIRST INCOMPLETE LESSON')
  console.log('Course modules:', course.value.modules.length)
  console.log('Lesson progress:', lessonProgress.value)
  console.log('Course completed:', courseCompleted.value)
  
  // If course is completed, show first module
  if (courseCompleted.value) {
    console.log('✅ Course is completed, showing first module')
    if (course.value.modules && course.value.modules.length > 0) {
      currentModuleIndex.value = 0
      currentLessonIndex.value = 0
      if (course.value.modules[0].lessons && course.value.modules[0].lessons.length > 0) {
        currentLesson.value = course.value.modules[0].lessons[0]
      }
    }
    return
  }
  
  for (let moduleIndex = 0; moduleIndex < course.value.modules.length; moduleIndex++) {
    const module = course.value.modules[moduleIndex]
    console.log(`Checking module ${moduleIndex}:`, module.title)
    
    if (module.lessons && module.lessons.length > 0) {
      for (let lessonIndex = 0; lessonIndex < module.lessons.length; lessonIndex++) {
        const lesson = module.lessons[lessonIndex]
        const isCompleted = lesson.completed || lessonProgress.value[lesson.id]?.completed
        
        console.log(`  Lesson ${lessonIndex}:`, lesson.title, 'Completed:', isCompleted)
        
        if (!isCompleted) {
          console.log('✅ Found incomplete lesson:', lesson.title)
          currentModuleIndex.value = moduleIndex
          currentLessonIndex.value = lessonIndex
          currentLesson.value = lesson
          return
        }
      }
    }
  }
  
  // All lessons completed
  console.log('🎓 All lessons completed')
  courseCompleted.value = true
  showCompletionModal.value = true
}

const checkCourseCompletion = () => {
  if (!course.value.modules) return
  
  let allLessonsCompleted = true
  
  course.value.modules.forEach(module => {
    if (module.lessons) {
      module.lessons.forEach(lesson => {
        const progress = lessonProgress.value[lesson.id]
        if (!progress || !progress.completed) {
          allLessonsCompleted = false
        }
      })
    }
  })
  
  if (allLessonsCompleted && !courseCompleted.value) {
    console.log('🎓 Course is already completed')
    courseCompleted.value = true
    // Don't show modal automatically, let user navigate freely
    // Set to first module so user can see all modules
    findFirstIncompleteLesson()
  }
}

const goToDashboard = () => {
  router.push('/dashboard')
}

// Lifecycle
onMounted(() => {
  loadCourse()
})
</script>