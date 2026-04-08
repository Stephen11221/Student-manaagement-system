# School Portal - API Reference Guide

This guide documents all available routes and their parameters for the School Portal application.

## Authentication Routes

### Register User
```
POST /register
Content-Type: application/x-www-form-urlencoded

Parameters:
  name              (string, required) - User full name
  email             (string, required) - Email address
  password          (string, required) - Password (min 8 chars)
  password_confirmation (string, required) - Confirm password

Response: 302 Redirect to /dashboard
```

### Login User
```
POST /login
Content-Type: application/x-www-form-urlencoded

Parameters:
  email             (string, required) - User email
  password          (string, required) - User password
  remember          (boolean, optional) - Remember me

Response: 302 Redirect to /dashboard
```

### Logout
```
POST /logout
Authentication: Required

Response: 302 Redirect to /welcome with success message
```

---

## Dashboard Routes

### Get Dashboard
```
GET /dashboard
Authentication: Required

Response: 
  - Student: view('dashboard.student')
  - Trainer: view('dashboard.trainer')
  - Admin: view('dashboard.admin')
  - Career Coach: view('dashboard.career_coach')
```

---

## Student Routes

### View Timetable
```
GET /student/timetable
Authentication: Required
Role: Student

Response: HTML view with student's class schedule
```

### View Homework Assignments
```
GET /student/homework
Authentication: Required
Role: Student

Response: HTML view with all assignments for enrolled classes
```

### Get Homework Submission Form
```
GET /student/homework/{id}/submit
Authentication: Required
Role: Student

URL Parameters:
  id (integer) - Homework ID

Response: HTML form for submission
```

### Submit Homework
```
POST /student/homework/{id}/submit
Authentication: Required
Role: Student
Content-Type: application/x-www-form-urlencoded or multipart/form-data

URL Parameters:
  id (integer) - Homework ID

Parameters:
  content           (string, optional) - Written answer (if submission_type='written')
  file              (file, optional) - File upload (if submission_type='file')

Response: 302 Redirect to /student/homework with success message

Database Change:
  Creates/Updates HomeworkSubmission record
  Sets submitted_at timestamp
```

### View Attendance Record
```
GET /student/attendance
Authentication: Required
Role: Student

Response: HTML view with attendance records and percentage
```

---

## Trainer Routes

### List My Classes
```
GET /trainer/classes
Authentication: Required
Role: Trainer

Response: HTML view with trainer's classes
```

### Get Class Creation Form
```
GET /trainer/classes/create
Authentication: Required
Role: Trainer

Response: HTML form
```

### Create New Class
```
POST /trainer/classes
Authentication: Required
Role: Trainer
Content-Type: application/x-www-form-urlencoded

Parameters:
  name              (string, required) - Class name
  room_number       (string, optional) - Room number
  description       (string, optional) - Class description

Response: 302 Redirect to /trainer/classes with success message

Database Change:
  Creates ClassRoom record with trainer_id = Auth::id()
```

### Get Class Edit Form
```
GET /trainer/classes/{id}/edit
Authentication: Required
Role: Trainer
Authorization: Must be the class trainer

URL Parameters:
  id (integer) - Class ID

Response: HTML form with current data
```

### Update Class
```
POST /trainer/classes/{id}
Authentication: Required
Role: Trainer
Content-Type: application/x-www-form-urlencoded

URL Parameters:
  id (integer) - Class ID

Parameters:
  name              (string, required) - Class name
  room_number       (string, optional) - Room number
  description       (string, optional) - Class description

Response: 302 Redirect to /trainer/classes with success message

Database Change:
  Updates ClassRoom record
```

### Delete Class
```
POST /trainer/classes/{id}/delete
Authentication: Required
Role: Trainer
Authorization: Must be the class trainer
Content-Type: application/x-www-form-urlencoded

URL Parameters:
  id (integer) - Class ID

Response: 302 Redirect to /trainer/classes with success message

Database Change:
  Deletes ClassRoom record (cascades to related records)
```

### View Class Timetable
```
GET /trainer/classes/{id}/timetable
Authentication: Required
Role: Trainer
Authorization: Must be the class trainer

URL Parameters:
  id (integer) - Class ID

Response: HTML view with timetable entries
```

### View Class Homework
```
GET /trainer/classes/{id}/homework
Authentication: Required
Role: Trainer
Authorization: Must be the class trainer

URL Parameters:
  id (integer) - Class ID

Response: HTML view with homework assignments
```

### View Homework Submissions
```
GET /trainer/homework/{id}/submissions
Authentication: Required
Role: Trainer
Authorization: Must be the homework creator

URL Parameters:
  id (integer) - Homework ID

Response: HTML view with student submissions and grading interface
```

### Grade Submission
```
POST /trainer/homework/{id}/grade
Authentication: Required
Role: Trainer
Content-Type: application/x-www-form-urlencoded

URL Parameters:
  id (integer) - Submission ID

Parameters:
  marks             (integer, optional) - Marks (0-100)

Response: 302 Redirect back to submissions with success message

Database Changes:
  Updates HomeworkSubmission.marks
  Creates Notification for student
```

---

## Admin Routes

### List All Users
```
GET /admin/users
Authentication: Required
Role: Admin

Query Parameters:
  page              (integer, optional) - Pagination page (default: 1)

Response: HTML view with paginated user list (15 per page)
```

### Get User Creation Form
```
GET /admin/users/create
Authentication: Required
Role: Admin

Response: HTML form
```

### Create New User
```
POST /admin/users
Authentication: Required
Role: Admin
Content-Type: application/x-www-form-urlencoded

Parameters:
  name              (string, required) - User full name
  email             (string, required) - Email (unique)
  password          (string, required) - Password (min 8 chars)
  role              (enum, required) - Role: student|trainer|admin|department_admin|career_coach

Response: 302 Redirect to /admin/users with success message

Database Change:
  Creates User record with hashed password
```

