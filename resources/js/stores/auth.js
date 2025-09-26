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
        isCompanyManager: (state) => !!state.user?.is_company_manager,
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

        setToken(token) {
            console.log('Setting token:', token);
            this.token = token;
            localStorage.setItem('token', token);
            axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
            return this.fetchUser();
        },

async fetchUser() {
    if (!this.token) {
        console.log('No token available');
        return;
    }
    
    try {
        console.log('Fetching user with token:', this.token);
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
        const response = await axios.get('/api/v1/auth/me');
        this.user = response.data.data;
        console.log('User fetched successfully:', this.user);
        console.log('User ID:', this.user.id);
        console.log('User email:', this.user.email);
    } catch (error) {
        console.error('Fetch user error:', error);
        console.error('Error response:', error.response);
        // Don't logout automatically, just log the error
        // this.logout();
    }
},
        async socialLogin(provider) {
            window.location.href = `/auth/${provider}`;
        },
    },
});
