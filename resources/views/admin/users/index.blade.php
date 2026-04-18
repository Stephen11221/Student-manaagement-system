<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Management | {{ config('app.name', 'School Portal') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: "Instrument Sans", sans-serif; background: linear-gradient(135deg, #020617, #0f172a 54%, #111827 100%); color: #e2e8f0; min-height: 100vh; }
        .container { max-width: 1400px; margin: 0 auto; padding: 40px 20px; }
        header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; border-bottom: 1px solid rgba(148, 163, 184, 0.1); padding-bottom: 20px; }
        h1 { color: #f8fafc; margin: 0; }
        .header-actions { display: flex; gap: 12px; }
        button, .btn { background: linear-gradient(135deg, #22d3ee, #06b6d4); color: #082f49; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 700; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 30px; }
        .stat-card { background: rgba(15, 23, 42, 0.78); border: 1px solid rgba(148, 163, 184, 0.18); border-radius: 12px; padding: 20px; text-align: center; }
        .stat-num { font-size: 2rem; color: #22d3ee; font-weight: 700; }
        .stat-label { color: #94a3b8; font-size: 0.9rem; margin-top: 8px; }
        .filter-bar { background: rgba(15, 23, 42, 0.78); border: 1px solid rgba(148, 163, 184, 0.18); border-radius: 12px; padding: 16px; margin-bottom: 20px; display: flex; gap: 12px; flex-wrap: wrap; }
        .filter-bar input, .filter-bar select { padding: 8px 12px; border-radius: 6px; border: 1px solid rgba(148, 163, 184, 0.2); background: rgba(2, 6, 23, 0.56); color: #f8fafc; }
        .filter-bar button { padding: 8px 16px; font-size: 0.9rem; }
        table { width: 100%; border-collapse: collapse; background: rgba(15, 23, 42, 0.78); border-radius: 12px; overflow: hidden; }
        th, td { padding: 14px; text-align: left; border-bottom: 1px solid rgba(148, 163, 184, 0.1); vertical-align: top; }
        th { background: rgba(34, 211, 238, 0.15); color: #dbeafe; font-weight: 600; }
        td { color: #e2e8f0; }
        .role { background: rgba(34, 211, 238, 0.2); color: #22d3ee; padding: 4px 12px; border-radius: 4px; font-size: 0.85rem; display: inline-block; font-weight: 600; }
        .status { padding: 4px 12px; border-radius: 4px; font-size: 0.85rem; font-weight: 600; display: inline-block; }
        .status.online { background: rgba(52, 211, 153, 0.2); color: #34d399; }
        .status.offline { background: rgba(107, 114, 128, 0.2); color: #94a3b8; }
        .status.suspended { background: rgba(239, 68, 68, 0.2); color: #ef4444; }
        .action-btns { display: flex; gap: 6px; flex-wrap: wrap; }
        .btn { padding: 6px 12px; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; font-size: 0.85rem; }
        .btn-edit { background: #0ea5e9; color: #082f49; }
        .btn-suspend { background: #f59e0b; color: #082f49; }
        .btn-activate { background: #10b981; color: white; }
        .btn-delete { background: #ef4444; color: white; }
        .muted { color:#94a3b8; font-size:.85rem; margin-top:4px; }
        .status-banner { background: rgba(16,185,129,.12); border: 1px solid rgba(16,185,129,.3); color: #86efac; border-radius: 12px; padding: 14px 16px; margin-bottom: 20px; }
        .role-section { margin-top: 18px; background: rgba(15, 23, 42, 0.78); border: 1px solid rgba(148, 163, 184, 0.18); border-radius: 14px; overflow: hidden; }
        .role-section + .role-section { margin-top: 16px; }
        .role-section__header {
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:12px;
            padding:16px 18px;
            background: rgba(34, 211, 238, 0.08);
            border-bottom:1px solid rgba(148, 163, 184, 0.1);
        }
        .role-section__title { display:flex; align-items:center; gap:10px; color:#f8fafc; font-weight:700; }
        .role-section__meta { color:#94a3b8; font-size:.9rem; }
        .role-section__table { width:100%; border-collapse: collapse; }
        .role-section__table th { background: rgba(2, 6, 23, 0.55); }
        .role-section__empty { padding: 18px; color:#94a3b8; }
    </style>
</head>
<body>
    @php
        $roleLabels = [
            'student' => ['label' => 'Students', 'icon' => 'fa-book'],
            'trainer' => ['label' => 'Trainers', 'icon' => 'fa-chalkboard-user'],
            'career_coach' => ['label' => 'Career Coaches', 'icon' => 'fa-briefcase'],
            'accountant' => ['label' => 'Accountants', 'icon' => 'fa-calculator'],
            'manager' => ['label' => 'Managers', 'icon' => 'fa-briefcase'],
            'department_admin' => ['label' => 'Department Admins', 'icon' => 'fa-building-user'],
            'admin' => ['label' => 'Admins', 'icon' => 'fa-shield-halved'],
            'other' => ['label' => 'Other Users', 'icon' => 'fa-user-group'],
        ];
        $groupedUsers = $users->getCollection()->groupBy(function ($user) use ($roleLabels) {
            return array_key_exists($user->role, $roleLabels) ? $user->role : 'other';
        });
        $roleOrder = array_keys($roleLabels);
    @endphp

    <div class="container">
        <header>
            <h1><i class="fas fa-users"></i> User Management</h1>
            <div class="header-actions">
                <a href="{{ route('dashboard') }}" class="btn" style="text-decoration:none; background:rgba(34,211,238,0.1); color:#22d3ee; border:1px solid rgba(34,211,238,0.3);">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
                <a href="/admin/users/create" class="btn" style="text-decoration:none; background:linear-gradient(135deg,#22d3ee,#06b6d4); color:#082f49;">
                    <i class="fas fa-plus"></i> Add User
                </a>
                <a href="/admin/homework" class="btn" style="text-decoration:none; background:rgba(34,211,238,0.1); color:#22d3ee; border:1px solid rgba(34,211,238,0.3);">
                    <i class="fas fa-book-open"></i> Homework
                </a>
            </div>
        </header>

        @if(session('status'))
            <div class="status-banner"><i class="fas fa-circle-check"></i> {{ session('status') }}</div>
        @endif

        <div class="stats">
            <div class="stat-card">
                <div class="stat-num">{{ $totalUsers }}</div>
                <div class="stat-label"><i class="fas fa-users"></i> Total Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-num">{{ $students }}</div>
                <div class="stat-label"><i class="fas fa-book"></i> Students</div>
            </div>
            <div class="stat-card">
                <div class="stat-num">{{ $trainers }}</div>
                <div class="stat-label"><i class="fas fa-chalkboard-user"></i> Trainers</div>
            </div>
            <div class="stat-card">
                <div class="stat-num">{{ $admins }}</div>
                <div class="stat-label"><i class="fas fa-shield-alt"></i> Admins</div>
            </div>
            <div class="stat-card">
                <div class="stat-num">{{ $managers ?? 0 }}</div>
                <div class="stat-label"><i class="fas fa-briefcase"></i> Managers</div>
            </div>
        </div>

        <div class="filter-bar">
            <input type="text" id="search" placeholder="Search by name or email" style="flex:1">
            <select id="roleFilter">
                <option value="">All Roles</option>
                <option value="student">Student</option>
                <option value="trainer">Trainer</option>
                <option value="admin">Admin</option>
                <option value="department_admin">Department Admin</option>
                <option value="career_coach">Career Coach</option>
                <option value="accountant">Accountant</option>
                <option value="manager">Manager</option>
            </select>
            <button onclick="filterTable()"><i class="fas fa-filter"></i> Filter</button>
        </div>

        @foreach($roleOrder as $roleKey)
            @php
                $roleUsers = $groupedUsers->get($roleKey, collect());
            @endphp
            @if($roleUsers->count())
                <section class="role-section" data-role-section="{{ $roleKey }}">
                    <div class="role-section__header">
                        <div class="role-section__title">
                            <i class="fas {{ $roleLabels[$roleKey]['icon'] }}"></i>
                            {{ $roleLabels[$roleKey]['label'] }}
                        </div>
                        <div class="role-section__meta">{{ $roleUsers->count() }} user(s) on this page</div>
                    </div>
                    <table class="role-section__table">
                        <thead>
                            <tr>
                                <th><i class="fas fa-user"></i> Name</th>
                                <th><i class="fas fa-id-badge"></i> Admission</th>
                                <th><i class="fas fa-envelope"></i> Email</th>
                                <th><i class="fas fa-building"></i> Department / Class</th>
                                <th><i class="fas fa-check-circle"></i> Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roleUsers as $user)
                                <tr class="user-row" data-role="{{ $user->role }}" data-name="{{ strtolower($user->name) }}" data-email="{{ strtolower($user->email) }}" data-role-section="{{ $roleKey }}">
                                    <td>
                                        <strong>{{ $user->name }}</strong>
                                        <div class="muted"><span class="role">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</span></div>
                                        @if($user->role === 'student' && $user->guardian_name)
                                            <div class="muted"><i class="fas fa-people-roof"></i> Guardian: {{ $user->guardian_name }}</div>
                                        @endif
                                    </td>
                                    <td>{{ $user->admission_number ?? '-' }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td style="color:#94a3b8">
                                        <div>{{ $user->department ?? '-' }}</div>
                                        @if($user->role === 'student')
                                            <div class="muted">
                                                {{ $user->currentClass?->name ?? 'No class' }}
                                                @if($user->stream)
                                                    • {{ $user->stream }}
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="status {{ $user->deleted_at ? 'suspended' : 'online' }}">
                                            <i class="fas {{ $user->deleted_at ? 'fa-lock' : 'fa-check-circle' }}"></i>
                                            {{ $user->deleted_at ? 'Suspended' : 'Active' }}
                                        </span>
                                        @if($user->role === 'student' && $user->student_status)
                                            <div class="muted"><i class="fas fa-route"></i> {{ ucfirst($user->student_status) }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="action-btns">
                                            <a href="/admin/users/{{ $user->id }}/edit" class="btn btn-edit">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            @if(!$user->deleted_at)
                                                <form method="POST" action="/admin/users/{{ $user->id }}/suspend" style="display:inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-suspend" onclick="return confirm('Suspend this user?')">
                                                        <i class="fas fa-pause"></i> Suspend
                                                    </button>
                                                </form>
                                            @else
                                                <form method="POST" action="/admin/users/{{ $user->id }}/activate" style="display:inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-activate">
                                                        <i class="fas fa-play"></i> Activate
                                                    </button>
                                                </form>
                                            @endif
                                            <form method="POST" action="/admin/users/{{ $user->id }}/delete" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-delete" onclick="return confirm('Permanently delete this user?')">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </section>
            @endif
        @endforeach

        @if($groupedUsers->count() === 0)
            <div class="status-banner" style="background: rgba(148,163,184,.12); border-color: rgba(148,163,184,.24); color:#cbd5e1;">
                No users found.
            </div>
        @endif
    </div>

    <script>
        function filterTable() {
            const search = document.getElementById('search').value.toLowerCase();
            const roleFilter = document.getElementById('roleFilter').value;
            const rows = document.querySelectorAll('.user-row');
            const sections = document.querySelectorAll('.role-section');
            
            rows.forEach(row => {
                const name = row.dataset.name;
                const email = row.dataset.email;
                const role = row.dataset.role;
                const matchSearch = name.includes(search) || email.includes(search);
                const matchRole = !roleFilter || role === roleFilter;
                row.style.display = (matchSearch && matchRole) ? '' : 'none';
            });

            sections.forEach(section => {
                const visibleRows = section.querySelectorAll('.user-row:not([style*="display: none"])');
                section.style.display = visibleRows.length ? '' : 'none';
            });
        }

        document.getElementById('search').addEventListener('keyup', filterTable);
        document.getElementById('roleFilter').addEventListener('change', filterTable);
    </script>

    @include('partials.idle-timeout-modal')
    <script src="{{ asset('js/idle-timeout.js') }}"></script>
    <script>
        document.documentElement.dataset.idleTimeout = "{{ config('idle.idle_timeout', 15) }}";
        document.documentElement.dataset.warningTime = "{{ config('idle.warning_time', 1) }}";
    </script>
</body>
</html>
