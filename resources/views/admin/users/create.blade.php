<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create User</title>
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body{font-family:"Instrument Sans",sans-serif;background:linear-gradient(135deg,#020617,#0f172a 54%,#111827 100%);color:#e2e8f0;margin:0;padding:0}
        .container{max-width:980px;margin:0 auto;padding:40px 20px}.card{background:rgba(15,23,42,.78);border:1px solid rgba(148,163,184,.18);border-radius:16px;padding:24px}
        h1{color:#f8fafc;margin-top:0}h3{color:#dbeafe;font-size:1rem;margin:0 0 16px}
        .form-section{margin-bottom:24px;padding-bottom:20px;border-bottom:1px solid rgba(148,163,184,.1)}
        .form-group{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}.full-width{grid-column:1/-1}
        label{display:block;margin-bottom:8px;color:#dbeafe;font-weight:600}input,select,textarea{width:100%;padding:12px;border:1px solid rgba(148,163,184,.2);border-radius:8px;background:rgba(2,6,23,.56);color:#f8fafc;font-family:inherit;box-sizing:border-box}
        textarea{resize:vertical;min-height:96px}button{background:linear-gradient(135deg,#22d3ee,#06b6d4);color:#082f49;border:none;padding:12px 24px;border-radius:8px;cursor:pointer;font-weight:700;width:100%;margin-top:20px}
        .password-field{position:relative}.password-field input{padding-right:96px}.password-toggle{position:absolute;right:10px;top:38px;border:1px solid rgba(148,163,184,.2);background:rgba(2,6,23,.7);color:#dbeafe;border-radius:999px;padding:8px 12px;font:inherit;font-size:.82rem;display:inline-flex;align-items:center;gap:8px;width:auto;margin-top:0}
        .back-link{color:#22d3ee;text-decoration:none;font-weight:600;margin-top:16px;display:inline-flex;align-items:center;gap:8px}.hint{color:#94a3b8;font-size:.85rem;margin-top:6px}
        .student-only{display:none}.error-box{background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.32);border-radius:12px;padding:14px 16px;margin-bottom:18px;color:#fecaca}
        .error-list{margin-top:10px;padding-left:18px}.field-error{color:#fca5a5;font-size:.85rem;margin-top:6px}
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1><i class="fas fa-user-plus"></i> Create New User</h1>
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
            @if(session('status'))
                <div class="error-box" style="background:rgba(16,185,129,.12);border-color:rgba(16,185,129,.3);color:#86efac;">
                    <i class="fas fa-circle-check"></i> {{ session('status') }}
                </div>
            @endif
            @if(session('import_warnings'))
                <div class="error-box" style="background:rgba(245,158,11,.12);border-color:rgba(245,158,11,.3);color:#fde68a;">
                    <strong><i class="fas fa-triangle-exclamation"></i> Some rows were skipped.</strong>
                    <ul class="error-list">
                        @foreach(session('import_warnings') as $warning)
                            <li>{{ $warning }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-section">
                    <h3><i class="fas fa-id-card"></i> Personal Information</h3>
                    <div class="form-group">
                        <div>
                            <label>Full Name</label>
                            <input type="text" name="name" value="{{ old('name') }}" required placeholder="John Doe">
                        </div>
                        <div>
                            <label>Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" required placeholder="john@example.com">
                        </div>
                        <div>
                            <label>Phone</label>
                            <input type="text" name="phone" value="{{ old('phone') }}" placeholder="+2547...">
                        </div>
                        <div>
                            <label>Department</label>
                            <input type="text" name="department" value="{{ old('department') }}" placeholder="E.g., Mathematics">
                        </div>
                        <div>
                            <label>Date of Birth</label>
                            <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}">
                        </div>
                        <div>
                            <label>Gender</label>
                            <select name="gender">
                                <option value="">Select gender</option>
                                <option value="male" @selected(old('gender') === 'male')>Male</option>
                                <option value="female" @selected(old('gender') === 'female')>Female</option>
                                <option value="other" @selected(old('gender') === 'other')>Other</option>
                            </select>
                        </div>
                        <div class="full-width">
                            <label>Address</label>
                            <textarea name="address" placeholder="Student home address">{{ old('address') }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="form-section">
                    <h3><i class="fas fa-lock"></i> Account Details</h3>
                    <div class="form-group">
                        <div class="password-field">
                            <label>Password</label>
                            <input type="password" name="password" required placeholder="Min 8 characters">
                            <button type="button" class="password-toggle" data-password-toggle data-show-label="Show" data-hide-label="Hide" aria-label="Show password" aria-pressed="false">
                                <i class="fa-regular fa-eye"></i> <span data-password-label>Show</span>
                            </button>
                        </div>
                        <div>
                            <label>Role</label>
                            <select name="role" id="role-select" required>
                                <option value="" disabled {{ old('role') ? '' : 'selected' }}>Select a role</option>
                                <option value="student" @selected(old('role') === 'student')>Student</option>
                                <option value="trainer" @selected(old('role') === 'trainer')>Trainer</option>
                                <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                                <option value="department_admin" @selected(old('role') === 'department_admin')>Department Admin</option>
                                <option value="career_coach" @selected(old('role') === 'career_coach')>Career Coach</option>
                                <option value="accountant" @selected(old('role') === 'accountant')>Accountant</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-section student-only">
                    <h3><i class="fas fa-user-graduate"></i> Admission Details</h3>
                    <div class="form-group">
                        <div>
                            <label>Admission Date</label>
                            <input type="date" name="admission_date" value="{{ old('admission_date', now()->toDateString()) }}">
                            <div class="hint">Admission number will be generated automatically.</div>
                        </div>
                        <div>
                            <label>Student Status</label>
                            <select name="student_status">
                                <option value="active" @selected(old('student_status', 'active') === 'active')>Active</option>
                                <option value="transferred" @selected(old('student_status') === 'transferred')>Transferred</option>
                                <option value="alumni" @selected(old('student_status') === 'alumni')>Alumni</option>
                            </select>
                        </div>
                        <div>
                            <label>Class Assignment</label>
                            <select name="current_class_id">
                                <option value="">No class assigned</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" @selected((string) old('current_class_id') === (string) $class->id)>{{ $class->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label>Stream</label>
                            <input type="text" name="stream" value="{{ old('stream') }}" placeholder="E.g., East, Blue, STEM">
                        </div>
                        <div>
                            <label>Career Coach</label>
                            <select name="career_coach_id" id="coach-select">
                                <option value="">No coach assigned</option>
                                @foreach($careerCoaches as $coach)
                                    <option value="{{ $coach->id }}" @selected((string) old('career_coach_id') === (string) $coach->id)>{{ $coach->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label>Exit Date</label>
                            <input type="date" name="exit_date" value="{{ old('exit_date') }}">
                        </div>
                        <div class="full-width">
                            <label>Transfer / Alumni Notes</label>
                            <textarea name="transfer_notes" placeholder="Reason for transfer, alumni notes, destination school, or any important record.">{{ old('transfer_notes') }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="form-section student-only">
                    <h3><i class="fas fa-people-roof"></i> Guardian Information</h3>
                    <div class="form-group">
                        <div>
                            <label>Guardian Name</label>
                            <input type="text" name="guardian_name" value="{{ old('guardian_name') }}" placeholder="Parent or guardian name">
                        </div>
                        <div>
                            <label>Guardian Phone</label>
                            <input type="text" name="guardian_phone" value="{{ old('guardian_phone') }}" placeholder="+2547...">
                        </div>
                        <div class="full-width">
                            <label>Relationship</label>
                            <input type="text" name="guardian_relationship" value="{{ old('guardian_relationship') }}" placeholder="Mother, Father, Aunt, Guardian">
                        </div>
                    </div>
                </div>
                <div class="form-section student-only">
                    <h3><i class="fas fa-folder-open"></i> Student Documents</h3>
                    <div class="form-group">
                        <div>
                            <label>Birth Certificate</label>
                            <input type="file" name="birth_certificate" accept=".pdf,.jpg,.jpeg,.png">
                            <div class="hint">Supported: PDF, JPG, JPEG, PNG. Max {{ number_format(studentDocumentMaxKilobytes() / 1024, 0) }}MB.</div>
                            @error('birth_certificate')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label>Report Form</label>
                            <input type="file" name="report_form" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                            <div class="hint">Supported: PDF, JPG, JPEG, PNG, DOC, DOCX. Max {{ number_format(studentDocumentMaxKilobytes() / 1024, 0) }}MB.</div>
                            @error('report_form')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <button type="submit"><i class="fas fa-check"></i> Create User</button>
            </form>
            <div class="form-section" style="margin-top:24px;">
                <h3><i class="fas fa-file-import"></i> Bulk Student Import</h3>
                <p class="hint" style="margin-bottom:14px;">
                    Upload a CSV, XLSX, TXT, or PDF file to create students only. The first row should contain headers such as
                    <code>name</code>, <code>first_name</code>, <code>middle_name</code>, <code>last_name</code>,
                    <code>email</code>, <code>password</code>,
                    <code>admission_number</code>, <code>current_class_id</code>, <code>class_name</code>,
                    <code>phone</code>, <code>department</code>, <code>guardian_name</code>, and <code>parent_phone</code>.
                    PDF files should be text-based tables, not scanned images.
                </p>
                <div style="margin-bottom:14px;">
                    <a href="{{ route('admin.users.import-students-template') }}" class="back-link" style="margin-top:0;">
                        <i class="fas fa-download"></i> Download student template
                    </a>
                </div>

                <form method="POST" action="{{ route('admin.users.import-students') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <div>
                            <label>Student File</label>
                            <input type="file" name="student_import_file" accept=".csv,.txt,.xlsx,.pdf" required>
                            <div class="hint">Supported: CSV, TXT, XLSX, PDF.</div>
                            @error('student_import_file')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="password-field">
                            <label>Default Password</label>
                            <input type="password" name="student_import_password" required placeholder="Temporary password for imported students">
                            <button type="button" class="password-toggle" data-password-toggle data-show-label="Show" data-hide-label="Hide" aria-label="Show temporary password" aria-pressed="false">
                                <i class="fa-regular fa-eye"></i> <span data-password-label>Show</span>
                            </button>
                            <div class="hint">Used when the file row does not include a password.</div>
                            @error('student_import_password')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label>Default Class</label>
                            <select name="student_import_class_id">
                                <option value="">No class assigned</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label>Default Career Coach</label>
                            <select name="student_import_career_coach_id">
                                <option value="">No coach assigned</option>
                                @foreach($careerCoaches as $coach)
                                    <option value="{{ $coach->id }}">{{ $coach->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label>Default Department</label>
                            <input type="text" name="student_import_department" placeholder="Applied to rows without a department">
                        </div>
                        <div class="full-width">
                            <label>Student Only</label>
                            <input type="text" value="This import creates student accounts only." disabled>
                            <div class="hint">Trainer, admin, and staff roles are intentionally not part of this upload flow.</div>
                        </div>
                        <div>
                            <label>Default Student Status</label>
                            <select name="student_import_student_status">
                                <option value="active">Active</option>
                                <option value="transferred">Transferred</option>
                                <option value="alumni">Alumni</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" style="margin-top:16px;"><i class="fas fa-upload"></i> Import Students</button>
                </form>
            </div>
            <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:16px;">
                <a href="{{ route('dashboard') }}" class="back-link"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
                <a href="{{ route('admin.users.index') }}" class="back-link"><i class="fas fa-arrow-left"></i> Back to Users</a>
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
    <script src="{{ asset('js/password-toggle.js') }}"></script>
</body>
</html>
