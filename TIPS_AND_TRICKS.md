# School Portal - Tips & Tricks

Quick tips for working with the School Portal application.

## 🚀 Getting Started Tips

### First Run
```bash
# Make execution easier - add alias
alias portal="cd /home/stephen/Desktop/school-portal"

# Then just type: portal
```

### Quick Start
```bash
# Set up everything at once
cd /home/stephen/Desktop/school-portal && \
composer install && \
php artisan key:generate && \
php artisan migrate --seed && \
php artisan serve
```

---

## 💡 Development Tips

### View All Routes at Once
```bash
php artisan route:list
# Shows all available routes with their HTTP method
```

### Check What PHP You're Using
```bash
php --version
# Must be 8.0 or higher
```

### Access Database Directly
```bash
# Quick SQLite access
sqlite3 database.sqlite

# View all tables
.tables

# Query users
SELECT * FROM users;

# Exit
.exit
```

### Test Code Snippets
```bash
php artisan tinker
# Now you can run PHP in interactive mode
> User::all()
> User::count()
> $user = User::first()
> $user->name
> exit
```

---

## 🎨 Customization Tips

### Change Application Colors

Edit each Blade view and change these color codes:

**Dark theme colors:**
- Background: `#020617` (darkest)
- Secondary: `#0f172a` (dark blue)
- Cards: `rgba(15,23,42,.78)`
- Accent: `#22d3ee` (cyan)
- Text: `#f8fafc` (white)
- Secondary text: `#94a3b8` (gray)

**Example:**
```blade
<style>
  body {
    background: linear-gradient(135deg, #020617, #0f172a 54%, #111827 100%);
    color: #e2e8f0;
  }
  button {
    background: linear-gradient(135deg, #22d3ee, #06b6d4); /* Cyan accent */
    color: #082f49;
  }
</style>
```

### Change App Name
```bash
# Edit .env file
APP_NAME="Your School Name Here"

# Then clear config cache
php artisan config:clear
```

### Change Default Port
```bash
# Instead of :8000, use different port
php artisan serve --port=3000
```

---

## 🗂️ File Organization Tips

### Where to Add New Models
```
app/Models/MyNewModel.php
```

### Where to Add New Controllers
```
app/Http/Controllers/MyNewController.php
```

### Where to Add New Routes
```
routes/web.php
// Add inside appropriate group (trainer, student, admin)
```

### Where to Add New Views
```
resources/views/my-feature/index.blade.php
```

---

## 🔍 Debugging Tips

### Enable Debug Mode
```bash
# Edit .env
APP_DEBUG=true   # Shows error details
# Restart server to apply
```

### View Error Logs
```bash
# Real-time log viewing
tail -f storage/logs/laravel.log

# Or use pail
php artisan pail

# View last 50 lines
tail -50 storage/logs/laravel.log
```

### Check Database Errors
```bash
# Enable query logging in .env
DB_QUERY_LOG=true

# View queries in log
tail -f storage/logs/laravel.log
```

### Debug a Specific Request
```bash
# Use Tinker to test
php artisan tinker
> User::where('email', 'test@example.com')->first()
> ClassRoom::with('students')->first()
> auth()->user()->role
> exit
```

---

## 📊 Database Tips

### Backup Database
```bash
# Copy database file
cp database.sqlite database.sqlite.backup

# Or create timestamped backup
cp database.sqlite database.sqlite.$(date +%Y%m%d_%H%M%S).backup
```

### Reset Database
```bash
# WARNING: Deletes all data!
php artisan migrate:refresh --seed

# Safer: Just delete records
php artisan tinker
> HomeworkSubmission::truncate()
> exit
```

### Add Test Data
```bash
php artisan tinker

# Create a user
> $user = User::create(['name' => 'John', 'email' => 'john@test.com', 'password' => bcrypt('password'), 'role' => 'student'])

# Create a class
> $class = ClassRoom::create(['name' => 'Math 101', 'trainer_id' => 2])

# Exit
> exit
```

---

## 🔒 Security Tips

### Change Database from SQLite to MySQL
```bash
# Edit .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=school_portal
DB_USERNAME=root
DB_PASSWORD=yourpassword

# Run migrations
php artisan migrate
php artisan db:seed
```

### Create Strong Passwords
```bash
# In Tinker or code
bcrypt('YourStr0ng!Password')

# Or use artisan
php artisan tinker
> Hash::make('MyStrongPassword123!')
> exit
```

### Secure .env File
```bash
# Never commit .env to git
echo ".env" >> .gitignore

# Set proper permissions
chmod 600 .env
```

---

## 🚀 Performance Tips

### Clear All Caches
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:cache
```

### Enable Query Caching (Production)
```bash
# In .env
CACHE_DRIVER=redis
SESSION_DRIVER=redis
```

### Optimize Autoloader
```bash
# For production
composer install --optimize-autoloader --no-dev

# Dump optimized autoloader
composer dump-autoload --optimize
```

---

## 📱 Testing Tips

### Test as Different Users

**Create test users:**
```bash
php artisan tinker

# Student
> User::create(['name' => 'Student', 'email' => 'student@test.com', 'password' => bcrypt('password'), 'role' => 'student'])

# Trainer  
> User::create(['name' => 'Trainer', 'email' => 'trainer@test.com', 'password' => bcrypt('password'), 'role' => 'trainer'])

