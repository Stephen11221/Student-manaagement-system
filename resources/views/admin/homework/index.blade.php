<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Homework | {{ config('app.name', 'School Portal') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:"Instrument Sans",sans-serif;background:linear-gradient(135deg,#020617 0%,#0f172a 54%,#111827 100%);color:#e2e8f0;min-height:100vh}
        .container{max-width:1300px;margin:0 auto;padding:40px 20px}
        header{display:flex;justify-content:space-between;align-items:center;margin-bottom:32px;padding-bottom:20px;border-bottom:1px solid rgba(148,163,184,.1);gap:12px;flex-wrap:wrap}
        h1{color:#f8fafc;font-size:2rem}
        .header-actions,.class-actions,.filter-row{display:flex;gap:12px;flex-wrap:wrap;align-items:center}
        .btn{padding:10px 18px;border:none;border-radius:8px;font-weight:700;text-decoration:none;cursor:pointer;display:inline-flex;align-items:center;gap:8px}
        .btn-primary{background:linear-gradient(135deg,#22d3ee,#06b6d4);color:#082f49}
        .btn-secondary{background:rgba(148,163,184,.1);color:#94a3b8;border:1px solid rgba(148,163,184,.2)}
        .btn-edit{background:rgba(34,211,238,.1);color:#22d3ee;border:1px solid rgba(34,211,238,.3)}
        .btn-delete{background:rgba(239,68,68,.1);color:#ef4444;border:1px solid rgba(239,68,68,.3)}
        .stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:20px;margin-bottom:24px}
        .stat-card,.class-card,.filter-bar,.status-banner{background:rgba(15,23,42,.78);border:1px solid rgba(148,163,184,.18);border-radius:16px;padding:20px;backdrop-filter:blur(18px)}
        .stat-number{font-size:2rem;color:#22d3ee;font-weight:700}.stat-label{color:#94a3b8;font-size:.9rem;margin-top:8px}
        .filter-bar{margin-bottom:24px}.filter-bar select{padding:10px 14px;border-radius:8px;border:1px solid rgba(148,163,184,.2);background:rgba(2,6,23,.56);color:#f8fafc}
        .status-banner{margin-bottom:20px;border-color:rgba(16,185,129,.3);background:rgba(16,185,129,.12);color:#86efac}
        .class-card{margin-bottom:22px}
        .class-header{display:flex;justify-content:space-between;gap:12px;align-items:flex-start;flex-wrap:wrap;margin-bottom:18px;padding-bottom:18px;border-bottom:1px solid rgba(148,163,184,.1)}
        .class-title{color:#f8fafc;font-size:1.35rem;margin-bottom:6px}
        .class-meta{color:#94a3b8;font-size:.92rem;display:flex;gap:14px;flex-wrap:wrap}
        table{width:100%;border-collapse:collapse}
        th,td{padding:14px;text-align:left;border-bottom:1px solid rgba(148,163,184,.1);vertical-align:top}
        th{color:#22d3ee;font-size:.85rem;text-transform:uppercase;letter-spacing:.5px}
        .badge{display:inline-block;padding:4px 12px;border-radius:20px;font-size:.8rem;font-weight:700}
        .badge-blue{background:rgba(59,130,246,.2);color:#93c5fd}
        .badge-green{background:rgba(16,185,129,.2);color:#6ee7b7}
        .badge-orange{background:rgba(249,115,22,.2);color:#fdba74}
        .badge-slate{background:rgba(148,163,184,.15);color:#cbd5e1}
        .actions{display:flex;gap:8px;flex-wrap:wrap}
        .empty-state{text-align:center;padding:36px 20px;color:#94a3b8}
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1><i class="fas fa-book-open"></i> Manage Homework</h1>
            <div class="header-actions">
                <a href="{{ route('dashboard') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
                <a href="{{ route('admin.homework.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Create Homework</a>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary"><i class="fas fa-user-shield"></i> Back to Admin</a>
            </div>
        </header>

        <div class="stats-grid">
            <div class="stat-card"><div class="stat-number">{{ $totalHomework ?? 0 }}</div><div class="stat-label"><i class="fas fa-clipboard-list"></i> Total Homework</div></div>
            <div class="stat-card"><div class="stat-number">{{ $activeHomework ?? 0 }}</div><div class="stat-label"><i class="fas fa-check-circle"></i> Active</div></div>
            <div class="stat-card"><div class="stat-number">{{ $totalClasses ?? 0 }}</div><div class="stat-label"><i class="fas fa-chalkboard"></i> Classes</div></div>
        </div>

        @if(session('status'))
            <div class="status-banner"><i class="fas fa-circle-check"></i> {{ session('status') }}</div>
        @endif

        <form method="GET" action="{{ route('admin.homework.index') }}" class="filter-bar">
            <div class="filter-row">
                <input type="text" name="search" placeholder="Search by title..." value="{{ $searchTitle ?? '' }}" style="padding:10px 14px;border-radius:8px;border:1px solid rgba(148,163,184,.2);background:rgba(2,6,23,.56);color:#f8fafc;flex:1;min-width:150px;">
                
                <select name="class_id" style="padding:10px 14px;border-radius:8px;border:1px solid rgba(148,163,184,.2);background:rgba(2,6,23,.56);color:#f8fafc;">
                    <option value="">All classes</option>
                    @forelse($allClasses ?? [] as $class)
                        <option value="{{ $class->id }}" @selected((string) $selectedClassId === (string) $class->id)>{{ $class->name }} ({{ $class->trainer?->name ?? 'No trainer' }}) - {{ $class->homeworks_count }} homework</option>
                    @empty
                        <option value="" disabled>No classes available</option>
                    @endforelse
                </select>
                
                <select name="type" style="padding:10px 14px;border-radius:8px;border:1px solid rgba(148,163,184,.2);background:rgba(2,6,23,.56);color:#f8fafc;">
                    <option value="">All types</option>
                    <option value="written" @selected(($filterType ?? '') === 'written')>Written</option>
                    <option value="upload" @selected(($filterType ?? '') === 'upload')>File Upload</option>
                </select>
                
                <select name="status" style="padding:10px 14px;border-radius:8px;border:1px solid rgba(148,163,184,.2);background:rgba(2,6,23,.56);color:#f8fafc;">
                    <option value="">All statuses</option>
                    <option value="active" @selected(($filterStatus ?? '') === 'active')>Active</option>
                    <option value="past_due" @selected(($filterStatus ?? '') === 'past_due')>Past Due</option>
                    <option value="no_deadline" @selected(($filterStatus ?? '') === 'no_deadline')>No Deadline</option>
                </select>
                
                <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
                @if(($selectedClassId ?? false) || ($searchTitle ?? false) || ($filterType ?? false) || ($filterStatus ?? false))
                    <a href="{{ route('admin.homework.index') }}" class="btn btn-secondary"><i class="fas fa-rotate-left"></i> Clear</a>
                @endif
            </div>
        </form>

        @forelse($classes ?? [] as $class)
            <div class="class-card">
                <div class="class-header">
                    <div>
                        <div class="class-title">{{ $class->name }}</div>
                        <div class="class-meta">
                            <span><i class="fas fa-chalkboard-user"></i> {{ $class->trainer?->name ?? 'No trainer assigned' }}</span>
                            <span><i class="fas fa-list-check"></i> {{ $class->homeworks_count }} homework items</span>
                        </div>
                    </div>
                    <div class="class-actions">
                        <a href="{{ route('admin.homework.create', ['class_id' => $class->id]) }}" class="btn btn-primary"><i class="fas fa-plus"></i> Create for This Class</a>
                    </div>
                </div>

                @if($class->homeworks->count())
                    <table>
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Due Date</th>
                                <th>Type</th>
                                <th>Submissions</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($class->homeworks as $homework)
                                <tr>
                                    <td>
                                        <strong>{{ $homework->title }}</strong>
                                        <br>
                                        <span style="color:#94a3b8;font-size:.85rem;">{{ Str::limit($homework->description, 70) }}</span>
                                    </td>
                                    <td>
                                        @if($homework->due_date)
                                            {{ $homework->due_date->format('M d, Y') }}
                                            @if($homework->due_date->isPast())
                                                <span class="badge badge-orange"><i class="fas fa-exclamation-circle"></i> Past Due</span>
                                            @endif
                                        @else
                                            <span class="badge badge-slate">No deadline</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $homework->submission_type === 'written' ? 'badge-blue' : 'badge-green' }}">
                                            <i class="fas fa-{{ $homework->submission_type === 'written' ? 'pen-fancy' : 'file-upload' }}"></i>
                                            {{ ucfirst($homework->submission_type === 'upload' ? 'file' : $homework->submission_type) }}
                                        </span>
                                    </td>
                                    <td><span class="badge badge-blue">{{ $homework->submissions_count }} submitted</span></td>
                                    <td>
                                        <div class="actions">
                                            <a href="{{ route('admin.homework.edit', $homework->id) }}" class="btn btn-edit"><i class="fas fa-edit"></i> Edit</a>
                                            <form method="POST" action="{{ route('admin.homework.delete', $homework->id) }}" style="display:inline;" onsubmit="return confirm('Delete this homework?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-delete"><i class="fas fa-trash"></i> Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i> No homework for this class yet.
                    </div>
                @endif
            </div>
        @empty
            <div class="class-card">
                <div class="empty-state">
                    <i class="fas fa-inbox"></i> No classes or homework found. <a href="{{ route('admin.homework.create') }}" style="color:#22d3ee;text-decoration:none;">Create homework now</a>
                </div>
            </div>
        @endforelse
    </div>
</body>
</html>
