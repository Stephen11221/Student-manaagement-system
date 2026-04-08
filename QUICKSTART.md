# School Portal - Quick Start Guide

Welcome to the School Portal! This guide will get you up and running in 5 minutes.

## 📋 Prerequisites

- PHP 8.0 or higher
- Composer (dependency manager for PHP)
- Git (optional, for cloning)

**Check if you have these:**
```bash
php --version          # Should show 8.0+
composer --version     # Should show version info
```

## 🚀 Installation (5 minutes)

### Step 1: Navigate to Project
```bash
cd /home/stephen/Desktop/school-portal
```

### Step 2: Install Dependencies
```bash
composer install
```
*This may take 1-2 minutes on first run.*

### Step 3: Setup Environment (One-time)
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run database migrations
php artisan migrate

# Seed initial roles
php artisan db:seed
```

**Expected output:**
```
Generating APP_KEY: Done
Migration table created successfully
Migrating: 2024_01_01_000000_create_users_table
... (should see ✓ DONE for each migration)

Seeding database
RoleSeeder ........................ 1,234 ms DONE
```

### Step 4: Start the Server
```bash
php artisan serve
```

**Expected output:**
```
Starting Laravel development server: http://127.0.0.1:8000
```

### Step 5: Open in Browser
Visit: **http://localhost:8000**

---

## 👤 First Login

### Option 1: Register a New Account
1. Click "Register" on the welcome page
2. Fill in your details
3. Choose a password (min 8 characters)
4. Click "Register"
5. You'll be auto-logged in as a Student

### Option 2: Create Account via Admin Panel (After registration)
1. Register first as any user
2. Ask a developer to promote you to Admin (updating database)
3. Then use admin features

---

## 🎯 What You Can Do

### As a Student
✅ View your timetable  
✅ See assignments  
✅ Submit homework (written or file)  
✅ Track your attendance  
✅ View grades  

### As a Trainer
✅ Create classes  
✅ Manage class timetables  
✅ Assign homework  
✅ Grade student submissions  
✅ Mark attendance  

### As an Admin
✅ Manage all users  
✅ Create/edit/delete accounts  
✅ Assign user roles  
✅ View system statistics  

---

## 📁 Project Structure

```
│
├── app/Models/        → Database models
├── routes/web.php     → Application routes
├── resources/views/   → HTML templates
├── database/          → Migrations & seeders
├── public/            → Public assets
└── storage/           → File uploads & logs
```

---

## 🔧 Common Commands

### View all routes
```bash
php artisan route:list
```

### Clear cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Reset database (WARNING: Deletes all data!)
```bash
php artisan migrate:refresh --seed
```

### Check database
```bash
# View SQLite database
sqlite3 database.sqlite
```

---

## 🐛 Troubleshooting

### "Port 8000 already in use"
```bash
# Use a different port
php artisan serve --port=8001
```

### "Migrations failed"
```bash
# Check database file exists
ls -la database.sqlite

# Reset and try again
php artisan migrate:refresh --seed
```

### "Files won't upload"
```bash
# Fix permissions
chmod -R 755 storage public/storage
```

### "Can't login"
```bash
# Clear sessions
php artisan cache:clear
# Re-register or check email/password
```

---

## 📊 Database Overview

**11 Tables:**
- users (accounts)
- roles (role definitions)
- class_rooms (classes)
- timetables (schedules)
- homework (assignments)
- homework_submissions (submissions)
- attendance (attendance records)
- notifications (alerts)
- And more...

**View database:**
```bash
sqlite3 database.sqlite
sqlite> .tables
sqlite> SELECT * FROM users;
sqlite> .exit
```

---

## 🎨 Customization

### Change App Name
Edit `.env`:
```
APP_NAME="My School Portal"
```

### Change Database
Edit `.env`:
```
# Use MySQL instead of SQLite
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=school_portal
DB_USERNAME=root
DB_PASSWORD=
```

### Change Port
```bash
php artisan serve --port=3000
```

---

## 📚 Full Documentation

For detailed information, see:
- **README.md** - Complete overview
- **ARCHITECTURE.md** - System design
- **API_REFERENCE.md** - All endpoints
- **IMPLEMENTATION_SUMMARY.md** - What's been built

---

## 🛠️ Development Tips

### Auto-reload on file changes
Install Laravel Pail:
```bash
composer require laravel/pail --dev
php artisan pail
```

### SQL Query logging
Edit `.env`:
```
DB_QUERY_LOG=true
```

### Debug mode
Edit `.env`:
```
APP_DEBUG=true     # Shows errors in browser
APP_DEBUG=false    # Production setting
```

---

## 🔒 Security Reminders

✅ Change password regularly  
✅ Don't share login credentials  
✅ Use strong passwords  
✅ Keep `.env` file secure  
✅ Don't commit `.env` to git  

---

## 🚀 Next Steps

1. **Register an account** - Try the system
2. **Create test data** - Add classes, assignments
3. **Invite others** - Share login info
4. **Customize** - Add your school details
5. **Deploy** - Set up on a server

---

## 📞 Getting Help

### Check Logs
```bash
tail -f storage/logs/laravel.log
```

### Debug Console
```bash
php artisan tinker
# Then type PHP commands
> User::all()
> exit
```

### Laravel Documentation
https://laravel.com/docs/12

---

## ⏱️ Quick Checklist

- [ ] PHP 8.0+ installed
- [ ] Composer installed
- [ ] Navigated to project folder
- [ ] Ran `composer install`
- [ ] Ran `php artisan key:generate`
- [ ] Ran `php artisan migrate`
- [ ] Ran `php artisan db:seed`
- [ ] Started server with `php artisan serve`
- [ ] Opened http://localhost:8000
- [ ] Registered test account
- [ ] Logged in successfully

**All done? You're ready to use the School Portal! 🎉**

---

**Questions?** Check the README.md or ARCHITECTURE.md files for more details.
