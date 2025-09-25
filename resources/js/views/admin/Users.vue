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
              <h1 class="text-2xl font-bold text-cdf-deep">Gestione Utenti</h1>
              <p class="text-cdf-slate700 mt-1">Gestisci gli utenti della piattaforma</p>
            </div>
          </div>
          <div class="flex gap-3">
            <button
              @click="downloadTemplate"
              class="bg-cdf-slate600 text-white px-4 py-2 rounded-xl font-semibold hover:bg-cdf-slate700 transition-all"
            >
              📥 Template CSV
            </button>
            <button
              @click="showGoogleImportModal = true"
              class="bg-blue-600 text-white px-4 py-2 rounded-xl font-semibold hover:bg-blue-700 transition-all"
            >
              🔐 Importa Google OAuth
            </button>
            <button
              @click="showImportModal = true"
              class="bg-cdf-teal text-white px-4 py-2 rounded-xl font-semibold hover:bg-cdf-deep transition-all"
            >
              📤 Importa CSV
            </button>
            <button
              @click="showCreateModal = true"
              class="bg-cdf-amber text-cdf-ink px-4 py-2 rounded-xl font-semibold hover:brightness-95 transition-all"
            >
              + Nuovo Utente
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <div class="bg-white rounded-xl shadow-sm border border-cdf-slate200 p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <!-- Search -->
          <div>
            <label class="block text-sm font-medium text-cdf-deep mb-2">Cerca</label>
            <input
              v-model="filters.search"
              type="text"
              placeholder="Nome, email..."
              class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
              @input="debouncedSearch"
            />
          </div>

          <!-- Admin Filter -->
          <div>
            <label class="block text-sm font-medium text-cdf-deep mb-2">Tipo Utente</label>
            <select
              v-model="filters.is_admin"
              class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
              @change="loadUsers"
            >
              <option value="">Tutti</option>
              <option value="true">Solo Admin</option>
              <option value="false">Solo Utenti</option>
            </select>
          </div>

          <!-- Email Verified Filter -->
          <div>
            <label class="block text-sm font-medium text-cdf-deep mb-2">Email Verificata</label>
            <select
              v-model="filters.email_verified"
              class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
              @change="loadUsers"
            >
              <option value="">Tutti</option>
              <option value="true">Verificati</option>
              <option value="false">Non Verificati</option>
            </select>
          </div>

          <!-- Sort -->
          <div>
            <label class="block text-sm font-medium text-cdf-deep mb-2">Ordina per</label>
            <select
              v-model="filters.sort_by"
              class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
              @change="loadUsers"
            >
              <option value="created_at">Data Registrazione</option>
              <option value="name">Nome</option>
              <option value="email">Email</option>
              <option value="last_login_at">Ultimo Accesso</option>
            </select>
          </div>
        </div>
        
        <!-- Additional Filters -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
          <!-- Per Page -->
          <div>
            <label class="block text-sm font-medium text-cdf-deep mb-2">Elementi per pagina</label>
            <select
              v-model="pagination.per_page"
              class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
              @change="loadUsers(1)"
            >
              <option value="10">10</option>
              <option value="15">15</option>
              <option value="25">25</option>
              <option value="50">50</option>
              <option value="100">100</option>
            </select>
          </div>
          
          <!-- Sort Order -->
          <div>
            <label class="block text-sm font-medium text-cdf-deep mb-2">Ordine</label>
            <select
              v-model="filters.sort_order"
              class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
              @change="loadUsers"
            >
              <option value="desc">Decrescente</option>
              <option value="asc">Crescente</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Statistics and Info -->
      <div class="flex justify-between items-center mb-6">
        <div class="text-sm text-cdf-slate600">
          Mostrando {{ users.length }} di {{ pagination.total }} utenti
        </div>
        <div class="text-sm text-cdf-slate600">
          Pagina {{ pagination.current_page }} di {{ pagination.last_page }}
        </div>
      </div>
      
      <!-- Statistics -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-cdf-slate200 p-6">
          <div class="flex items-center">
            <div class="p-2 bg-cdf-teal/10 rounded-lg">
              <svg class="w-6 h-6 text-cdf-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-cdf-slate600">Totale Utenti</p>
              <p class="text-2xl font-bold text-cdf-deep">{{ statistics.total_users || 0 }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-cdf-slate200 p-6">
          <div class="flex items-center">
            <div class="p-2 bg-cdf-amber/10 rounded-lg">
              <svg class="w-6 h-6 text-cdf-amber" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-cdf-slate600">Admin</p>
              <p class="text-2xl font-bold text-cdf-deep">{{ statistics.admin_users || 0 }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-cdf-slate200 p-6">
          <div class="flex items-center">
            <div class="p-2 bg-green-100 rounded-lg">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-cdf-slate600">Email Verificate</p>
              <p class="text-2xl font-bold text-cdf-deep">{{ statistics.verified_users || 0 }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-cdf-slate200 p-6">
          <div class="flex items-center">
            <div class="p-2 bg-blue-100 rounded-lg">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-cdf-slate600">Ultimi 30 giorni</p>
              <p class="text-2xl font-bold text-cdf-deep">{{ statistics.recent_users || 0 }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Users Table -->
      <div class="bg-white rounded-xl shadow-sm border border-cdf-slate200">
        <div class="p-6">
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="border-b border-cdf-slate200">
                  <th class="text-left py-3 px-4 font-semibold text-cdf-deep">Utente</th>
                  <th class="text-left py-3 px-4 font-semibold text-cdf-deep">Email</th>
                  <th class="text-left py-3 px-4 font-semibold text-cdf-deep">Tipo</th>
                  <th class="text-left py-3 px-4 font-semibold text-cdf-deep">Registrato</th>
                  <th class="text-left py-3 px-4 font-semibold text-cdf-deep">Ultimo Accesso</th>
                  <th class="text-left py-3 px-4 font-semibold text-cdf-deep">Corsi</th>
                  <th class="text-right py-3 px-4 font-semibold text-cdf-deep">Azioni</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="loading" class="border-b border-cdf-slate200">
                  <td colspan="7" class="py-8 text-center text-cdf-slate600">
                    <div class="flex items-center justify-center">
                      <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-cdf-teal" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                      </svg>
                      Caricamento...
                    </div>
                  </td>
                </tr>
                <tr v-else-if="users.length === 0" class="border-b border-cdf-slate200">
                  <td colspan="7" class="py-8 text-center text-cdf-slate600">
                    Nessun utente trovato
                  </td>
                </tr>
                <tr v-else v-for="user in users" :key="user.id" class="border-b border-cdf-slate200 hover:bg-cdf-slate50">
                  <td class="py-4 px-4">
                    <div class="flex items-center">
                      <div class="w-10 h-10 bg-cdf-teal/10 rounded-full flex items-center justify-center">
                        <span class="text-cdf-teal font-semibold text-sm">
                          {{ user.first_name?.[0] || user.name?.[0] || 'U' }}
                        </span>
                      </div>
                      <div class="ml-3">
                        <p class="font-medium text-cdf-deep">{{ user.name }}</p>
                        <p class="text-sm text-cdf-slate600">{{ user.company || 'Nessuna azienda' }}</p>
                      </div>
                    </div>
                  </td>
                  <td class="py-4 px-4">
                    <div>
                      <p class="text-cdf-deep">{{ user.email }}</p>
                      <div class="flex items-center mt-1">
                        <span v-if="user.email_verified_at" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                          <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                          </svg>
                          Verificato
                        </span>
                        <span v-else class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                          <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                          </svg>
                          Non verificato
                        </span>
                      </div>
                    </div>
                  </td>
                  <td class="py-4 px-4">
                    <span v-if="user.is_admin" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-cdf-amber/10 text-cdf-amber">
                      <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                      </svg>
                      Admin
                    </span>
                    <span v-else class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-cdf-slate100 text-cdf-slate700">
                      Utente
                    </span>
                  </td>
                  <td class="py-4 px-4 text-cdf-slate600">
                    {{ formatDate(user.created_at) }}
                  </td>
                  <td class="py-4 px-4 text-cdf-slate600">
                    {{ user.last_login_at ? formatDate(user.last_login_at) : 'Mai' }}
                  </td>
                  <td class="py-4 px-4">
                    <span class="text-cdf-deep font-medium">{{ user.enrollments_count || 0 }}</span>
                  </td>
                  <td class="py-4 px-4">
                    <div class="flex items-center justify-end gap-2">
                      <button
                        @click="enrollUser(user)"
                        class="p-2 text-cdf-teal hover:text-cdf-deep hover:bg-cdf-teal/10 rounded-lg transition-colors"
                        title="Iscrivi a Corso"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                      </button>
                      <button
                        @click="viewUserEnrollments(user)"
                        class="p-2 text-cdf-blue hover:text-cdf-deep hover:bg-cdf-blue/10 rounded-lg transition-colors"
                        title="Visualizza Iscrizioni"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                      </button>
                      <button
                        @click="editUser(user)"
                        class="p-2 text-cdf-slate600 hover:text-cdf-deep hover:bg-cdf-slate100 rounded-lg transition-colors"
                        title="Modifica Utente"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                      </button>
                      <button
                        @click="toggleAdminStatus(user)"
                        class="p-2 text-cdf-slate600 hover:text-cdf-deep hover:bg-cdf-slate100 rounded-lg transition-colors"
                        :title="user.is_admin ? 'Rimuovi Admin' : 'Rendi Admin'"
                      >
                        <svg v-if="user.is_admin" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                        </svg>
                        <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                      </button>
                      <button
                        @click="deleteUser(user)"
                        class="p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors"
                        title="Elimina Utente"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div v-if="pagination.last_page > 1" class="flex items-center justify-between mt-6">
            <div class="text-sm text-cdf-slate600">
              Mostrando {{ ((pagination.current_page - 1) * pagination.per_page) + 1 }} a 
              {{ Math.min(pagination.current_page * pagination.per_page, pagination.total) }} di 
              {{ pagination.total }} risultati
            </div>
            <div class="flex gap-2">
              <button
                @click="loadUsers(pagination.current_page - 1)"
                :disabled="!pagination.prev"
                class="px-3 py-2 text-sm border border-cdf-slate200 rounded-lg hover:bg-cdf-slate50 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Precedente
              </button>
              <button
                v-for="page in visiblePages"
                :key="page"
                @click="loadUsers(page)"
                :class="[
                  'px-3 py-2 text-sm border rounded-lg',
                  page === pagination.current_page
                    ? 'bg-cdf-teal text-white border-cdf-teal'
                    : 'border-cdf-slate200 hover:bg-cdf-slate50'
                ]"
              >
                {{ page }}
              </button>
              <button
                @click="loadUsers(pagination.current_page + 1)"
                :disabled="!pagination.next"
                class="px-3 py-2 text-sm border border-cdf-slate200 rounded-lg hover:bg-cdf-slate50 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Successivo
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- User Modal -->
    <UserModal
      v-if="showUserModal"
      :is-open="showUserModal"
      :user="selectedUser"
      :is-edit="isEdit"
      @close="closeModal"
      @saved="onUserSaved"
    />

    <!-- Import CSV Modal -->
    <div v-if="showImportModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-xl shadow-xl max-w-md w-full">
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-cdf-slate200">
          <h2 class="text-xl font-bold text-cdf-deep">Importa Utenti da CSV</h2>
          <button
            @click="showImportModal = false"
            class="p-2 text-cdf-slate400 hover:text-cdf-slate600 hover:bg-cdf-slate200 rounded-lg transition-all"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <!-- Content -->
        <div class="p-6">
          <div class="mb-4">
            <p class="text-cdf-slate600 mb-4">
              Seleziona un file CSV per importare gli utenti. Assicurati che il file segua il formato del template.
            </p>
            
            <div class="border-2 border-dashed border-cdf-slate300 rounded-lg p-6 text-center">
              <input
                ref="fileInput"
                type="file"
                accept=".csv,.txt"
                @change="handleFileSelect"
                class="hidden"
              />
              <div v-if="!selectedFile" @click="$refs.fileInput.click()" class="cursor-pointer">
                <svg class="mx-auto h-12 w-12 text-cdf-slate400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                  <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <p class="mt-2 text-sm text-cdf-slate600">
                  <span class="font-medium text-cdf-teal">Clicca per selezionare</span> o trascina il file CSV qui
                </p>
                <p class="text-xs text-cdf-slate500">File supportati: CSV, TXT (max 10MB)</p>
              </div>
              <div v-else class="text-left">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-sm font-medium text-cdf-deep">{{ selectedFile.name }}</p>
                    <p class="text-xs text-cdf-slate500">{{ formatFileSize(selectedFile.size) }}</p>
                  </div>
                  <button
                    @click="selectedFile = null"
                    class="text-red-600 hover:text-red-700"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Import Results -->
          <div v-if="importResults" class="mb-4 p-4 rounded-lg" :class="importResults.success ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200'">
            <h3 class="font-semibold mb-2" :class="importResults.success ? 'text-green-800' : 'text-red-800'">
              {{ importResults.success ? 'Import Completato!' : 'Errore nell\'Import' }}
            </h3>
            <div class="text-sm" :class="importResults.success ? 'text-green-700' : 'text-red-700'">
              <p v-if="importResults.data">
                ✅ Importati: {{ importResults.data.imported_count }} utenti<br>
                ⚠️ Saltati: {{ importResults.data.skipped_count }} utenti
              </p>
              <div v-if="importResults.data?.errors?.length" class="mt-2">
                <p class="font-medium">Errori:</p>
                <ul class="list-disc list-inside text-xs">
                  <li v-for="error in importResults.data.errors" :key="error">{{ error }}</li>
                </ul>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex justify-end gap-3">
            <button
              @click="showImportModal = false"
              class="px-4 py-2 text-cdf-slate600 border border-cdf-slate200 rounded-lg hover:bg-cdf-slate50 transition-colors"
            >
              Annulla
            </button>
            <button
              @click="importCsv"
              :disabled="!selectedFile || importing"
              class="px-4 py-2 bg-cdf-teal text-white rounded-lg hover:bg-cdf-deep transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="importing" class="flex items-center">
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Importazione...
              </span>
              <span v-else>Importa</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Google OAuth Import Modal -->
    <div v-if="showGoogleImportModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-cdf-slate200">
          <div>
            <h2 class="text-xl font-bold text-cdf-deep">Importa Utenti Google OAuth</h2>
            <p class="text-sm text-cdf-slate600 mt-1">Importa utenti che accederanno con Google for Work</p>
          </div>
          <button
            @click="showGoogleImportModal = false"
            class="p-2 text-cdf-slate400 hover:text-cdf-slate600 hover:bg-cdf-slate200 rounded-lg transition-all"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <!-- Content -->
        <div class="p-6 space-y-6">
          <!-- Instructions -->
          <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
            <h3 class="font-semibold text-blue-900 mb-2">Come funziona:</h3>
            <ol class="text-sm text-blue-800 space-y-1">
              <li>1. L'azienda ti invia un CSV con nome, cognome, email Google</li>
              <li>2. Importi il CSV qui sotto</li>
              <li>3. Gli utenti potranno accedere cliccando "Entra con Google"</li>
              <li>4. Il sistema riconoscerà automaticamente l'email e farà il login</li>
            </ol>
          </div>

          <!-- Organization Selection -->
          <div>
            <label class="block text-sm font-medium text-cdf-deep mb-2">
              Assegna a Azienda (opzionale)
            </label>
            <select
              v-model="googleImportForm.organization_id"
              class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
            >
              <option value="">Nessuna azienda</option>
              <option
                v-for="org in organizations"
                :key="org.id"
                :value="org.id"
              >
                {{ org.name }}
              </option>
            </select>
          </div>

          <!-- CSV Input -->
          <div>
            <label class="block text-sm font-medium text-cdf-deep mb-2">
              Dati CSV
            </label>
            <textarea
              v-model="googleImportForm.csv_data"
              rows="8"
              class="w-full px-3 py-2 border border-cdf-slate200 rounded-lg focus:ring-2 focus:ring-cdf-teal focus:border-transparent"
              placeholder="Incolla qui i dati CSV nel formato: nome,cognome,email,azienda"
            ></textarea>
            <p class="text-xs text-cdf-slate600 mt-1">
              Formato: nome, cognome, email (obbligatori), azienda (opzionale)
            </p>
          </div>

          <!-- Template -->
          <div>
            <button
              @click="loadGoogleTemplate"
              class="text-sm text-cdf-teal hover:text-cdf-deep underline"
            >
              📋 Carica template di esempio
            </button>
          </div>

          <!-- Results -->
          <div v-if="googleImportResults" class="p-4 rounded-lg" :class="googleImportResults.success ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200'">
            <h4 class="font-semibold mb-2" :class="googleImportResults.success ? 'text-green-900' : 'text-red-900'">
              {{ googleImportResults.success ? 'Import completato!' : 'Errore durante l\'import' }}
            </h4>
            <div v-if="googleImportResults.success" class="text-sm text-green-800">
              <p>✅ Importati: {{ googleImportResults.data.imported }}</p>
              <p>⚠️ Saltati: {{ googleImportResults.data.skipped }}</p>
              <div v-if="googleImportResults.data.errors.length > 0" class="mt-2">
                <p class="font-medium">Errori:</p>
                <ul class="list-disc list-inside space-y-1">
                  <li v-for="error in googleImportResults.data.errors" :key="error">{{ error }}</li>
                </ul>
              </div>
            </div>
            <div v-else class="text-sm text-red-800">
              <p>{{ googleImportResults.error }}</p>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-end gap-3 p-6 border-t border-cdf-slate200">
          <button
            @click="showGoogleImportModal = false"
            class="px-4 py-2 text-cdf-slate600 border border-cdf-slate200 rounded-lg hover:bg-cdf-slate50 transition-colors"
          >
            Annulla
          </button>
          <button
            @click="importGoogleUsers"
            :disabled="!googleImportForm.csv_data || googleImporting"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="googleImporting" class="flex items-center">
              <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Importazione...
            </span>
            <span v-else>Importa Utenti Google</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Create User Modal -->
    <CreateUserModal
      :is-open="showCreateModal"
      @close="showCreateModal = false"
      @success="onUserCreated"
    />

    <!-- Enroll User Modal -->
    <EnrollUserModal
      :is-open="showEnrollModal"
      :preselected-user="selectedUserForEnrollment"
      @close="showEnrollModal = false"
      @success="onEnrollmentSuccess"
    />

    <!-- User Enrollments Modal -->
    <UserEnrollmentsModal
      :is-open="showUserEnrollmentsModal"
      :user="selectedUserForEnrollment"
      @close="showUserEnrollmentsModal = false"
      @enroll-user="enrollUser"
    />
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/api'
import UserModal from '@/components/Admin/UserModal.vue'
import CreateUserModal from '@/components/Admin/CreateUserModal.vue'
import EnrollUserModal from '@/components/Admin/EnrollUserModal.vue'
import UserEnrollmentsModal from '@/components/Admin/UserEnrollmentsModal.vue'

const router = useRouter()

// State
const users = ref([])
const loading = ref(false)
const statistics = ref({})
const organizations = ref([])
const showUserModal = ref(false)
const selectedUser = ref(null)
const isEdit = ref(false)
const showImportModal = ref(false)
const selectedFile = ref(null)
const importing = ref(false)
const importResults = ref(null)
const showEnrollmentModal = ref(false)
const selectedUserForEnrollment = ref(null)
const availableCourses = ref([])
const enrolling = ref(false)
const showCreateModal = ref(false)
const showEnrollModal = ref(false)
const showUserEnrollmentsModal = ref(false)
const showGoogleImportModal = ref(false)
const googleImporting = ref(false)
const googleImportResults = ref(null)
const googleImportForm = reactive({
  csv_data: '',
  organization_id: ''
})
const enrollmentForm = reactive({
  course_id: null,
  source: 'assign',
  payment_method: 'manual',
  start_date: new Date().toISOString().split('T')[0],
  expiry_date: '',
  send_notification: true
})

// Filters
const filters = reactive({
  search: '',
  is_admin: '',
  email_verified: '',
  sort_by: 'created_at',
  sort_order: 'desc'
})

// Pagination
const pagination = ref({
  current_page: 1,
  last_page: 1,
  per_page: 15,
  total: 0,
  prev: null,
  next: null
})

// Computed
const visiblePages = computed(() => {
  const current = pagination.value.current_page
  const last = pagination.value.last_page
  const pages = []
  
  for (let i = Math.max(1, current - 2); i <= Math.min(last, current + 2); i++) {
    pages.push(i)
  }
  
  return pages
})

// Methods
const goToDashboard = () => {
  router.push('/admin')
}

const loadUsers = async (page = 1) => {
  try {
    loading.value = true
    
    const params = new URLSearchParams({
      page: page.toString(),
      per_page: pagination.value.per_page.toString(),
      ...Object.fromEntries(
        Object.entries(filters).filter(([_, value]) => value !== '')
      )
    })
    
    const response = await api.get(`/v1/admin/users?${params}`)
    
    users.value = response.data.data
    pagination.value = {
      current_page: response.data.meta.current_page,
      last_page: response.data.meta.last_page,
      per_page: response.data.meta.per_page,
      total: response.data.meta.total,
      prev: response.data.links.prev,
      next: response.data.links.next
    }
  } catch (error) {
    console.error('Errore nel caricamento utenti:', error)
  } finally {
    loading.value = false
  }
}

const loadStatistics = async () => {
  try {
    const response = await api.get('/v1/admin/users/statistics')
    statistics.value = response.data.data
  } catch (error) {
    console.error('Errore nel caricamento statistiche:', error)
  }
}

const debouncedSearch = debounce(() => {
  loadUsers(1)
}, 500)

const editUser = (user) => {
  selectedUser.value = user
  isEdit.value = true
  showUserModal.value = true
}

const deleteUser = async (user) => {
  if (!confirm(`Sei sicuro di voler eliminare l'utente "${user.name}"?`)) {
    return
  }
  
  try {
    await api.delete(`/v1/admin/users/${user.id}`)
    await loadUsers(pagination.value.current_page)
    alert('Utente eliminato con successo')
  } catch (error) {
    console.error('Errore nell\'eliminazione utente:', error)
    alert('Errore nell\'eliminazione dell\'utente')
  }
}

const toggleAdminStatus = async (user) => {
  try {
    await api.patch(`/v1/admin/users/${user.id}/toggle-admin`)
    await loadUsers(pagination.value.current_page)
    await loadStatistics()
    alert(`Status admin ${user.is_admin ? 'rimosso' : 'assegnato'} con successo`)
  } catch (error) {
    console.error('Errore nel cambio status admin:', error)
    alert('Errore nel cambio status admin')
  }
}

const closeModal = () => {
  showUserModal.value = false
  selectedUser.value = null
  isEdit.value = false
}

const onUserSaved = () => {
  closeModal()
  loadUsers(pagination.value.current_page)
  loadStatistics()
}

const downloadTemplate = async () => {
  try {
    const response = await api.get('/v1/admin/users/download-template', {
      responseType: 'blob'
    })
    
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', 'template_import_utenti.csv')
    document.body.appendChild(link)
    link.click()
    link.remove()
    window.URL.revokeObjectURL(url)
  } catch (error) {
    console.error('Errore nel download del template:', error)
    alert('Errore nel download del template')
  }
}

const handleFileSelect = (event) => {
  const file = event.target.files[0]
  if (file) {
    selectedFile.value = file
    importResults.value = null // Reset previous results
  }
}

const importCsv = async () => {
  if (!selectedFile.value) return
  
  try {
    importing.value = true
    importResults.value = null
    
    const formData = new FormData()
    formData.append('file', selectedFile.value)
    
    const response = await api.post('/v1/admin/users/import-csv', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
    
    importResults.value = {
      success: true,
      data: response.data.data
    }
    
    // Reload users and statistics
    await loadUsers(pagination.value.current_page)
    await loadStatistics()
    
  } catch (error) {
    console.error('Errore nell\'import CSV:', error)
    importResults.value = {
      success: false,
      error: error.response?.data?.message || 'Errore durante l\'import'
    }
  } finally {
    importing.value = false
  }
}

const loadGoogleTemplate = async () => {
  try {
    const response = await api.get('/v1/admin/import/google-users/template')
    googleImportForm.csv_data = response.data.template
  } catch (error) {
    console.error('Errore nel caricamento del template:', error)
    alert('Errore nel caricamento del template')
  }
}

const importGoogleUsers = async () => {
  if (!googleImportForm.csv_data) return
  
  try {
    googleImporting.value = true
    googleImportResults.value = null
    
    const response = await api.post('/v1/admin/import/google-users', {
      csv_data: googleImportForm.csv_data,
      organization_id: googleImportForm.organization_id || null
    })
    
    googleImportResults.value = {
      success: true,
      data: response.data.data
    }
    
    // Reload users and statistics
    await loadUsers(pagination.value.current_page)
    await loadStatistics()
    
  } catch (error) {
    console.error('Errore nell\'import Google OAuth:', error)
    googleImportResults.value = {
      success: false,
      error: error.response?.data?.message || 'Errore durante l\'import'
    }
  } finally {
    googleImporting.value = false
  }
}

const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const formatPrice = (priceCents) => {
  return new Intl.NumberFormat('it-IT', {
    style: 'currency',
    currency: 'EUR'
  }).format(priceCents / 100)
}




const onUserCreated = async (user) => {
  // Reload users list
  await loadUsers(pagination.value.current_page)
  await loadStatistics()
  alert('Utente creato con successo!')
}

const enrollUser = (user) => {
  selectedUserForEnrollment.value = user
  showEnrollModal.value = true
}

const viewUserEnrollments = (user) => {
  selectedUserForEnrollment.value = user
  showUserEnrollmentsModal.value = true
}

const onEnrollmentSuccess = async () => {
  // Reload users list to update enrollment counts
  await loadUsers(pagination.value.current_page)
  await loadStatistics()
  alert('Utente iscritto al corso con successo!')
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString('it-IT', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

// Debounce utility
function debounce(func, wait) {
  let timeout
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout)
      func(...args)
    }
    clearTimeout(timeout)
    timeout = setTimeout(later, wait)
  }
}

// Lifecycle
const loadOrganizations = async () => {
  try {
    const response = await api.get('/v1/admin/organizations')
    organizations.value = response.data.data
  } catch (error) {
    console.error('Errore nel caricamento delle organizzazioni:', error)
  }
}

onMounted(() => {
  loadUsers()
  loadStatistics()
  loadOrganizations()
})
</script>
