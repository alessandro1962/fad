# Google OAuth Setup per Campus Digitale Forma

## 🎯 **Workflow Implementato**

### **Come funziona:**
1. **Azienda invia CSV** con: `nome, cognome, email_google, azienda`
2. **Admin importa CSV** → crea utenti con `provider: 'google'` e `provider_id: null`
3. **Utente clicca "Entra con Google"** → Google OAuth
4. **Sistema matcha email** → se trova match nel DB, fa login automatico
5. **Se non trova match** → mostra errore "Contatta il tuo amministratore"

## 🔧 **Configurazione Google OAuth**

### **1. Google Cloud Console Setup**

1. Vai su [Google Cloud Console](https://console.cloud.google.com/)
2. Crea un nuovo progetto o seleziona uno esistente
3. Abilita l'API "Google+ API" o "Google Identity"
4. Vai su "Credenziali" → "Crea credenziali" → "ID client OAuth 2.0"
5. Configura:
   - **Tipo applicazione**: Applicazione web
   - **Nome**: Campus Digitale Forma
   - **URI di reindirizzamento autorizzati**: 
     - `http://localhost:8000/auth/google/callback` (per sviluppo)
     - `https://tuodominio.com/auth/google/callback` (per produzione)

### **2. Configurazione .env**

Aggiungi al file `.env`:

```env
# Google OAuth
GOOGLE_CLIENT_ID=your_google_client_id_here
GOOGLE_CLIENT_SECRET=your_google_client_secret_here
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

### **3. Configurazione Produzione**

Per la produzione su DigitalOcean, aggiorna:

```env
GOOGLE_REDIRECT_URI=https://tuodominio.com/auth/google/callback
```

## 📋 **Import CSV per Google OAuth**

### **Formato CSV:**
```csv
nome,cognome,email,azienda
Mario,Rossi,mario.rossi@azienda.com,Azienda SRL
Giulia,Bianchi,giulia.bianchi@azienda.com,Azienda SRL
```

### **Come usare:**
1. Vai su **Admin** → **Gestione Utenti**
2. Clicca **"🔐 Importa Google OAuth"**
3. Seleziona l'azienda (opzionale)
4. Incolla i dati CSV nel formato richiesto
5. Clicca **"Importa Utenti Google"**

### **Cosa succede:**
- Gli utenti vengono creati con `provider: 'google'`
- `provider_id` rimane `null` fino al primo login
- `email_verified_at` viene impostato automaticamente
- Password casuale (non verrà mai usata)

## 🔐 **Login OAuth**

### **Per gli utenti:**
1. Vai su `/login`
2. Clicca **"Entra con Google"**
3. Autorizza l'applicazione
4. Se l'email è nel database → **Login automatico**
5. Se l'email non è nel database → **Errore "Contatta amministratore"**

### **Sicurezza:**
- Solo email presenti nel database possono fare login
- Matching automatico per email
- Nessuna creazione automatica di account
- Controllo provider per evitare conflitti

## 🚀 **Deployment**

### **1. Aggiorna .env su DigitalOcean:**
```bash
# SSH su DigitalOcean
cd /var/www/campus-digitale-fad
nano .env

# Aggiungi:
GOOGLE_CLIENT_ID=your_production_client_id
GOOGLE_CLIENT_SECRET=your_production_client_secret
GOOGLE_REDIRECT_URI=https://tuodominio.com/auth/google/callback
```

### **2. Aggiorna Google Cloud Console:**
- Aggiungi l'URI di produzione: `https://tuodominio.com/auth/google/callback`
- Rimuovi l'URI di sviluppo se non serve più

### **3. Test in produzione:**
1. Importa alcuni utenti di test
2. Prova il login con Google
3. Verifica che il matching funzioni

## 🔍 **Troubleshooting**

### **Errore "redirect_uri_mismatch":**
- Verifica che l'URI in Google Cloud Console corrisponda esattamente
- Controlla http vs https
- Controlla la porta (8000 per sviluppo)

### **Errore "Account non trovato":**
- L'email non è presente nel database
- Importa l'utente tramite CSV prima del login

### **Errore "Provider già associato":**
- L'utente esiste ma con un provider diverso
- Controlla il campo `provider` nella tabella `users`

## 📊 **Monitoraggio**

### **Log da controllare:**
```bash
# Log OAuth
tail -f storage/logs/laravel.log | grep "OAuth"

# Log Google
tail -f storage/logs/laravel.log | grep "Google"
```

### **Database queries utili:**
```sql
-- Utenti con Google OAuth
SELECT * FROM users WHERE provider = 'google';

-- Utenti senza provider_id (non hanno mai fatto login)
SELECT * FROM users WHERE provider = 'google' AND provider_id IS NULL;
```

## 🎉 **Vantaggi del Sistema**

✅ **Sicurezza**: Solo utenti autorizzati possono accedere  
✅ **Semplicità**: Un click per accedere  
✅ **Gestione centralizzata**: Admin controlla chi può accedere  
✅ **Scalabilità**: Import CSV per centinaia di utenti  
✅ **Integrazione aziendale**: Perfetto per Google for Work  

---

**Implementato da:** Campus Digitale Forma Team  
**Data:** 25 Settembre 2025  
**Versione:** 1.0
