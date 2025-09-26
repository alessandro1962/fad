# Campus Digitale Forma - Piattaforma FAD

> **Trasforma l'apprendimento in valore.**

Una piattaforma moderna di Formazione a Distanza (FAD) progettata per superare i limiti degli strumenti attuali e offrire un ambiente di apprendimento personalizzato, coinvolgente e professionale.

## 🎯 Visione

Campus Digitale Forma nasce con l'obiettivo di creare un **hub unico** per l'utente, concentrando in un solo spazio tutti i servizi formativi e semplificando la gestione amministrativa. La piattaforma offre un'interfaccia intuitiva e accattivante, con design moderno e colori pensati per favorire la fruizione.

## ✨ Caratteristiche Principali

### 🎨 Design & UX
- **Interfaccia moderna** con palette colori "no-bianco-dominante"
- **Design responsive** per desktop, tablet e smartphone
- **Accessibilità** completa (WCAG 2.1 AA)
- **Tema scuro/chiaro** con palette brand personalizzata

### 🔐 Autenticazione & Accesso
- **Registrazione tradizionale** (email + password)
- **Single Sign-On (SSO)** con Google
- **Azure AD/Microsoft Entra ID** per aziende
- **Consensi GDPR** (privacy e marketing)
- **Gestione organizzazioni** multi-tenant

### 📚 Catalogo & Corsi
- **Catalogo completo** con filtri avanzati
- **Corsi strutturati** in moduli e lezioni
- **Livelli di difficoltà** (beginner, intermediate, expert)
- **Sistema di prezzi** flessibile
- **Corsi in evidenza** e raccomandazioni

### 🎓 Sistema di Apprendimento
- **Iscrizioni automatiche** e manuali
- **Tracciamento progressi** dettagliato
- **Sistema di completamento** con controlli rigorosi
- **Video player** con controlli avanzati
- **Questionari interattivi** tra le sezioni

### 🏆 Gamification
- **Sistema di livelli** (principiante → esperto)
- **Badge e premi** digitali
- **Punti esperienza** e classifiche
- **Progressi visibili** con grafici e timeline
- **Sezione "da completare"** per motivare

### 📜 Certificazioni
- **Attestati per singoli corsi**
- **Certificati di percorso** completi
- **Link pubblici** per condivisione
- **Generazione PDF** automatica
- **Verifica pubblica** degli attestati

### 📊 Analytics & Reportistica
- **Dashboard amministrativa** con KPI
- **Tracciamento comportamenti** utenti
- **Report dettagliati** per aziende
- **Analisi abbandono** e completamento
- **Export dati** (CSV, XLSX)

## 🛠 Stack Tecnologico

### Backend
- **Laravel 11** (PHP 8.2+)
- **MySQL 8** (database principale)
- **Redis** (cache e code)
- **Laravel Sanctum** (autenticazione API)
- **Laravel Socialite** (SSO Google)
- **OIDC** (Azure AD/Microsoft Entra ID)

### Frontend
- **Vue 3** + **Vite** (framework frontend)
- **Pinia** (gestione stato)
- **Vue Router** (routing)
- **TailwindCSS 3** (styling)
- **Headless UI** (componenti accessibili)

### Pacchetti Laravel
- **spatie/laravel-permission** (ruoli e permessi)
- **spatie/laravel-medialibrary** (gestione media)
- **spatie/laravel-activitylog** (audit trail)
- **maatwebsite/excel** (export dati)
- **barryvdh/laravel-dompdf** (generazione PDF)
- **laravel/horizon** (monitoraggio code)

## 📁 Struttura Progetto

```
campus-digitale-fad/
├── app/
│   ├── Domain/                 # Logica di dominio
│   │   ├── Users/
│   │   ├── Courses/
│   │   ├── Tracks/
│   │   ├── Learning/
│   │   ├── Gamification/
│   │   ├── Orders/
│   │   └── Analytics/
│   ├── Http/
│   │   ├── Controllers/Api/v1/ # API Controllers
│   │   ├── Middleware/         # Middleware personalizzati
│   │   ├── Requests/           # Form Request validation
│   │   └── Resources/          # API Resources
│   ├── Models/                 # Modelli Eloquent
│   ├── Services/               # Servizi business logic
│   ├── Jobs/                   # Job per code
│   ├── Listeners/              # Event listeners
│   └── Events/                 # Eventi personalizzati
├── database/
│   ├── migrations/             # Migrazioni database
│   ├── seeders/                # Seeder per dati demo
│   └── factories/              # Factory per testing
├── resources/
│   ├── js/                     # Frontend Vue.js
│   │   ├── views/              # Pagine Vue
│   │   ├── components/         # Componenti riutilizzabili
│   │   ├── stores/             # Store Pinia
│   │   └── router/             # Configurazione routing
│   └── css/                    # Stili Tailwind
└── routes/
    ├── api.php                 # Route API
    └── web.php                 # Route web
```

## 🗄️ Modello Dati

