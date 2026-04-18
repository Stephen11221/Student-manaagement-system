<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AccountingController;
use App\Http\Controllers\AttendanceController;
use App\Models\Attendance;
use App\Models\AuditLog;
use App\Models\ClassRoom;
use App\Models\FeePayment;
use App\Models\Exam;
use App\Models\ExamQuestion;
use App\Models\ExamSubmission;
use App\Models\Homework;
use App\Models\HomeworkSubmission;
use App\Models\Notification;
use App\Models\Timetable;
use App\Models\User;
use App\Services\AccountingService;
use App\Services\DarajaService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

const APP_MAX_UPLOAD_KILOBYTES = 1048576; // 1 GB

function generateAdmissionNumber(): string
{
    $prefix = 'ADM' . now()->format('Y');
    $latestAdmission = User::withTrashed()
        ->whereNotNull('admission_number')
        ->where('admission_number', 'like', $prefix . '%')
        ->orderByDesc('admission_number')
        ->value('admission_number');

    $nextSequence = $latestAdmission
        ? ((int) substr($latestAdmission, -4)) + 1
        : 1;

    return $prefix . str_pad((string) $nextSequence, 4, '0', STR_PAD_LEFT);
}

function deleteUserStudentDocuments(User $user): void
{
    foreach (['birth_certificate_path', 'report_form_path'] as $attribute) {
        if ($user->{$attribute}) {
            Storage::disk('public')->delete($user->{$attribute});
        }
    }
}

function syncStudentEnrollment(User $user): void
{
    if ($user->role === 'student' && $user->current_class_id) {
        $user->enrolledClasses()->sync([$user->current_class_id]);

        return;
    }

    $user->enrolledClasses()->detach();
}

function studentCurrentClass(User $user): ?ClassRoom
{
    return $user->currentClass()->with('trainer')->first();
}

function studentClassJoinUrl(ClassRoom $class): string
{
    if ($class->delivery_mode === 'online') {
        $meetingLink = $class->relationLoaded('timetables')
            ? $class->timetables->firstWhere('meeting_link')?->meeting_link
            : $class->timetables()->whereNotNull('meeting_link')->value('meeting_link');

        if ($meetingLink) {
            return $meetingLink;
        }
    }

    return route('student.classes.show', $class->id) . ($class->delivery_mode === 'online' ? '#schedule' : '#location');
}

function studentClassJoinLabel(ClassRoom $class): string
{
    return $class->delivery_mode === 'online' ? 'Join Online Class' : 'Join Physical Class';
}

function accountantDashboardData(): array
{
    $students = User::where('role', 'student')
        ->with(['currentClass', 'feePayments' => fn ($query) => $query->orderByDesc('paid_at')->orderByDesc('id')])
        ->orderBy('name')
        ->get();

    $paymentQuery = FeePayment::with('student.currentClass')->orderByDesc('paid_at')->orderByDesc('id');
    $recentPayments = (clone $paymentQuery)->limit(10)->get();
    $cashReceived = (clone $paymentQuery)->where('payment_method', 'cash')->sum('amount_paid');
    $pochiReceived = (clone $paymentQuery)->where('payment_method', 'pochi_la_biashara')->sum('amount_paid');
    $bankReceived = (clone $paymentQuery)->where('payment_method', 'bank_transfer')->sum('amount_paid');

    $totalDue = $students->sum(function (User $student) {
        return $student->feePayments->sum('amount_due');
    });

    $totalPaid = $students->sum(function (User $student) {
        return $student->feePayments->sum('amount_paid');
    });

    $outstandingBalance = max($totalDue - $totalPaid, 0);
    $paidStudents = $students->filter(function (User $student) {
        $due = (float) $student->feePayments->sum('amount_due');
        $paid = (float) $student->feePayments->sum('amount_paid');

        return $due > 0 && $paid >= $due;
    })->count();

    $partiallyPaidStudents = $students->filter(function (User $student) {
        $due = (float) $student->feePayments->sum('amount_due');
        $paid = (float) $student->feePayments->sum('amount_paid');

        return $due > 0 && $paid > 0 && $paid < $due;
    })->count();

    $unpaidStudents = $students->filter(function (User $student) {
        $due = (float) $student->feePayments->sum('amount_due');
        $paid = (float) $student->feePayments->sum('amount_paid');

        return $due <= 0 || $paid <= 0;
    })->count();

    $collectionRate = $totalDue > 0 ? round(($totalPaid / $totalDue) * 100, 1) : 0;

    return compact(
        'students',
        'recentPayments',
        'totalDue',
        'totalPaid',
        'outstandingBalance',
        'paidStudents',
        'partiallyPaidStudents',
        'unpaidStudents',
        'collectionRate'
        ,
        'cashReceived',
        'pochiReceived',
        'bankReceived'
    );
}

Route::post('/daraja/mpesa/callback', function (Request $request) {
    $callback = data_get($request->all(), 'Body.stkCallback', []);
    $checkoutRequestId = data_get($callback, 'CheckoutRequestID');

    if (! $checkoutRequestId) {
        return response()->json(['message' => 'Missing CheckoutRequestID.']);
    }

    $payment = FeePayment::where('checkout_request_id', $checkoutRequestId)->first();

    if (! $payment) {
        return response()->json(['message' => 'Payment record not found.'], 404);
    }

    $items = collect(data_get($callback, 'CallbackMetadata.Item', []))
        ->mapWithKeys(function ($item) {
            return [data_get($item, 'Name') => data_get($item, 'Value')];
        });

    $resultCode = (string) data_get($callback, 'ResultCode', '1');
    $resultDesc = data_get($callback, 'ResultDesc');
    $merchantRequestId = data_get($callback, 'MerchantRequestID');
    $amount = (float) ($items->get('Amount') ?? $payment->amount_paid);
    $receiptNumber = $items->get('MpesaReceiptNumber');
    $phoneNumber = $items->get('PhoneNumber');

    $payment->fill([
        'daraja_payload' => $request->all(),
        'daraja_completed_at' => now(),
        'daraja_response_code' => $resultCode,
        'daraja_response_description' => $resultDesc,
        'merchant_request_id' => $merchantRequestId ?: $payment->merchant_request_id,
    ]);

    if ($resultCode === '0') {
        $payment->amount_paid = $amount;
        $payment->payment_method = 'mpesa';
        $payment->receipt_number = $receiptNumber ?: $payment->receipt_number;
        $payment->transaction_id = $receiptNumber ?: $payment->transaction_id;
        $payment->phone_number = $phoneNumber ?: $payment->phone_number;
        $payment->paid_at = now();
        $payment->status = $amount >= (float) $payment->amount_due ? 'paid' : ($amount > 0 ? 'partial' : 'unpaid');
    }

    $payment->save();

    return response()->json(['message' => 'Callback received.']);
})->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class])->name('daraja.mpesa.callback');

function normalizeImportHeader(string $header): string
{
    $header = preg_replace('/^\xEF\xBB\xBF/', '', trim($header)) ?? trim($header);

    return preg_replace('/[^a-z0-9]+/i', '', strtolower($header)) ?? strtolower($header);
}

function canonicalStudentImportField(string $header): ?string
{
    static $map = [
        'name' => 'name',
        'fullname' => 'name',
        'studentname' => 'name',
        'middlename' => 'middle_name',
        'middle_name' => 'middle_name',
        'middleinitial' => 'middle_name',
        'lastname' => 'last_name',
        'secondname' => 'last_name',
        'firstname' => 'first_name',
        'firstnameandlastname' => 'name',
        'surname' => 'last_name',
        'email' => 'email',
        'emailaddress' => 'email',
        'password' => 'password',
        'phone' => 'phone',
        'phonenumber' => 'phone',
        'mobile' => 'phone',
        'department' => 'department',
        'dateofbirth' => 'date_of_birth',
        'dob' => 'date_of_birth',
        'gender' => 'gender',
        'address' => 'address',
        'guardianname' => 'guardian_name',
        'parentname' => 'guardian_name',
        'parent_name' => 'guardian_name',
        'parent' => 'guardian_name',
        'guardianphone' => 'guardian_phone',
        'parentphone' => 'guardian_phone',
        'parent_phone' => 'guardian_phone',
        'guardianrelationship' => 'guardian_relationship',
        'parentrelationship' => 'guardian_relationship',
        'parent_relationship' => 'guardian_relationship',
        'currentclassid' => 'current_class_id',
        'classid' => 'current_class_id',
        'class' => 'current_class_id',
        'classname' => 'class_name',
        'stream' => 'stream',
        'studentstatus' => 'student_status',
        'status' => 'student_status',
        'admissionnumber' => 'admission_number',
        'admissionno' => 'admission_number',
        'admno' => 'admission_number',
        'regno' => 'admission_number',
        'admissiondate' => 'admission_date',
        'exitdate' => 'exit_date',
        'transfernotes' => 'transfer_notes',
        'careercoachid' => 'career_coach_id',
        'careercoach' => 'career_coach_id',
        'coachemail' => 'career_coach_id',
        'coachid' => 'career_coach_id',
        'coachname' => 'career_coach_id',
    ];

    return $map[normalizeImportHeader($header)] ?? null;
}

function splitImportLine(string $line): array
{
    $line = trim($line);

    if ($line === '') {
        return [];
    }

    if (str_contains($line, "\t")) {
        return array_map('trim', explode("\t", $line));
    }

    if (str_contains($line, '|')) {
        return array_map('trim', explode('|', $line));
    }

    if (str_contains($line, ',')) {
        return array_map('trim', str_getcsv($line));
    }

    return array_map('trim', preg_split('/\s{2,}/', $line) ?: [$line]);
}

function parseDelimitedStudentImportRows(string $text): array
{
    $lines = array_values(array_filter(
        array_map('trim', preg_split('/\R/', $text) ?: []),
        fn ($line) => $line !== '',
    ));

    if ($lines === []) {
        return [];
    }

    $headerCells = splitImportLine(array_shift($lines));
    $headers = [];

    foreach ($headerCells as $cell) {
        $headers[] = canonicalStudentImportField($cell);
    }

    $rows = [];

    foreach ($lines as $line) {
        $values = splitImportLine($line);
        $row = [];

        foreach ($headers as $index => $field) {
            if (! $field) {
                continue;
            }

            $row[$field] = $values[$index] ?? '';
        }

        if ($row !== []) {
            $rows[] = $row;
        }
    }

    return $rows;
}

function xlsxColumnIndex(string $column): int
{
    $column = strtoupper(trim($column));
    $index = 0;

    foreach (str_split($column) as $char) {
        if ($char < 'A' || $char > 'Z') {
            continue;
        }

        $index = ($index * 26) + (ord($char) - 64);
    }

    return $index;
}

