#!/bin/bash

# School Portal - Quick Setup Script
# This script sets up the Laravel application for first-time use

echo "🎓 School Portal - Setup Script"
echo "================================"
echo ""

# Check if composer dependencies are installed
if [ ! -d "vendor" ]; then
    echo "📦 Installing Composer dependencies..."
    composer install
    echo "✅ Dependencies installed"
else
    echo "✅ Dependencies already installed"
fi

echo ""
echo "🔧 Setting up environment..."

# Copy .env file if it doesn't exist
if [ ! -f ".env" ]; then
    echo "📝 Creating .env file..."
    cp .env.example .env
    echo "✅ .env created"
else
    echo "✅ .env already exists"
fi

# Generate application key
if ! grep -q "APP_KEY=" .env || grep -q "APP_KEY=$" .env; then
    echo "🔑 Generating application key..."
    php artisan key:generate
    echo "✅ Key generated"
else
    echo "✅ Application key already set"
fi

echo ""
echo "🗄️  Setting up database..."

# Run migrations
echo "📊 Running migrations..."
php artisan migrate --force
echo "✅ Migrations completed"

echo ""
echo "🌱 Seeding database with initial roles..."
php artisan db:seed
echo "✅ Database seeded"

echo ""
echo "📁 Setting file permissions..."
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod -R 777 storage/logs
chmod -R 777 public/storage
echo "✅ Permissions set"

echo ""
echo "✨ Setup completed successfully!"
echo ""
echo "🚀 To start the development server, run:"
echo "   cd $(pwd)"
echo "   php artisan serve"
echo ""
echo "📱 Then visit: http://localhost:8000"
echo ""
echo "📚 Default test accounts:"
echo "   • Email: Create accounts via registration or admin panel"
echo "   • Roles: Student, Trainer, Admin, Department Admin, Career Coach"
echo ""
