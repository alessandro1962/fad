<template>
    <AppLayout>

        <!-- Hero Section -->
        <section class="mx-auto max-w-7xl px-6 pt-10 pb-16 lg:pt-16">
            <div class="grid lg:grid-cols-2 gap-10 items-center">
                <div>
                    <h1 class="text-4xl md:text-5xl font-extrabold text-cdf-deep leading-tight">
                        Forma competenze. <span class="text-cdf-teal">Misura risultati.</span>
                    </h1>
                    <p class="mt-4 text-lg text-cdf-slate700">
                        Piattaforma FAD con percorsi personalizzati, gamification e attestati verificabili.
                        <span class="font-medium">Trasforma l'apprendimento in valore.</span>
                    </p>
                    <div class="mt-6 flex gap-3">
                        <router-link to="/catalogo" class="btn-primary">
                            Scopri i percorsi
                        </router-link>
                        <router-link to="/catalogo" class="btn-secondary">
                            Guarda il catalogo
                        </router-link>
                    </div>
                    <div class="mt-6 text-sm text-cdf-slate700">
                        SSO Google/Microsoft • Attestati pubblici • Report per aziende
                    </div>
                </div>
                
                <div class="card">
                    <div class="grid sm:grid-cols-3 gap-4">
                        <div class="p-4 rounded-2xl bg-cdf-sand ring-1 ring-cdf-slate200">
                            <h3 class="font-semibold text-cdf-deep">Percorsi</h3>
                            <p class="text-sm text-cdf-slate700">Guidati da questionario e cluster aziendali.</p>
                        </div>
                        <div class="p-4 rounded-2xl bg-cdf-sand ring-1 ring-cdf-slate200">
                            <h3 class="font-semibold text-cdf-deep">Gamification</h3>
                            <p class="text-sm text-cdf-slate700">Badge, livelli e progressi visibili.</p>
                        </div>
                        <div class="p-4 rounded-2xl bg-cdf-sand ring-1 ring-cdf-slate200">
                            <h3 class="font-semibold text-cdf-deep">Attestati</h3>
                            <p class="text-sm text-cdf-slate700">Corso e percorso con link pubblico.</p>
                        </div>
                    </div>
                    <div class="mt-4 h-40 rounded-2xl bg-gradient-to-br from-cdf-teal/15 to-cdf-deep/15 grid place-items-center text-cdf-deep font-semibold">
                        Anteprima player & timeline progressi
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="mx-auto max-w-7xl px-6 py-16">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-cdf-deep mb-4">
                    Perché scegliere Campus Digitale Forma?
                </h2>
                <p class="text-lg text-cdf-slate700">
                    Una piattaforma moderna che unisce fruizione intuitiva e risultati misurabili
                </p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <div class="card text-center">
                    <div class="w-16 h-16 bg-cdf-teal/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-cdf-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-cdf-deep mb-2">Percorsi Personalizzati</h3>
                    <p class="text-cdf-slate700">Questionari intelligenti e cluster aziendali per percorsi formativi su misura</p>
                </div>
                
                <div class="card text-center">
                    <div class="w-16 h-16 bg-cdf-amber/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-cdf-amber" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-cdf-deep mb-2">Gamification</h3>
                    <p class="text-cdf-slate700">Badge, livelli e progressi visibili per motivare l'apprendimento</p>
                </div>
                
                <div class="card text-center">
                    <div class="w-16 h-16 bg-cdf-deep/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-cdf-deep" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-cdf-deep mb-2">Attestati Verificabili</h3>
                    <p class="text-cdf-slate700">Certificazioni pubbliche per singoli corsi e percorsi completi</p>
                </div>
            </div>
        </section>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/components/Layout/AppLayout.vue';
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();
</script>