function xlsxSharedStrings(string $path): array
{
    $zip = new ZipArchive();

    if ($zip->open($path) !== true) {
        return [];
    }

    $xml = $zip->getFromName('xl/sharedStrings.xml') ?: '';
    $zip->close();

    if ($xml === '') {
        return [];
    }

    $doc = new DOMDocument();
    $doc->preserveWhiteSpace = false;

    if (! $doc->loadXML($xml)) {
        return [];
    }

    $xpath = new DOMXPath($doc);
    $xpath->registerNamespace('a', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');

    $strings = [];
    foreach ($xpath->query('//a:si') as $node) {
        $text = '';
        foreach ($xpath->query('.//a:t', $node) as $textNode) {
            $text .= $textNode->textContent;
        }

        $strings[] = $text;
    }

    return $strings;
}

function parseXlsxStudentImportRows(string $path): array
{
    $zip = new ZipArchive();

    if ($zip->open($path) !== true) {
        return [];
    }

    $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml') ?: '';
    $zip->close();

    if ($sheetXml === '') {
        return [];
    }

    $sharedStrings = xlsxSharedStrings($path);
    $doc = new DOMDocument();
    $doc->preserveWhiteSpace = false;

    if (! $doc->loadXML($sheetXml)) {
        return [];
    }

    $xpath = new DOMXPath($doc);
    $xpath->registerNamespace('a', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');

    $rows = [];
    foreach ($xpath->query('//a:sheetData/a:row') as $rowNode) {
        $cells = [];

        foreach ($xpath->query('a:c', $rowNode) as $cellNode) {
            $ref = $cellNode->attributes?->getNamedItem('r')?->nodeValue ?? '';
            $columnLetters = preg_replace('/\d+$/', '', $ref) ?: '';
            $columnIndex = xlsxColumnIndex($columnLetters);
            $type = $cellNode->attributes?->getNamedItem('t')?->nodeValue ?? '';
            $value = '';

            if ($type === 's') {
                $sharedIndex = (int) ($xpath->evaluate('string(a:v)', $cellNode) ?: 0);
                $value = $sharedStrings[$sharedIndex] ?? '';
            } elseif ($type === 'inlineStr') {
                $value = trim($xpath->evaluate('string(a:is//a:t)', $cellNode));
            } else {
                $value = trim($xpath->evaluate('string(a:v)', $cellNode));
            }

            if ($columnIndex > 0) {
                $cells[$columnIndex] = $value;
            }
        }

        ksort($cells);
        $rows[] = array_values($cells);
    }

    if ($rows === []) {
        return [];
    }

    $headers = array_map(
        fn ($header) => canonicalStudentImportField((string) $header),
        array_shift($rows),
    );

    $importRows = [];
    foreach ($rows as $rowValues) {
        $row = [];

        foreach ($headers as $index => $field) {
            if (! $field) {
                continue;
            }

            $row[$field] = $rowValues[$index] ?? '';
        }

        if ($row !== []) {
            $importRows[] = $row;
        }
    }

    return $importRows;
}

function extractStudentImportTextFromPdf(string $path): string
{
    $command = sprintf(
        'pdftotext -layout -nopgbrk %s - 2>/dev/null',
        escapeshellarg($path),
    );

    $output = shell_exec($command);

    return is_string($output) ? $output : '';
}

function extractStudentImportRowsFromUpload(UploadedFile $file): array
{
    $extension = strtolower($file->getClientOriginalExtension());
    $path = $file->getRealPath() ?: $file->path();

    return match ($extension) {
        'xlsx' => parseXlsxStudentImportRows($path),
        'pdf' => parseDelimitedStudentImportRows(extractStudentImportTextFromPdf($path)),
        default => parseDelimitedStudentImportRows((string) file_get_contents($path)),
    };
}

function resolveImportedClassId(?string $value, ?int $defaultId = null): ?int
{
    $value = trim((string) $value);

    if ($value === '') {
        return $defaultId;
    }

    if (ctype_digit($value)) {
        return ClassRoom::find((int) $value)?->id ?? $defaultId;
    }

    return ClassRoom::whereRaw('LOWER(name) = ?', [strtolower($value)])->value('id') ?? $defaultId;
}

function resolveImportedCoachId(?string $value, ?int $defaultId = null): ?int
{
    $value = trim((string) $value);

    if ($value === '') {
        return $defaultId;
    }

    if (ctype_digit($value)) {
        return User::find((int) $value)?->id ?? $defaultId;
    }

    return User::where(function ($query) use ($value) {
        $query->whereRaw('LOWER(email) = ?', [strtolower($value)])
            ->orWhereRaw('LOWER(name) = ?', [strtolower($value)]);
    })->value('id') ?? $defaultId;
}

function buildImportedStudentName(array $row): string
{
    $name = trim((string) ($row['name'] ?? ''));

    if ($name !== '') {
        return $name;
    }

    $parts = [
        trim((string) ($row['first_name'] ?? '')),
        trim((string) ($row['middle_name'] ?? '')),
        trim((string) ($row['last_name'] ?? '')),
    ];

    if (($row['first_name'] ?? '') === '' && ($row['last_name'] ?? '') === '') {
        $parts[] = trim((string) ($row['surname'] ?? ''));
    }

    return trim(implode(' ', array_filter($parts, fn ($part) => $part !== '')));
}

function extractExamQuestionTextFromDocx(string $path): string
{
    $zip = new ZipArchive();

    if ($zip->open($path) !== true) {
        return '';
    }

    $xml = $zip->getFromName('word/document.xml') ?: '';
    $zip->close();

    if ($xml === '') {
        return '';
    }

    $xml = str_replace(['</w:p>', '</w:tr>', '<w:br/>', '<w:br />'], "\n", $xml);
    $text = strip_tags($xml);

    return html_entity_decode($text, ENT_QUOTES | ENT_XML1, 'UTF-8');
}

function extractExamQuestionTextFromPdf(string $path): string
{
    $command = sprintf(
        'pdftotext -layout -nopgbrk %s - 2>/dev/null',
        escapeshellarg($path),
    );

    $output = shell_exec($command);

    return is_string($output) ? $output : '';
}

function extractExamQuestionsFromText(string $text): array
{
    $text = str_replace("\r\n", "\n", $text);
    $text = str_replace("\r", "\n", $text);

    $lines = array_values(array_filter(array_map('trim', preg_split('/\n+/', $text) ?: [])));
    $questions = [];
    $buffer = '';

    foreach ($lines as $line) {
        $line = preg_replace('/^\x{feff}/u', '', $line) ?? $line;

        if (preg_match('/^(?:question\s*)?(\d+)[\.\)\:\-]\s*(.+)$/iu', $line, $matches)) {
            if (trim($buffer) !== '') {
                $questions[] = trim($buffer);
            }

            $buffer = trim($matches[2]);

            continue;
        }

        if ($buffer !== '') {
            if (preg_match('/^[A-Da-d][\.\)]\s+/', $line) || preg_match('/^(?:option|answer)\s+/i', $line)) {
                $buffer .= ' ' . $line;
                continue;
            }

            if (preg_match('/^(?:\d+[\.\)]|question\s*\d+)/i', $line)) {
                continue;
            }

            $buffer .= ' ' . $line;
        }
    }

    if (trim($buffer) !== '') {
        $questions[] = trim($buffer);
    }

    return array_values(array_filter(array_map(
        fn ($question) => trim(preg_replace('/\s+/', ' ', $question) ?? $question),
        $questions,
    )));
}

function extractExamQuestionsFromUpload(UploadedFile $file): array
{
    $extension = strtolower($file->getClientOriginalExtension());
    $path = $file->getRealPath() ?: $file->path();
    $text = match ($extension) {
        'docx' => extractExamQuestionTextFromDocx($path),
        'pdf' => extractExamQuestionTextFromPdf($path),
        default => file_exists($path) ? (string) file_get_contents($path) : '',
    };

    return extractExamQuestionsFromText($text);
}

function syncExamQuestions(Exam $exam, array $questions): void
{
    $exam->questions()->delete();

    foreach (array_values($questions) as $index => $questionText) {
        if (! is_string($questionText) || trim($questionText) === '') {
            continue;
        }

        $exam->questions()->create([
            'question_text' => trim($questionText),
            'sort_order' => $index + 1,
        ]);
    }
}

function notifyStudentsAboutExam(Exam $exam, string $title, string $message): void
{
    $class = $exam->relationLoaded('class') ? $exam->class : $exam->class()->with('students')->first();

    if (! $class) {
        return;
    }

    foreach ($class->students as $student) {
        Notification::create([
            'user_id' => $student->id,
            'title' => $title,
            'message' => $message,
            'type' => 'exam',
            'link' => route('student.exams.submit', $exam->id),
        ]);
    }
}

function storeStudentDocument(Request $request, string $field): ?string
{
    if (! $request->hasFile($field)) {
        return null;
    }

    Storage::disk('public')->makeDirectory('student-documents');

    return $request->file($field)->store('student-documents', 'public');
}

function parsePhpIniSizeToKilobytes(string $value): int
{
    $value = trim($value);

    if ($value === '') {
        return 0;
    }

    $unit = strtolower(substr($value, -1));
    $number = (float) $value;

    return match ($unit) {
        'g' => (int) ($number * 1024 * 1024),
        'm' => (int) ($number * 1024),
        'k' => (int) $number,
        default => (int) ceil($number / 1024),
    };
}

function studentDocumentMaxKilobytes(): int
{
    return min(APP_MAX_UPLOAD_KILOBYTES, requestUploadMaxKilobytes());
}

function studentDocumentValidationRules(string $field, string $mimes): array
{
    return ['nullable', 'file', 'mimes:' . $mimes, 'max:' . studentDocumentMaxKilobytes()];
}

function studentDocumentValidationMessages(): array
{
    $maxMb = round(studentDocumentMaxKilobytes() / 1024, 2);

    return [
        'birth_certificate.uploaded' => "Birth certificate upload failed. The server currently accepts files up to {$maxMb} MB.",
        'birth_certificate.max' => "Birth certificate must be {$maxMb} MB or smaller.",
        'report_form.uploaded' => "Report form upload failed. The server currently accepts files up to {$maxMb} MB.",
        'report_form.max' => "Report form must be {$maxMb} MB or smaller.",
    ];
}

function requestUploadMaxKilobytes(): int
{
    return min(
        parsePhpIniSizeToKilobytes(ini_get('upload_max_filesize')),
        parsePhpIniSizeToKilobytes(ini_get('post_max_size')),
    );
}

function submissionUploadMaxKilobytes(): int
{
    return min(APP_MAX_UPLOAD_KILOBYTES, requestUploadMaxKilobytes());
}

function submissionUploadValidationRules(bool $required = true): array
{
    return [
        $required ? 'required' : 'nullable',
        'file',
        'max:' . submissionUploadMaxKilobytes(),
    ];
}

function submissionUploadValidationMessages(string $label = 'File'): array
{
    $maxMb = round(submissionUploadMaxKilobytes() / 1024, 2);

    return [
        'file.uploaded' => "{$label} upload failed. The server currently accepts files up to {$maxMb} MB.",
        'file.max' => "{$label} must be {$maxMb} MB or smaller.",
    ];
}

function storeSubmissionFile(Request $request, string $field, string $directory): ?string
{
    if (! $request->hasFile($field)) {
        return null;
    }

    Storage::disk('public')->makeDirectory($directory);

    return $request->file($field)->store($directory, 'public');
}

function notifyTrainerOfSubmission(?int $trainerId, string $title, string $message): void
{
    if (! $trainerId) {
        return;
    }

    Notification::create([
        'user_id' => $trainerId,
        'title' => $title,
        'message' => $message,
        'type' => 'submission',
    ]);
}

function submissionFileResponse(string $path)
{
    abort_unless(Storage::disk('public')->exists($path), 404);

    $absolutePath = Storage::disk('public')->path($path);
    $extension = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));

    if (in_array($extension, ['pdf', 'jpg', 'jpeg', 'png', 'txt'], true)) {
        return response()->file($absolutePath);
    }

    return response()->download($absolutePath);
}

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/login', function () {
    return view('welcome');
})->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required', 'string'],
    ]);

    if (Auth::attempt($credentials, $request->boolean('remember'))) {
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    return back()
        ->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])
        ->onlyInput('email');
})->name('login.attempt');

