#!/bin/bash

# Deploy script for DigitalOcean production server
# This script pulls the latest changes and rebuilds the application

echo "🚀 Starting deployment to DigitalOcean production server..."

# Server configuration
SSH_USER="root"
SSH_HOST="167.71.152.69"
SSH_PASSWORD="ALF1123pmp!6665b5mhyn"
PROJECT_DIR="/var/www/campus-digitale-fad"

echo "📡 Connecting to production server..."

# Connect to server and run deployment commands
sshpass -p "$SSH_PASSWORD" ssh -o StrictHostKeyChecking=no "$SSH_USER@$SSH_HOST" << 'EOF'
    echo "📂 Navigating to project directory..."
    cd /var/www/campus-digitale-fad
    
    echo "🔄 Pulling latest changes from Git..."
    git pull origin main
    
    echo "📦 Installing/updating Composer dependencies..."
    composer install --no-dev --optimize-autoloader
    
    echo "📦 Installing/updating NPM dependencies..."
    npm ci
    
    echo "🔨 Building frontend assets..."
    npm run build
    
    echo "🗄️ Running database migrations..."
    php artisan migrate --force
    
    echo "🔄 Clearing application cache..."
    php artisan config:clear
    php artisan cache:clear
    php artisan route:clear
    php artisan view:clear
    
    echo "⚙️ Optimizing application..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    
    echo "🔐 Setting proper permissions..."
    chown -R www-data:www-data storage bootstrap/cache
    chmod -R 775 storage bootstrap/cache
    
    echo "🔄 Restarting services..."
    systemctl reload nginx
    systemctl restart php8.2-fpm
    
    echo "✅ Deployment completed successfully!"
EOF

if [ $? -eq 0 ]; then
    echo "🎉 Deployment to DigitalOcean completed successfully!"
    echo "🌐 Your application is now live at: https://fad.campusdigitale.online"
else
    echo "❌ Deployment failed. Please check the logs above."
    exit 1
fi
