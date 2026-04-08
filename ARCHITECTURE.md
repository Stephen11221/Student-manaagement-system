# School Portal - Architecture Guide

## System Overview

The School Portal is built using **Laravel 12** with a **role-based access control (RBAC)** architecture. The application follows the Model-View-Controller (MVC) pattern with middleware for authentication and authorization.

```
┌─────────────────────────────────────────────────────────────┐
│                    CLIENT (Browser)                         │
└────────────┬────────────────────────────────────┬───────────┘
             │                                    │
    ┌────────▼────────┐              ┌──────────▼─────────┐
    │   Public Routes │              │  Protected Routes  │
    │  (Register/     │              │  (Authenticated)   │
    │   Login)        │              │                    │
    └────────┬────────┘              └──────────┬─────────┘
             │                                   │
             └─────────────────┬─────────────────┘
                               │
                ┌──────────────▼────────────────┐
                │   Laravel Router (web.php)    │
                │   • Route Matching            │
                │   • Middleware Pipeline       │
                │   • Controller Dispatch       │
                └──────────────┬─────────────────┘
                               │
         ┌─────────────────────┼─────────────────────┐
         │                     │                     │
    ┌────▼────┐          ┌─────▼────┐         ┌────▼─────┐
    │ Auth    │          │ Role     │         │ View      │
    │ Midware │          │ Middleware│        │ Rendering │
    └────┬────┘          └─────┬────┘         └────┬──────┘
         │                     │                    │
    ┌────▼──────────────────────▼────────────────────▼───────┐
    │           Controllers                                  │
    │  • StudentController                                   │
    │  • TrainerController                                   │
    │  • AdminController                                     │
    │  • ClassRoomController                                 │
    │  • HomeworkController                                  │
    └────┬──────────────────────┬────────────────────────────┘
         │                      │
    ┌────▼──────┐          ┌────▼──────────┐
    │Models     │          │  Business     │
    │(Eloquent) │          │  Logic        │
    │           │          │  • Queries    │
    │ • User    │          │  • Validation │
    │ • Class   │          │  • Notifications
    │ • etc     │          │                │
    └────┬──────┘          └────┬───────────┘
         │                      │
         └──────────────┬───────┘
                        │
            ┌───────────▼──────────────┐
            │   SQLite Database        │
            │                          │
            │ Tables:                  │
            │ • users                  │
            │ • class_rooms            │
            │ • homework               │
            │ • homework_submissions   │
            │ • attendance             │
            │ • etc (11 total)         │
            └──────────────────────────┘
```

## Authentication Flow

```
User Registration/Login
    │
    ├→ POST /register or POST /login
    │
    ├→ Input Validation
    │
    ├→ Database Query
    │   └→ User::where('email', $email)->first()
    │
    ├→ Password Verification
    │   └→ Hash::check($password, $user->password)
    │
    ├→ Session Created
    │   └→ Auth::login($user)
    │
    └→ Redirect to /dashboard
      └→ Route decides view based on $user->role
```

## Role-Based Routing

```
/dashboard
    │
    ├─ Student    → dashboard.student
    ├─ Trainer    → dashboard.trainer
    ├─ Admin      → dashboard.admin
    └─ Other      → dashboard.default
```

## Data Flow Example: Student Submitting Homework

```
1. Student navigates to /student/homework/{id}/submit
                        │
                        ├→ Route matches
                        ├→ Auth middleware checks login
                        ├→ Controller renders form view
                        │
2. Student fills form & submits
                        │
                        ├→ POST /student/homework/{id}/submit
                        ├→ Input validation in controller
                        ├→ File upload if needed
                        │   └→ File stored in storage/public/homework/
                        │
3. HomeworkSubmission record created
                        │
                        ├→ $submission->create([...])
                        ├→ Data written to database
                        ├→ Timestamp recorded (submitted_at)
                        │
4. Response sent
                        │
                        └→ Redirect with success message
                           /student/homework?status=submitted
```

## Database Schema Relationships