### Tabelle Principali
- **users** - Utenti della piattaforma
- **organizations** - Organizzazioni/aziende
- **courses** - Corsi disponibili
- **modules** - Moduli dei corsi
- **lessons** - Lezioni individuali
- **enrollments** - Iscrizioni utenti
- **progress** - Progressi di apprendimento
- **tracks** - Percorsi formativi
- **certificates** - Attestati rilasciati
- **badges** - Badge gamification
- **orders** - Ordini e acquisti
- **events** - Eventi per analytics

### Relazioni Chiave
- User → Organization (many-to-one)
- Course → Modules → Lessons (one-to-many)
- User → Enrollments → Course (many-to-many)
- User → Progress → Lesson (many-to-many)
- User → Certificates (one-to-many)
- User → Badges (many-to-many)

## 🔌 API Endpoints

### Autenticazione
```
POST   /api/v1/auth/register    # Registrazione utente
POST   /api/v1/auth/login       # Login utente
GET    /api/v1/auth/me          # Profilo utente
POST   /api/v1/auth/logout      # Logout
POST   /api/v1/auth/refresh     # Refresh token
```

### Catalogo Corsi
```
GET    /api/v1/courses          # Lista corsi (con filtri)
GET    /api/v1/courses/featured # Corsi in evidenza
GET    /api/v1/courses/level/{level} # Corsi per livello
GET    /api/v1/courses/{id}     # Dettaglio corso
```

### Iscrizioni & Apprendimento
```
GET    /api/v1/enrollments      # Iscrizioni utente
POST   /api/v1/enrollments      # Iscrizione a corso
GET    /api/v1/enrollments/active # Iscrizioni attive
GET    /api/v1/enrollments/completed # Iscrizioni completate
PATCH  /api/v1/enrollments/{id}/progress # Aggiorna progresso
```

### Gamification
```
GET    /api/v1/badges           # Badge disponibili
GET    /api/v1/badges/mine      # Badge utente
GET    /api/v1/certificates/mine # Certificati utente
GET    /api/v1/certificates/public/{uid} # Certificato pubblico
```

## 🎨 Brand Kit

### Colori
- **Deep (Primario)**: `#0B3B5E` - 40%
- **Teal (Secondario)**: `#00A7B7` - 20%
- **Amber (Accento)**: `#FFC857` - 5%
- **Sand (Background)**: `#F4F1EA` - 25%
- **Ink (Testo)**: `#14161A`
- **Slate (Neutri)**: `#334155` / `#E2E8F0` - 10%

### Tipografia
- **Font**: Inter (700/800 per titoli, 600 per CTA, 400/500 per testo)
- **Fallback**: ui-sans-serif, system-ui, Segoe UI, Roboto

### Logo
- **Varianti**: Orizzontale (icon + wordmark) e solo icona
- **Uso**: Deep su sfondi chiari, bianco su sfondi scuri
- **Clear space**: Altezza della "C" attorno al marchio

## 🚀 Installazione

### Prerequisiti
- PHP 8.2+
- Composer
- Node.js 18+
- MySQL 8
- Redis

### Setup
```bash
# Clone repository
git clone <repository-url>
cd campus-digitale-fad

# Installazione dipendenze
composer install
npm install

# Configurazione ambiente
cp .env.example .env
php artisan key:generate

# Configurazione database
# Modifica .env con le credenziali MySQL

# Migrazioni e seeder
php artisan migrate
php artisan db:seed

# Build frontend
npm run build

# Avvio server
php artisan serve
npm run dev
```

### Configurazione .env
```env
APP_NAME="Campus Digitale Forma"
APP_URL=http://localhost:8000
FRONTEND_URL=http://localhost:5173

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=campus_digitale
DB_USERNAME=campus_user
DB_PASSWORD=password123

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis

# Google OAuth
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
GOOGLE_REDIRECT_URI=${APP_URL}/auth/callback/google

# Azure AD
AZURE_TENANT_ID=your_tenant_id
AZURE_CLIENT_ID=your_client_id
AZURE_CLIENT_SECRET=your_client_secret
AZURE_REDIRECT_URI=${APP_URL}/auth/callback/azure
```

## 🎯 Funzionalità Chiave

### 1. Hub Personale
- **Dashboard centralizzata** con progressi e statistiche
- **Accesso rapido** a tutti i contenuti
- **Notifiche personalizzate** per scadenze e aggiornamenti
- **Calendario eventi** e sessioni live
- **Area supporto** integrata

### 2. Percorsi Personalizzati
- **Questionario onboarding** per profilo utente
- **Suggerimenti automatici** basati su interessi
- **Cluster utenti** per targeting mirato
- **Percorsi guidati** da principiante a esperto
- **Assegnazione automatica** corsi

### 3. Sistema Video Avanzato
- **Integrazione Vimeo/YouTube** con controlli personalizzati
- **Disabilitazione autoplay** non desiderato
- **Controllo completamento** obbligatorio
- **Blocco progressione** se video non completato
- **Tracciamento tempo** di visualizzazione

