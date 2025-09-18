@extends('emails.layout')

@section('content')
    <h2 class="greeting">Benvenuto su Campus Digitale Forma, {{ $user->name }}! 🎉</h2>
    
    <p class="message">
        Grazie per esserti registrato alla nostra piattaforma di formazione online. 
        Il tuo account è stato creato con successo e sei pronto per iniziare il tuo percorso di apprendimento.
    </p>
    
    <p class="message">
        <strong>I tuoi dati di accesso:</strong><br>
        Email: {{ $user->email }}<br>
        Nome: {{ $user->name }}
    </p>
    
    <div style="text-align: center; margin: 32px 0;">
        <a href="{{ url('/dashboard') }}" class="cta-button">
            Accedi alla Dashboard
        </a>
    </div>
    
    <p class="message">
        <strong>Cosa puoi fare ora:</strong>
    </p>
    
    <ul style="color: #334155; font-size: 16px; line-height: 1.8;">
        <li>Esplora il <strong>catalogo corsi</strong> e scegli quello più adatto alle tue esigenze</li>
        <li>Scopri i <strong>percorsi formativi</strong> personalizzati</li>
        <li>Monitora i tuoi <strong>progressi</strong> nella dashboard</li>
        <li>Scarica gli <strong>attestati</strong> al completamento dei corsi</li>
        <li>Partecipa ai <strong>quiz</strong> per testare le tue conoscenze</li>
    </ul>
    
    <div class="course-card">
        <h3 class="course-title">🚀 Inizia subito!</h3>
        <p class="course-description">
            Ti consigliamo di iniziare con i corsi di base per familiarizzare con la piattaforma 
            e acquisire le competenze fondamentali in sicurezza digitale e privacy.
        </p>
        <div style="text-align: center;">
            <a href="{{ url('/catalogo') }}" class="cta-button">
                Esplora il Catalogo
            </a>
        </div>
    </div>
    
    <p class="message">
        Se hai domande o hai bisogno di supporto, non esitare a contattarci. 
        Il nostro team è sempre disponibile per aiutarti.
    </p>
    
    <p class="message">
        Buon apprendimento!<br>
        <strong>Il Team di Campus Digitale Forma</strong>
    </p>
@endsection
