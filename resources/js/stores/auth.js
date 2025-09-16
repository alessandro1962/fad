import { defineStore } from 'pinia';
import axios from 'axios';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        token: localStorage.getItem('token'),
        isLoading: false,
    }),

    getters: {
        isAuthenticated: (state) => !!state.token && !!state.user,
        isAdmin: (state) => !!state.user?.is_admin,
        isOrgAdmin: (state) => state.user?.roles?.some(role => role.name === 'org_admin'),
    },

    actions: {
        async login(credentials) {
            this.isLoading = true;
            try {
                const response = await axios.post('/api/v1/auth/login', credentials);
                const { user, token } = response.data.data;
                
                this.user = user;
                this.token = token;
                localStorage.setItem('token', token);
                
                axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
                
                return { success: true };
            } catch (error) {
                return { 
                    success: false, 
                    message: error.response?.data?.message || 'Errore durante il login' 
                };
            } finally {
                this.isLoading = false;
            }
        },

        async register(userData) {
            this.isLoading = true;
            try {
                const response = await axios.post('/api/v1/auth/register', userData);
                const { user, token } = response.data.data;
                
                this.user = user;
                this.token = token;
                localStorage.setItem('token', token);
                
                axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
                
                return { success: true };
            } catch (error) {
                return { 
                    success: false, 
                    message: error.response?.data?.message || 'Errore durante la registrazione' 
                };
            } finally {
                this.isLoading = false;
            }
        },

        async logout() {
            try {
                await axios.post('/api/v1/auth/logout');
            } catch (error) {
                console.error('Logout error:', error);
            } finally {
                this.user = null;
                this.token = null;
                localStorage.removeItem('token');
                delete axios.defaults.headers.common['Authorization'];
            }
        },

        async fetchUser() {
            if (!this.token) return;
            
            try {
                axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
                const response = await axios.get('/api/v1/me');
                this.user = response.data.data;
            } catch (error) {
                console.error('Fetch user error:', error);
                this.logout();
            }
        },

        async socialLogin(provider) {
            window.location.href = `/api/v1/auth/social/${provider}`;
        },
    },
});
