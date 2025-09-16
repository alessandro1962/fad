import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const routes = [
    {
        path: '/',
        name: 'home',
        component: () => import('@/views/Home.vue'),
    },
    {
        path: '/login',
        name: 'login',
        component: () => import('@/views/auth/Login.vue'),
        meta: { guest: true }
    },
    {
        path: '/register',
        name: 'register',
        component: () => import('@/views/auth/Register.vue'),
        meta: { guest: true }
    },
    {
        path: '/dashboard',
        name: 'dashboard',
        component: () => import('@/views/Dashboard.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/catalogo',
        name: 'catalog',
        component: () => import('@/views/Catalog.vue'),
    },
    {
        path: '/corso/:id',
        name: 'course',
        component: () => import('@/views/Course.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/i-miei-corsi',
        name: 'my-courses',
        component: () => import('@/views/MyCourses.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/attestati',
        name: 'certificates',
        component: () => import('@/views/Certificates.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/profilo',
        name: 'profile',
        component: () => import('@/views/Profile.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/calendario',
        name: 'calendar',
        component: () => import('@/views/Calendar.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/supporto',
        name: 'support',
        component: () => import('@/views/Support.vue'),
    },
    {
        path: '/admin',
        name: 'admin',
        component: () => import('@/views/admin/Dashboard.vue'),
        meta: { requiresAuth: true, requiresAdmin: true }
    },
    {
        path: '/admin/courses',
        name: 'admin-courses',
        component: () => import('@/views/admin/Courses.vue'),
        meta: { requiresAuth: true, requiresAdmin: true }
    },
    {
        path: '/admin/users',
        name: 'admin-users',
        component: () => import('@/views/admin/Users.vue'),
        meta: { requiresAuth: true, requiresAdmin: true }
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Navigation guards
router.beforeEach((to, from, next) => {
    const authStore = useAuthStore();
    
    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
        next('/login');
    } else if (to.meta.guest && authStore.isAuthenticated) {
        next(authStore.isAdmin ? '/admin' : '/dashboard');
    } else if (to.meta.requiresAdmin && !authStore.isAdmin) {
        next('/dashboard');
    } else {
        next();
    }
});

export default router;
