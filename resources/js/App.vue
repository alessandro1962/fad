<template>
    <div id="app" class="min-h-screen bg-cdf-sand">
        <router-view />
    </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();

onMounted(() => {
    // Initialize auth state on app mount
    if (authStore.token) {
        authStore.fetchUser();
    }
});
</script>
