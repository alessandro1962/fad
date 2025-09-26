# 📧 Configurazione Sistema di Mailing

## Configurazione Email per Campus Digitale Forma

### Variabili d'ambiente (.env)

```bash
# Driver Email (log per sviluppo, smtp per produzione)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@campusdigitale.it
MAIL_FROM_NAME="Campus Digitale Forma"

# Configurazione Coda (per notifiche asincrone)
QUEUE_CONNECTION=database
```

### Provider Email Supportati

#### Gmail
```bash
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_ENCRYPTION=tls
```

#### Outlook/Hotmail
```bash
MAIL_HOST=smtp-mail.outlook.com
MAIL_PORT=587
MAIL_ENCRYPTION=tls
```

#### Yahoo
```bash
MAIL_HOST=smtp.mail.yahoo.com
MAIL_PORT=587
MAIL_ENCRYPTION=tls
```

#### SendGrid
```bash
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key
```

#### Mailgun
```bash
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=your-mailgun-username
MAIL_PASSWORD=your-mailgun-password
```

#### Amazon SES
```bash
MAIL_HOST=email-smtp.us-east-1.amazonaws.com
MAIL_PORT=587
MAIL_USERNAME=your-ses-username
MAIL_PASSWORD=your-ses-password
```

## Comandi Utili

### Test Sistema di Mailing
```bash
# Testa tutte le notifiche
php artisan mailing:test

# Testa solo registrazione utente
php artisan mailing:test --type=registration

# Testa solo completamento modulo
php artisan mailing:test --type=module

# Testa solo completamento corso
php artisan mailing:test --type=course
```

### Gestione Code
```bash
# Avvia worker per le code
php artisan queue:work

# Processa una singola coda
php artisan queue:work --queue=default

# Monitora le code
php artisan queue:monitor
```

### Pulizia Code
```bash
# Pulisce le code fallite
php artisan queue:flush

# Riprova i job falliti
php artisan queue:retry all
```

## Notifiche Implementate

### 1. Registrazione Utente
- **Evento**: `UserRegistered`
- **Template**: `emails.user-registered`
- **Trigger**: Quando un utente si registra

### 2. Completamento Modulo
- **Evento**: `ModuleCompleted`
- **Template**: `emails.module-completed`
- **Trigger**: Quando un utente completa un modulo

### 3. Completamento Corso
- **Evento**: `CourseCompleted`
- **Template**: `emails.course-completed`
- **Trigger**: Quando un utente completa un corso

### 4. Completamento Quiz
- **Evento**: `QuizCompleted`
- **Template**: `emails.quiz-completed`
- **Trigger**: Quando un utente completa un quiz

### 5. Assegnazione Corso
- **Evento**: `CourseAssigned`
- **Template**: `emails.course-assigned`
- **Trigger**: Quando un corso viene assegnato a un utente

### 6. Promemoria Corso
- **Evento**: `CourseReminder`
- **Template**: `emails.course-reminder`
- **Trigger**: Quando un utente non completa un corso da tempo

## Template Email

Tutti i template sono basati su `emails.layout` e includono:
- Header con logo e branding
- Contenuto personalizzato
- Call-to-action buttons
- Footer con link utili
- Design responsive

## Configurazione Produzione

1. **Configura SMTP** con provider affidabile
2. **Abilita code** per notifiche asincrone
3. **Configura Redis** per performance migliori
4. **Monitora le code** per errori
5. **Testa regolarmente** il sistema

## Troubleshooting

### Email non inviate
- Verifica configurazione SMTP
- Controlla log Laravel
- Verifica credenziali email
- Controlla limiti provider

### Code non processate
- Avvia worker: `php artisan queue:work`
- Controlla job falliti: `php artisan queue:failed`
- Verifica configurazione database

### Template non trovati
- Verifica path template in `resources/views/emails/`
- Controlla sintassi Blade
- Verifica variabili passate
