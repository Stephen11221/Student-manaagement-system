<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit User</title>
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body{font-family:"Instrument Sans",sans-serif;background:linear-gradient(135deg,#020617,#0f172a 54%,#111827 100%);color:#e2e8f0;margin:0;padding:0}
        .container{max-width:980px;margin:0 auto;padding:40px 20px}.card{background:rgba(15,23,42,.78);border:1px solid rgba(148,163,184,.18);border-radius:16px;padding:24px}
        h1{color:#f8fafc;margin-top:0}h3{color:#dbeafe;font-size:1rem;margin:0 0 16px}
        label{display:block;margin-bottom:8px;color:#dbeafe;font-weight:600}input,select,textarea{width:100%;padding:12px;border:1px solid rgba(148,163,184,.2);border-radius:8px;background:rgba(2,6,23,.56);color:#f8fafc;font-family:inherit;box-sizing:border-box}
        textarea{resize:vertical;min-height:96px}.section{margin-bottom:24px;padding-bottom:20px;border-bottom:1px solid rgba(148,163,184,.1)}.grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}.hint{color:#94a3b8;font-size:.85rem;margin-top:6px}
        button,.link-btn,.doc-link{padding:12px 24px;border-radius:8px;cursor:pointer;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;gap:8px}
        button{border:none;width:100%}.primary{background:linear-gradient(135deg,#22d3ee,#06b6d4);color:#082f49}.warning{background:#f59e0b;color:#082f49}.link-btn{border:1px solid rgba(34,211,238,.3);color:#22d3ee}.actions{display:flex;gap:12px;margin-top:24px;flex-wrap:wrap}.actions form{margin:0;flex:1}.student-only{display:none}.doc-link{padding:10px 14px;background:rgba(34,211,238,.12);color:#22d3ee;border:1px solid rgba(34,211,238,.24)}
        .error-box{background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.32);border-radius:12px;padding:14px 16px;margin-bottom:18px;color:#fecaca}.error-list{margin-top:10px;padding-left:18px}.field-error{color:#fca5a5;font-size:.85rem;margin-top:6px}
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1><i class="fas fa-user-pen"></i> Edit User: {{ $user->name }}</h1>
            @if($errors->any())
                <div class="error-box">
                    <strong><i class="fas fa-triangle-exclamation"></i> Please fix the highlighted issues.</strong>
                    <ul class="error-list">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form method="POST" action="{{ route('admin.users.update', $user->id) }}" enctype="multipart/form-data">
                @csrf
                <div class="section">
                    <h3><i class="fas fa-id-card"></i> Personal Information</h3>
                    <div class="grid">
                        <div>
                            <label>Full Name</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
                        </div>
                        <div>
                            <label>Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
                        </div>
                        <div>
                            <label>Phone</label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}">
                        </div>
                        <div>
                            <label>Department</label>
                            <input type="text" name="department" value="{{ old('department', $user->department) }}" placeholder="E.g., Engineering">
                        </div>
                        <div>
                            <label>Date of Birth</label>
                            <input type="date" name="date_of_birth" value="{{ old('date_of_birth', optional($user->date_of_birth)->toDateString()) }}">
                        </div>
                        <div>
                            <label>Gender</label>
                            <select name="gender">
                                <option value="">Select gender</option>
                                <option value="male" @selected(old('gender', $user->gender) === 'male')>Male</option>
                                <option value="female" @selected(old('gender', $user->gender) === 'female')>Female</option>
                                <option value="other" @selected(old('gender', $user->gender) === 'other')>Other</option>
                            </select>
                        </div>
                        <div style="grid-column:1/-1">
                            <label>Address</label>
                            <textarea name="address">{{ old('address', $user->address) }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="section">
                    <h3><i class="fas fa-user-tag"></i> Role and Assignment</h3>
                    <div class="grid">
                        <div>
                            <label>Role</label>
                            <select name="role" id="role-select" required>
                                @foreach(['student' => 'Student', 'trainer' => 'Trainer', 'admin' => 'Admin', 'department_admin' => 'Department Admin', 'career_coach' => 'Career Coach'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('role', $user->role) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label>Career Coach</label>
                            <select name="career_coach_id" id="coach-select">
                                <option value="">No coach assigned</option>
                                @foreach($careerCoaches as $coach)
                                    <option value="{{ $coach->id }}" @selected((string) old('career_coach_id', $user->career_coach_id) === (string) $coach->id)>{{ $coach->name }}</option>
                                @endforeach
                            </select>
                            <div class="hint">Career coach assignment is used only for students.</div>
                        </div>
                    </div>
                </div>
                <div class="section student-only">
                    <h3><i class="fas fa-user-graduate"></i> Admission and Tracking</h3>
                    <div class="grid">
                        <div>
                            <label>Admission Number</label>
                            <input type="text" value="{{ $user->admission_number ?? 'Will be generated automatically' }}" disabled>
                        </div>
                        <div>
                            <label>Admission Date</label>
                            <input type="date" name="admission_date" value="{{ old('admission_date', optional($user->admission_date)->toDateString()) }}">
                        </div>
                        <div>
                            <label>Class Assignment</label>
                            <select name="current_class_id">
                                <option value="">No class assigned</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" @selected((string) old('current_class_id', $user->current_class_id) === (string) $class->id)>{{ $class->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label>Stream</label>
                            <input type="text" name="stream" value="{{ old('stream', $user->stream) }}" placeholder="E.g., East, Blue, Science">
                        </div>
                        <div>
                            <label>Student Status</label>
                            <select name="student_status">
                                <option value="active" @selected(old('student_status', $user->student_status) === 'active')>Active</option>
                                <option value="transferred" @selected(old('student_status', $user->student_status) === 'transferred')>Transferred</option>
                                <option value="alumni" @selected(old('student_status', $user->student_status) === 'alumni')>Alumni</option>
                            </select>
                        </div>
                        <div>
                            <label>Exit Date</label>
                            <input type="date" name="exit_date" value="{{ old('exit_date', optional($user->exit_date)->toDateString()) }}">
                        </div>
                        <div style="grid-column:1/-1">
                            <label>Transfer / Alumni Notes</label>
                            <textarea name="transfer_notes">{{ old('transfer_notes', $user->transfer_notes) }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="section student-only">
                    <h3><i class="fas fa-people-roof"></i> Guardian Information</h3>
                    <div class="grid">
                        <div>
                            <label>Guardian Name</label>
                            <input type="text" name="guardian_name" value="{{ old('guardian_name', $user->guardian_name) }}">
                        </div>
                        <div>
                            <label>Guardian Phone</label>
                            <input type="text" name="guardian_phone" value="{{ old('guardian_phone', $user->guardian_phone) }}">
                        </div>
                        <div style="grid-column:1/-1">
                            <label>Relationship</label>
                            <input type="text" name="guardian_relationship" value="{{ old('guardian_relationship', $user->guardian_relationship) }}">
                        </div>
                    </div>
                </div>
                <div class="section student-only">
                    <h3><i class="fas fa-folder-open"></i> Student Documents</h3>
                    <div class="grid">
                        <div>
                            <label>Birth Certificate</label>
                            <input type="file" name="birth_certificate" accept=".pdf,.jpg,.jpeg,.png">
                            <div class="hint">Supported: PDF, JPG, JPEG, PNG. Max {{ number_format(studentDocumentMaxKilobytes() / 1024, 0) }}MB.</div>
                            @error('birth_certificate')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                            @if($user->birth_certificate_path)
                                <div class="hint"><a class="doc-link" href="{{ route('admin.users.documents.show', [$user->id, 'birth_certificate']) }}" target="_blank"><i class="fas fa-file-arrow-down"></i> View current file</a></div>
                            @endif
                        </div>
                        <div>
                            <label>Report Form</label>
                            <input type="file" name="report_form" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                            <div class="hint">Supported: PDF, JPG, JPEG, PNG, DOC, DOCX. Max {{ number_format(studentDocumentMaxKilobytes() / 1024, 0) }}MB.</div>
                            @error('report_form')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                            @if($user->report_form_path)
                                <div class="hint"><a class="doc-link" href="{{ route('admin.users.documents.show', [$user->id, 'report_form']) }}" target="_blank"><i class="fas fa-file-arrow-down"></i> View current file</a></div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="section">
                    <h3><i class="fas fa-shield-halved"></i> Account Status</h3>
                    <p style="color:#dbeafe;font-weight:600">Current status:
                        <span style="color:{{ $user->deleted_at ? '#fb7185' : '#34d399' }}">
                            <i class="fas {{ $user->deleted_at ? 'fa-lock' : 'fa-circle-check' }}"></i>
                            {{ $user->deleted_at ? 'Suspended' : 'Active' }}
                        </span>
                    </p>
                </div>
                <button type="submit" class="primary"><i class="fas fa-floppy-disk"></i> Save Changes</button>
            </form>
            <div class="actions">
                <form method="POST" action="{{ route($user->deleted_at ? 'admin.users.activate' : 'admin.users.suspend', $user->id) }}">
                    @csrf
                    <button type="submit" class="warning"><i class="fas {{ $user->deleted_at ? 'fa-play' : 'fa-pause' }}"></i> {{ $user->deleted_at ? 'Activate' : 'Suspend' }} Account</button>
                </form>
                <a href="{{ route('dashboard') }}" class="link-btn"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
                <a href="{{ route('admin.users.index') }}" class="link-btn"><i class="fas fa-arrow-left"></i> Back</a>
            </div>
        </div>
    </div>
    <script>
        const roleSelect = document.getElementById('role-select');
        const coachSelect = document.getElementById('coach-select');
        const studentSections = document.querySelectorAll('.student-only');
        function syncStudentFields() {
            const isStudent = roleSelect.value === 'student';
            studentSections.forEach(section => {
                section.style.display = isStudent ? 'block' : 'none';
            });
            coachSelect.disabled = !isStudent;
            if (!isStudent) coachSelect.value = '';
        }
        roleSelect.addEventListener('change', syncStudentFields);
        syncStudentFields();
    </script>
</body>
</html>
