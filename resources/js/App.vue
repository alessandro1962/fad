<template>
    <div id="app" class="min-h-screen bg-cdf-sand">
        <router-view />
    </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'vue-router';

const authStore = useAuthStore();
const router = useRouter();

onMounted(async () => {
    // Check for OAuth token in URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    const oauthToken = urlParams.get('token');
    
    if (oauthToken) {
        console.log('OAuth token found:', oauthToken);
        
        // Store the token directly
        authStore.token = oauthToken;
        localStorage.setItem('token', oauthToken);
        
        // Clean URL by removing token parameter first
        const newUrl = window.location.pathname;
        window.history.replaceState({}, document.title, newUrl);
        
        // Then fetch user data
        try {
            await authStore.fetchUser();
            console.log('User fetched successfully');
        } catch (error) {
            console.error('Error fetching user:', error);
        }
    } else if (authStore.token) {
        // Initialize auth state on app mount with existing token
        authStore.fetchUser();
    }
});
</script>