### Get User Edit Form
```
GET /admin/users/{id}/edit
Authentication: Required
Role: Admin

URL Parameters:
  id (integer) - User ID

Response: HTML form with current user data
```

### Update User
```
POST /admin/users/{id}
Authentication: Required
Role: Admin
Content-Type: application/x-www-form-urlencoded

URL Parameters:
  id (integer) - User ID

Parameters:
  name              (string, required) - User full name
  email             (string, required) - Email
  role              (enum, required) - Role: student|trainer|admin|department_admin|career_coach

Response: 302 Redirect to /admin/users with success message

Database Change:
  Updates User record (NOTE: password not updated via this route)
```

### Delete User
```
POST /admin/users/{id}/delete
Authentication: Required
Role: Admin
Content-Type: application/x-www-form-urlencoded

URL Parameters:
  id (integer) - User ID

Response: 302 Redirect to /admin/users with success message

Database Change:
  Deletes User record (cascades to related records)
```

### List All Classes
```
GET /admin/classes
Authentication: Required
Role: Admin

Response: HTML view with all classes and their trainers
```

---

## Data Models

### User
```json
{
  "id": 1,
  "name": "John Doe",
  "email": "john@example.com",
  "role": "student",
  "created_at": "2024-01-01T10:00:00Z",
  "updated_at": "2024-01-01T10:00:00Z"
}
```

### ClassRoom
```json
{
  "id": 1,
  "name": "Mathematics 101",
  "room_number": "A101",
  "description": "Basic mathematics",
  "trainer_id": 2,
  "created_at": "2024-01-01T10:00:00Z",
  "updated_at": "2024-01-01T10:00:00Z"
}
```

### Homework
```json
{
  "id": 1,
  "class_id": 1,
  "trainer_id": 2,
  "title": "Chapter 5 Exercises",
  "description": "Complete exercises 1-10",
  "submission_type": "written",
  "due_date": "2024-01-15",
  "created_at": "2024-01-01T10:00:00Z",
  "updated_at": "2024-01-01T10:00:00Z"
}
```

### HomeworkSubmission
```json
{
  "id": 1,
  "homework_id": 1,
  "student_id": 3,
  "content": null,
  "file_path": "homework/student_123.pdf",
  "marks": 85,
  "submitted_at": "2024-01-10T15:30:00Z",
  "created_at": "2024-01-10T15:30:00Z",
  "updated_at": "2024-01-12T10:00:00Z"
}
```

### Timetable
```json
{
  "id": 1,
  "class_id": 1,
  "trainer_id": 2,
  "day_of_week": "Monday",
  "start_time": "09:00",
  "end_time": "10:30",
  "topic": "Introduction to Fractions",
  "meeting_link": "https://zoom.us/meeting/123",
  "created_at": "2024-01-01T10:00:00Z",
  "updated_at": "2024-01-01T10:00:00Z"
}
```

### Attendance
```json
{
  "id": 1,
  "class_id": 1,
  "student_id": 3,
  "timetable_id": 1,
  "status": "present",
  "remarks": "On time",
  "marked_at": "2024-01-15T09:00:00Z",
  "created_at": "2024-01-15T09:00:00Z",
  "updated_at": "2024-01-15T09:00:00Z"
}
```

### Notification
```json
{
  "id": 1,
  "user_id": 3,
  "title": "Homework Graded",
  "message": "Your homework has been graded!",
  "type": "success",
  "link": "/student/homework/1",
  "read": false,
  "created_at": "2024-01-12T10:00:00Z",
  "updated_at": "2024-01-12T10:00:00Z"
}
```

---

## Status Codes & Responses

| Code | Meaning | Example |
|------|---------|---------|
| 200 | OK | GET request successful |
| 302 | Redirect | Form submission redirects to success page |
| 401 | Unauthorized | Not logged in |
| 403 | Forbidden | Don't have permission |
| 404 | Not Found | Resource doesn't exist |
| 422 | Validation Error | Invalid input data |
| 500 | Server Error | Database error or code exception |

## Authentication

All protected routes require:
1. Active session via login
2. Valid authentication token (CSRF)
3. Correct role for the route

Authentication checked via:
```
auth()->check()        // Is logged in?
auth()->user()         // Get current user
auth()->user()->role   // Get user's role
```

## Rate Limiting

Currently: None implemented (suitable for internal/admin system)

Could be added:
```php
Route::prefix('api')->middleware('throttle:60,1')->group(function () {
    // API routes
});
```

## Error Responses

### Validation Error
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email field is required."]
  }
}
```

### Authorization Error
```
Response: 403 Forbidden
Message: Not authorized to perform this action
```

### Not Found Error
```
Response: 404 Not Found
Message: Resource not found
```

---

## Usage Examples

### Create a homework submission
```bash
curl -X POST http://localhost:8000/student/homework/1/submit \
  -H "Cookie: XSRF-TOKEN=...; session=..." \
  -F "content=My homework answer"
```

### Grade homework
```bash
curl -X POST http://localhost:8000/trainer/homework/1/grade \
  -H "Cookie: XSRF-TOKEN=...; session=..." \
  -d "marks=85"
```

---

## Future API Integration Points

When extending with REST API:
- All routes can be duplicated as `/api/v1/*`
- Authentication via Bearer tokens
- JSON request/response format
- Proper error codes and messages
- Rate limiting

Example:
```php
Route::prefix('api/v1')->middleware('auth:sanctum')->group(function () {
    Route::get('/user', [UserController::class, 'show']);
    Route::post('/homework/{id}/submit', [StudentController::class, 'submitHomework']);
    // ... more API routes
});
```
