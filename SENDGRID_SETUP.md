# 🚀 Setup SendGrid per Campus Digitale Forma

## 📋 **Configurazione Automatica**

### **1. Comando di Setup**
```bash
# Setup interattivo
php artisan sendgrid:setup

# Setup con parametri
php artisan sendgrid:setup --api-key=YOUR_API_KEY --from-email=noreply@campusdigitale.it --from-name="Campus Digitale Forma"
```

### **2. Test del Sistema**
```bash
# Test completo
php artisan mailing:test

# Test solo SendGrid
php artisan mailing:test --type=sendgrid
```

## 🔑 **Come Ottenere l'API Key SendGrid**

### **Passo 1: Registrazione**
1. Vai su [SendGrid.com](https://sendgrid.com)
2. Clicca "Start for Free"
3. Compila il form di registrazione
4. Verifica la tua email

### **Passo 2: Creare API Key**
1. Accedi al dashboard SendGrid
2. Vai su **Settings** → **API Keys**
3. Clicca **Create API Key**
4. Scegli **Full Access** o **Restricted Access**
5. Copia l'API Key (inizia con `SG.`)

### **Passo 3: Configurazione**
```bash
php artisan sendgrid:setup --api-key=SG.your_api_key_here
```

## 📧 **Configurazione Manuale (.env)**

Se preferisci configurare manualmente:

```bash
# SendGrid Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=SG.your_api_key_here
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@campusdigitale.it
MAIL_FROM_NAME="Campus Digitale Forma"

# Queue Configuration
QUEUE_CONNECTION=database
```

## 🎯 **Funzionalità Implementate**

### **✅ Tracking Avanzato**
- **Click Tracking**: Traccia tutti i click sui link
- **Open Tracking**: Traccia quando le email vengono aperte
- **Subscription Tracking**: Gestione unsubscribe automatica
- **Category Tracking**: Categorizzazione per analytics

### **✅ Template Ottimizzati**
- Design responsive per tutti i dispositivi
- Compatibilità con client email
- Tracking pixel integrato
- Branding Campus Digitale Forma

### **✅ Servizi Automatici**
- **UserRegistered**: Email di benvenuto
- **ModuleCompleted**: Notifica completamento modulo
- **CourseCompleted**: Notifica completamento corso
- **QuizCompleted**: Notifica completamento quiz
- **CourseAssigned**: Notifica corso assegnato
- **CourseReminder**: Promemoria corsi abbandonati

## 📊 **Analytics e Monitoraggio**

### **Dashboard SendGrid**
- **Email Activity**: Visualizza tutte le email inviate
- **Statistics**: Aperture, click, bounce, spam
- **Categories**: Filtra per tipo di notifica
- **Suppressions**: Gestisci liste di esclusione

### **Log Laravel**
```bash
# Visualizza log email
tail -f storage/logs/laravel.log | grep SendGrid
```

## 🔧 **Troubleshooting**

### **Errore: API Key non valida**
```bash
# Verifica la configurazione
php artisan config:clear
php artisan cache:clear
php artisan sendgrid:setup
```

### **Errore: Email non inviate**
```bash
# Controlla le code
php artisan queue:work --verbose

# Controlla i log
php artisan log:clear
php artisan mailing:test --type=sendgrid
```

### **Errore: Template non trovati**
```bash
# Pulisci cache view
php artisan view:clear
php artisan config:clear
```

## 📈 **Limiti e Piani**

### **Piano Gratuito**
- **100 email/giorno** (3.000/mese)
- **Tracking completo**
- **Supporto email**
- **API completa**

### **Piano Essentials** ($19.95/mese)
- **50.000 email/mese**
- **Priority support**
- **Advanced analytics**
- **Dedicated IP**

### **Piano Pro** ($89.95/mese)
- **100.000 email/mese**
- **Advanced features**
- **Phone support**
- **Custom domains**

## 🚀 **Deploy in Produzione**

### **1. Configurazione Server**
```bash
# Sul server Digital Ocean
cd /var/www/campus-digitale-fad
php artisan sendgrid:setup --api-key=YOUR_PRODUCTION_API_KEY
```

### **2. Avvio Worker**
```bash
# Avvia worker per le code
php artisan queue:work --daemon

# O con supervisor (raccomandato)
sudo supervisorctl start laravel-worker:*
```

### **3. Monitoraggio**
```bash
# Controlla stato worker
php artisan queue:monitor

# Controlla job falliti
php artisan queue:failed
```

## 📞 **Supporto**

### **SendGrid Support**
- **Documentation**: [docs.sendgrid.com](https://docs.sendgrid.com)
- **Status Page**: [status.sendgrid.com](https://status.sendgrid.com)
- **Community**: [community.sendgrid.com](https://community.sendgrid.com)

### **Campus Digitale Forma**
- **Email**: supporto@campusdigitale.it
- **Documentation**: Questo file
- **Logs**: `storage/logs/laravel.log`

## ✅ **Checklist Pre-Produzione**

- [ ] API Key SendGrid configurata
- [ ] Test connessione superato
- [ ] Template email verificati
- [ ] Worker code attivo
- [ ] Log monitoring configurato
- [ ] Backup configurazione
- [ ] Test con email reali
- [ ] Documentazione aggiornata

---

**🎉 SendGrid è ora configurato e pronto per inviare email professionali!**
