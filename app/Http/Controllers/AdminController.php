<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\ClassRoom;
use App\Models\Department;
use App\Models\Setting;
use App\Models\AuditLog;
use App\Models\Permission;
use App\Models\Homework;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    // ============ USER MANAGEMENT ============

    public function getUsers()
    {
        $users = User::with('department')->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    public function createUser()
    {
        $departments = Department::where('is_active', true)->get();
        $roles = Role::all();
        return view('admin.users.create', compact('departments', 'roles'));
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:student,trainer,admin,department_admin,career_coach,accountant'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'is_active' => ['boolean'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'department_id' => $validated['department_id'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
            'status' => 'active',
        ]);

        AuditLog::log('create', 'User', $user->id, null, "Created user: {$user->name}");

        return redirect()->route('admin.users.index')->with('success', 'User created successfully!');
    }

    public function editUser(User $user)
    {
        $departments = Department::where('is_active', true)->get();
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'departments', 'roles'));
    }

    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'in:student,trainer,admin,department_admin,career_coach,accountant'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'is_active' => ['boolean'],
            'status' => ['required', 'in:active,inactive,suspended,locked'],
        ]);

        $changes = [];
        foreach (['name', 'email', 'role', 'department_id', 'is_active', 'status'] as $field) {
            if ($user->{$field} != ($validated[$field] ?? null)) {
                $changes[$field] = ['from' => $user->{$field}, 'to' => $validated[$field] ?? null];
            }
        }

        $user->update($validated);

        if (!empty($changes)) {
            AuditLog::log('update', 'User', $user->id, $changes, "Updated user: {$user->name}");
        }

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully!');
    }

    public function deleteUser(User $user)
    {
        $userName = $user->name;
        $user->delete();
        AuditLog::log('delete', 'User', $user->id, null, "Deleted user: {$userName}");
        return redirect()->back()->with('success', 'User deleted successfully!');
    }

    // ============ USER STATUS CONTROL ============

    public function suspendUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'days' => 'nullable|integer|min:1|max:365',
            'reason' => 'nullable|string|max:500',
        ]);

        $until = $validated['days'] ? now()->addDays($validated['days']) : null;
        $user->suspend($until, $validated['reason'] ?? null);

        AuditLog::log('suspend', 'User', $user->id, null, "Suspended for: {$validated['days']} days. Reason: {$validated['reason']}");

        return redirect()->back()->with('success', "User {$user->name} suspended successfully!");
    }

    public function unsuspendUser(User $user)
    {
        $user->unsuspend();
        AuditLog::log('unsuspend', 'User', $user->id, null, "Account unsuspended");
        return redirect()->back()->with('success', "User {$user->name} unsuspended successfully!");
    }

    public function lockUser(User $user)
    {
        $user->lock();
        AuditLog::log('lock', 'User', $user->id, null, "Account locked");
        return redirect()->back()->with('success', "User {$user->name} locked successfully!");
    }

    public function unlockUser(User $user)
    {
        $user->unlock();
        AuditLog::log('unlock', 'User', $user->id, null, "Account unlocked");
        return redirect()->back()->with('success', "User {$user->name} unlocked successfully!");
    }

    public function deactivateUser(User $user)
    {
        $user->deactivate();
        AuditLog::log('deactivate', 'User', $user->id, null, "Account deactivated");
        return redirect()->back()->with('success', "User {$user->name} deactivated successfully!");
    }

    public function activateUser(User $user)
    {
        $user->activate();
        AuditLog::log('activate', 'User', $user->id, null, "Account activated");
        return redirect()->back()->with('success', "User {$user->name} activated successfully!");
    }

    // ============ CLASS MANAGEMENT ============

    public function getClasses()
    {
        $classes = ClassRoom::with('trainer')->paginate(15);
        return view('admin.classes.index', compact('classes'));
    }

    public function createClass()
    {
        $trainers = User::where('role', 'trainer')->get();
        return view('admin.classes.create', compact('trainers'));
    }

    public function storeClass(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'trainer_id' => ['required', 'exists:users,id'],
            'max_students' => ['nullable', 'integer', 'min:1'],
            'room_number' => ['nullable', 'string', 'max:50'],
            'delivery_mode' => ['required', 'in:online,physical'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $class = ClassRoom::create($validated);
        AuditLog::log('create', 'ClassRoom', $class->id, null, "Created class: {$class->name}");

        return redirect()->route('admin.classes.index')->with('success', 'Class created successfully!');
    }

    public function editClass(ClassRoom $class)
    {
        $trainers = User::where('role', 'trainer')->get();
        return view('admin.classes.edit', compact('class', 'trainers'));
    }

    public function updateClass(Request $request, ClassRoom $class)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'trainer_id' => ['required', 'exists:users,id'],
            'max_students' => ['nullable', 'integer', 'min:1'],
            'room_number' => ['nullable', 'string', 'max:50'],
            'delivery_mode' => ['required', 'in:online,physical'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $changes = [];
        foreach (['name', 'description', 'trainer_id', 'max_students', 'room_number', 'delivery_mode', 'status'] as $field) {
            if ($class->{$field} != ($validated[$field] ?? null)) {
                $changes[$field] = ['from' => $class->{$field}, 'to' => $validated[$field] ?? null];
            }
        }

        $class->update($validated);

        if (!empty($changes)) {
            AuditLog::log('update', 'ClassRoom', $class->id, $changes, "Updated class: {$class->name}");
        }

        return redirect()->route('admin.classes.index')->with('success', 'Class updated successfully!');
    }

    public function deleteClass(ClassRoom $class)
    {
        $className = $class->name;
        $class->delete();
        AuditLog::log('delete', 'ClassRoom', $class->id, null, "Deleted class: {$className}");
        return redirect()->back()->with('success', 'Class deleted successfully!');
    }

    // ============ DEPARTMENT MANAGEMENT ============

    public function getDepartments()
    {
        $departments = Department::with('head', 'activeUsers')->paginate(15);
        return view('admin.departments.index', compact('departments'));
    }

    public function createDepartment()
    {
        $heads = User::where('role', 'department_admin')->orWhere('role', 'admin')->get();
        return view('admin.departments.create', compact('heads'));
    }

    public function storeDepartment(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:departments'],
            'slug' => ['required', 'string', 'max:255', 'unique:departments'],
            'description' => ['nullable', 'string'],
            'head_id' => ['nullable', 'exists:users,id'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'is_active' => ['boolean'],
        ]);

        $department = Department::create($validated);
        AuditLog::log('create', 'Department', $department->id, null, "Created department: {$department->name}");

        return redirect()->route('admin.departments.index')->with('success', 'Department created successfully!');
    }

    public function editDepartment(Department $department)
    {
        $heads = User::where('role', 'department_admin')->orWhere('role', 'admin')->get();
        return view('admin.departments.edit', compact('department', 'heads'));
    }

    public function updateDepartment(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:departments,name,' . $department->id],
            'description' => ['nullable', 'string'],
            'head_id' => ['nullable', 'exists:users,id'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'is_active' => ['boolean'],
        ]);

        $department->update($validated);
        AuditLog::log('update', 'Department', $department->id, null, "Updated department: {$department->name}");

        return redirect()->route('admin.departments.index')->with('success', 'Department updated successfully!');
    }

    public function deleteDepartment(Department $department)
    {
        $name = $department->name;
        $department->delete();
        AuditLog::log('delete', 'Department', $department->id, null, "Deleted department: {$name}");
        return redirect()->back()->with('success', 'Department deleted successfully!');
    }

    // ============ SYSTEM SETTINGS ============

    public function getSettings()
    {
        $allSettings = Setting::all()->groupBy('group');
        return view('admin.settings.index', compact('allSettings'));
    }

    public function updateSettings(Request $request)
    {
        foreach ($request->all() as $key => $value) {
            if ($key !== '_token') {
                Setting::set($key, $value ?? '', 'string', 'general');
            }
        }

        AuditLog::log('update', 'Settings', null, null, "System settings updated");

        return redirect()->back()->with('success', 'Settings updated successfully!');
    }

    // ============ AUDIT LOGS ============

    public function getAuditLogs()
    {
        $logs = AuditLog::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(50);
        return view('admin.audit-logs.index', compact('logs'));
    }

    public function viewAuditLog(AuditLog $log)
    {
        return view('admin.audit-logs.show', compact('log'));
    }

    // ============ PERMISSIONS & ROLES ============

    public function getPermissions()
    {
        $permissions = Permission::paginate(15);
        return view('admin.permissions.index', compact('permissions'));
    }

    public function createPermission()
    {
        return view('admin.permissions.create');
    }

    public function storePermission(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions'],
            'description' => ['nullable', 'string'],
        ]);

        $permission = Permission::create($validated);
        AuditLog::log('create', 'Permission', $permission->id, null, "Created permission: {$permission->name}");

        return redirect()->route('admin.permissions.index')->with('success', 'Permission created successfully!');
    }

    public function editPermission(Permission $permission)
    {
        return view('admin.permissions.edit', compact('permission'));
    }

    public function updatePermission(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name,' . $permission->id],
            'description' => ['nullable', 'string'],
        ]);

        $permission->update($validated);
        AuditLog::log('update', 'Permission', $permission->id, null, "Updated permission: {$permission->name}");

        return redirect()->route('admin.permissions.index')->with('success', 'Permission updated successfully!');
    }

    public function deletePermission(Permission $permission)
    {
        $name = $permission->name;
        $permission->delete();
        AuditLog::log('delete', 'Permission', $permission->id, null, "Deleted permission: {$name}");
        return redirect()->back()->with('success', 'Permission deleted successfully!');
    }

    public function getRoles()
    {
        $roles = Role::with('permissions')->paginate(15);
        return view('admin.roles.index', compact('roles'));
    }

    public function editRole(Role $role)
    {
        $permissions = Permission::all();
        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    public function updateRolePermissions(Request $request, Role $role)
    {
        $validated = $request->validate([
            'permissions' => ['array', 'exists:permissions,id'],
        ]);

        $role->permissions()->sync($validated['permissions'] ?? []);
        AuditLog::log('update', 'Role', $role->id, null, "Updated permissions for role: {$role->name}");

        return redirect()->back()->with('success', 'Role permissions updated successfully!');
    }

    // ============ DASHBOARD & ANALYTICS ============

    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'total_classes' => ClassRoom::count(),
            'total_departments' => Department::count(),
            'suspended_users' => User::where('status', 'suspended')->count(),
            'locked_users' => User::where('status', 'locked')->count(),
        ];

        $recent_logs = AuditLog::with('user')->orderBy('created_at', 'desc')->limit(10)->get();

        $user_by_role = User::selectRaw('role, count(*) as count')
            ->groupBy('role')
            ->get()
            ->keyBy('role');

        return view('admin.dashboard', compact('stats', 'recent_logs', 'user_by_role'));
    }

    public function getAnalytics()
    {
        $data = [
            'total_users' => User::count(),
            'total_trainers' => User::where('role', 'trainer')->count(),
            'total_students' => User::where('role', 'student')->count(),
            'total_admins' => User::where('role', 'admin')->count(),
            'total_classes' => ClassRoom::count(),
            'total_departments' => Department::count(),
            'users_by_department' => Department::withCount('users')->get(),
            'users_by_role' => User::selectRaw('role, count(*) as count')->groupBy('role')->get(),
        ];

        return view('admin.analytics.index', compact('data'));
    }

    // ============ ACCESS CONTROL ============

    // ============ HOMEWORK MANAGEMENT ============

    public function getHomework(Request $request)
    {
        $selectedClassId = $request->query('class_id');
        $searchTitle = $request->query('search');
        $filterType = $request->query('type');
        $filterStatus = $request->query('status');
        
        $query = ClassRoom::with(['homeworks' => function($q) use ($searchTitle, $filterType, $filterStatus) {
            $q->withCount('submissions');
            
            if ($searchTitle) {
                $q->where('title', 'like', '%' . $searchTitle . '%');
            }
            
            if ($filterType && in_array($filterType, ['written', 'upload'])) {
                $q->where('submission_type', $filterType);
            }
            
            if ($filterStatus === 'active') {
                $q->where('due_date', '>', now());
            } elseif ($filterStatus === 'past_due') {
                $q->where('due_date', '<=', now())->whereNotNull('due_date');
            } elseif ($filterStatus === 'no_deadline') {
                $q->whereNull('due_date');
            }
        }]);
        
        if ($selectedClassId) {
            $query->where('id', $selectedClassId);
        }
        
        $classes = $query->withCount('homeworks')->get();
        $allClasses = ClassRoom::with('trainer')->withCount('homeworks')->orderBy('name')->get();
        
        // Calculate stats
        $totalHomework = Homework::count();
        $activeHomework = Homework::where('due_date', '>', now())->count();
        $totalClasses = ClassRoom::count();
        
        return view('admin.homework.index', compact(
            'classes', 
            'allClasses', 
            'selectedClassId',
            'searchTitle',
            'filterType',
            'filterStatus',
            'totalHomework',
            'activeHomework',
            'totalClasses'
        ));
    }

    public function createHomework()
    {
        $classes = ClassRoom::all();
        return view('admin.homework.create', compact('classes'));
    }

    public function storeHomework(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'class_id' => ['required', 'exists:class_rooms,id'],
            'submission_type' => ['required', 'in:written,file,upload'],
            'due_date' => ['nullable', 'date', 'after:today'],
        ]);

        $class = ClassRoom::findOrFail($validated['class_id']);

        $homework = Homework::create([
            ...$validated,
            'trainer_id' => $class->trainer_id,
            'submission_type' => $validated['submission_type'] === 'file' ? 'upload' : $validated['submission_type'],
        ]);

        AuditLog::log('create', 'Homework', $homework->id, null, "Created homework: {$homework->title}");

        return redirect()->route('admin.homework.index')->with('success', 'Homework created successfully!');
    }

    public function editHomework(Homework $homework)
    {
        $classes = ClassRoom::all();
        return view('admin.homework.edit', compact('homework', 'classes'));
    }

    public function updateHomework(Request $request, Homework $homework)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'class_id' => ['required', 'exists:class_rooms,id'],
            'submission_type' => ['required', 'in:written,file,upload'],
            'due_date' => ['nullable', 'date'],
        ]);

        $class = ClassRoom::findOrFail($validated['class_id']);

        $changes = [];
        foreach (['title', 'description', 'class_id', 'submission_type', 'due_date'] as $field) {
            if ($homework->{$field} != ($validated[$field] ?? null)) {
                $changes[$field] = ['from' => $homework->{$field}, 'to' => $validated[$field] ?? null];
            }
        }

        $homework->update([
            ...$validated,
            'trainer_id' => $class->trainer_id,
            'submission_type' => $validated['submission_type'] === 'file' ? 'upload' : $validated['submission_type'],
        ]);

        if (!empty($changes)) {
            AuditLog::log('update', 'Homework', $homework->id, $changes, "Updated homework: {$homework->title}");
        }

        return redirect()->route('admin.homework.index')->with('success', 'Homework updated successfully!');
    }

    public function deleteHomework(Homework $homework)
    {
        $homeworkTitle = $homework->title;
        $homework->delete();
        AuditLog::log('delete', 'Homework', $homework->id, null, "Deleted homework: {$homeworkTitle}");
        return redirect()->back()->with('success', 'Homework deleted successfully!');
    }

    // ============ MESSAGING SYSTEM ============

    public function getMessaging()
    {
        $classes = ClassRoom::all();
        $users = User::where('role', '!=', 'admin')->orderBy('name')->get();
        return view('admin.messaging.index', compact('classes', 'users'));
    }

    public function sendMessageForm()
    {
        $classes = ClassRoom::all();
        $users = User::where('role', '!=', 'admin')->orderBy('name')->get();
        return view('admin.messaging.send', compact('classes', 'users'));
    }

    public function sendMessage(Request $request)
    {
        $validated = $request->validate([
            'recipient_type' => ['required', 'in:individual,class,all'],
            'user_id' => ['nullable', 'exists:users,id', 'required_if:recipient_type,individual'],
            'class_id' => ['nullable', 'exists:class_rooms,id', 'required_if:recipient_type,class'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
        ]);

        $recipients = [];
        $recipientDetails = '';

        if ($validated['recipient_type'] === 'individual') {
            $recipients = [User::findOrFail($validated['user_id'])];
            $recipientDetails = "individual user";
        } elseif ($validated['recipient_type'] === 'class') {
            $class = ClassRoom::findOrFail($validated['class_id']);
            $recipients = $class->students()->get();
            $recipientDetails = "class: {$class->name}";
        } elseif ($validated['recipient_type'] === 'all') {
            $recipients = User::where('role', '!=', 'admin')->get();
            $recipientDetails = "all users";
        }

        // Send notifications to each recipient
        foreach ($recipients as $user) {
            $user->notifications()->create([
                'title' => $validated['subject'],
                'message' => $validated['message'],
                'type' => 'message',
                'read' => false,
            ]);
        }

        AuditLog::log(
            'send_message',
            'User',
            null,
            ['count' => count($recipients)],
            "Sent message to {$recipientDetails}: {$validated['subject']}"
        );

        return redirect()->back()->with('success', 'Message sent to ' . count($recipients) . ' recipient(s)!');
    }

    // ============ ACCESS CONTROL ============
    public function getAccessControl()
    {
        $users = User::where('is_active', false)
            ->orWhere('status', '!=', 'active')
            ->with('department')
            ->paginate(15);

        $stats = [
            'inactive' => User::where('is_active', false)->count(),
            'suspended' => User::where('status', 'suspended')->count(),
            'locked' => User::where('status', 'locked')->count(),
            'total_restricted' => User::where('is_active', false)
                ->orWhere('status', '!=', 'active')
                ->count(),
        ];

        return view('admin.access-control.index', compact('users', 'stats'));
    }
}
