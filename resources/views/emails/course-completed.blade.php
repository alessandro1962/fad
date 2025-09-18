@extends('emails.layout')

@section('content')
    <h2 class="greeting">🎉 Congratulazioni {{ $user->name }}! 🎉</h2>
    
    <p class="message">
        Hai completato con successo il corso <strong>"{{ $course->title }}"</strong>! 
        Questo è un traguardo importante nel tuo percorso di apprendimento.
    </p>
    
    <div class="course-card">
        <h3 class="course-title">🏆 Corso Completato</h3>
        <p class="course-description"><strong>{{ $course->title }}</strong></p>
        
        <div style="margin: 16px 0;">
            <strong>Durata corso:</strong> {{ $course->duration_hours ?? 'N/A' }} ore<br>
            <strong>Data completamento:</strong> {{ now()->format('d/m/Y') }}<br>
            <strong>Attestato:</strong> Disponibile per il download
        </div>
    </div>
    
    <p class="message">
        Il tuo attestato di partecipazione è ora disponibile nella sezione "I miei attestati" 
        e può essere condiviso pubblicamente per dimostrare le tue competenze acquisite.
    </p>
    
    <div style="text-align: center; margin: 32px 0;">
        <a href="{{ url('/attestati') }}" class="cta-button">
            Scarica Attestato
        </a>
    </div>
    
    <p class="message">
        <strong>Continua il tuo percorso di apprendimento:</strong>
    </p>
    
    <ul style="color: #334155; font-size: 16px; line-height: 1.8;">
        <li>Esplora altri corsi nel <strong>catalogo</strong></li>
        <li>Scopri i <strong>percorsi formativi</strong> personalizzati</li>
        <li>Partecipa ai <strong>quiz avanzati</strong> per testare le tue conoscenze</li>
        <li>Condividi il tuo <strong>attestato</strong> sui social network</li>
    </ul>
    
    <div class="course-card">
        <h3 class="course-title">🚀 Prossimi Corsi Consigliati</h3>
        <p class="course-description">
            Basandoci sui tuoi interessi, ti consigliamo di esplorare questi corsi correlati 
            per continuare a sviluppare le tue competenze.
        </p>
        <div style="text-align: center;">
            <a href="{{ url('/catalogo') }}" class="cta-button">
                Esplora Altri Corsi
            </a>
        </div>
    </div>
    
    <p class="message">
        Grazie per aver scelto Campus Digitale Forma per la tua formazione. 
        Continua così e diventa un esperto in sicurezza digitale e privacy!
    </p>
    
    <p class="message">
        Congratulazioni ancora!<br>
        <strong>Il Team di Campus Digitale Forma</strong>
    </p>
@endsection
