<template>
  <div class="min-h-screen bg-cdf-sand">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-cdf-slate200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-6">
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
              <h1 class="text-2xl font-bold text-cdf-deep">Impostazioni Sistema</h1>
              <p class="text-cdf-slate700 mt-1">Configura le impostazioni della piattaforma</p>
            </div>
          </div>
          <div class="flex gap-3">
            <button
              @click="saveSettings"
              :disabled="loading"
              class="bg-cdf-teal text-white px-4 py-2 rounded-xl font-semibold hover:bg-cdf-deep transition-all disabled:opacity-50"
            >
              💾 Salva Impostazioni
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Settings Navigation -->
        <div class="lg:col-span-1">
          <div class="bg-white rounded-xl shadow-sm border border-cdf-slate200 p-6">
            <h3 class="text-lg font-semibold text-cdf-deep mb-4">Categorie</h3>
            <nav class="space-y-2">
              <button
                v-for="category in categories"
                :key="category.id"
                @click="activeCategory = category.id"
                class="w-full text-left px-3 py-2 rounded-lg transition-colors"
                :class="activeCategory === category.id 
                  ? 'bg-cdf-teal text-white' 
                  : 'text-cdf-slate700 hover:bg-cdf-slate100'"
              >
                <div class="flex items-center">
                  <component :is="category.icon" class="w-5 h-5 mr-3" />
                  {{ category.name }}
                </div>
              </button>
            </nav>
          </div>
        </div>

        <!-- Settings Content -->
        <div class="lg:col-span-2">
          <div class="bg-white rounded-xl shadow-sm border border-cdf-slate200 p-6">
            <!-- General Settings -->
            <div v-if="activeCategory === 'general'">
              <h3 class="text-lg font-semibold text-cdf-deep mb-6">Impostazioni Generali</h3>
              <div class="space-y-6">
                <div>
                  <label class="block text-sm font-medium text-cdf-deep mb-2">
                    Nome Piattaforma
                  </label>
                  <input
                    v-model="settings.general.platform_name"
                    type="text"
                    class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-cdf-deep mb-2">
                    Email Amministratore
                  </label>
                  <input
                    v-model="settings.general.admin_email"
                    type="email"
                    class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-cdf-deep mb-2">
                    URL Piattaforma
                  </label>
                  <input
                    v-model="settings.general.platform_url"
                    type="url"
                    class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-cdf-deep mb-2">
                    Timezone
                  </label>
                  <select
                    v-model="settings.general.timezone"
                    class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
                  >
                    <option value="Europe/Rome">Europe/Rome</option>
                    <option value="Europe/London">Europe/London</option>
                    <option value="America/New_York">America/New_York</option>
                    <option value="UTC">UTC</option>
                  </select>
                </div>
              </div>
            </div>

            <!-- Email Settings -->
            <div v-if="activeCategory === 'email'">
              <h3 class="text-lg font-semibold text-cdf-deep mb-6">Impostazioni Email</h3>
              <div class="space-y-6">
                <div>
                  <label class="block text-sm font-medium text-cdf-deep mb-2">
                    SMTP Host
                  </label>
                  <input
                    v-model="settings.email.smtp_host"
                    type="text"
                    class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-cdf-deep mb-2">
                    SMTP Port
                  </label>
                  <input
                    v-model="settings.email.smtp_port"
                    type="number"
                    class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-cdf-deep mb-2">
                    SMTP Username
                  </label>
                  <input
                    v-model="settings.email.smtp_username"
                    type="text"
                    class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-cdf-deep mb-2">
                    SMTP Password
                  </label>
                  <input
                    v-model="settings.email.smtp_password"
                    type="password"
                    class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
                  />
                </div>
                <div class="flex items-center">
                  <input
                    v-model="settings.email.smtp_encryption"
                    type="checkbox"
                    class="h-4 w-4 text-cdf-teal focus:ring-cdf-teal border-cdf-slate300 rounded"
                  />
                  <label class="ml-2 block text-sm text-cdf-deep">
                    Usa crittografia SSL/TLS
                  </label>
                </div>
              </div>
            </div>

            <!-- Security Settings -->
            <div v-if="activeCategory === 'security'">
              <h3 class="text-lg font-semibold text-cdf-deep mb-6">Impostazioni Sicurezza</h3>
              <div class="space-y-6">
                <div>
                  <label class="block text-sm font-medium text-cdf-deep mb-2">
                    Durata Sessione (minuti)
                  </label>
                  <input
                    v-model.number="settings.security.session_timeout"
                    type="number"
                    min="5"
                    max="1440"
                    class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-cdf-deep mb-2">
                    Tentativi Login Massimi
                  </label>
                  <input
                    v-model.number="settings.security.max_login_attempts"
                    type="number"
                    min="3"
                    max="10"
                    class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-cdf-deep mb-2">
                    Durata Blocco Account (minuti)
                  </label>
                  <input
                    v-model.number="settings.security.lockout_duration"
                    type="number"
                    min="5"
                    max="60"
                    class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
                  />
                </div>
                <div class="flex items-center">
                  <input
                    v-model="settings.security.require_2fa"
                    type="checkbox"
                    class="h-4 w-4 text-cdf-teal focus:ring-cdf-teal border-cdf-slate300 rounded"
                  />
                  <label class="ml-2 block text-sm text-cdf-deep">
                    Richiedi autenticazione a due fattori
                  </label>
                </div>
                <div class="flex items-center">
                  <input
                    v-model="settings.security.password_policy"
                    type="checkbox"
                    class="h-4 w-4 text-cdf-teal focus:ring-cdf-teal border-cdf-slate300 rounded"
                  />
                  <label class="ml-2 block text-sm text-cdf-deep">
                    Applica politica password forte
                  </label>
                </div>
              </div>
            </div>

            <!-- Course Settings -->
            <div v-if="activeCategory === 'courses'">
              <h3 class="text-lg font-semibold text-cdf-deep mb-6">Impostazioni Corsi</h3>
              <div class="space-y-6">
                <div>
                  <label class="block text-sm font-medium text-cdf-deep mb-2">
                    Durata Massima Video (minuti)
                  </label>
                  <input
                    v-model.number="settings.courses.max_video_duration"
                    type="number"
                    min="1"
                    max="180"
                    class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-cdf-deep mb-2">
                    Dimensione Massima File (MB)
                  </label>
                  <input
                    v-model.number="settings.courses.max_file_size"
                    type="number"
                    min="1"
                    max="500"
                    class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
                  />
                </div>
                <div class="flex items-center">
                  <input
                    v-model="settings.courses.auto_approve"
                    type="checkbox"
                    class="h-4 w-4 text-cdf-teal focus:ring-cdf-teal border-cdf-slate300 rounded"
                  />
                  <label class="ml-2 block text-sm text-cdf-deep">
                    Approvazione automatica nuovi corsi
                  </label>
                </div>
                <div class="flex items-center">
                  <input
                    v-model="settings.courses.allow_downloads"
                    type="checkbox"
                    class="h-4 w-4 text-cdf-teal focus:ring-cdf-teal border-cdf-slate300 rounded"
                  />
                  <label class="ml-2 block text-sm text-cdf-deep">
                    Permetti download materiali
                  </label>
                </div>
              </div>
            </div>

            <!-- Notification Settings -->
            <div v-if="activeCategory === 'notifications'">
              <h3 class="text-lg font-semibold text-cdf-deep mb-6">Impostazioni Notifiche</h3>
              <div class="space-y-6">
                <div class="flex items-center">
                  <input
                    v-model="settings.notifications.email_enabled"
                    type="checkbox"
                    class="h-4 w-4 text-cdf-teal focus:ring-cdf-teal border-cdf-slate300 rounded"
                  />
                  <label class="ml-2 block text-sm text-cdf-deep">
                    Abilita notifiche email
                  </label>
                </div>
                <div class="flex items-center">
                  <input
                    v-model="settings.notifications.push_enabled"
                    type="checkbox"
                    class="h-4 w-4 text-cdf-teal focus:ring-cdf-teal border-cdf-slate300 rounded"
                  />
                  <label class="ml-2 block text-sm text-cdf-deep">
                    Abilita notifiche push
                  </label>
                </div>
                <div class="flex items-center">
                  <input
                    v-model="settings.notifications.course_reminders"
                    type="checkbox"
                    class="h-4 w-4 text-cdf-teal focus:ring-cdf-teal border-cdf-slate300 rounded"
                  />
                  <label class="ml-2 block text-sm text-cdf-deep">
                    Invia promemoria corsi
                  </label>
                </div>
                <div class="flex items-center">
                  <input
                    v-model="settings.notifications.achievement_notifications"
                    type="checkbox"
                    class="h-4 w-4 text-cdf-teal focus:ring-cdf-teal border-cdf-slate300 rounded"
                  />
                  <label class="ml-2 block text-sm text-cdf-deep">
                    Notifica conseguimenti
                  </label>
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
import { ref, reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

// State
const loading = ref(false)
const activeCategory = ref('general')

const categories = [
  { id: 'general', name: 'Generali', icon: 'CogIcon' },
  { id: 'email', name: 'Email', icon: 'MailIcon' },
  { id: 'security', name: 'Sicurezza', icon: 'ShieldCheckIcon' },
  { id: 'courses', name: 'Corsi', icon: 'BookOpenIcon' },
  { id: 'notifications', name: 'Notifiche', icon: 'BellIcon' }
]

const settings = reactive({
  general: {
    platform_name: 'Campus Digitale FAD',
    admin_email: 'admin@campusdigitale.it',
    platform_url: 'https://campusdigitale.it',
    timezone: 'Europe/Rome'
  },
  email: {
    smtp_host: '',
    smtp_port: 587,
    smtp_username: '',
    smtp_password: '',
    smtp_encryption: true
  },
  security: {
    session_timeout: 120,
    max_login_attempts: 5,
    lockout_duration: 15,
    require_2fa: false,
    password_policy: true
  },
  courses: {
    max_video_duration: 60,
    max_file_size: 100,
    auto_approve: false,
    allow_downloads: true
  },
  notifications: {
    email_enabled: true,
    push_enabled: false,
    course_reminders: true,
    achievement_notifications: true
  }
})

// Methods
const goToDashboard = () => {
  router.push('/admin')
}

const saveSettings = async () => {
  loading.value = true
  try {
    // Here you would typically save to backend
    console.log('Saving settings:', settings)
    alert('Impostazioni salvate con successo!')
  } catch (error) {
    console.error('Errore nel salvataggio:', error)
    alert('Errore nel salvataggio delle impostazioni')
  } finally {
    loading.value = false
  }
}

// Lifecycle
onMounted(() => {
  // Load settings from backend
})
</script>
