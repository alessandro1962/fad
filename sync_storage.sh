#!/bin/bash

# Script per sincronizzare i file storage dal server di produzione
# Campus Digitale Forma - FAD

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
PROD_PATH="/var/www/campus-digitale-fad/storage/app/public"
LOCAL_PATH="./storage/app/public"

echo -e "${BLUE}🚀 Sincronizzazione Storage - Campus Digitale Forma${NC}"
echo -e "${BLUE}==============================================${NC}"

echo -e "${YELLOW}📋 Configurazione:${NC}"
echo -e "   Server: ${PROD_SERVER}"
echo -e "   Path remoto: ${PROD_PATH}"
echo -e "   Path locale: ${LOCAL_PATH}"
echo ""

# Crea directory locale se non esiste
mkdir -p "$LOCAL_PATH"

echo -e "${YELLOW}🔍 Connessione SSH al server...${NC}"
echo -e "${BLUE}Password server: ALF1123pmp!6665b5mhyn${NC}"
echo ""

echo -e "${YELLOW}📥 Sincronizzando file storage...${NC}"
rsync -avz --progress "$PROD_USER@$PROD_SERVER:$PROD_PATH/" "$LOCAL_PATH/"

echo -e "${GREEN}✅ Sincronizzazione completata!${NC}"
echo ""
echo -e "${BLUE}📋 File sincronizzati in: ${LOCAL_PATH}${NC}"
echo ""
echo -e "${YELLOW}🔧 Prossimi passi:${NC}"
echo -e "   1. ${BLUE}php artisan storage:link${NC} (se non già fatto)"
echo -e "   2. Verifica che i file siano accessibili"
echo ""
echo -e "${GREEN}🎉 Storage sincronizzato!${NC}"
