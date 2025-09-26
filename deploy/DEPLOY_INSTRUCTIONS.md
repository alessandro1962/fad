# 🚀 Istruzioni Deploy Campus Digitale FAD

## 📋 Prerequisiti
- Droplet DigitalOcean creata
- Dominio configurato (fad.campusdigitale.online)
- Accesso SSH alla droplet

## 🔧 Step 1: Setup Server

### Connettiti alla droplet:
```bash
ssh root@167.71.152.69
# Password: ALF1123pmp!6665b5mhyn
```

### Esegui lo script di setup:
```bash
# Scarica lo script
curl -o setup-server.sh https://raw.githubusercontent.com/alessandro1962/fad/main/deploy/setup-server.sh

# Rendi eseguibile
chmod +x setup-server.sh

# Esegui setup
./setup-server.sh
```

**⏱️ Tempo stimato: 15-20 minuti**

## 🔧 Step 2: Clone Repository

```bash
# Vai nella directory
cd /var/www

# Clona il repository
git clone https://github.com/alessandro1962/fad.git campus-digitale-fad

# Imposta permessi
chown -R campus:www-data campus-digitale-fad
chmod -R 755 campus-digitale-fad
```

## 🔧 Step 3: Configurazione Ambiente

```bash
# Vai nella directory dell'app
cd /var/www/campus-digitale-fad

# Copia il file .env di produzione
cp deploy/env.production .env

# Genera APP_KEY
php artisan key:generate

# Installa dipendenze
composer install --optimize-autoloader --no-dev
npm ci --production

# Build assets
npm run build
```

## 🔧 Step 4: Database e Migrazioni

```bash
# Esegui migrazioni
php artisan migrate --force

# Esegui seeder
php artisan db:seed --force

# Crea storage link
php artisan storage:link

# Ottimizza Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🔧 Step 5: Configurazione SSL

```bash
# Installa certificato SSL
certbot --nginx -d fad.campusdigitale.online

# Test rinnovo automatico
certbot renew --dry-run
```

## 🔧 Step 6: Configurazione Finale

```bash
# Imposta permessi finali
chown -R campus:www-data /var/www/campus-digitale-fad
chmod -R 755 /var/www/campus-digitale-fad
chmod -R 775 /var/www/campus-digitale-fad/storage
chmod -R 775 /var/www/campus-digitale-fad/bootstrap/cache

# Riavvia servizi
systemctl restart php8.2-fpm
systemctl restart nginx
```

## 🔧 Step 7: Sincronizzazione WooCommerce

```bash
# Sincronizza prodotti da WooCommerce
php artisan woocommerce:sync

# Configura cron per sincronizzazione automatica
crontab -u campus -e
# Aggiungi: 0 */6 * * * cd /var/www/campus-digitale-fad && php artisan woocommerce:sync
```

## ✅ Verifica Deploy

1. **Visita**: https://fad.campusdigitale.online
2. **Testa**: Registrazione utente
3. **Verifica**: Catalogo corsi
4. **Controlla**: Logs per errori

## 🔧 Comandi Utili

### Logs
```bash
# Logs applicazione
tail -f /var/www/campus-digitale-fad/storage/logs/laravel.log

# Logs Nginx
tail -f /var/log/nginx/error.log
tail -f /var/log/nginx/access.log

# Logs PHP-FPM
tail -f /var/log/php8.2-fpm.log
```

### Manutenzione
```bash
# Pulizia cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Ottimizzazione
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Backup database
php artisan db:backup
```

### Aggiornamenti
```bash
# Aggiorna codice
git pull origin main

# Aggiorna dipendenze
composer install --optimize-autoloader --no-dev
npm ci --production
npm run build

# Esegui migrazioni
php artisan migrate --force

# Ottimizza
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🚨 Troubleshooting

### Errore 500
```bash
# Controlla logs
tail -f /var/www/campus-digitale-fad/storage/logs/laravel.log

# Verifica permessi
ls -la /var/www/campus-digitale-fad/storage
ls -la /var/www/campus-digitale-fad/bootstrap/cache
```

### Errore Database
```bash
# Test connessione
php artisan tinker
# DB::connection()->getPdo();

# Verifica credenziali
cat .env | grep DB_
```

### Errore SSL
```bash
# Rinnova certificato
certbot renew --force-renewal

# Test configurazione Nginx
nginx -t
```

## 📞 Supporto

In caso di problemi:
1. Controlla i logs
2. Verifica permessi
3. Testa configurazione
4. Contatta supporto tecnico

---

**🎉 Il tuo Campus Digitale FAD è ora online!**
