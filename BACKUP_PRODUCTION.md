# 🚀 Backup Database di Produzione - Campus Digitale Forma

## 📋 Panoramica

Questo documento spiega come copiare il database di produzione di `fad.campusdigitale.online` in locale per continuare a debuggare senza fare casino.

## 🎯 Obiettivo

- Copiare il database di produzione in locale
- Mantenere la stessa struttura e dati
- Permettere debug locale senza impattare la produzione

## 🔧 Prerequisiti

### Software Necessario
- **MySQL** installato in locale
- **SSH** client
- **rsync** (per sincronizzazione file)

### Credenziali Server
- **Server**: `167.71.152.69`
- **User**: `root`
- **Password**: `ALF1123pmp!6665b5mhyn`
- **Database**: `campus_digitale_fad`
- **DB User**: `campus_fad`
- **DB Password**: `CampusFAD2024!`

## 🚀 Metodo 1: Script Automatico (Raccomandato)

### Esegui lo script:
```bash
./backup_db_simple.sh
```

### Cosa fa lo script:
1. **Connette** al server di produzione
2. **Crea dump** del database MySQL
3. **Scarica** il dump in locale
4. **Crea** database locale `campus_digitale_fad_local`
5. **Importa** i dati nel database locale
6. **Genera** file `.env.local` con configurazione locale

### Output atteso:
```
🚀 Backup Database di Produzione - Campus Digitale Forma
================================================

📋 Configurazione:
   Server: 167.71.152.69
   Database: campus_digitale_fad
   Backup locale: campus_digitale_fad_20250116_143022.sql

🔍 Connessione SSH al server...
Password server: ALF1123pmp!6665b5mhyn

📊 Creando dump e scaricando...
🗄️ Creando database locale...
📥 Importando database...
🔧 Creando configurazione locale...

✅ Backup completato!

📋 Riepilogo:
   📁 File backup: campus_digitale_fad_20250116_143022.sql
   🗄️  Database locale: campus_digitale_fad_local
   ⚙️  Configurazione: .env.local

🔧 Prossimi passi:
   1. cp .env.local .env
   2. php artisan key:generate
   3. php artisan serve
   4. npm run dev

🎉 Database di produzione copiato in locale!
```

## 🚀 Metodo 2: Comandi Manuali

### 1. Crea dump del database remoto:
```bash
ssh root@167.71.152.69 "mysqldump -u campus_fad -pCampusFAD2024! --single-transaction --routines --triggers campus_digitale_fad" > campus_digitale_fad_backup.sql
```

### 2. Crea database locale:
```bash
mysql -u root -p -e "CREATE DATABASE campus_digitale_fad_local CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### 3. Importa i dati:
```bash
mysql -u root -p campus_digitale_fad_local < campus_digitale_fad_backup.sql
```

### 4. Configura ambiente locale:
```bash
cp .env.local .env
php artisan key:generate
```

## 📁 Sincronizzazione File Storage (Opzionale)

Se hai bisogno anche dei file caricati (immagini, documenti, etc.):

```bash
./sync_storage.sh
```

Questo sincronizza la cartella `storage/app/public` dal server di produzione.

## 🔧 Configurazione Post-Backup

### 1. Copia configurazione locale:
```bash
cp .env.local .env
```

### 2. Genera APP_KEY:
```bash
php artisan key:generate
```

### 3. Crea storage link:
```bash
php artisan storage:link
```

### 4. Pulisci cache:
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### 5. Avvia il server:
```bash
php artisan serve
```

### 6. Compila frontend (in un altro terminale):
```bash
npm run dev
```

## 🧪 Test del Backup

### 1. Verifica connessione database:
```bash
php artisan tinker
```
```php
DB::connection()->getPdo();
// Dovrebbe restituire: PDO object
```

### 2. Controlla utenti:
```php
User::count();
// Dovrebbe restituire il numero di utenti dal database di produzione
```

### 3. Testa login admin:
- Vai su `http://localhost:8000/login`
- Login con: `admin@campusdigitale.it` / `admin123`

## 🔍 Troubleshooting

### Errore: "Access denied for user"
```bash
# Verifica credenziali MySQL locali
mysql -u root -p -e "SHOW DATABASES;"
```

### Errore: "Database already exists"
```bash
# Elimina database esistente
mysql -u root -p -e "DROP DATABASE campus_digitale_fad_local;"
```

### Errore: "SSH connection failed"
```bash
# Testa connessione SSH
ssh root@167.71.152.69 "echo 'Connessione OK'"
```

### Errore: "Permission denied"
```bash
# Rendi eseguibili gli script
chmod +x backup_db_simple.sh
chmod +x sync_storage.sh
```

## 📊 Struttura Database

Il database include tutte le tabelle principali:
- **users** - Utenti della piattaforma
- **courses** - Corsi disponibili
- **modules** - Moduli dei corsi
- **lessons** - Lezioni individuali
- **enrollments** - Iscrizioni utenti
- **progress** - Progressi di apprendimento
- **certificates** - Certificati rilasciati
- **badges** - Badge gamification
- **organizations** - Organizzazioni/aziende

## 🎯 Vantaggi del Backup Locale

✅ **Debug sicuro** - Nessun impatto sulla produzione  
✅ **Dati reali** - Stesso database della produzione  
✅ **Sviluppo veloce** - Nessuna latenza di rete  
✅ **Test completi** - Tutte le funzionalità disponibili  
✅ **Rollback facile** - Puoi sempre rifare il backup  

## 🔄 Aggiornamento Periodico

Per mantenere i dati aggiornati, esegui periodicamente:

```bash
# Backup completo
./backup_db_simple.sh

# Solo storage (se necessario)
./sync_storage.sh
```

## 📞 Supporto

In caso di problemi:
1. Controlla i log: `tail -f storage/logs/laravel.log`
2. Verifica configurazione: `php artisan config:show`
3. Testa connessione: `php artisan tinker`

---

**🎉 Ora puoi debuggare in locale con i dati reali di produzione!**