Route::get('/register', function () {
    return view('register');
})->name('register');

Route::post('/register', function (Request $request) {
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'max:255', 'unique:users'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
    ]);

    $user = User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
    ]);

    Auth::login($user);

    return redirect()->route('dashboard')->with('status', 'Account created successfully! Welcome to your dashboard.');
})->name('register.store');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $user = Auth::user();

        if ($user->role === 'trainer') {
            $classes = $user->taughtClasses()->withCount('students')->get();

            return view('dashboard.trainer', compact('classes'));
        }

        if ($user->role === 'student') {
            $class = studentCurrentClass($user);
            $classes = $class ? collect([$class]) : collect();
            $availableClasses = $class
                ? collect()
                : ClassRoom::with('trainer')
                    ->where('status', 'active')
                    ->orderBy('name')
                    ->get();

            return view('dashboard.student', compact('classes', 'availableClasses'));
        }

        if ($user->role === 'accountant') {
            $dashboard = accountantDashboardData();

            return view('dashboard.accountant', $dashboard);
        }

        if ($user->role === 'manager') {
            $dashboard = app(AccountingService::class)->dashboardMetrics();

            return view('accounting.dashboard', $dashboard);
        }

        if (in_array($user->role, ['admin', 'department_admin'], true)) {
            $userQuery = User::query();

            if ($user->role === 'department_admin' && $user->department) {
                $userQuery->where('department', $user->department);
            }

            $totalUsers = (clone $userQuery)->count();
            $students = (clone $userQuery)->where('role', 'student')->count();
            $trainers = (clone $userQuery)->where('role', 'trainer')->count();

            return view('dashboard.admin', compact('totalUsers', 'students', 'trainers'));
        }

        if ($user->role === 'career_coach') {
            $students = $user->assignedStudents()
                ->where('role', 'student')
                ->with(['enrolledClasses.trainer', 'homeworkSubmissions'])
                ->get();

            return view('dashboard.career_coach', compact('students'));
        }

        return view('dashboard');
    })->name('dashboard');

    Route::get('/profile', function () {
        return view('profile.show');
    })->name('profile.show');

    Route::get('/profile/edit', function () {
        return view('profile.edit');
    })->name('profile.edit');

    Route::put('/profile', function (Request $request) {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'department' => ['nullable', 'string', 'max:255'],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'confirmed', 'min:8'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->department = $validated['department'] ?? null;

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('profile.show')->with('status', 'Profile updated successfully.');
    })->name('profile.update');

    Route::get('/notifications', function (Request $request) {
        $filter = $request->query('filter');
        $query = Auth::user()->notifications()->latest();

        if ($filter === 'unread') {
            $query->whereNull('read_at');
        } elseif ($filter === 'homework') {
            $query->where('type', 'homework');
        } elseif ($filter === 'attendance') {
            $query->where('type', 'attendance');
        } elseif ($filter === 'grade') {
            $query->where('type', 'grade');
        } elseif ($filter === 'submission') {
            $query->where('type', 'submission');
        } elseif ($filter === 'exam') {
            $query->where('type', 'exam');
        }

        $allNotifications = Auth::user()->notifications();

        return view('notifications.index', [
            'notifications' => $query->paginate(10)->withQueryString(),
            'unreadCount' => (clone $allNotifications)->whereNull('read_at')->count(),
            'homeworkCount' => (clone $allNotifications)->where('type', 'homework')->count(),
            'attendanceCount' => (clone $allNotifications)->where('type', 'attendance')->count(),
            'submissionCount' => (clone $allNotifications)->where('type', 'submission')->count(),
            'examCount' => (clone $allNotifications)->where('type', 'exam')->count(),
        ]);
    })->name('notifications.index');

    Route::post('/notifications/mark-all-as-read', function () {
        Auth::user()->notifications()
            ->whereNull('read_at')
            ->update([
                'read' => true,
                'read_at' => now(),
            ]);

        return back()->with('status', 'All notifications marked as read.');
    })->name('notifications.mark-all-as-read');

    Route::post('/notifications/{notification}/mark-as-read', function (Notification $notification) {
        abort_unless($notification->user_id === Auth::id(), 403);

        $notification->markAsRead();

        return back()->with('status', 'Notification marked as read.');
    })->name('notifications.mark-as-read');

    Route::delete('/notifications/{notification}', function (Notification $notification) {
        abort_unless($notification->user_id === Auth::id(), 403);

        $notification->delete();

        return back()->with('status', 'Notification deleted.');
    })->name('notifications.delete');

    Route::get('/homework-submissions/{submission}/file', function (HomeworkSubmission $submission) {
        $user = Auth::user();

        $canView = $submission->file_path && (
            $submission->student_id === $user->id ||
            ($submission->homework && $submission->homework->trainer_id === $user->id) ||
            in_array($user->role, ['admin', 'department_admin'], true)
        );

        abort_unless($canView, 403);

        return submissionFileResponse($submission->file_path);
    })->name('homework-submissions.file');

    Route::get('/exam-submissions/{submission}/file', function (ExamSubmission $submission) {
        $user = Auth::user();

        $canView = $submission->file_path && (
            $submission->student_id === $user->id ||
            ($submission->exam && $submission->exam->trainer_id === $user->id) ||
            in_array($user->role, ['admin', 'department_admin'], true)
        );

        abort_unless($canView, 403);

        return submissionFileResponse($submission->file_path);
    })->name('exam-submissions.file');

    Route::middleware('role:trainer')->prefix('trainer')->group(function () {
        Route::get('/classes', function () {
            $classes = Auth::user()->taughtClasses()->withCount('students')->get();

            return view('trainer.classes.index', compact('classes'));
        })->name('trainer.classes.index');

        Route::get('/classes/create', function () {
            return view('trainer.classes.create');
        })->name('trainer.classes.create');

        Route::get('/classes/{id}', function ($id) {
            $class = Auth::user()->taughtClasses()
                ->with(['students', 'timetables', 'homeworks.submissions', 'exams.submissions'])
                ->withCount('students')
                ->findOrFail($id);

            return view('trainer.classes.show', compact('class'));
        })->name('trainer.classes.show');

        Route::post('/classes', function (Request $request) {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'room_number' => ['nullable', 'string', 'max:255'],
                'delivery_mode' => ['required', 'in:online,physical'],
                'description' => ['nullable', 'string'],
            ]);

            $class = new ClassRoom([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'delivery_mode' => $validated['delivery_mode'],
                'status' => 'active',
            ]);
            $class->room_number = $validated['room_number'] ?? null;
            $class->trainer_id = Auth::id();
            $class->save();

            return redirect()->route('trainer.classes.index')->with('status', 'Class created successfully.');
        })->name('trainer.classes.store');

        Route::get('/classes/{id}/edit', function ($id) {
            $class = Auth::user()->taughtClasses()->findOrFail($id);

            return view('trainer.classes.edit', compact('class'));
        })->name('trainer.classes.edit');

        Route::post('/classes/{id}', function ($id, Request $request) {
            $class = Auth::user()->taughtClasses()->findOrFail($id);
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'room_number' => ['nullable', 'string', 'max:255'],
                'delivery_mode' => ['required', 'in:online,physical'],
                'description' => ['nullable', 'string'],
            ]);

            $class->update($validated);

            return redirect()->route('trainer.classes.index')->with('status', 'Class updated.');
        })->name('trainer.classes.update');

        Route::delete('/classes/{id}/delete', function ($id) {
            $class = Auth::user()->taughtClasses()->findOrFail($id);
            $class->delete();

            return redirect()->route('trainer.classes.index')->with('status', 'Class deleted.');
        })->name('trainer.classes.delete');

        Route::get('/classes/{id}/timetable', function ($id) {
            $class = Auth::user()->taughtClasses()->findOrFail($id);
            $timetables = $class->timetables()->orderBy('day_of_week')->get();

            return view('trainer.timetable.index', compact('class', 'timetables'));
        })->name('trainer.timetable.index');

        Route::get('/classes/{id}/timetable/create', function ($id) {
            $class = Auth::user()->taughtClasses()->findOrFail($id);

            return view('trainer.timetable.form', [
                'class' => $class,
                'timetable' => new Timetable(),
                'mode' => 'create',
            ]);
        })->name('trainer.timetable.create');

        Route::post('/classes/{id}/timetable', function ($id, Request $request) {
            $class = Auth::user()->taughtClasses()->findOrFail($id);
            $validated = $request->validate([
                'day_of_week' => ['required', 'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday'],
                'start_time' => ['required', 'date_format:H:i'],
                'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
                'topic' => ['nullable', 'string', 'max:255'],
                'meeting_link' => ['nullable', 'url'],
            ]);

            $class->timetables()->create($validated);

            return redirect()->route('trainer.timetable.index', $class->id)->with('status', 'Timetable slot created successfully.');
        })->name('trainer.timetable.store');

        Route::get('/timetable/{timetable}/edit', function (Timetable $timetable) {
            abort_unless($timetable->class && $timetable->class->trainer_id === Auth::id(), 403);

            return view('trainer.timetable.form', [
                'class' => $timetable->class,
                'timetable' => $timetable,
                'mode' => 'edit',
            ]);
        })->name('trainer.timetable.edit');

        Route::post('/timetable/{timetable}', function (Timetable $timetable, Request $request) {
            abort_unless($timetable->class && $timetable->class->trainer_id === Auth::id(), 403);
            $validated = $request->validate([
                'day_of_week' => ['required', 'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday'],
                'start_time' => ['required', 'date_format:H:i'],
                'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
                'topic' => ['nullable', 'string', 'max:255'],
                'meeting_link' => ['nullable', 'url'],
            ]);

            $timetable->update($validated);

            return redirect()->route('trainer.timetable.index', $timetable->class_id)->with('status', 'Timetable slot updated.');
        })->name('trainer.timetable.update');

        Route::delete('/timetable/{timetable}/delete', function (Timetable $timetable) {
            abort_unless($timetable->class && $timetable->class->trainer_id === Auth::id(), 403);
            $classId = $timetable->class_id;
            $timetable->delete();

            return redirect()->route('trainer.timetable.index', $classId)->with('status', 'Timetable slot deleted.');
        })->name('trainer.timetable.delete');

        Route::get('/classes/{id}/homework', function ($id) {
            $class = Auth::user()->taughtClasses()->findOrFail($id);
            $homeworks = $class->homeworks()->withCount('submissions')->latest('due_date')->get();

            return view('trainer.homework.index', compact('class', 'homeworks'));
        })->name('trainer.homework.index');

        Route::get('/classes/{id}/exams', function ($id) {
            $class = Auth::user()->taughtClasses()->findOrFail($id);
            $exams = $class->exams()->withCount(['submissions', 'questions'])->orderByRaw('exam_date is null')->orderBy('exam_date')->get();
            $recentSubmissions = ExamSubmission::with(['student', 'exam'])
                ->whereHas('exam', fn ($query) => $query->where('class_id', $class->id))
                ->latest('submitted_at')
                ->latest('updated_at')
                ->take(12)
                ->get();

            return view('trainer.exams.index', compact('class', 'exams', 'recentSubmissions'));
        })->name('trainer.exams.index');

        Route::get('/classes/{id}/exams/create', function ($id) {
            $class = Auth::user()->taughtClasses()->findOrFail($id);

            return view('trainer.exams.form', [
                'class' => $class,
                'exam' => new Exam(),
                'mode' => 'create',
                'questions' => collect(range(1, 5))->map(fn () => ''),
            ]);
        })->name('trainer.exams.create');

        Route::post('/classes/{id}/exams', function ($id, Request $request) {
            $class = Auth::user()->taughtClasses()->with('students')->findOrFail($id);
            $validated = $request->validate([
                'title' => ['required', 'string', 'max:255'],
                'description' => ['required', 'string'],
                'exam_date' => ['nullable', 'date'],
                'exam_mode' => ['required', 'in:online,physical'],
                'submission_type' => ['required', 'in:written,file,upload'],
                'status' => ['required', 'in:open,closed'],
                'questions' => ['nullable', 'array', 'max:5'],
                'questions.*' => ['nullable', 'string', 'max:1000'],
                'question_file' => ['nullable', 'file', 'mimes:txt,md,csv,pdf,docx'],
            ]);

            $questionTexts = [];

            if ($request->hasFile('question_file')) {
                $questionTexts = extractExamQuestionsFromUpload($request->file('question_file'));
            } else {
                $questionTexts = array_values(array_filter($validated['questions'] ?? [], fn ($question) => is_string($question) && trim($question) !== ''));
            }

            if ($validated['exam_mode'] === 'online' && count($questionTexts) === 0) {
                return back()
                    ->withErrors(['questions' => 'Online exams need at least one question. Upload a question file or enter the questions manually.'])
                    ->withInput();
            }

            $examData = collect($validated)->except('questions')->all();

            $exam = $class->exams()->create([
                ...$examData,
                'trainer_id' => Auth::id(),
                'exam_mode' => $validated['exam_mode'],
                'submission_type' => $validated['submission_type'] === 'file' ? 'upload' : $validated['submission_type'],
            ]);

            syncExamQuestions($exam, $validated['exam_mode'] === 'online' ? $questionTexts : []);

            if ($validated['exam_mode'] === 'online') {
                notifyStudentsAboutExam(
                    $exam,
                    'New online exam published',
                    "An online exam, {$exam->title}, is ready for {$class->name}. Use the link to open it."
                );
            } else {
                foreach ($class->students as $student) {
                    Notification::create([
                        'user_id' => $student->id,
                        'title' => 'New exam scheduled',
                        'message' => "New exam: {$exam->title} for {$class->name}.",
                        'type' => 'exam',
                        'link' => route('student.exams.submit', $exam->id),
                    ]);
                }
            }

            return redirect()->route('trainer.exams.index', $class->id)->with('status', 'Exam created successfully.');
        })->name('trainer.exams.store');

        Route::delete('/exams/{exam}/delete', function (Exam $exam) {
            abort_unless($exam->class && $exam->class->trainer_id === Auth::id(), 403);
            $classId = $exam->class_id;
            $exam->delete();

            return redirect()->route('trainer.exams.index', $classId)->with('status', 'Exam deleted.');
        })->name('trainer.exams.delete');

        Route::get('/exams/{id}/submissions', function ($id) {
            $exam = Exam::whereHas('class', function ($query) {
                $query->where('trainer_id', Auth::id());
            })->findOrFail($id);

            $submissions = $exam->submissions()->with('student')->get();

            return view('trainer.exams.submissions', compact('exam', 'submissions'));
        })->name('trainer.exams.submissions');

        Route::post('/exam-submissions/{id}/grade', function ($id, Request $request) {
            $submission = ExamSubmission::with('exam')->findOrFail($id);
            abort_unless($submission->exam && $submission->exam->trainer_id === Auth::id(), 403);

            $validated = $request->validate([
                'marks' => ['required', 'integer', 'min:0', 'max:100'],
                'feedback' => ['nullable', 'string'],
            ]);

            $submission->marks = $validated['marks'];
            $submission->feedback = $validated['feedback'] ?? null;
            $submission->status = 'graded';
            $submission->save();

            Notification::create([
                'user_id' => $submission->student_id,
                'title' => 'Exam graded',
                'message' => "Your exam submission for {$submission->exam->title} has been graded.",
                'type' => 'grade',
            ]);

            return back()->with('status', 'Exam grade saved.');
        })->name('trainer.exams.grade');

        Route::get('/classes/{id}/homework/create', function ($id) {
            $class = Auth::user()->taughtClasses()->findOrFail($id);

            return view('trainer.homework.form', [
                'class' => $class,
                'homework' => new Homework(),
                'mode' => 'create',
            ]);
        })->name('trainer.homework.create');

        Route::post('/classes/{id}/homework', function ($id, Request $request) {
            $class = Auth::user()->taughtClasses()->findOrFail($id);
            $validated = $request->validate([
                'title' => ['required', 'string', 'max:255'],
                'description' => ['required', 'string'],
                'submission_type' => ['required', 'in:written,file,upload'],
                'due_date' => ['nullable', 'date'],
                'status' => ['required', 'in:open,closed'],
            ]);

            $homework = $class->homeworks()->create([
                ...$validated,
                'trainer_id' => Auth::id(),
                'submission_type' => $validated['submission_type'] === 'file' ? 'upload' : $validated['submission_type'],
            ]);

            foreach ($class->students as $student) {
                Notification::create([
                    'user_id' => $student->id,
                    'title' => 'New homework assigned',
                    'message' => "New homework: {$homework->title} for {$class->name}.",
                    'type' => 'homework',
                ]);
            }

            return redirect()->route('trainer.homework.index', $class->id)->with('status', 'Homework created successfully.');
        })->name('trainer.homework.store');

        Route::get('/homework/{homework}/edit', function (Homework $homework) {
            abort_unless($homework->class && $homework->class->trainer_id === Auth::id(), 403);

            return view('trainer.homework.form', [
                'class' => $homework->class,
                'homework' => $homework,
                'mode' => 'edit',
            ]);
        })->name('trainer.homework.edit');

        Route::post('/homework/{homework}', function (Homework $homework, Request $request) {
            abort_unless($homework->class && $homework->class->trainer_id === Auth::id(), 403);
            $validated = $request->validate([
                'title' => ['required', 'string', 'max:255'],
                'description' => ['required', 'string'],
                'submission_type' => ['required', 'in:written,file,upload'],
                'due_date' => ['nullable', 'date'],
                'status' => ['required', 'in:open,closed'],
            ]);

            $homework->update([
                ...$validated,
                'submission_type' => $validated['submission_type'] === 'file' ? 'upload' : $validated['submission_type'],
            ]);

            return redirect()->route('trainer.homework.index', $homework->class_id)->with('status', 'Homework updated successfully.');
        })->name('trainer.homework.update');

        Route::post('/homework/{homework}/delete', function (Homework $homework) {
            abort_unless($homework->class && $homework->class->trainer_id === Auth::id(), 403);
            $classId = $homework->class_id;
            $homework->delete();

            return redirect()->route('trainer.homework.index', $classId)->with('status', 'Homework deleted.');
        })->name('trainer.homework.delete');

        Route::get('/classes/{id}/attendance', function ($id) {
            $class = Auth::user()->taughtClasses()->with('students')->findOrFail($id);
            $attendance = Attendance::where('class_id', $class->id)->get()->keyBy('student_id');

            return view('trainer.attendance.index', compact('class', 'attendance'));
        })->name('trainer.attendance.index');

        Route::post('/classes/{id}/attendance', function ($id, Request $request) {
            $class = Auth::user()->taughtClasses()->with('students')->findOrFail($id);
            $firstTimetable = $class->timetables()->first();
            $attendanceDate = $request->validate([
                'attendance_date' => ['nullable', 'date'],
                'attendance' => ['required', 'array'],
                'attendance.*' => ['required', 'in:present,absent,late,excused'],
            ])['attendance_date'] ?? now()->toDateString();

            if (! $firstTimetable) {
                return back()->with('error', 'Create at least one timetable slot before marking attendance.');
            }

            foreach ($class->students as $student) {
                $status = $request->input("attendance.{$student->id}", 'absent');

                Attendance::updateOrCreate(
                    [
                        'attendance_date' => $attendanceDate,
                        'scope_type' => 'class',
                        'scope_id' => $class->id,
                        'class_id' => $class->id,
                        'student_id' => $student->id,
                    ],
                    [
                        'department_id' => null,
                        'timetable_id' => $firstTimetable->id,
                        'status' => $status,
                        'marked_at' => now(),
                        'recorded_by' => Auth::id(),
                        'source' => 'bulk',
                    ]
                );

                Notification::create([
                    'user_id' => $student->id,
                    'title' => 'Attendance Updated',
                    'message' => "Your attendance for {$class->name} on {$attendanceDate} was marked as " . ucfirst($status) . '.',
                    'type' => 'attendance',
                ]);
            }

            return back()->with('status', 'Attendance saved successfully.');
        })->name('trainer.attendance.store');

        Route::get('/homework/{id}/submissions', function ($id) {
            $homework = Homework::whereHas('class', function ($query) {
                $query->where('trainer_id', Auth::id());
            })->findOrFail($id);

            $submissions = $homework->submissions()->with('student')->get();

            return view('trainer.homework.submissions', compact('homework', 'submissions'));
        })->name('trainer.homework.submissions');

        Route::post('/homework/{id}/grade', function ($id, Request $request) {
            $submission = HomeworkSubmission::with('homework')->findOrFail($id);
            abort_unless($submission->homework && $submission->homework->trainer_id === Auth::id(), 403);

            $validated = $request->validate([
                'marks' => ['required', 'integer', 'min:0', 'max:100'],
                'feedback' => ['nullable', 'string'],
            ]);

            $submission->marks = $validated['marks'];
            $submission->feedback = $validated['feedback'] ?? null;
            $submission->status = 'graded';
            $submission->save();

            Notification::create([
                'user_id' => $submission->student_id,
                'title' => 'Homework graded',
                'message' => "Your submission for {$submission->homework->title} has been graded.",
                'type' => 'grade',
            ]);

            return back()->with('status', 'Marks saved.');
        })->name('trainer.homework.grade');

        Route::get('/exams/{id}/submissions', function ($id) {
            $exam = Exam::with(['questions', 'class'])
                ->whereHas('class', fn ($query) => $query->where('trainer_id', Auth::id()))
                ->findOrFail($id);
            $submissions = $exam->submissions()->with('student')->latest('submitted_at')->get();
            $passMark = 50;

            return view('trainer.exams.submissions', compact('exam', 'submissions', 'passMark'));
        })->name('trainer.exams.submissions');

        Route::post('/exams/{id}/grade', function ($id, Request $request) {
            $submission = ExamSubmission::with('exam')->findOrFail($id);
            abort_unless($submission->exam && $submission->exam->trainer_id === Auth::id(), 403);

            $validated = $request->validate([
                'marks' => ['required', 'integer', 'min:0', 'max:100'],
                'feedback' => ['nullable', 'string'],
            ]);

            $submission->marks = $validated['marks'];
            $submission->feedback = $validated['feedback'] ?? null;
            $submission->status = 'graded';
            $submission->save();

            Notification::create([
                'user_id' => $submission->student_id,
                'title' => 'Exam graded',
                'message' => "Your exam {$submission->exam->title} has been graded.",
                'type' => 'grade',
                'link' => route('student.exams.submit', $submission->exam_id),
            ]);

            return back()->with('status', 'Exam grade saved.');
        })->name('trainer.exams.grade');
    });

    Route::middleware('role:student')->prefix('student')->group(function () {
        Route::get('/classes/{class}', function (ClassRoom $class) {
            $student = Auth::user();
            abort_unless($student->current_class_id === $class->id, 403);

            $class->load([
                'trainer',
                'timetables',
                'homeworks' => fn ($query) => $query->with([
                    'submissions' => fn ($submissionQuery) => $submissionQuery->where('student_id', Auth::id()),
                ])->orderBy('due_date'),
                'exams' => fn ($query) => $query->with([
                    'submissions' => fn ($submissionQuery) => $submissionQuery->where('student_id', Auth::id()),
                ])->orderBy('exam_date'),
            ]);

            return view('student.classes.show', compact('class'));
        })->name('student.classes.show');

        Route::post('/classes/{class}/enroll', function (ClassRoom $class) {
            abort_unless($class->status === 'active', 403);

            $student = Auth::user();

            if ($student->current_class_id === $class->id) {
                return redirect()->route('dashboard')->with('status', 'You are already enrolled in that class.');
            }

            if ($student->current_class_id && $student->current_class_id !== $class->id) {
                return redirect()->route('dashboard')->with('status', 'You are already enrolled in another class. Unenroll first to join a different one.');
            }

            $student->current_class_id = $class->id;
            $student->save();
            syncStudentEnrollment($student);

            if ($class->trainer_id) {
                Notification::create([
                    'user_id' => $class->trainer_id,
                    'title' => 'New student enrolled',
                    'message' => $student->name . " enrolled in {$class->name}.",
                    'type' => 'class',
                ]);
            }

            return redirect()->route('dashboard')->with('status', "You have enrolled in {$class->name}.");
        })->name('student.classes.enroll');

        Route::post('/classes/{class}/unenroll', function (ClassRoom $class) {
            $student = Auth::user();

            if ($student->current_class_id !== $class->id) {
                return redirect()->route('dashboard')->with('status', 'You are not enrolled in that class.');
            }

            $student->current_class_id = null;
            $student->save();
            syncStudentEnrollment($student);

            return redirect()->route('dashboard')->with('status', "You have unenrolled from {$class->name}.");
        })->name('student.classes.unenroll');

        Route::get('/timetable', function () {
            $class = studentCurrentClass(Auth::user());
            $classes = $class ? collect([$class]) : collect();
            $dayOrder = [
                'Monday' => 1,
                'Tuesday' => 2,
                'Wednesday' => 3,
                'Thursday' => 4,
                'Friday' => 5,
                'Saturday' => 6,
                'Sunday' => 7,
            ];
            $timetables = Timetable::with(['class.trainer'])
                ->when($class, fn ($query) => $query->where('class_id', $class->id), fn ($query) => $query->whereRaw('1 = 0'))
                ->get()
                ->sortBy(fn ($slot) => sprintf('%02d-%s', $dayOrder[$slot->day_of_week] ?? 99, $slot->start_time))
                ->values();

            return view('student.timetable.index', compact('timetables', 'classes'));
        })->name('student.timetable.index');

        Route::get('/homework', function () {
            $class = studentCurrentClass(Auth::user());
            $homeworks = Homework::with([
                'class.trainer',
                'submissions' => fn ($query) => $query->where('student_id', Auth::id()),
            ])
                ->when($class, fn ($query) => $query->where('class_id', $class->id), fn ($query) => $query->whereRaw('1 = 0'))
                ->orderByRaw('due_date is null')
                ->orderBy('due_date')
                ->get();

            return view('student.homework.index', compact('homeworks'));
        })->name('student.homework.index');

        Route::get('/exams', function () {
            $class = studentCurrentClass(Auth::user());
            $exams = Exam::with([
                'class.trainer',
                'submissions' => fn ($query) => $query->where('student_id', Auth::id()),
            ])
                ->withCount('questions')
                ->when($class, fn ($query) => $query->where('class_id', $class->id), fn ($query) => $query->whereRaw('1 = 0'))
                ->orderByRaw('exam_date is null')
                ->orderBy('exam_date')
                ->get();

            return view('student.exams.index', compact('exams'));
        })->name('student.exams.index');

        Route::get('/exams/{id}/submit', function ($id) {
            $class = studentCurrentClass(Auth::user());
            $exam = Exam::with('class.trainer')
                ->with('questions')
                ->when($class, fn ($query) => $query->where('class_id', $class->id), fn ($query) => $query->whereRaw('1 = 0'))
                ->findOrFail($id);
            $submission = ExamSubmission::where('exam_id', $id)
                ->where('student_id', Auth::id())
                ->first();

            return view('student.exams.submit', compact('exam', 'submission'));
        })->name('student.exams.submit');

        Route::post('/exams/{id}/submit', function ($id, Request $request) {
            $class = studentCurrentClass(Auth::user());
            $exam = Exam::with('questions')
                ->when($class, fn ($query) => $query->where('class_id', $class->id), fn ($query) => $query->whereRaw('1 = 0'))
                ->findOrFail($id);
            abort_if($exam->status === 'closed', 403);

            $existingSubmission = ExamSubmission::where('exam_id', $id)
                ->where('student_id', Auth::id())
                ->first();

            if ($existingSubmission) {
                return back()->with('error', 'You have already submitted this exam. Updates are not allowed.');
            }

            $submission = new ExamSubmission([
                'exam_id' => $id,
                'student_id' => Auth::id(),
            ]);

            if ($exam->isOnline()) {
                $answerRules = ['answers' => ['required', 'array']];
                foreach ($exam->questions as $question) {
                    $answerRules["answers.{$question->id}"] = ['required', 'string', 'max:5000'];
                }

                $validated = $request->validate($answerRules);

                $answers = [];
                foreach ($exam->questions as $question) {
                    $answers[$question->id] = $validated['answers'][$question->id] ?? '';
                }

                $submission->content = null;
                $submission->answers_json = $answers;
                $submission->file_path = null;
            } else {
                if ($exam->submission_type === 'written') {
                    $request->validate([
                        'content' => ['required', 'string'],
                    ]);

                    $submission->content = $request->input('content');
                    $submission->answers_json = null;
                    $submission->file_path = null;
                } else {
                    $request->validate([
                        'file' => submissionUploadValidationRules(! $submission->file_path),
                    ], submissionUploadValidationMessages('Exam file'));

                    if ($request->hasFile('file')) {
                        $previousPath = $submission->file_path;
                        $path = storeSubmissionFile($request, 'file', 'exams');
                        $submission->file_path = $path;
                        $submission->content = null;
                        $submission->answers_json = null;

                        if ($previousPath) {
                            Storage::disk('public')->delete($previousPath);
                        }
                    }
                }
            }

            $submission->status = 'submitted';
            $submission->submitted_at = now();
            $submission->save();

            notifyTrainerOfSubmission(
                $exam->trainer_id,
                $request->hasFile('file') ? 'New exam upload' : 'New exam submission',
                Auth::user()->name . ($request->hasFile('file')
                    ? " uploaded an exam file for {$exam->title}."
                    : " submitted {$exam->title}.")
            );

            return redirect()->route('student.exams.index')->with('status', 'Exam submitted.');
        })->name('student.exams.store');

        Route::get('/homework/{id}/submit', function ($id) {
            $class = studentCurrentClass(Auth::user());
            $homework = Homework::with('class.trainer')
                ->when($class, fn ($query) => $query->where('class_id', $class->id), fn ($query) => $query->whereRaw('1 = 0'))
                ->findOrFail($id);
            $submission = HomeworkSubmission::where('homework_id', $id)
                ->where('student_id', Auth::id())
                ->first();

            return view('student.homework.submit', compact('homework', 'submission'));
        })->name('student.homework.submit');

        Route::post('/homework/{id}/submit', function ($id, Request $request) {
            $class = studentCurrentClass(Auth::user());
            $homework = Homework::when($class, fn ($query) => $query->where('class_id', $class->id), fn ($query) => $query->whereRaw('1 = 0'))->findOrFail($id);
            $existingSubmission = HomeworkSubmission::where('homework_id', $id)
                ->where('student_id', Auth::id())
                ->first();

            if ($existingSubmission) {
                return back()->with('error', 'You have already submitted this homework. Updates are not allowed.');
            }

            $submission = new HomeworkSubmission([
                'homework_id' => $id,
                'student_id' => Auth::id(),
            ]);

            if ($homework->submission_type === 'written') {
                $request->validate([
                    'content' => ['required', 'string'],
                ]);

                $submission->content = $request->input('content');
                $submission->file_path = null;
            } else {
                $request->validate([
                    'file' => submissionUploadValidationRules(! $submission->file_path),
                ], submissionUploadValidationMessages('Homework file'));

                if ($request->hasFile('file')) {
                    $previousPath = $submission->file_path;
                    $path = storeSubmissionFile($request, 'file', 'homework');
                    $submission->file_path = $path;
                    $submission->content = null;

                    if ($previousPath) {
                        Storage::disk('public')->delete($previousPath);
                    }
                }
            }

            $submission->status = 'submitted';
            $submission->submitted_at = now();
            $submission->save();

            notifyTrainerOfSubmission(
                $homework->trainer_id,
                $request->hasFile('file') ? 'New homework upload' : 'New homework submission',
                Auth::user()->name . ($request->hasFile('file')
                    ? " uploaded homework for {$homework->title}."
                    : " submitted {$homework->title}.")
            );

            return redirect()->route('student.homework.index')->with('status', 'Homework submitted.');
        })->name('student.homework.store');

        Route::get('/attendance', function () {
            $attendance = Auth::user()->attendanceRecords()
                ->with(['classRoom', 'department', 'timetable', 'recordedBy'])
                ->when(request()->filled('date'), fn ($query) => $query->whereDate('attendance_date', request('date')))
                ->when(request()->filled('class_id'), fn ($query) => $query->where('class_id', request('class_id')))
                ->when(request()->filled('department_id'), fn ($query) => $query->where('department_id', request('department_id')))
                ->latest('attendance_date')
                ->latest('marked_at')
                ->get();
            $total = $attendance->count();
            $presentCount = $attendance->where('status', 'present')->count();
            $lateCount = $attendance->where('status', 'late')->count();
            $excusedCount = $attendance->where('status', 'excused')->count();
            $absentCount = $attendance->where('status', 'absent')->count();
            $attendancePercentage = $total > 0 ? (($presentCount + $lateCount + $excusedCount) / $total) * 100 : 0;

            return view('student.attendance.index', compact('attendance', 'attendancePercentage', 'presentCount', 'lateCount', 'excusedCount', 'absentCount'));
        })->name('student.attendance.index');
    });

    Route::middleware('role:career_coach')->prefix('career-coach')->group(function () {
        Route::get('/students/{student}', function (User $student) {
            abort_unless($student->career_coach_id === Auth::id(), 403);

            $student->load(['enrolledClasses.trainer', 'homeworkSubmissions.homework']);

            return view('career-coach.student', compact('student'));
        })->name('career-coach.students.show');
    });

    Route::middleware('auth')->prefix('attendance')->group(function () {
        Route::post('/check-in', [AttendanceController::class, 'checkIn'])->name('attendance.check-in');
        Route::post('/check-out', [AttendanceController::class, 'checkOut'])->name('attendance.check-out');
    });

    Route::middleware('role:accountant')->prefix('accountant')->group(function () {
        Route::get('/', function () {
            return view('dashboard.accountant', accountantDashboardData());
        })->name('accountant.dashboard');

        Route::get('/payments/{payment}/edit', function (FeePayment $payment) {
            $students = User::where('role', 'student')->orderBy('name')->get();

            return view('accountant.payments.edit', compact('payment', 'students'));
        })->name('accountant.payments.edit');

        Route::put('/payments/{payment}', function (FeePayment $payment, Request $request) {
            $validated = $request->validate([
                'student_id' => ['required', 'exists:users,id'],
                'academic_year' => ['nullable', 'string', 'max:255'],
                'term' => ['nullable', 'string', 'max:255'],
                'amount_due' => ['required', 'numeric', 'min:0'],
                'amount_paid' => ['required', 'numeric', 'min:0'],
                'payment_method' => ['required', 'in:cash,mpesa,pochi_la_biashara,bank_transfer,card,other'],
                'phone_number' => ['nullable', 'string', 'max:255'],
                'receipt_number' => ['nullable', 'string', 'max:255', 'unique:fee_payments,receipt_number,' . $payment->id],
                'paid_at' => ['nullable', 'date'],
                'status' => ['required', 'in:paid,partial,unpaid'],
                'notes' => ['nullable', 'string'],
            ]);

            $student = User::where('role', 'student')->findOrFail($validated['student_id']);

            $payment->update([
                ...$validated,
                'student_id' => $student->id,
                'paid_at' => $validated['paid_at'] ?? ($validated['status'] === 'unpaid' ? null : ($payment->paid_at ?? now())),
            ]);

            AuditLog::log('update', 'FeePayment', $payment->id, $validated, "Updated fee payment for {$student->name}");

            return redirect()->route('accountant.dashboard')->with('status', 'Fee payment updated successfully.');
        })->name('accountant.payments.update');

        Route::post('/payments/{payment}/daraja/stk-push', function (FeePayment $payment, Request $request, DarajaService $darajaService) {
            $validated = $request->validate([
                'phone_number' => ['required', 'string', 'max:255'],
                'amount' => ['nullable', 'numeric', 'min:1'],
            ]);

            $response = $darajaService->initiateStkPush(
                $payment,
                $validated['phone_number'],
                $validated['amount'] ?? null,
                $payment->receipt_number ?: 'FEE-' . $payment->id,
                'School fee payment'
            );

            $payment->fill([
                'payment_method' => 'mpesa',
                'phone_number' => $validated['phone_number'],
                'checkout_request_id' => data_get($response, 'CheckoutRequestID'),
                'merchant_request_id' => data_get($response, 'MerchantRequestID'),
                'daraja_response_code' => (string) data_get($response, 'ResponseCode'),
                'daraja_response_description' => data_get($response, 'ResponseDescription'),
                'daraja_payload' => $response,
                'daraja_requested_at' => now(),
            ]);
            $payment->save();

            AuditLog::log('update', 'FeePayment', $payment->id, ['daraja' => $response], "Sent Daraja STK push for fee payment #{$payment->id}");

            return back()->with('status', 'Daraja STK push sent successfully.');
        })->name('accountant.payments.daraja.push');
    });

    Route::middleware('role:admin,accountant,manager')->prefix('accounting')->group(function () {
        Route::get('/', [AccountingController::class, 'dashboard'])->name('accounting.dashboard');
        Route::get('/accounts', [AccountingController::class, 'accounts'])->name('accounting.accounts.index');
        Route::post('/accounts', [AccountingController::class, 'storeAccount'])->name('accounting.accounts.store');
        Route::put('/accounts/{account}', [AccountingController::class, 'updateAccount'])->name('accounting.accounts.update');
        Route::delete('/accounts/{account}', [AccountingController::class, 'destroyAccount'])->name('accounting.accounts.destroy');

        Route::get('/transactions', [AccountingController::class, 'transactions'])->name('accounting.transactions.index');
        Route::post('/transactions', [AccountingController::class, 'storeJournal'])->name('accounting.transactions.store');
        Route::put('/transactions/{journal}', [AccountingController::class, 'updateJournal'])->name('accounting.transactions.update');
        Route::delete('/transactions/{journal}', [AccountingController::class, 'destroyJournal'])->name('accounting.transactions.destroy');

        Route::get('/invoices', [AccountingController::class, 'invoices'])->name('accounting.invoices.index');
        Route::post('/invoices', [AccountingController::class, 'storeInvoice'])->name('accounting.invoices.store');
        Route::put('/invoices/{invoice}', [AccountingController::class, 'updateInvoice'])->name('accounting.invoices.update');
        Route::post('/invoices/{invoice}/payments', [AccountingController::class, 'recordPayment'])->name('accounting.invoices.payments.store');

        Route::get('/reports', [AccountingController::class, 'reports'])->name('accounting.reports.index');
        Route::get('/reports/export/{type}', [AccountingController::class, 'export'])->name('accounting.reports.export');
    });

    Route::middleware('role:admin,department_admin')->prefix('admin')->group(function () {
        Route::get('/users', function () {
            $viewer = Auth::user();
            $userQuery = User::withTrashed();

            if ($viewer->role === 'department_admin' && $viewer->department) {
                $userQuery->where('department', $viewer->department);
            }

            $users = $userQuery->with(['careerCoach', 'currentClass'])->orderBy('role')->orderBy('name')->paginate(15);
            $totalUsers = (clone $userQuery)->count();
            $students = (clone $userQuery)->where('role', 'student')->count();
            $trainers = (clone $userQuery)->where('role', 'trainer')->count();
            $admins = (clone $userQuery)->whereIn('role', ['admin', 'department_admin'])->count();
            $managers = (clone $userQuery)->where('role', 'manager')->count();

            return view('admin.users.index', compact('users', 'totalUsers', 'students', 'trainers', 'admins', 'managers'));
        })->name('admin.users.index');

        Route::get('/users/create', function () {
            $careerCoaches = User::where('role', 'career_coach')->orderBy('name')->get();
            $classes = ClassRoom::orderBy('name')->get();

            return view('admin.users.create', compact('careerCoaches', 'classes'));
        })->name('admin.users.create');

        Route::post('/users', function (Request $request) {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'unique:users'],
                'password' => ['required', 'string', 'min:8'],
                'role' => ['required', 'in:student,trainer,admin,department_admin,career_coach,accountant,manager'],
                'department' => ['nullable', 'string', 'max:255'],
                'career_coach_id' => ['nullable', 'exists:users,id'],
                'date_of_birth' => ['nullable', 'date'],
                'gender' => ['nullable', 'in:male,female,other'],
                'phone' => ['nullable', 'string', 'max:255'],
                'address' => ['nullable', 'string'],
                'guardian_name' => ['nullable', 'string', 'max:255'],
                'guardian_phone' => ['nullable', 'string', 'max:255'],
                'guardian_relationship' => ['nullable', 'string', 'max:255'],
                'current_class_id' => ['nullable', 'exists:class_rooms,id'],
                'stream' => ['nullable', 'string', 'max:255'],
                'student_status' => ['nullable', 'in:active,transferred,alumni'],
                'admission_date' => ['nullable', 'date'],
                'exit_date' => ['nullable', 'date'],
                'transfer_notes' => ['nullable', 'string'],
                'birth_certificate' => studentDocumentValidationRules('birth_certificate', 'pdf,jpg,jpeg,png'),
                'report_form' => studentDocumentValidationRules('report_form', 'pdf,jpg,jpeg,png,doc,docx'),
            ], studentDocumentValidationMessages());

            $validated['password'] = Hash::make($validated['password']);
            $validated['career_coach_id'] = $validated['role'] === 'student'
                ? ($validated['career_coach_id'] ?? null)
                : null;
            $validated['admission_number'] = $validated['role'] === 'student' ? generateAdmissionNumber() : null;
            $validated['student_status'] = $validated['role'] === 'student'
                ? ($validated['student_status'] ?? 'active')
                : null;
            $validated['current_class_id'] = $validated['role'] === 'student'
                ? ($validated['current_class_id'] ?? null)
                : null;
            $validated['stream'] = $validated['role'] === 'student'
                ? ($validated['stream'] ?? null)
                : null;
            $validated['admission_date'] = $validated['role'] === 'student'
                ? ($validated['admission_date'] ?? now()->toDateString())
                : null;
            $validated['exit_date'] = $validated['role'] === 'student'
                ? ($validated['exit_date'] ?? null)
                : null;
            $validated['transfer_notes'] = $validated['role'] === 'student'
                ? ($validated['transfer_notes'] ?? null)
                : null;

            if ($validated['role'] === 'student') {
                $birthCertificatePath = storeStudentDocument($request, 'birth_certificate');
                $reportFormPath = storeStudentDocument($request, 'report_form');

                if ($birthCertificatePath) {
                    $validated['birth_certificate_path'] = $birthCertificatePath;
                }

                if ($reportFormPath) {
                    $validated['report_form_path'] = $reportFormPath;
                }
            }

            $user = User::create($validated);

            syncStudentEnrollment($user);

            return redirect()->route('admin.users.index')->with('status', 'User created.');
        })->name('admin.users.store');

        Route::post('/users/import-students', function (Request $request) {
            $validated = $request->validate([
                'student_import_file' => ['required', 'file', 'mimes:csv,txt,pdf,xlsx'],
                'student_import_password' => ['required', 'string', 'min:8'],
                'student_import_class_id' => ['nullable', 'exists:class_rooms,id'],
                'student_import_career_coach_id' => ['nullable', 'exists:users,id'],
                'student_import_department' => ['nullable', 'string', 'max:255'],
                'student_import_student_status' => ['nullable', 'in:active,transferred,alumni'],
            ]);

            $importRows = extractStudentImportRowsFromUpload($request->file('student_import_file'));

            if (count($importRows) === 0) {
                return back()
                    ->withErrors(['student_import_file' => 'No student rows could be read from the uploaded file.'])
                    ->withInput();
            }

            $created = 0;
            $skipped = [];
            $defaultClassId = $validated['student_import_class_id'] ?? null;
            $defaultCoachId = $validated['student_import_career_coach_id'] ?? null;
            $defaultDepartment = trim((string) ($validated['student_import_department'] ?? ''));
            $defaultStatus = $validated['student_import_student_status'] ?? 'active';

            foreach ($importRows as $index => $row) {
                $rowNumber = $index + 2;

                try {
                    $name = buildImportedStudentName($row);

                    $email = trim((string) ($row['email'] ?? ''));

                    if ($name === '' || $email === '') {
                        $skipped[] = "Row {$rowNumber}: missing name or email.";
                        continue;
                    }

                    if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $skipped[] = "Row {$rowNumber}: invalid email address.";
                        continue;
                    }

                    if (User::withTrashed()->whereRaw('LOWER(email) = ?', [strtolower($email)])->exists()) {
                        $skipped[] = "Row {$rowNumber}: email {$email} already exists.";
                        continue;
                    }

                    $password = trim((string) ($row['password'] ?? '')) ?: $validated['student_import_password'];
                    $studentStatus = trim((string) ($row['student_status'] ?? '')) ?: $defaultStatus;
                    $admissionDate = trim((string) ($row['admission_date'] ?? '')) ?: now()->toDateString();
                    $classId = resolveImportedClassId($row['current_class_id'] ?? $row['class_id'] ?? $row['class_name'] ?? null, $defaultClassId);
                    $coachId = resolveImportedCoachId($row['career_coach_id'] ?? $row['coach_id'] ?? $row['coach_name'] ?? $row['coach_email'] ?? null, $defaultCoachId);
                    $admissionNumber = trim((string) ($row['admission_number'] ?? '')) ?: generateAdmissionNumber();

                    $student = User::create([
                        'name' => $name,
                        'email' => $email,
                        'password' => Hash::make($password),
                        'role' => 'student',
                        'department' => trim((string) ($row['department'] ?? $defaultDepartment)) ?: null,
                        'career_coach_id' => $coachId,
                        'admission_number' => $admissionNumber,
                        'date_of_birth' => ! empty($row['date_of_birth']) ? $row['date_of_birth'] : null,
                        'gender' => ! empty($row['gender']) ? strtolower(trim((string) $row['gender'])) : null,
                        'phone' => ! empty($row['phone']) ? $row['phone'] : null,
                        'address' => ! empty($row['address']) ? $row['address'] : null,
                        'guardian_name' => ! empty($row['guardian_name']) ? $row['guardian_name'] : null,
                        'guardian_phone' => ! empty($row['guardian_phone']) ? $row['guardian_phone'] : null,
                        'guardian_relationship' => ! empty($row['guardian_relationship']) ? $row['guardian_relationship'] : null,
                        'current_class_id' => $classId,
                        'stream' => ! empty($row['stream']) ? $row['stream'] : null,
                        'student_status' => in_array($studentStatus, ['active', 'transferred', 'alumni'], true) ? $studentStatus : $defaultStatus,
                        'admission_date' => $admissionDate,
                        'exit_date' => ! empty($row['exit_date']) ? $row['exit_date'] : null,
                        'transfer_notes' => ! empty($row['transfer_notes']) ? $row['transfer_notes'] : null,
                    ]);

                    syncStudentEnrollment($student);
                    $created++;
                } catch (\Throwable $e) {
                    $skipped[] = "Row {$rowNumber}: import failed.";
                }
            }

            $message = "Imported {$created} student" . ($created === 1 ? '' : 's') . '.';
            if ($skipped !== []) {
                $message .= ' Skipped ' . count($skipped) . ' row' . (count($skipped) === 1 ? '' : 's') . '.';
            }

            return redirect()
                ->route('admin.users.create')
                ->with('status', $message)
                ->with('import_warnings', array_slice($skipped, 0, 5));
        })->name('admin.users.import-students');

        Route::get('/users/import-students-template', function () {
            $headers = [
                'name',
                'first_name',
                'middle_name',
                'last_name',
                'email',
                'password',
                'phone',
                'department',
                'date_of_birth',
                'gender',
                'address',
                'guardian_name',
                'guardian_phone',
                'guardian_relationship',
                'parent_name',
                'parent_phone',
                'parent_relationship',
                'admission_number',
                'current_class_id',
                'class_name',
                'stream',
                'student_status',
                'admission_date',
                'exit_date',
                'transfer_notes',
                'career_coach_id',
                'career_coach_email',
            ];

            $sampleRows = [
                [
                    'John Doe',
                    'John',
                    'Michael',
                    'Doe',
                    'john.doe@example.com',
                    'Student@123',
                    '+254700000001',
                    'Science',
                    '2010-05-12',
                    'male',
                    'Nairobi',
                    'Jane Doe',
                    '+254700000002',
                    'Mother',
                    'Jane Doe',
                    '+254700000002',
                    'Mother',
                    'ADM20260001',
                    '1',
                    'Grade 10 A',
                    'East',
                    'active',
                    '2026-01-10',
                    '',
                    '',
                    '2',
                    'coach@example.com',
                ],
                [
                    'Mary Wanjiku',
                    'Mary',
                    '',
                    'Wanjiku',
                    'mary.wanjiku@example.com',
                    'Student@123',
                    '+254700000003',
                    'Arts',
                    '2009-11-04',
                    'female',
                    'Kiambu',
                    'Peter Wanjiku',
                    '+254700000004',
                    'Father',
                    'Peter Wanjiku',
                    '+254700000004',
                    'Father',
                    'ADM20260002',
                    '2',
                    'Grade 9 B',
                    'West',
                    'active',
                    '2026-01-10',
                    '',
                    '',
                    '2',
                    'coach@example.com',
                ],
            ];

            return response()->streamDownload(function () use ($headers, $sampleRows) {
                $output = fopen('php://output', 'w');
                fputcsv($output, $headers);

                foreach ($sampleRows as $row) {
                    fputcsv($output, $row);
                }

                fclose($output);
            }, 'student-import-template.csv', [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]);
        })->name('admin.users.import-students-template');

        Route::get('/users/{id}/edit', function ($id) {
            $user = User::withTrashed()->findOrFail($id);
            $careerCoaches = User::where('role', 'career_coach')->orderBy('name')->get();
            $classes = ClassRoom::orderBy('name')->get();

            return view('admin.users.edit', compact('user', 'careerCoaches', 'classes'));
        })->name('admin.users.edit');

        Route::get('/users/{id}/documents/{document}', function ($id, $document) {
            $user = User::withTrashed()->findOrFail($id);

            abort_unless(in_array($document, ['birth_certificate', 'report_form'], true), 404);

            $pathAttribute = $document . '_path';
            $path = $user->{$pathAttribute};

            abort_unless($path && Storage::disk('public')->exists($path), 404);

            $absolutePath = Storage::disk('public')->path($path);
            $extension = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));

            if (in_array($extension, ['pdf', 'jpg', 'jpeg', 'png'], true)) {
                return response()->file($absolutePath);
            }

            return response()->download($absolutePath);
        })->name('admin.users.documents.show');

        Route::post('/users/{id}', function ($id, Request $request) {
            $user = User::withTrashed()->findOrFail($id);
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
                'role' => ['required', 'in:student,trainer,admin,department_admin,career_coach,accountant,manager'],
                'department' => ['nullable', 'string', 'max:255'],
                'career_coach_id' => ['nullable', 'exists:users,id'],
                'date_of_birth' => ['nullable', 'date'],
                'gender' => ['nullable', 'in:male,female,other'],
                'phone' => ['nullable', 'string', 'max:255'],
                'address' => ['nullable', 'string'],
                'guardian_name' => ['nullable', 'string', 'max:255'],
                'guardian_phone' => ['nullable', 'string', 'max:255'],
                'guardian_relationship' => ['nullable', 'string', 'max:255'],
                'current_class_id' => ['nullable', 'exists:class_rooms,id'],
                'stream' => ['nullable', 'string', 'max:255'],
                'student_status' => ['nullable', 'in:active,transferred,alumni'],
                'admission_date' => ['nullable', 'date'],
                'exit_date' => ['nullable', 'date'],
                'transfer_notes' => ['nullable', 'string'],
                'birth_certificate' => studentDocumentValidationRules('birth_certificate', 'pdf,jpg,jpeg,png'),
                'report_form' => studentDocumentValidationRules('report_form', 'pdf,jpg,jpeg,png,doc,docx'),
            ], studentDocumentValidationMessages());

            $validated['career_coach_id'] = $validated['role'] === 'student'
                ? ($validated['career_coach_id'] ?? null)
                : null;
            $validated['admission_number'] = $validated['role'] === 'student'
                ? ($user->admission_number ?: generateAdmissionNumber())
                : null;
            $validated['student_status'] = $validated['role'] === 'student'
                ? ($validated['student_status'] ?? 'active')
                : null;
            $validated['current_class_id'] = $validated['role'] === 'student'
                ? ($validated['current_class_id'] ?? null)
                : null;
            $validated['stream'] = $validated['role'] === 'student'
                ? ($validated['stream'] ?? null)
                : null;
            $validated['admission_date'] = $validated['role'] === 'student'
                ? ($validated['admission_date'] ?? $user->admission_date?->toDateString() ?? now()->toDateString())
                : null;
            $validated['exit_date'] = $validated['role'] === 'student'
                ? ($validated['exit_date'] ?? null)
                : null;
            $validated['transfer_notes'] = $validated['role'] === 'student'
                ? ($validated['transfer_notes'] ?? null)
                : null;

            if ($validated['role'] !== 'student') {
                deleteUserStudentDocuments($user);
                $validated['birth_certificate_path'] = null;
                $validated['report_form_path'] = null;
                $validated['career_coach_id'] = null;
                $validated['current_class_id'] = null;
                $validated['stream'] = null;
                $validated['student_status'] = null;
                $validated['admission_date'] = null;
                $validated['exit_date'] = null;
                $validated['transfer_notes'] = null;
                $validated['admission_number'] = null;
            }

            if ($validated['role'] === 'student' && $request->hasFile('birth_certificate')) {
                if ($user->birth_certificate_path) {
                    Storage::disk('public')->delete($user->birth_certificate_path);
                }

                $validated['birth_certificate_path'] = storeStudentDocument($request, 'birth_certificate');
            }

            if ($validated['role'] === 'student' && $request->hasFile('report_form')) {
                if ($user->report_form_path) {
                    Storage::disk('public')->delete($user->report_form_path);
                }

                $validated['report_form_path'] = storeStudentDocument($request, 'report_form');
            }

            $user->update($validated);

            syncStudentEnrollment($user);

            return redirect()->route('admin.users.index')->with('status', 'User updated successfully.');
        })->name('admin.users.update');

        Route::post('/users/{id}/role', function ($id, Request $request) {
            $user = User::withTrashed()->findOrFail($id);
            $validated = $request->validate([
                'role' => ['required', 'in:student,trainer,admin,department_admin,career_coach,accountant,manager'],
            ]);

            $previousRole = $user->role;
            $user->role = $validated['role'];

            if ($validated['role'] !== 'student') {
                deleteUserStudentDocuments($user);
                $user->career_coach_id = null;
                $user->current_class_id = null;
                $user->stream = null;
                $user->student_status = null;
                $user->admission_date = null;
                $user->exit_date = null;
                $user->transfer_notes = null;
                $user->admission_number = null;
                $user->birth_certificate_path = null;
                $user->report_form_path = null;
                $user->enrolledClasses()->detach();
            } elseif (! $user->admission_number) {
                $user->admission_number = generateAdmissionNumber();
            }

            $user->save();
            syncStudentEnrollment($user);

            AuditLog::log('update', 'User', $user->id, ['role' => ['from' => $previousRole, 'to' => $user->role]], "Changed role for {$user->name} from {$previousRole} to {$user->role}");

            return redirect()->route('admin.users.index')->with('status', 'User role updated successfully.');
        })->name('admin.users.role');

        Route::post('/users/{id}/suspend', function ($id) {
            $user = User::findOrFail($id);
            $user->delete();

            return redirect()->route('admin.users.index')->with('status', 'User suspended successfully.');
        })->name('admin.users.suspend');

        Route::post('/users/{id}/activate', function ($id) {
            $user = User::withTrashed()->findOrFail($id);
            $user->restore();

            return redirect()->route('admin.users.index')->with('status', 'User activated successfully.');
        })->name('admin.users.activate');

        Route::delete('/users/{id}/delete', function ($id) {
            $user = User::withTrashed()->findOrFail($id);
            deleteUserStudentDocuments($user);
            $user->enrolledClasses()->detach();
            $user->forceDelete();

            return redirect()->route('admin.users.index')->with('status', 'User permanently deleted.');
        })->name('admin.users.delete');

        Route::get('/attendance', [AttendanceController::class, 'dashboard'])->name('admin.attendance.index');
        Route::get('/attendance/report', [AttendanceController::class, 'report'])->name('admin.attendance.report');
        Route::get('/attendance/export.csv', [AttendanceController::class, 'csv'])->name('admin.attendance.export.csv');
        Route::post('/attendance/bulk', [AttendanceController::class, 'bulkStore'])->name('admin.attendance.bulk');
        Route::get('/attendance/classes/{class}', [AttendanceController::class, 'manageClass'])->name('admin.attendance.class');

        Route::get('/classes', function () {
            $classes = ClassRoom::with(['trainer', 'students', 'homeworks'])->get();

            return view('admin.classes.index', compact('classes'));
        })->name('admin.classes.index');

        Route::get('/homework', function (Request $request) {
            $selectedClassId = $request->query('class_id');
            $classesQuery = ClassRoom::with([
                'trainer',
                'homeworks' => function ($query) {
                    $query->withCount('submissions')
                        ->orderByRaw('due_date is null')
                        ->orderBy('due_date');
                },
            ])->withCount('homeworks');

            if ($selectedClassId) {
                $classesQuery->where('id', $selectedClassId);
            }

            $classes = $classesQuery->orderBy('name')->get();
            $totalHomework = Homework::count();
            $activeHomework = Homework::where('due_date', '>', now())->count();
            $totalClasses = ClassRoom::count();

            return view('admin.homework.index', compact('classes', 'totalHomework', 'activeHomework', 'totalClasses', 'selectedClassId'));
        })->name('admin.homework.index');

        Route::get('/homework/create', function (Request $request) {
            $classes = ClassRoom::all();
            $selectedClassId = $request->query('class_id');

            return view('admin.homework.create', compact('classes', 'selectedClassId'));
        })->name('admin.homework.create');

        Route::post('/homework', function (Request $request) {
            $validated = $request->validate([
                'title' => ['required', 'string', 'max:255'],
                'description' => ['required', 'string'],
                'class_id' => ['required', 'exists:class_rooms,id'],
                'submission_type' => ['required', 'in:written,file,upload'],
                'due_date' => ['nullable', 'date'],
            ]);

            $class = ClassRoom::findOrFail($validated['class_id']);

            Homework::create([
                ...$validated,
                'trainer_id' => $class->trainer_id,
                'submission_type' => $validated['submission_type'] === 'file' ? 'upload' : $validated['submission_type'],
            ]);

            return redirect()->route('admin.homework.index')->with('status', 'Homework created successfully.');
        })->name('admin.homework.store');

        Route::get('/homework/{id}/edit', function ($id) {
            $homework = Homework::findOrFail($id);
            $classes = ClassRoom::all();

            return view('admin.homework.edit', compact('homework', 'classes'));
        })->name('admin.homework.edit');

        Route::put('/homework/{id}', function ($id, Request $request) {
            $homework = Homework::findOrFail($id);
            $validated = $request->validate([
                'title' => ['required', 'string', 'max:255'],
                'description' => ['required', 'string'],
                'class_id' => ['required', 'exists:class_rooms,id'],
                'submission_type' => ['required', 'in:written,file,upload'],
                'due_date' => ['nullable', 'date'],
            ]);

            $class = ClassRoom::findOrFail($validated['class_id']);

            $homework->update([
                ...$validated,
                'trainer_id' => $class->trainer_id,
                'submission_type' => $validated['submission_type'] === 'file' ? 'upload' : $validated['submission_type'],
            ]);

            return redirect()->route('admin.homework.index')->with('status', 'Homework updated successfully.');
        })->name('admin.homework.update');

        Route::delete('/homework/{id}', function ($id) {
            $homework = Homework::findOrFail($id);
            $homework->delete();

            return redirect()->route('admin.homework.index')->with('status', 'Homework deleted successfully.');
        })->name('admin.homework.delete');

        // ============ NEW ADMIN FEATURES ============

        // Dashboard & Analytics
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/analytics', [AdminController::class, 'getAnalytics'])->name('admin.analytics');
        Route::get('/access-control', [AdminController::class, 'getAccessControl'])->name('admin.access-control');

        // Departments
        Route::get('/departments', [AdminController::class, 'getDepartments'])->name('admin.departments.index');
        Route::get('/departments/create', [AdminController::class, 'createDepartment'])->name('admin.departments.create');
        Route::post('/departments', [AdminController::class, 'storeDepartment'])->name('admin.departments.store');
        Route::get('/departments/{department}/edit', [AdminController::class, 'editDepartment'])->name('admin.departments.edit');
        Route::put('/departments/{department}', [AdminController::class, 'updateDepartment'])->name('admin.departments.update');
        Route::delete('/departments/{department}', [AdminController::class, 'deleteDepartment'])->name('admin.departments.delete');

        // System Settings
        Route::get('/settings', [AdminController::class, 'getSettings'])->name('admin.settings.index');
        Route::post('/settings', [AdminController::class, 'updateSettings'])->name('admin.settings.update');

        // Audit Logs
        Route::get('/audit-logs', [AdminController::class, 'getAuditLogs'])->name('admin.audit-logs.index');
        Route::get('/audit-logs/{audit_log}', [AdminController::class, 'viewAuditLog'])->name('admin.audit-logs.show');

        // Permissions & Roles
        Route::get('/permissions', [AdminController::class, 'getPermissions'])->name('admin.permissions.index');
        Route::get('/permissions/create', [AdminController::class, 'createPermission'])->name('admin.permissions.create');
        Route::post('/permissions', [AdminController::class, 'storePermission'])->name('admin.permissions.store');
        Route::get('/permissions/{permission}/edit', [AdminController::class, 'editPermission'])->name('admin.permissions.edit');
        Route::put('/permissions/{permission}', [AdminController::class, 'updatePermission'])->name('admin.permissions.update');
        Route::delete('/permissions/{permission}', [AdminController::class, 'deletePermission'])->name('admin.permissions.delete');

        Route::get('/roles', [AdminController::class, 'getRoles'])->name('admin.roles.index');
        Route::get('/roles/{role}/edit', [AdminController::class, 'editRole'])->name('admin.roles.edit');
        Route::put('/roles/{role}/permissions', [AdminController::class, 'updateRolePermissions'])->name('admin.roles.permissions.update');

        // User Status Control
        Route::post('/users/{user}/suspend', [AdminController::class, 'suspendUser'])->name('admin.users.suspend.new');
        Route::post('/users/{user}/unsuspend', [AdminController::class, 'unsuspendUser'])->name('admin.users.unsuspend');
        Route::post('/users/{user}/lock', [AdminController::class, 'lockUser'])->name('admin.users.lock');
        Route::post('/users/{user}/unlock', [AdminController::class, 'unlockUser'])->name('admin.users.unlock');
        Route::post('/users/{user}/deactivate', [AdminController::class, 'deactivateUser'])->name('admin.users.deactivate');
        Route::post('/users/{user}/activate', [AdminController::class, 'activateUser'])->name('admin.users.activate.new');

        // Class Management (Enhanced)
        Route::get('/classes/create', [AdminController::class, 'createClass'])->name('admin.classes.create');
        Route::post('/classes', [AdminController::class, 'storeClass'])->name('admin.classes.store');
        Route::get('/classes/{class}/edit', [AdminController::class, 'editClass'])->name('admin.classes.edit');
        Route::put('/classes/{class}', [AdminController::class, 'updateClass'])->name('admin.classes.update');
        Route::delete('/classes/{class}', [AdminController::class, 'deleteClass'])->name('admin.classes.delete');

        // Homework Management (Enhanced)
        Route::get('/homework-admin', [AdminController::class, 'getHomework'])->name('admin.homework-admin.index');
        Route::get('/homework-admin/create', [AdminController::class, 'createHomework'])->name('admin.homework-admin.create');
        Route::post('/homework-admin', [AdminController::class, 'storeHomework'])->name('admin.homework-admin.store');
        Route::get('/homework-admin/{homework}/edit', [AdminController::class, 'editHomework'])->name('admin.homework-admin.edit');
        Route::put('/homework-admin/{homework}', [AdminController::class, 'updateHomework'])->name('admin.homework-admin.update');
        Route::delete('/homework-admin/{homework}', [AdminController::class, 'deleteHomework'])->name('admin.homework-admin.delete');

        // Messaging System
        Route::get('/messaging', [AdminController::class, 'getMessaging'])->name('admin.messaging.index');
        Route::get('/messaging/send', [AdminController::class, 'sendMessageForm'])->name('admin.messaging.send-form');
        Route::post('/messaging/send', [AdminController::class, 'sendMessage'])->name('admin.messaging.send');
    });

    Route::post('/logout', function (Request $request) {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('welcome')->with('status', 'You have been logged out successfully.');
    })->name('logout');
});
