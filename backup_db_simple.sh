#!/bin/bash

# Script semplificato per copiare il database di produzione
# Usa SSH diretto (senza sshpass)

set -e

# Colori per output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configurazione
PROD_SERVER="167.71.152.69"
PROD_USER="root"
PROD_DB_NAME="campus_digitale_fad"
PROD_DB_USER="campus_fad"
PROD_DB_PASSWORD="CampusFAD2024!"

LOCAL_DB_NAME="campus_digitale_fad_local"
LOCAL_DB_USER="root"
LOCAL_DB_PASSWORD=""

TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
BACKUP_FILE="campus_digitale_fad_${TIMESTAMP}.sql"

echo -e "${BLUE}🚀 Backup Database di Produzione - Campus Digitale Forma${NC}"
echo -e "${BLUE}================================================${NC}"

echo -e "${YELLOW}📋 Configurazione:${NC}"
echo -e "   Server: ${PROD_SERVER}"
echo -e "   Database: ${PROD_DB_NAME}"
echo -e "   Backup locale: ${BACKUP_FILE}"
echo ""

echo -e "${YELLOW}🔍 Connessione SSH al server...${NC}"
echo -e "${BLUE}Password server: ALF1123pmp!6665b5mhyn${NC}"
echo ""

# Crea dump sul server e scarica direttamente
echo -e "${YELLOW}📊 Creando dump e scaricando...${NC}"
ssh "$PROD_USER@$PROD_SERVER" "mysqldump -u $PROD_DB_USER -p$PROD_DB_PASSWORD --single-transaction --routines --triggers $PROD_DB_NAME" > "$BACKUP_FILE"

echo -e "${YELLOW}🗄️ Creando database locale...${NC}"
mysql -u "$LOCAL_DB_USER" -p"$LOCAL_DB_PASSWORD" -e "DROP DATABASE IF EXISTS $LOCAL_DB_NAME; CREATE DATABASE $LOCAL_DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null || {
    echo -e "${YELLOW}⚠️  Errore nella creazione del database locale${NC}"
    echo -e "${BLUE}Prova manualmente: mysql -u root -p -e \"CREATE DATABASE $LOCAL_DB_NAME;\"${NC}"
}

echo -e "${YELLOW}📥 Importando database...${NC}"
mysql -u "$LOCAL_DB_USER" -p"$LOCAL_DB_PASSWORD" "$LOCAL_DB_NAME" < "$BACKUP_FILE"

echo -e "${YELLOW}🔧 Creando configurazione locale...${NC}"
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

echo -e "${GREEN}✅ Backup completato!${NC}"
echo ""
echo -e "${BLUE}📋 Riepilogo:${NC}"
echo -e "   📁 File backup: ${BACKUP_FILE}"
echo -e "   🗄️  Database locale: ${LOCAL_DB_NAME}"
echo -e "   ⚙️  Configurazione: .env.local"
echo ""
echo -e "${YELLOW}🔧 Prossimi passi:${NC}"
echo -e "   1. ${BLUE}cp .env.local .env${NC}"
echo -e "   2. ${BLUE}php artisan key:generate${NC}"
echo -e "   3. ${BLUE}php artisan serve${NC}"
echo -e "   4. ${BLUE}npm run dev${NC}"
echo ""
echo -e "${GREEN}🎉 Database di produzione copiato in locale!${NC}"
