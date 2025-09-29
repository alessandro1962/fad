<template>
  <div class="min-h-screen bg-gray-50">
    <AppLayout>
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
          <h1 class="text-3xl font-bold text-gray-900">Dashboard Rivenditore</h1>
          <p class="text-gray-600 mt-2">Gestisci i tuoi clienti, aziende e gettoni</p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                  </svg>
                </div>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Gettoni Disponibili</p>
                <p class="text-2xl font-semibold text-gray-900">{{ dashboardData.tokens?.available_tokens || 0 }}</p>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                  </svg>
                </div>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Utenti Standalone</p>
                <p class="text-2xl font-semibold text-gray-900">{{ standaloneUsers.length }}</p>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-purple-600 rounded-full flex items-center justify-center">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                  </svg>
                </div>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Aziende</p>
                <p class="text-2xl font-semibold text-gray-900">{{ organizations.length }}</p>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-orange-600 rounded-full flex items-center justify-center">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                  </svg>
                </div>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Company Manager</p>
                <p class="text-2xl font-semibold text-gray-900">{{ companyManagers.length }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow mb-8">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Azioni Rapide</h3>
          </div>
          <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <button
                @click="showCreateUserModal = true"
                class="flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
              >
                <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <span class="text-sm font-medium text-gray-700">Crea Utente Standalone (1 gettone)</span>
              </button>
              
              <button
                @click="showCreateOrganizationModal = true"
                class="flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
              >
                <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <span class="text-sm font-medium text-gray-700">Crea Azienda + Manager (0 gettoni)</span>
              </button>
            </div>
          </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="bg-white rounded-lg shadow">
          <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
              <button
                v-for="tab in tabs"
                :key="tab.id"
                @click="activeTab = tab.id"
                :class="[
                  activeTab === tab.id
                    ? 'border-blue-500 text-blue-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                  'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm'
                ]"
              >
                {{ tab.name }}
                <span v-if="tab.count !== undefined" class="ml-2 bg-gray-100 text-gray-900 py-0.5 px-2.5 rounded-full text-xs">
                  {{ tab.count }}
                </span>
              </button>
            </nav>
          </div>

          <!-- Tab Content -->
          <div class="p-6">
            <!-- Standalone Users Tab -->
            <div v-if="activeTab === 'users'" class="space-y-6">
              <div class="flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Utenti Standalone</h3>
                <button
                  @click="showCreateUserModal = true"
                  class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                  </svg>
                  Nuovo Utente
                </button>
              </div>

              <div v-if="loading" class="text-center py-8">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                <p class="mt-2 text-gray-600">Caricamento...</p>
              </div>

              <div v-else-if="standaloneUsers.length === 0" class="text-center py-8 text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Nessun utente standalone</h3>
                <p class="mt-1 text-sm text-gray-500">Inizia creando il tuo primo utente standalone.</p>
              </div>

              <div v-else class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                <table class="min-w-full divide-y divide-gray-300">
                  <thead class="bg-gray-50">
                    <tr>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Utente</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data Creazione</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Azioni</th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="user in standaloneUsers" :key="user.id">
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                          <div class="flex-shrink-0 h-10 w-10">
                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                              <span class="text-blue-600 font-medium text-sm">{{ user.name.charAt(0).toUpperCase() }}</span>
                            </div>
                          </div>
                          <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                            <div class="text-sm text-gray-500">{{ user.first_name }} {{ user.last_name }}</div>
                          </div>
                        </div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ user.email }}</td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(user.created_at) }}</td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                          <button
                            @click="editUser(user)"
                            class="text-blue-600 hover:text-blue-900"
                          >
                            Modifica
                          </button>
                          <button
                            @click="assignUserToOrganization(user)"
                            class="text-green-600 hover:text-green-900"
                          >
                            Assegna ad Azienda
                          </button>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Organizations Tab -->
            <div v-if="activeTab === 'organizations'" class="space-y-6">
              <div class="flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Aziende</h3>
                <button
                  @click="showCreateOrganizationModal = true"
                  class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-purple-600 hover:bg-purple-700"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                  </svg>
                  Nuova Azienda
                </button>
              </div>

              <div v-if="loading" class="text-center py-8">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div>
                <p class="mt-2 text-gray-600">Caricamento...</p>
              </div>

              <div v-else-if="organizations.length === 0" class="text-center py-8 text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Nessuna azienda</h3>
                <p class="mt-1 text-sm text-gray-500">Inizia creando la tua prima azienda.</p>
              </div>

              <div v-else class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                <table class="min-w-full divide-y divide-gray-300">
                  <thead class="bg-gray-50">
                    <tr>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Azienda</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Manager</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data Creazione</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Azioni</th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="organization in organizations" :key="organization.id">
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                          <div class="flex-shrink-0 h-10 w-10">
                            <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                              <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                              </svg>
                            </div>
                          </div>
                          <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">{{ organization.name }}</div>
                          </div>
                        </div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div v-if="organization.manager" class="text-sm text-gray-900">{{ organization.manager.name }}</div>
                        <div v-else class="text-sm text-gray-500">Nessun manager</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(organization.created_at) }}</td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button
                          @click="editOrganization(organization)"
                          class="text-purple-600 hover:text-purple-900"
                        >
                          Modifica
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Company Managers Tab -->
            <div v-if="activeTab === 'managers'" class="space-y-6">
              <div class="flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Company Manager</h3>
              </div>

              <div v-if="loading" class="text-center py-8">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-orange-600"></div>
                <p class="mt-2 text-gray-600">Caricamento...</p>
              </div>

              <div v-else-if="companyManagers.length === 0" class="text-center py-8 text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Nessun company manager</h3>
                <p class="mt-1 text-sm text-gray-500">I company manager vengono creati insieme alle aziende.</p>
              </div>

              <div v-else class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                <table class="min-w-full divide-y divide-gray-300">
                  <thead class="bg-gray-50">
                    <tr>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Manager</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Azienda</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data Creazione</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Azioni</th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="manager in companyManagers" :key="manager.id">
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                          <div class="flex-shrink-0 h-10 w-10">
                            <div class="h-10 w-10 rounded-full bg-orange-100 flex items-center justify-center">
                              <span class="text-orange-600 font-medium text-sm">{{ manager.name.charAt(0).toUpperCase() }}</span>
                            </div>
                          </div>
                          <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">{{ manager.name }}</div>
                            <div class="text-sm text-gray-500">{{ manager.first_name }} {{ manager.last_name }}</div>
                          </div>
                        </div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ manager.email }}</td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <span v-if="manager.organization" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                          {{ manager.organization.name }}
                        </span>
                        <span v-else class="text-gray-500">Nessuna azienda</span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(manager.created_at) }}</td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button
                          @click="editManager(manager)"
                          class="text-orange-600 hover:text-orange-900"
                        >
                          Modifica
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </AppLayout>

    <!-- Create User Modal -->
    <div v-if="showCreateUserModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Crea Nuovo Utente Standalone</h3>
          <form @submit.prevent="createStandaloneUser" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Nome Completo</label>
              <input
                v-model="userForm.name"
                type="text"
                required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Nome</label>
              <input
                v-model="userForm.first_name"
                type="text"
                required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Cognome</label>
              <input
                v-model="userForm.last_name"
                type="text"
                required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Email</label>
              <input
                v-model="userForm.email"
                type="email"
                required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Password</label>
              <input
                v-model="userForm.password"
                type="password"
                required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
              />
            </div>
            <div class="flex justify-end space-x-3">
              <button
                type="button"
                @click="showCreateUserModal = false"
                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
              >
                Annulla
              </button>
              <button
                type="submit"
                :disabled="loading"
                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50"
              >
                {{ loading ? 'Creazione...' : 'Crea Utente' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Create Organization Modal -->
    <div v-if="showCreateOrganizationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Crea Nuova Azienda + Manager</h3>
          <form @submit.prevent="createOrganization" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Nome Azienda</label>
              <input
                v-model="organizationForm.organization_name"
                type="text"
                required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Nome Completo Manager</label>
              <input
                v-model="organizationForm.manager_name"
                type="text"
                required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Nome Manager</label>
              <input
                v-model="organizationForm.manager_first_name"
                type="text"
                required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Cognome Manager</label>
              <input
                v-model="organizationForm.manager_last_name"
                type="text"
                required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Email Manager</label>
              <input
                v-model="organizationForm.manager_email"
                type="email"
                required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Password Manager</label>
              <input
                v-model="organizationForm.manager_password"
                type="password"
                required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500"
              />
            </div>
            <div class="flex justify-end space-x-3">
              <button
                type="button"
                @click="showCreateOrganizationModal = false"
                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
              >
                Annulla
              </button>
              <button
                type="submit"
                :disabled="loading"
                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 disabled:opacity-50"
              >
                {{ loading ? 'Creazione...' : 'Crea Azienda' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Edit User Modal -->
    <div v-if="showEditUserModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Modifica Utente</h3>
          <form @submit.prevent="saveUserEdit" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Nome</label>
              <input
                v-model="editUserForm.name"
                type="text"
                required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-cdf-primary focus:border-cdf-primary"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Nome</label>
              <input
                v-model="editUserForm.first_name"
                type="text"
                required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-cdf-primary focus:border-cdf-primary"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Cognome</label>
              <input
                v-model="editUserForm.last_name"
                type="text"
                required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-cdf-primary focus:border-cdf-primary"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Email</label>
              <input
                v-model="editUserForm.email"
                type="email"
                required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-cdf-primary focus:border-cdf-primary"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Nuova Password (opzionale)</label>
              <input
                v-model="editUserForm.password"
                type="password"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-cdf-primary focus:border-cdf-primary"
                placeholder="Lascia vuoto per non cambiare"
              />
            </div>
            <div class="flex justify-end space-x-3">
              <button
                type="button"
                @click="showEditUserModal = false"
                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
              >
                Annulla
              </button>
              <button
                type="submit"
                :disabled="loading"
                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50"
              >
                {{ loading ? 'Salvataggio...' : 'Salva' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Edit Organization Modal -->
    <div v-if="showEditOrganizationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Modifica Azienda</h3>
          <form @submit.prevent="saveOrganizationEdit" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Nome Azienda</label>
              <input
                v-model="editOrganizationForm.name"
                type="text"
                required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-cdf-primary focus:border-cdf-primary"
              />
            </div>
            <div class="flex justify-end space-x-3">
              <button
                type="button"
                @click="showEditOrganizationModal = false"
                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
              >
                Annulla
              </button>
              <button
                type="submit"
                :disabled="loading"
                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 disabled:opacity-50"
              >
                {{ loading ? 'Salvataggio...' : 'Salva' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Edit Manager Modal -->
    <div v-if="showEditManagerModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Modifica Manager</h3>
          <form @submit.prevent="saveManagerEdit" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Nome</label>
              <input
                v-model="editManagerForm.name"
                type="text"
                required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-cdf-primary focus:border-cdf-primary"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Nome</label>
              <input
                v-model="editManagerForm.first_name"
                type="text"
                required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-cdf-primary focus:border-cdf-primary"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Cognome</label>
              <input
                v-model="editManagerForm.last_name"
                type="text"
                required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-cdf-primary focus:border-cdf-primary"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Email</label>
              <input
                v-model="editManagerForm.email"
                type="email"
                required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-cdf-primary focus:border-cdf-primary"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Nuova Password (opzionale)</label>
              <input
                v-model="editManagerForm.password"
                type="password"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-cdf-primary focus:border-cdf-primary"
                placeholder="Lascia vuoto per non cambiare"
              />
            </div>
            <div class="flex justify-end space-x-3">
              <button
                type="button"
                @click="showEditManagerModal = false"
                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
              >
                Annulla
              </button>
              <button
                type="submit"
                :disabled="loading"
                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 disabled:opacity-50"
              >
                {{ loading ? 'Salvataggio...' : 'Salva' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Assign User to Organization Modal -->
    <div v-if="showAssignUserModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Assegna Utente ad Azienda</h3>
          <form @submit.prevent="saveUserAssignment" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Seleziona Azienda</label>
              <select
                v-model="assignUserForm.organization_id"
                required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-cdf-primary focus:border-cdf-primary"
              >
                <option value="">Seleziona un'azienda</option>
                <option v-for="org in organizations" :key="org.id" :value="org.id">
                  {{ org.name }}
                </option>
              </select>
            </div>
            <div class="flex justify-end space-x-3">
              <button
                type="button"
                @click="showAssignUserModal = false"
                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
              >
                Annulla
              </button>
              <button
                type="submit"
                :disabled="loading"
                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 disabled:opacity-50"
              >
                {{ loading ? 'Assegnazione...' : 'Assegna' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import AppLayout from '@/components/Layout/AppLayout.vue'
import api from '@/api'

// Data
const dashboardData = ref({})
const standaloneUsers = ref([])
const organizations = ref([])
const companyManagers = ref([])
const loading = ref(false)
const activeTab = ref('users')

// Modals
const showCreateUserModal = ref(false)
const showCreateOrganizationModal = ref(false)
const showEditUserModal = ref(false)
const showEditOrganizationModal = ref(false)
const showEditManagerModal = ref(false)
const showAssignUserModal = ref(false)

// Forms
const userForm = ref({
  name: '',
  first_name: '',
  last_name: '',
  email: '',
  password: ''
})

const organizationForm = ref({
  organization_name: '',
  manager_name: '',
  manager_first_name: '',
  manager_last_name: '',
  manager_email: '',
  manager_password: ''
})

// Edit forms
const editUserForm = ref({
  id: null,
  name: '',
  first_name: '',
  last_name: '',
  email: '',
  password: ''
})

const editOrganizationForm = ref({
  id: null,
  name: ''
})

const editManagerForm = ref({
  id: null,
  name: '',
  first_name: '',
  last_name: '',
  email: '',
  password: ''
})

const assignUserForm = ref({
  user_id: null,
  organization_id: null
})

// Tabs configuration
const tabs = computed(() => [
  { id: 'users', name: 'Utenti Standalone', count: standaloneUsers.value.length },
  { id: 'organizations', name: 'Aziende', count: organizations.value.length },
  { id: 'managers', name: 'Company Manager', count: companyManagers.value.length }
])

// Methods
const loadDashboard = async () => {
  loading.value = true
  try {
    const response = await api.get('/v1/reseller/dashboard')
    dashboardData.value = response.data.data
    
    // Load the separate sections after dashboard data is loaded
    await loadStandaloneUsers()
    await loadOrganizations()
    await loadCompanyManagers()
  } catch (error) {
    console.error('Error loading dashboard:', error)
  } finally {
    loading.value = false
  }
}

const loadStandaloneUsers = async () => {
  try {
    // Use data from dashboard instead of separate endpoint
    if (dashboardData.value.created_users) {
      standaloneUsers.value = dashboardData.value.created_users.filter(user => 
        !user.organization_id && !user.is_company_manager
      )
    }
  } catch (error) {
    console.error('Error loading standalone users:', error)
  }
}

const loadOrganizations = async () => {
  try {
    // Use data from dashboard instead of separate endpoint
    if (dashboardData.value.created_organizations) {
      organizations.value = dashboardData.value.created_organizations
    }
  } catch (error) {
    console.error('Error loading organizations:', error)
  }
}

const loadCompanyManagers = async () => {
  try {
    // Use data from dashboard instead of separate endpoint
    if (dashboardData.value.created_users) {
      companyManagers.value = dashboardData.value.created_users.filter(user => 
        user.is_company_manager
      )
    }
  } catch (error) {
    console.error('Error loading company managers:', error)
  }
}

const createStandaloneUser = async () => {
  loading.value = true
  try {
    await api.post('/v1/reseller/clients/standalone', userForm.value)
    
    // Reset form
    userForm.value = {
      name: '',
      first_name: '',
      last_name: '',
      email: '',
      password: ''
    }
    showCreateUserModal.value = false
    
    // Reload data
    await Promise.all([
      loadDashboard(),
      loadStandaloneUsers()
    ])
    
    alert('Utente creato con successo!')
  } catch (error) {
    console.error('Error creating user:', error)
    alert('Errore durante la creazione dell\'utente')
  } finally {
    loading.value = false
  }
}

const createOrganization = async () => {
  loading.value = true
  try {
    await api.post('/v1/reseller/clients/organization', organizationForm.value)
    
    // Reset form
    organizationForm.value = {
      organization_name: '',
      manager_name: '',
      manager_first_name: '',
      manager_last_name: '',
      manager_email: '',
      manager_password: ''
    }
    showCreateOrganizationModal.value = false
    
    // Reload data
    await Promise.all([
      loadDashboard(),
      loadOrganizations(),
      loadCompanyManagers()
    ])
    
    alert('Azienda e manager creati con successo!')
  } catch (error) {
    console.error('Error creating organization:', error)
    alert('Errore durante la creazione dell\'azienda')
  } finally {
    loading.value = false
  }
}

const editUser = (user) => {
  editUserForm.value = {
    id: user.id,
    name: user.name,
    first_name: user.first_name,
    last_name: user.last_name,
    email: user.email,
    password: ''
  }
  showEditUserModal.value = true
}

const editOrganization = (organization) => {
  editOrganizationForm.value = {
    id: organization.id,
    name: organization.name
  }
  showEditOrganizationModal.value = true
}

const editManager = (manager) => {
  editManagerForm.value = {
    id: manager.id,
    name: manager.name,
    first_name: manager.first_name,
    last_name: manager.last_name,
    email: manager.email,
    password: ''
  }
  showEditManagerModal.value = true
}

const assignUserToOrganization = (user) => {
  assignUserForm.value = {
    user_id: user.id,
    organization_id: null
  }
  showAssignUserModal.value = true
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('it-IT')
}

// Save functions
const saveUserEdit = async () => {
  loading.value = true
  try {
    const updateData = { ...editUserForm.value }
    // Remove password if empty
    if (!updateData.password) {
      delete updateData.password
    }
    await api.put(`/v1/reseller/standalone-users/${editUserForm.value.id}`, updateData)
    showEditUserModal.value = false
    await loadDashboard()
    alert('Utente aggiornato con successo!')
  } catch (error) {
    console.error('Error updating user:', error)
    alert('Errore durante l\'aggiornamento dell\'utente')
  } finally {
    loading.value = false
  }
}

const saveOrganizationEdit = async () => {
  loading.value = true
  try {
    await api.put(`/v1/reseller/organizations/${editOrganizationForm.value.id}`, editOrganizationForm.value)
    showEditOrganizationModal.value = false
    await loadDashboard()
    alert('Organizzazione aggiornata con successo!')
  } catch (error) {
    console.error('Error updating organization:', error)
    alert('Errore durante l\'aggiornamento dell\'organizzazione')
  } finally {
    loading.value = false
  }
}

const saveManagerEdit = async () => {
  loading.value = true
  try {
    const updateData = { ...editManagerForm.value }
    // Remove password if empty
    if (!updateData.password) {
      delete updateData.password
    }
    await api.put(`/v1/reseller/company-managers/${editManagerForm.value.id}`, updateData)
    showEditManagerModal.value = false
    await loadDashboard()
    alert('Manager aggiornato con successo!')
  } catch (error) {
    console.error('Error updating manager:', error)
    alert('Errore durante l\'aggiornamento del manager')
  } finally {
    loading.value = false
  }
}

const saveUserAssignment = async () => {
  loading.value = true
  try {
    await api.post('/v1/reseller/assign-user-to-organization', assignUserForm.value)
    showAssignUserModal.value = false
    await loadDashboard()
    alert('Utente assegnato all\'azienda con successo!')
  } catch (error) {
    console.error('Error assigning user:', error)
    alert('Errore durante l\'assegnazione dell\'utente')
  } finally {
    loading.value = false
  }
}


// Lifecycle
onMounted(async () => {
  await loadDashboard()
})
</script>