```
users
 ├─ trainer → class_rooms (one-to-many)
 ├─ student → class_student → classrooms (many-to-many)
 ├─ homeworks (trainer created)
 ├─ homework_submissions (student submissions)
 ├─ notifications
 ├─ attendance_records
 └─ roles (many-to-many via role_user)

class_rooms
 ├─ trainer → users
 ├─ students → class_student → users (many-to-many)
 ├─ timetables (one-to-many)
 ├─ homeworks (one-to-many)
 └─ attendance (one-to-many)

homework
 ├─ class → class_rooms
 ├─ trainer → users
 └─ submissions → homework_submissions

homework_submissions
 ├─ homework
 ├─ student → users
 ├─ marks (nullable, filled by trainer)
 └─ submitted_at (timestamp)

attendance
 ├─ class_room
 ├─ student → users
 ├─ timetable
 ├─ status (present/absent/late)
 └─ marked_at

notifications
 ├─ user
 ├─ title, message
 ├─ type (info/warning/success/error)
 └─ read (boolean)
```

## Model Relationships Code Example

```php
// User Model
class User extends Model {
    // One trainer can teach many classes
    public function taughtClasses() {
        return $this->hasMany(ClassRoom::class, 'trainer_id');
    }
    
    // Many students in many classes
    public function enrolledClasses() {
        return $this->belongsToMany(ClassRoom::class, 'class_student');
    }
    
    // Student submissions
    public function homeworkSubmissions() {
        return $this->hasMany(HomeworkSubmission::class, 'student_id');
    }
    
    // Attendance records
    public function attendanceRecords() {
        return $this->hasMany(Attendance::class, 'student_id');
    }
}

// ClassRoom Model
class ClassRoom extends Model {
    // Belongs to one trainer
    public function trainer() {
        return $this->belongsTo(User::class, 'trainer_id');
    }
    
    // Many students through pivot
    public function students() {
        return $this->belongsToMany(User::class, 'class_student');
    }
    
    // Timetable entries
    public function timetables() {
        return $this->hasMany(Timetable::class, 'class_id');
    }
}
```

## Middleware Stack

```
Route Request
    │
    ├→ 1. Web Middleware Group
    │     • EncryptCookies
    │     • AddQueuedCookiesToResponse
    │     • StartSession
    │     • VerifyCsrfToken
    │     • SubstituteBindings
    │
    ├→ 2. Auth Middleware (if required)
    │     • Redirects to /login if not authenticated
    │
    ├→ 3. Role Middleware (if required)
    │     • CheckRole(role1, role2, ...)
    │     • Abort 403 if role doesn't match
    │
    └→ 4. Route Handler
         • Controller method executes
         • View rendered or redirect
```

## File Organization

```
school-portal/
│
├── app/
│   ├── Models/                    (Database models - 8 files)
│   │   ├── User.php
│   │   ├── ClassRoom.php
│   │   ├── Timetable.php
│   │   ├── Homework.php
│   │   ├── HomeworkSubmission.php
│   │   ├── Attendance.php
│   │   ├── Notification.php
│   │   └── Role.php
│   │
│   └── Http/
│       ├── Controllers/           (Business logic - 5 files)
│       │   ├── TrainerController.php
│       │   ├── StudentController.php
│       │   ├── AdminController.php
│       │   ├── ClassRoomController.php
│       │   └── HomeworkController.php
│       │
│       └── Middleware/            (Access control - 1 file)
│           └── CheckRole.php
│
├── database/
│   ├── migrations/                (10 files - schema)
│   └── seeders/                   (1 file - initial data)
│
├── resources/views/
│   ├── welcome.blade.php          (Landing)
│   ├── register.blade.php         (Registration)
│   │
│   ├── dashboard/                 (Role-specific dashboards)
│   │   ├── student.blade.php
│   │   ├── trainer.blade.php
│   │   ├── admin.blade.php
│   │   └── career_coach.blade.php
│   │
│   ├── student/                   (Student features)
│   │   ├── timetable/index.blade.php
│   │   ├── homework/
│   │   │   ├── index.blade.php
│   │   │   └── submit.blade.php
│   │   └── attendance/index.blade.php
│   │
│   ├── trainer/                   (Trainer features)
│   │   ├── classes/
│   │   │   ├── index.blade.php
│   │   │   ├── create.blade.php
│   │   │   └── edit.blade.php
│   │   ├── timetable/index.blade.php
│   │   └── homework/
│   │       ├── index.blade.php
│   │       └── submissions.blade.php
│   │
│   └── admin/                     (Admin features)
│       ├── users/
│       │   ├── index.blade.php
│       │   ├── create.blade.php
│       │   └── edit.blade.php
│       └── classes/index.blade.php
│
├── routes/
│   └── web.php                    (All routes - 40+ routes)
│
├── public/
│   └── storage/                   (File uploads - homework)
│
├── database.sqlite                (SQLite database file)
├── .env                           (Environment config)
├── composer.json                  (PHP dependencies)
└── README.md                      (Documentation)
```

