#!/bin/bash

# Campus Digitale FAD - Server Setup Script
# Per Ubuntu 22.04 LTS

set -e

echo "🚀 Iniziando setup del server per Campus Digitale FAD..."

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

# Aggiorna sistema
log "Aggiornando sistema..."
apt update && apt upgrade -y

# Installa pacchetti base
log "Installando pacchetti base..."
apt install -y curl wget git unzip software-properties-common apt-transport-https ca-certificates gnupg lsb-release

# Installa PHP 8.2
log "Installando PHP 8.2..."
add-apt-repository ppa:ondrej/php -y
apt update
apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-xml php8.2-gd php8.2-curl php8.2-mbstring php8.2-zip php8.2-bcmath php8.2-intl php8.2-redis php8.2-cli

# Installa Composer
log "Installando Composer..."
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
chmod +x /usr/local/bin/composer

# Installa Node.js 18
log "Installando Node.js 18..."
curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
apt install -y nodejs

# Installa MySQL 8
log "Installando MySQL 8..."
apt install -y mysql-server
systemctl start mysql
systemctl enable mysql

# Configura MySQL
log "Configurando MySQL..."
mysql -e "CREATE DATABASE campus_digitale_fad CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -e "CREATE USER 'campus_fad'@'localhost' IDENTIFIED BY 'CampusFAD2024!';"
mysql -e "GRANT ALL PRIVILEGES ON campus_digitale_fad.* TO 'campus_fad'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"

# Installa Redis
log "Installando Redis..."
apt install -y redis-server
systemctl start redis-server
systemctl enable redis-server

# Installa Nginx
log "Installando Nginx..."
apt install -y nginx
systemctl start nginx
systemctl enable nginx

# Installa Certbot per SSL
log "Installando Certbot..."
apt install -y certbot python3-certbot-nginx

# Crea utente per l'applicazione
log "Creando utente applicazione..."
useradd -m -s /bin/bash campus
usermod -aG www-data campus

# Crea directory per l'applicazione
log "Creando directory applicazione..."
mkdir -p /var/www/campus-digitale-fad
chown -R campus:www-data /var/www/campus-digitale-fad
chmod -R 755 /var/www/campus-digitale-fad

# Configura PHP-FPM
log "Configurando PHP-FPM..."
sed -i 's/user = www-data/user = campus/g' /etc/php/8.2/fpm/pool.d/www.conf
sed -i 's/group = www-data/group = www-data/g' /etc/php/8.2/fpm/pool.d/www.conf
sed -i 's/listen.owner = www-data/listen.owner = campus/g' /etc/php/8.2/fpm/pool.d/www.conf
sed -i 's/listen.group = www-data/listen.group = www-data/g' /etc/php/8.2/fpm/pool.d/www.conf

# Configura PHP
log "Configurando PHP..."
sed -i 's/upload_max_filesize = 2M/upload_max_filesize = 100M/g' /etc/php/8.2/fpm/php.ini
sed -i 's/post_max_size = 8M/post_max_size = 100M/g' /etc/php/8.2/fpm/php.ini
sed -i 's/max_execution_time = 30/max_execution_time = 300/g' /etc/php/8.2/fpm/php.ini
sed -i 's/memory_limit = 128M/memory_limit = 512M/g' /etc/php/8.2/fpm/php.ini

# Riavvia PHP-FPM
systemctl restart php8.2-fpm

# Configura Nginx
log "Configurando Nginx..."
cat > /etc/nginx/sites-available/campus-digitale-fad << 'EOF'
server {
    listen 80;
    server_name fad.campusdigitale.online;
    root /var/www/campus-digitale-fad/public;
    index index.php index.html;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_proxied expired no-cache no-store private must-revalidate auth;
    gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/xml+rss application/javascript;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Handle Laravel storage
    location /storage {
        alias /var/www/campus-digitale-fad/storage/app/public;
        try_files $uri $uri/ =404;
    }

    # Cache static assets
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|woff|woff2|ttf|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
EOF

# Abilita il sito
ln -sf /etc/nginx/sites-available/campus-digitale-fad /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default

# Test configurazione Nginx
nginx -t

# Riavvia Nginx
systemctl restart nginx

# Configura firewall
log "Configurando firewall..."
ufw allow OpenSSH
ufw allow 'Nginx Full'
ufw --force enable

# Installa Supervisor per code
log "Installando Supervisor..."
apt install -y supervisor

# Configura cron per Laravel
log "Configurando cron..."
(crontab -u campus -l 2>/dev/null; echo "* * * * * cd /var/www/campus-digitale-fad && php artisan schedule:run >> /dev/null 2>&1") | crontab -u campus -

log "✅ Setup server completato!"
log "📋 Prossimi passi:"
log "1. Clona il repository: git clone https://github.com/alessandro1962/fad.git /var/www/campus-digitale-fad"
log "2. Configura .env per produzione"
log "3. Installa dipendenze: composer install && npm install"
log "4. Esegui migrazioni: php artisan migrate"
log "5. Configura SSL: certbot --nginx -d fad.campusdigitale.online"

info "🔐 Credenziali database:"
info "Database: campus_digitale_fad"
info "User: campus_fad"
info "Password: CampusFAD2024!"

info "🌐 Il server è pronto per il deployment!"
