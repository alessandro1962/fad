<template>
    <div class="min-h-screen bg-cdf-sand">
        <!-- Top Navigation -->
        <header class="bg-white shadow-sm border-b border-cdf-slate200 sticky top-0 z-50">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <router-link to="/" class="flex items-center space-x-3">
                            <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-cdf-deep to-cdf-teal shadow-lg flex items-center justify-center">
                                <svg viewBox="0 0 64 64" class="h-6 w-6 text-white" aria-hidden="true">
                                    <rect x="0" y="0" width="64" height="64" rx="12" fill="none"/>
                                    <path d="M46,16 A18,18 0 1 0 46,48" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round"/>
                                    <polygon points="30,24 40,32 30,40" fill="currentColor"/>
                                </svg>
                            </div>
                            <div class="hidden sm:block">
                                <span class="text-xl font-bold text-cdf-deep">Campus Digitale</span>
                                <span class="text-sm text-cdf-teal font-medium block -mt-1">Forma</span>
                            </div>
                        </router-link>
                    </div>

                    <!-- Desktop Navigation -->
                    <nav class="hidden md:flex items-center space-x-8">
                        <router-link 
                            to="/catalogo" 
                            class="text-cdf-slate700 hover:text-cdf-deep font-medium transition-colors duration-200"
                            :class="{ 'text-cdf-deep': $route.name === 'catalog' }"
                        >
                            Catalogo
                        </router-link>
                        <router-link 
                            to="/percorsi" 
                            class="text-cdf-slate700 hover:text-cdf-deep font-medium transition-colors duration-200"
                        >
                            Percorsi
                        </router-link>
                        <router-link 
                            to="/aziende" 
                            class="text-cdf-slate700 hover:text-cdf-deep font-medium transition-colors duration-200"
                        >
                            Aziende
                        </router-link>
                        <router-link 
                            to="/supporto" 
                            class="text-cdf-slate700 hover:text-cdf-deep font-medium transition-colors duration-200"
                        >
                            Supporto
                        </router-link>
                    </nav>

                    <!-- User Menu -->
                    <div class="flex items-center space-x-4">
                        <!-- Notifications -->
                        <button 
                            v-if="authStore.isAuthenticated"
                            class="relative p-2 text-cdf-slate700 hover:text-cdf-deep transition-colors duration-200"
                        >
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.828 7l2.586 2.586a2 2 0 002.828 0L12.828 7H4.828zM4 7h16v2H4V7zM4 11h16v2H4v-2zM4 15h16v2H4v-2z"></path>
                            </svg>
                            <span class="absolute -top-1 -right-1 h-4 w-4 bg-cdf-amber text-white text-xs rounded-full flex items-center justify-center">3</span>
                        </button>

                        <!-- User Dropdown -->
                        <div v-if="authStore.isAuthenticated" class="relative">
                            <button 
                                @click="showUserMenu = !showUserMenu"
                                class="flex items-center space-x-3 p-2 rounded-lg hover:bg-cdf-sand transition-colors duration-200"
                            >
                                <div class="h-8 w-8 rounded-full bg-gradient-to-br from-cdf-teal to-cdf-deep flex items-center justify-center">
                                    <span class="text-white text-sm font-semibold">
                                        {{ authStore.user?.first_name?.charAt(0) }}{{ authStore.user?.last_name?.charAt(0) }}
                                    </span>
                                </div>
                                <div class="hidden sm:block text-left">
                                    <p class="text-sm font-medium text-cdf-deep">
                                        {{ authStore.user?.first_name }} {{ authStore.user?.last_name }}
                                    </p>
                                    <p class="text-xs text-cdf-slate700">{{ authStore.user?.email }}</p>
                                </div>
                                <svg class="h-4 w-4 text-cdf-slate700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div 
                                v-show="showUserMenu"
                                class="absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-lg border border-cdf-slate200 py-2 z-50"
                            >
                                <router-link 
                                    to="/dashboard" 
                                    class="flex items-center px-4 py-3 text-sm text-cdf-slate700 hover:bg-cdf-sand transition-colors duration-200"
                                    @click="showUserMenu = false"
                                >
                                    <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                    </svg>
                                    Dashboard
                                </router-link>
                                <router-link 
                                    to="/i-miei-corsi" 
                                    class="flex items-center px-4 py-3 text-sm text-cdf-slate700 hover:bg-cdf-sand transition-colors duration-200"
                                    @click="showUserMenu = false"
                                >
                                    <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                    I Miei Corsi
                                </router-link>
                                <router-link 
                                    to="/attestati" 
                                    class="flex items-center px-4 py-3 text-sm text-cdf-slate700 hover:bg-cdf-sand transition-colors duration-200"
                                    @click="showUserMenu = false"
                                >
                                    <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Attestati
                                </router-link>
                                <router-link 
                                    to="/profilo" 
                                    class="flex items-center px-4 py-3 text-sm text-cdf-slate700 hover:bg-cdf-sand transition-colors duration-200"
                                    @click="showUserMenu = false"
                                >
                                    <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Profilo
                                </router-link>
                                <div class="border-t border-cdf-slate200 my-2"></div>
                                <button 
                                    @click="handleLogout"
                                    class="flex items-center w-full px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition-colors duration-200"
                                >
                                    <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                    Esci
                                </button>
                            </div>
                        </div>

                        <!-- Auth Buttons -->
                        <div v-else class="flex items-center space-x-3">
                            <router-link to="/login" class="btn-secondary">
                                Accedi
                            </router-link>
                            <router-link to="/register" class="btn-primary">
                                Inizia ora
                            </router-link>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-cdf-slate200 mt-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <!-- Company Info -->
                    <div class="col-span-1 md:col-span-2">
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-cdf-deep to-cdf-teal flex items-center justify-center">
                                <svg viewBox="0 0 64 64" class="h-5 w-5 text-white" aria-hidden="true">
                                    <rect x="0" y="0" width="64" height="64" rx="12" fill="none"/>
                                    <path d="M46,16 A18,18 0 1 0 46,48" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round"/>
                                    <polygon points="30,24 40,32 30,40" fill="currentColor"/>
                                </svg>
                            </div>
                            <div>
                                <span class="text-lg font-bold text-cdf-deep">Campus Digitale</span>
                                <span class="text-sm text-cdf-teal font-medium block -mt-1">Forma</span>
                            </div>
                        </div>
                        <p class="text-cdf-slate700 text-sm mb-4 max-w-md">
                            La piattaforma FAD moderna che unisce fruizione intuitiva, percorsi personalizzati e certificazioni misurabili. Trasforma l'apprendimento in valore.
                        </p>
                        <div class="flex space-x-4">
                            <a href="#" class="text-cdf-slate700 hover:text-cdf-deep transition-colors duration-200">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                                </svg>
                            </a>
                            <a href="#" class="text-cdf-slate700 hover:text-cdf-deep transition-colors duration-200">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                </svg>
                            </a>
                            <a href="#" class="text-cdf-slate700 hover:text-cdf-deep transition-colors duration-200">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.174-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.402.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.357-.629-2.746-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24.009 12.017 24.009c6.624 0 11.99-5.367 11.99-11.988C24.007 5.367 18.641.001.012.001z"/>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div>
                        <h3 class="text-sm font-semibold text-cdf-deep uppercase tracking-wider mb-4">Link Rapidi</h3>
                        <ul class="space-y-3">
                            <li><router-link to="/catalogo" class="text-sm text-cdf-slate700 hover:text-cdf-deep transition-colors duration-200">Catalogo Corsi</router-link></li>
                            <li><router-link to="/percorsi" class="text-sm text-cdf-slate700 hover:text-cdf-deep transition-colors duration-200">Percorsi Formativi</router-link></li>
                            <li><router-link to="/aziende" class="text-sm text-cdf-slate700 hover:text-cdf-deep transition-colors duration-200">Soluzioni Aziendali</router-link></li>
                            <li><router-link to="/supporto" class="text-sm text-cdf-slate700 hover:text-cdf-deep transition-colors duration-200">Supporto</router-link></li>
                        </ul>
                    </div>

                    <!-- Legal -->
                    <div>
                        <h3 class="text-sm font-semibold text-cdf-deep uppercase tracking-wider mb-4">Legale</h3>
                        <ul class="space-y-3">
                            <li><a href="#" class="text-sm text-cdf-slate700 hover:text-cdf-deep transition-colors duration-200">Privacy Policy</a></li>
                            <li><a href="#" class="text-sm text-cdf-slate700 hover:text-cdf-deep transition-colors duration-200">Termini di Servizio</a></li>
                            <li><a href="#" class="text-sm text-cdf-slate700 hover:text-cdf-deep transition-colors duration-200">Cookie Policy</a></li>
                            <li><a href="#" class="text-sm text-cdf-slate700 hover:text-cdf-deep transition-colors duration-200">GDPR</a></li>
                        </ul>
                    </div>
                </div>

                <div class="border-t border-cdf-slate200 mt-8 pt-8">
                    <div class="flex flex-col md:flex-row justify-between items-center">
                        <p class="text-sm text-cdf-slate700">
                            © 2024 Campus Digitale Forma. Tutti i diritti riservati.
                        </p>
                        <p class="text-sm text-cdf-slate700 mt-2 md:mt-0">
                            P.IVA: 12345678901 | info@campusdigitale.it
                        </p>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const router = useRouter();
const authStore = useAuthStore();
const showUserMenu = ref(false);

const handleLogout = async () => {
    await authStore.logout();
    showUserMenu.value = false;
    router.push('/');
};

// Close user menu when clicking outside
const handleClickOutside = (event) => {
    if (!event.target.closest('.relative')) {
        showUserMenu.value = false;
    }
};

// Add event listener for clicking outside
import { onMounted, onUnmounted } from 'vue';

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>
