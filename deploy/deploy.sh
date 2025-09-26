#!/bin/bash

# Campus Digitale FAD - Deploy Script
# Esegui questo script sulla droplet dopo il setup

set -e

# Colori per output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Funzione per log
log() {
    echo -e "${GREEN}[$(date +'%Y-%m-%d %H:%M:%S')] $1${NC}"
}

error() {
    echo -e "${RED}[ERROR] $1${NC}"
    exit 1
}

warning() {
    echo -e "${YELLOW}[WARNING] $1${NC}"
}

info() {
    echo -e "${BLUE}[INFO] $1${NC}"
}

# Variabili
APP_DIR="/var/www/campus-digitale-fad"
REPO_URL="https://github.com/alessandro1962/fad.git"

log "🚀 Iniziando deployment di Campus Digitale FAD..."

# Vai nella directory dell'applicazione
cd $APP_DIR

# Backup database se esiste
if [ -f ".env" ]; then
    log "Creando backup database..."
    php artisan db:backup --destination=local --disk=local || warning "Backup fallito, continuo..."
fi

# Pull ultime modifiche
log "Aggiornando codice dal repository..."
git pull origin main

# Installa/aggiorna dipendenze PHP
log "Installando dipendenze PHP..."
composer install --optimize-autoloader --no-dev

# Installa/aggiorna dipendenze Node.js
log "Installando dipendenze Node.js..."
npm ci --production

# Build assets
log "Building assets..."
npm run build

# Ottimizza Laravel
log "Ottimizzando Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Esegui migrazioni
log "Eseguendo migrazioni database..."
php artisan migrate --force

# Aggiorna storage link
log "Aggiornando storage link..."
php artisan storage:link

# Pulisci cache
log "Pulendo cache..."
php artisan cache:clear
php artisan config:clear

# Imposta permessi
log "Impostando permessi..."
chown -R campus:www-data $APP_DIR
chmod -R 755 $APP_DIR
chmod -R 775 $APP_DIR/storage
chmod -R 775 $APP_DIR/bootstrap/cache

# Riavvia servizi
log "Riavviando servizi..."
systemctl restart php8.2-fpm
systemctl restart nginx

# Test configurazione
log "Testando configurazione..."
php artisan config:cache
nginx -t

log "✅ Deployment completato!"
log "🌐 L'applicazione è disponibile su: https://fad.campusdigitale.online"

info "📋 Comandi utili:"
info "• Logs: tail -f $APP_DIR/storage/logs/laravel.log"
info "• Queue: php artisan queue:work"
info "• Cache: php artisan cache:clear"
info "• Config: php artisan config:clear"
