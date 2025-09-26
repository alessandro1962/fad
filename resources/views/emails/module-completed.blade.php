@extends('emails.layout')

@section('content')
    <h2 class="greeting">Complimenti {{ $user->name }}! 🎯</h2>
    
    <p class="message">
        Hai completato con successo il modulo <strong>"{{ $module->title }}"</strong> 
        del corso <strong>"{{ $course->title }}"</strong>.
    </p>
    
    <div class="course-card">
        <h3 class="course-title">{{ $course->title }}</h3>
        <p class="course-description">{{ $course->summary }}</p>
        
        <div style="margin: 16px 0;">
            <strong>Modulo completato:</strong> {{ $module->title }}<br>
            <strong>Progresso corso:</strong> {{ $progressPercentage }}%
        </div>
        
        <div class="progress-bar">
            <div class="progress-fill" style="width: {{ $progressPercentage }}%;"></div>
        </div>
    </div>
    
    <p class="message">
        Continua così! Sei sulla buona strada per completare l'intero corso e ottenere il tuo attestato.
    </p>
    
    <div style="text-align: center; margin: 32px 0;">
        <a href="{{ url('/corso/' . $course->id) }}" class="cta-button">
            Continua il Corso
        </a>
    </div>
    
    <p class="message">
        <strong>Prossimi passi:</strong>
    </p>
    
    <ul style="color: #334155; font-size: 16px; line-height: 1.8;">
        <li>Procedi con il prossimo modulo del corso</li>
        <li>Completa tutti i quiz per consolidare le tue conoscenze</li>
        <li>Scarica i materiali aggiuntivi se disponibili</li>
        <li>Esplora altri corsi correlati nel catalogo</li>
    </ul>
    
    <p class="message">
        Mantieni questo ritmo e presto avrai completato il corso!<br>
        <strong>Il Team di Campus Digitale Forma</strong>
    </p>
@endsection
