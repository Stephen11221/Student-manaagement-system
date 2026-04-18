@php
    $role = $role ?? 'student';
    $classId = $classId ?? null;
    $user = auth()->user();
    $name = $user->name ?? 'User';
    $email = $user->email ?? '';
    $initials = collect(explode(' ', trim($name)))
        ->filter()
        ->take(2)
        ->map(fn ($part) => mb_substr($part, 0, 1))
        ->implode('');

    $financeRoles = ['admin', 'accountant', 'manager'];

    $sections = in_array($role, $financeRoles, true)
        ? [
            [
                'label' => 'Accounting',
                'items' => [
                    ['label' => 'Dashboard', 'icon' => 'calculator', 'route' => 'accounting.dashboard', 'active' => 'accounting.dashboard'],
                    ['label' => 'Chart of Accounts', 'icon' => 'layers', 'route' => 'accounting.accounts.index', 'active' => 'accounting.accounts.*'],
                    ['label' => 'Transactions', 'icon' => 'document', 'route' => 'accounting.transactions.index', 'active' => 'accounting.transactions.*'],
                    ['label' => 'Invoices', 'icon' => 'receipt', 'route' => 'accounting.invoices.index', 'active' => 'accounting.invoices.*'],
                    ['label' => 'Reports', 'icon' => 'chart', 'route' => 'accounting.reports.index', 'active' => 'accounting.reports.*'],
                ],
            ],
            [
                'label' => 'Account',
                'items' => [
                    ['label' => 'Portal Home', 'icon' => 'home', 'route' => 'dashboard', 'active' => 'dashboard'],
                    ['label' => 'Notifications', 'icon' => 'bell', 'route' => 'notifications.index', 'active' => 'notifications.*'],
                    ['label' => 'Profile', 'icon' => 'user', 'route' => 'profile.show', 'active' => 'profile.*'],
                ],
            ],
        ]
        : ($role === 'trainer'
        ? [
            [
                'label' => 'Teaching',
                'items' => [
                    ['label' => 'Classes', 'icon' => 'school', 'route' => 'trainer.classes.index', 'active' => 'trainer.classes.*'],
                    ['label' => 'Timetable', 'icon' => 'calendar', 'route' => $classId ? 'trainer.timetable.index' : 'trainer.classes.index', 'routeParams' => $classId ? [$classId] : [], 'active' => $classId ? 'trainer.timetable.*' : 'trainer.classes.*'],
                    ['label' => 'Homework', 'icon' => 'book', 'route' => $classId ? 'trainer.homework.index' : 'trainer.classes.index', 'routeParams' => $classId ? [$classId] : [], 'active' => $classId ? 'trainer.homework.*' : 'trainer.classes.*'],
                    ['label' => 'Exams', 'icon' => 'clipboard', 'route' => $classId ? 'trainer.exams.index' : 'trainer.classes.index', 'routeParams' => $classId ? [$classId] : [], 'active' => $classId ? 'trainer.exams.*' : 'trainer.classes.*'],
                    ['label' => 'Attendance', 'icon' => 'clipboard', 'route' => $classId ? 'trainer.attendance.index' : 'trainer.classes.index', 'routeParams' => $classId ? [$classId] : [], 'active' => $classId ? 'trainer.attendance.*' : 'trainer.classes.*'],
                ],
            ],
            [
                'label' => 'Account',
                'items' => [
                    ['label' => 'Dashboard', 'icon' => 'home', 'route' => 'dashboard', 'active' => 'dashboard'],
                    ['label' => 'Notifications', 'icon' => 'bell', 'route' => 'notifications.index', 'active' => 'notifications.*'],
                    ['label' => 'Profile', 'icon' => 'user', 'route' => 'profile.show', 'active' => 'profile.*'],
                ],
            ],
        ]
        : [
            [
                'label' => 'Learning',
                'items' => [
                    ['label' => 'My Classes', 'icon' => 'school', 'href' => url('/dashboard#your-classes'), 'active' => null],
                    ['label' => 'Available', 'icon' => 'book', 'href' => url('/dashboard#available-classes'), 'active' => null],
                    ['label' => 'Timetable', 'icon' => 'calendar', 'route' => 'student.timetable.index', 'active' => 'student.timetable.*'],
                    ['label' => 'Homework', 'icon' => 'book', 'route' => 'student.homework.index', 'active' => 'student.homework.*'],
                    ['label' => 'Attendance', 'icon' => 'clipboard', 'route' => 'student.attendance.index', 'active' => 'student.attendance.*'],
                    ['label' => 'Exams', 'icon' => 'chart', 'route' => 'student.exams.index', 'active' => 'student.exams.*'],
                ],
            ],
            [
                'label' => 'Account',
                'items' => [
                    ['label' => 'Dashboard', 'icon' => 'home', 'route' => 'dashboard', 'active' => 'dashboard'],
                    ['label' => 'Notifications', 'icon' => 'bell', 'route' => 'notifications.index', 'active' => 'notifications.*'],
                    ['label' => 'Profile', 'icon' => 'user', 'route' => 'profile.show', 'active' => 'profile.*'],
                ],
            ],
        ];

    $sidebarId = 'app-sidebar';
@endphp

<button
    type="button"
    class="sidebar-toggle"
    data-sidebar-toggle
    aria-controls="{{ $sidebarId }}"
    aria-expanded="true"
    aria-label="Collapse sidebar"
>
    @include('partials.heroicon', ['name' => 'menu'])
</button>

<div class="sidebar-overlay" data-sidebar-overlay aria-hidden="true"></div>

<aside id="{{ $sidebarId }}" class="app-sidebar" data-app-sidebar aria-label="Primary sidebar" aria-hidden="false">
    <div class="sidebar-brand">
        <div class="sidebar-brand__avatar">{{ strtoupper($initials ?: 'U') }}</div>
        <div class="sidebar-brand__text">
            <div class="sidebar-brand__name">{{ config('app.name', 'School Portal') }}</div>
            <div class="sidebar-brand__role">{{ ucfirst($role) }} workspace</div>
        </div>
    </div>

    @foreach($sections as $section)
        <div class="sidebar-section">
            <div class="sidebar-section__label">{{ $section['label'] }}</div>
            @foreach($section['items'] as $item)
                @php
                    $isActive = !empty($item['active']) && request()->routeIs($item['active']);
                    $url = $item['href'] ?? route($item['route'], $item['routeParams'] ?? []);
                @endphp
                <a
                    href="{{ $url }}"
                    class="sidebar-link {{ $isActive ? 'active' : '' }}"
                    data-tooltip="{{ $item['label'] }}"
                    aria-current="{{ $isActive ? 'page' : 'false' }}"
                >
                    @include('partials.heroicon', ['name' => $item['icon']])
                    <span class="sidebar-link__text">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </div>
    @endforeach

    <div class="sidebar-footer">
        <details class="sidebar-profile">
            <summary class="sidebar-profile__summary" aria-label="Open profile menu">
                <div class="sidebar-profile__avatar">{{ strtoupper($initials ?: 'U') }}</div>
                <div class="sidebar-profile__meta">
                    <div class="sidebar-profile__name">{{ $name }}</div>
                    <div class="sidebar-profile__role">{{ $email }}</div>
                </div>
                @include('partials.heroicon', ['name' => 'chevron-down', 'class' => 'sidebar-profile__chevron'])
            </summary>
            <div class="sidebar-profile__menu" role="menu" aria-label="Profile options">
                <a href="{{ route('profile.show') }}" role="menuitem">
                    @include('partials.heroicon', ['name' => 'user'])
                    <span>Profile settings</span>
                </a>
                <a href="{{ route('notifications.index') }}" role="menuitem">
                    @include('partials.heroicon', ['name' => 'bell'])
                    <span>Notifications</span>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" role="menuitem">
                        @include('partials.heroicon', ['name' => 'logout'])
                        <span>Log out</span>
                    </button>
                </form>
            </div>
        </details>
    </div>
</aside>