### 4. Questionari Interattivi
- **Design accattivante** e moderno
- **Tipologie multiple** (scelta multipla, vero/falso, testo)
- **Feedback immediato** con spiegazioni
- **Sistema punteggi** e soglie di superamento
- **Tentativi multipli** configurabili

### 5. Gamification Completa
- **Livelli progressivi** basati su ore e quiz
- **Badge tematici** per milestone
- **Sistema punti** e classifiche
- **Streak di apprendimento** per motivazione
- **Achievement** speciali per eventi

### 6. Certificazioni Professionali
- **Template personalizzabili** per attestati
- **Generazione PDF** automatica
- **Link pubblici** per verifica
- **Certificati di percorso** con ore totali
- **Integrazione blockchain** (futuro)

## 📊 Analytics & Reporting

### Metriche Utente
- **Tempo di apprendimento** per corso/lezione
- **Tasso di completamento** e abbandono
- **Interazioni** con materiali
- **Performance** nei quiz
- **Engagement** e frequenza accessi

### Report Aziendali
- **Utenti attivi** per organizzazione
- **Corsi più popolari** e completati
- **ROI formazione** e investimenti
- **Trend di apprendimento** nel tempo
- **Export personalizzati** per analisi

### Dashboard Admin
- **KPI real-time** della piattaforma
- **Heatmap** accessi e utilizzo
- **Alert** per anomalie o problemi
- **Gestione utenti** e organizzazioni
- **Configurazione** corsi e percorsi

## 🔒 Sicurezza & Compliance

### GDPR Compliance
- **Consensi granulari** (privacy, marketing)
- **Diritto all'oblio** con cancellazione dati
- **Portabilità dati** con export completo
- **Audit trail** per tutte le operazioni
- **Informativa privacy** sempre accessibile

### Sicurezza Tecnica
- **Rate limiting** su API critiche
- **Sanitizzazione input** con validation
- **Headers di sicurezza** (CSP, HSTS)
- **Logging sicuro** senza dati sensibili
- **Backup automatici** database e file

### Autenticazione
- **JWT tokens** con scadenza configurabile
- **Refresh token** per sessioni lunghe
- **SSO enterprise** con Azure AD
- **MFA** (Multi-Factor Authentication) - fase 2
- **Sessioni revocabili** per admin

## 🚀 Roadmap Futuro

### Fase 2 - White Label
- **Temi personalizzabili** per organizzazioni
- **Domini custom** per clienti enterprise
- **SMTP dedicati** per notifiche
- **Override contenuti** e testi
- **Branding completo** per aziende

### Fase 3 - Mobile & Advanced
- **App mobile nativa** (React Native/Flutter)
- **Notifiche push** avanzate
- **Offline learning** con sincronizzazione
- **Realtà aumentata** per contenuti interattivi
- **AI/ML** per raccomandazioni personalizzate

### Fase 4 - Enterprise
- **Marketplace** corsi di terze parti
- **Integrazione LMS** esistenti
- **API pubbliche** per sviluppatori
- **Webhook** per integrazioni
- **Multi-tenant** completo

## 🤝 Contribuire

### Setup Sviluppo
```bash
# Fork del repository
# Clone del fork locale
git clone <your-fork-url>
cd campus-digitale-fad

# Branch per feature
git checkout -b feature/nome-feature

# Installazione dipendenze
composer install
npm install

# Test
php artisan test
npm run test
```

### Standard di Codice
- **PSR-12** per PHP
- **Laravel Pint** per formattazione
- **ESLint + Prettier** per JavaScript
- **Conventional Commits** per messaggi
- **Test coverage** minimo 80%

### Processo di Review
1. **Fork** del repository
2. **Branch** per feature/bugfix
3. **Test** completi e passanti
4. **Pull Request** con descrizione dettagliata
5. **Review** da maintainer
6. **Merge** dopo approvazione

## 📞 Supporto

### Documentazione
- **Wiki completa** su GitHub
- **API Documentation** con OpenAPI
- **Video tutorial** per setup
- **FAQ** per problemi comuni
- **Changelog** dettagliato

### Community
- **GitHub Issues** per bug e feature
- **Discussions** per domande
- **Discord/Slack** per chat real-time
- **Newsletter** per aggiornamenti
- **Blog** per case study

### Contatti
- **Email**: support@campusdigitale.it
- **Telefono**: +39 XXX XXX XXXX
- **Indirizzo**: Via Example, 123 - 00100 Roma
- **Orari**: Lun-Ven 9:00-18:00

## 📄 Licenza

Questo progetto è rilasciato sotto licenza **MIT**. Vedi il file [LICENSE](LICENSE) per i dettagli.

## 🙏 Ringraziamenti

- **Laravel Team** per il framework eccezionale
- **Vue.js Team** per l'ecosistema frontend
- **Tailwind CSS** per il sistema di design
- **Spatie** per i pacchetti Laravel di qualità
- **Community** per feedback e contributi

---

**Campus Digitale Forma** - *Trasforma l'apprendimento in valore.*