## Adding New Features - Template

### 1. Create Migration
```bash
php artisan make:migration create_features_table
```

### 2. Create Model
```bash
php artisan make:model Feature
```

### 3. Add Relationships
```php
// In Model
public function user() {
    return $this->belongsTo(User::class);
}
```

### 4. Create Controller
```bash
php artisan make:controller FeatureController
```

### 5. Add Routes
```php
Route::prefix('feature')->group(function () {
    Route::get('/', [FeatureController::class, 'index']);
    // ... more routes
});
```

### 6. Create Views
```
resources/views/feature/index.blade.php
resources/views/feature/show.blade.php
```

## Error Handling

```
Exception Thrown
    │
    ├→ Authentication Error (401)
    │  └→ Redirect to /login
    │
    ├→ Authorization Error (403)
    │  └→ Show "Unauthorized" message
    │
    ├→ Validation Error
    │  └→ Back with errors highlighted
    │
    ├→ Not Found (404)
    │  └→ Show 404 view
    │
    └→ Server Error (500)
       └→ Show error view (log details)
```

## Performance Considerations

### Eager Loading (N+1 Prevention)
```php
// ❌ Bad - 11 queries
$users = User::all();
foreach ($users as $user) {
    echo $user->taughtClasses; // Extra query each loop
}

// ✅ Good - 1 query
$users = User::with('taughtClasses')->get();
```

### Pagination
```php
$users = User::paginate(15); // Limit queries with pagination
```

### Indexing
- All foreign keys indexed
- Role column indexed for fast filtering
- Student/class queries optimized via pivot table

## Security Architecture

```
Input
  │
  ├→ CSRF Protection (VerifyCsrfToken)
  ├→ SQL Injection Prevention (Eloquent ORM)
  ├→ XSS Protection (Blade escaping)
  ├→ Input Validation (Laravel Validator)
  │
  └→ Database
     │
     ├→ Password Hashing (bcrypt)
     ├→ Row-level Access Control
     ├→ Foreign Key Constraints
     └→ Unique Constraints
```

## Deployment Checklist

- [ ] Set `APP_DEBUG=false` in .env
- [ ] Generate new APP_KEY
- [ ] Configure database (MySQL)
- [ ] Set up file permissions
- [ ] Configure storage symlink
- [ ] Set up email (for notifications)
- [ ] Configure SSL/HTTPS
- [ ] Set up caching (Redis)
- [ ] Configure logging
- [ ] Set up backup strategy
- [ ] Monitor application logs

## Development Workflow

```
1. Create Feature Branch
   git checkout -b feature/my-feature

2. Create Migration
   php artisan make:migration add_feature

3. Create Model/Controller
   php artisan make:model MyModel
   php artisan make:controller MyController

4. Run Migration
   php artisan migrate

5. Implement Feature
   • Write business logic in controller
   • Create Blade templates
   • Add routes

6. Test Feature
   php artisan serve
   Manual testing in browser

7. Commit & Push
   git add .
   git commit -m "Add feature: my-feature"
   git push origin feature/my-feature

8. Merge to Main
   Create Pull Request
   Review & Merge
```

---

This architecture ensures:
- ✅ Scalability through proper separation of concerns
- ✅ Security through middleware and validation
- ✅ Maintainability through organized structure
- ✅ Performance through proper database optimization
