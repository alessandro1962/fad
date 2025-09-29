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
        path: '/forgot-password',
        name: 'forgot-password',
        component: () => import('@/views/auth/ForgotPassword.vue'),
        meta: { guest: true }
    },
    {
        path: '/password-reset/:token',
        name: 'reset-password',
        component: () => import('@/views/auth/ResetPassword.vue'),
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
        component: () => import('@/views/CoursePublic.vue'),
        meta: { requiresAuth: false }
    },
    {
        path: '/corso-autenticato/:id',
        name: 'course-authenticated',
        component: () => import('@/views/CoursePlayer.vue'),
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
        path: '/certificates/:uid',
        name: 'public-certificate',
        component: () => import('@/views/PublicCertificate.vue'),
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
        path: '/gamification',
        name: 'gamification',
        component: () => import('@/components/Gamification/GamificationDashboard.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/badges',
        name: 'badges',
        component: () => import('@/components/Gamification/BadgeCollection.vue'),
        meta: { requiresAuth: true }
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
    {
        path: '/admin/analytics',
        name: 'admin-analytics',
        component: () => import('@/views/admin/Analytics.vue'),
        meta: { requiresAuth: true, requiresAdmin: true }
    },
    {
        path: '/admin/settings',
        name: 'admin-settings',
        component: () => import('@/views/admin/Settings.vue'),
        meta: { requiresAuth: true, requiresAdmin: true }
    },
    {
        path: '/admin/certificate-templates',
        name: 'admin-certificate-templates',
        component: () => import('@/views/admin/CertificateTemplates.vue'),
        meta: { requiresAuth: true, requiresAdmin: true }
    },
    {
        path: '/admin/organizations',
        name: 'admin-organizations',
        component: () => import('@/views/admin/Organizations.vue'),
        meta: { requiresAuth: true, requiresAdmin: true }
    },
    {
        path: '/admin/resellers',
        name: 'admin-resellers',
        component: () => import('@/views/admin/Resellers.vue'),
        meta: { requiresAuth: true, requiresAdmin: true }
    },
    // Company routes
    {
        path: '/company/dashboard',
        name: 'company-dashboard',
        component: () => import('@/views/company/Dashboard.vue'),
        meta: { requiresAuth: true, requiresCompanyManager: true }
    },
    {
        path: '/company/employees/:employeeId',
        name: 'company-employee-details',
        component: () => import('@/views/company/EmployeeDetails.vue'),
        meta: { requiresAuth: true, requiresCompanyManager: true }
    },
    // Reseller routes
    {
        path: '/reseller/dashboard',
        name: 'reseller-dashboard',
        component: () => import('@/views/reseller/Dashboard.vue'),
        meta: { requiresAuth: true, requiresReseller: true }
    },
    {
        path: '/reseller/clients',
        name: 'reseller-clients',
        component: () => import('@/views/reseller/Clients.vue'),
        meta: { requiresAuth: true, requiresReseller: true }
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Navigation guards
router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore();
    
    // If we have a token but no user, try to fetch the user first
    if (authStore.token && !authStore.user) {
        try {
            await authStore.fetchUser();
        } catch (error) {
            console.error('Failed to fetch user:', error);
        }
    }
    
    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
        next('/login');
    } else if (to.meta.guest && authStore.isAuthenticated) {
        // Redirect based on user role
        if (authStore.isAdmin) {
            next('/admin');
        } else if (authStore.isReseller) {
            next('/reseller/dashboard');
        } else if (authStore.isCompanyManager) {
            next('/company/dashboard');
        } else {
            next('/dashboard');
        }
    } else if (to.meta.requiresAdmin && !authStore.isAdmin) {
        next('/dashboard');
    } else if (to.meta.requiresCompanyManager && !authStore.isCompanyManager) {
        next('/dashboard');
    } else if (to.meta.requiresReseller && !authStore.isReseller) {
        next('/dashboard');
    } else if (to.path === '/dashboard' && authStore.isAuthenticated) {
        // Redirect to appropriate dashboard based on user role
        if (authStore.isAdmin) {
            next('/admin');
        } else if (authStore.isReseller) {
            next('/reseller/dashboard');
        } else if (authStore.isCompanyManager) {
            next('/company/dashboard');
        } else {
            next();
        }
    } else {
        next();
    }
});

export default router;