# Admin
> User::create(['name' => 'Admin', 'email' => 'admin@test.com', 'password' => bcrypt('password'), 'role' => 'admin'])

> exit
```

### Test Features Manually
1. Login as student → Submit homework
2. Login as trainer → Grade submission
3. Login as admin → Create new user
4. Check attendance tracking
5. Verify notifications created

---

## 🆘 Troubleshooting Quick Fixes

### "Command not found: php"
```bash
# Make sure you're in correct directory
cd /home/stephen/Desktop/school-portal

# Or use full path
/usr/bin/php artisan serve
```

### "Database connection refused"
```bash
# Check if database file exists
ls -la database.sqlite

# If not, recreate
php artisan migrate
```

### "Migrations already exist"
```bash
# Probably already ran once
php artisan migrate

# Should show "Nothing to migrate"
```

### "Class not found"
```bash
# Clear and regenerate autoloader
composer dump-autoload
php artisan config:clear
```

### "Permission denied"
```bash
# Fix storage permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod 777 storage/logs
```

---

## 📝 Code Snippets

### Create User in Code
```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

$user = User::create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'password' => Hash::make('password123'),
    'role' => 'student'
]);
```

### Check User Role
```php
if (auth()->user()->isTrainer()) {
    // Show trainer features
}

if (auth()->user()->hasRole('admin')) {
    // Show admin features
}
```

### Get User's Classes
```php
// Student's enrolled classes
$classes = auth()->user()->enrolledClasses()->get();

// Trainer's taught classes
$classes = auth()->user()->taughtClasses()->get();
```

### Create Notification
```php
use App\Models\Notification;

Notification::create([
    'user_id' => $studentId,
    'title' => 'Homework Graded',
    'message' => 'Your homework got 85%!',
    'type' => 'success',
    'link' => '/student/homework/' . $hwId,
]);
```

---

## 🔧 Useful Commands Reference

```bash
# Server
php artisan serve                          # Start dev server
php artisan serve --port=3000              # Custom port

# Database
php artisan migrate                         # Run migrations
php artisan migrate:refresh                 # Reset and run
php artisan db:seed                         # Seed data
php artisan tinker                          # Interactive shell

# Cache
php artisan cache:clear                     # Clear cache
php artisan config:clear                    # Clear config
php artisan view:clear                      # Clear views

# Routes
php artisan route:list                      # Show all routes
php artisan route:cache                     # Cache routes

# Generate
php artisan make:model MyModel              # Create model
php artisan make:controller MyController    # Create controller
php artisan make:migration create_table     # Create migration
php artisan make:seeder MySeeder            # Create seeder
php artisan make:middleware MyMiddleware    # Create middleware

# Composer
composer install                            # Install dependencies
composer update                             # Update packages
composer dump-autoload                      # Rebuild autoloader
```

---

## 💾 Git Tips (If Using Version Control)

### Initial Setup
```bash
cd /home/stephen/Desktop/school-portal
git init
git add .
git commit -m "Initial commit: School Portal v1.0"
```

### Never Commit
```bash
# Create .gitignore
echo ".env" >> .gitignore
echo "database.sqlite" >> .gitignore
echo "vendor/" >> .gitignore
echo "node_modules/" >> .gitignore
echo "storage/logs/" >> .gitignore
```

### Regular Commits
```bash
git add .
git commit -m "Add feature: xyz"
git push origin main
```

---

## 📚 Learning Resources

### Within the Project
- README.md - Overview and features
- ARCHITECTURE.md - System design
- API_REFERENCE.md - Route documentation
- QUICKSTART.md - Getting started

### External Resources
- Laravel Docs: https://laravel.com/docs/12
- Eloquent ORM: https://laravel.com/docs/12/eloquent
- Blade: https://laravel.com/docs/12/blade

---

## 🎓 Common Tasks

### Add a New Role
```php
// In app/Models/User.php
// Update the fillable and add method:
public function isNewRole() {
    return $this->role === 'new_role';
}
```

### Add a New Feature
1. Create migration: `php artisan make:migration add_feature`
2. Create model: `php artisan make:model Feature`
3. Create controller: `php artisan make:controller FeatureController`
4. Add routes to routes/web.php
5. Create views in resources/views/feature/

### Add Email Notifications
```bash
php artisan make:mail HomeworkGradedMail

# In controller:
Mail::to($user->email)->send(new HomeworkGradedMail($submission));
```

---

## ⚡ Performance Checklist

- [ ] Enable query caching
- [ ] Use eager loading (with())
- [ ] Paginate large lists
- [ ] Compress CSS/JS
- [ ] Use CDN for assets
- [ ] Enable HTTP caching
- [ ] Use database indexing
- [ ] Monitor error logs
- [ ] Set up monitoring
- [ ] Regular backups

---

## 🎯 Next Level Tips

### Custom Blade Components
```php
// Create component
php artisan make:component Button

// Use in views
<x-button>Click me</x-button>
```

### API Development
```php
// Add routes in routes/api.php
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function () {
        return auth()->user();
    });
});
```

### Testing
```bash
# Create test
php artisan make:test UserTest

# Run tests
php artisan test
```

---

**Happy developing! 🚀**

For more help, check the documentation files or Laravel docs: https://laravel.com/docs/12
