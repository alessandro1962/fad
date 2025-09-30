#!/bin/bash

# Script per copiare il database di produzione in locale
# Campus Digitale Forma - FAD

set -e

# Colori per output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configurazione server di produzione
PROD_SERVER="167.71.152.69"
PROD_USER="root"
PROD_PASSWORD="ALF1123pmp!6665b5mhyn"
PROD_DB_NAME="campus_digitale_fad"
PROD_DB_USER="campus_fad"
PROD_DB_PASSWORD="CampusFAD2024!"

# Configurazione locale
LOCAL_DB_NAME="campus_digitale_fad_local"
LOCAL_DB_USER="root"
LOCAL_DB_PASSWORD=""

# Directory di backup
BACKUP_DIR="./backups"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
BACKUP_FILE="${BACKUP_DIR}/campus_digitale_fad_${TIMESTAMP}.sql"

echo -e "${BLUE}🚀 Backup Database di Produzione - Campus Digitale Forma${NC}"
echo -e "${BLUE}================================================${NC}"

# Crea directory backup se non esiste
mkdir -p "$BACKUP_DIR"

echo -e "${YELLOW}📋 Configurazione:${NC}"
echo -e "   Server: ${PROD_SERVER}"
echo -e "   Database: ${PROD_DB_NAME}"
echo -e "   Backup locale: ${BACKUP_FILE}"
echo ""

# Funzione per eseguire comandi SSH
run_ssh() {
    sshpass -p "$PROD_PASSWORD" ssh -o StrictHostKeyChecking=no "$PROD_USER@$PROD_SERVER" "$@"
}

# Funzione per eseguire comandi SCP
run_scp() {
    sshpass -p "$PROD_PASSWORD" scp -o StrictHostKeyChecking=no "$@"
}

echo -e "${YELLOW}🔍 Verificando connessione al server...${NC}"
if ! run_ssh "echo 'Connessione OK'"; then
    echo -e "${RED}❌ Errore: Impossibile connettersi al server${NC}"
    exit 1
fi
echo -e "${GREEN}✅ Connessione al server stabilita${NC}"

echo -e "${YELLOW}📊 Creando dump del database di produzione...${NC}"
run_ssh "mysqldump -u $PROD_DB_USER -p$PROD_DB_PASSWORD --single-transaction --routines --triggers $PROD_DB_NAME > /tmp/campus_digitale_fad_backup.sql"

echo -e "${YELLOW}📥 Scaricando il dump in locale...${NC}"
run_scp "$PROD_USER@$PROD_SERVER:/tmp/campus_digitale_fad_backup.sql" "$BACKUP_FILE"

echo -e "${YELLOW}🧹 Pulizia file temporanei sul server...${NC}"
run_ssh "rm -f /tmp/campus_digitale_fad_backup.sql"

echo -e "${YELLOW}🗄️ Creando database locale se non esiste...${NC}"
mysql -u "$LOCAL_DB_USER" -p"$LOCAL_DB_PASSWORD" -e "CREATE DATABASE IF NOT EXISTS $LOCAL_DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null || {
    echo -e "${YELLOW}⚠️  Database locale non creato (potrebbe già esistere)${NC}"
}

echo -e "${YELLOW}📥 Importando il database in locale...${NC}"
mysql -u "$LOCAL_DB_USER" -p"$LOCAL_DB_PASSWORD" "$LOCAL_DB_NAME" < "$BACKUP_FILE"

echo -e "${YELLOW}🔧 Aggiornando configurazione locale...${NC}"

# Crea file .env locale per il database
cat > .env.local << EOF
APP_NAME="Campus Digitale Forma (Local)"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=$LOCAL_DB_NAME
DB_USERNAME=$LOCAL_DB_USER
DB_PASSWORD=$LOCAL_DB_PASSWORD

CACHE_DRIVER=file
QUEUE_CONNECTION=sync

MAIL_MAILER=log
MAIL_FROM_ADDRESS="noreply@localhost"
MAIL_FROM_NAME="Campus Digitale Forma (Local)"
EOF

echo -e "${GREEN}✅ Backup completato con successo!${NC}"
echo ""
echo -e "${BLUE}📋 Riepilogo:${NC}"
echo -e "   📁 File backup: ${BACKUP_FILE}"
echo -e "   🗄️  Database locale: ${LOCAL_DB_NAME}"
echo -e "   ⚙️  Configurazione: .env.local"
echo ""
echo -e "${YELLOW}🔧 Prossimi passi:${NC}"
echo -e "   1. Copia .env.local in .env: ${BLUE}cp .env.local .env${NC}"
echo -e "   2. Genera APP_KEY: ${BLUE}php artisan key:generate${NC}"
echo -e "   3. Avvia il server: ${BLUE}php artisan serve${NC}"
echo -e "   4. Compila frontend: ${BLUE}npm run dev${NC}"
echo ""
echo -e "${GREEN}🎉 Il database di produzione è ora disponibile in locale!${NC}"
