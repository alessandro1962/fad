#!/bin/bash

# Script per fare backup del database direttamente dal server DigitalOcean
# Campus Digitale Forma - FAD

set -e

# Colori per output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configurazione database di produzione
DB_NAME="campus_digitale_fad"
DB_USER="campus_fad"
DB_PASSWORD="CampusFAD2024!"

# Directory di backup
BACKUP_DIR="./backups"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
BACKUP_FILE="${BACKUP_DIR}/campus_digitale_fad_${TIMESTAMP}.sql"

echo -e "${BLUE}🚀 Backup Database di Produzione - Campus Digitale Forma${NC}"
echo -e "${BLUE}================================================${NC}"

# Crea directory backup se non esiste
mkdir -p "$BACKUP_DIR"

echo -e "${YELLOW}📋 Configurazione:${NC}"
echo -e "   Database: ${DB_NAME}"
echo -e "   User: ${DB_USER}"
echo -e "   Backup file: ${BACKUP_FILE}"
echo ""

echo -e "${YELLOW}📊 Creando dump del database...${NC}"
mysqldump -u "$DB_USER" -p"$DB_PASSWORD" \
    --single-transaction \
    --routines \
    --triggers \
    --complete-insert \
    --extended-insert \
    "$DB_NAME" > "$BACKUP_FILE"

echo -e "${YELLOW}📁 Creando archivio compresso...${NC}"
gzip "$BACKUP_FILE"
BACKUP_FILE="${BACKUP_FILE}.gz"

echo -e "${YELLOW}📊 Informazioni backup:${NC}"
ls -lh "$BACKUP_FILE"

echo -e "${GREEN}✅ Backup completato!${NC}"
echo ""
echo -e "${BLUE}📋 Riepilogo:${NC}"
echo -e "   📁 File backup: ${BACKUP_FILE}"
echo -e "   📊 Dimensione: $(du -h "$BACKUP_FILE" | cut -f1)"
echo ""
echo -e "${YELLOW}🔧 Prossimi passi:${NC}"
echo -e "   1. Scarica il file: ${BLUE}scp root@167.71.152.69:$(pwd)/${BACKUP_FILE} .${NC}"
echo -e "   2. Decomprimi: ${BLUE}gunzip ${BACKUP_FILE}${NC}"
echo -e "   3. Importa in locale: ${BLUE}mysql -u root -p database_locale < ${BACKUP_FILE%.gz}${NC}"
echo ""
echo -e "${GREEN}🎉 Backup del database di produzione completato!${NC}"
