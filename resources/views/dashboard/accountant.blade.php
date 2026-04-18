<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Accountant Dashboard | {{ config('app.name', 'School Portal') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            :root { color-scheme: dark; --text:#e2e8f0; --muted:#94a3b8; --heading:#f8fafc; --primary:#22d3ee; --accent:#f59e0b; --success:#34d399; }
            * { box-sizing:border-box; margin:0; padding:0; }
            body { font-family:"Instrument Sans",sans-serif; color:var(--text); background:radial-gradient(circle at top left, rgba(245, 158, 11, .16), transparent 24%), linear-gradient(135deg,#020617 0%,#0f172a 54%,#111827 100%); min-height:100vh; }
            .container { max-width:1200px; margin:0 auto; padding:40px 20px; }
            header,.toolbar,.metric { display:flex; align-items:center; }
            header { justify-content:space-between; margin-bottom:40px; padding-bottom:20px; border-bottom:1px solid rgba(148,163,184,.1); gap:16px; flex-wrap:wrap; }
            h1 { font-size:2.5rem; color:var(--heading); }
            .toolbar { gap:8px; flex-wrap:wrap; }
            .nav-btn,.logout-btn,.action-btn { display:inline-flex; align-items:center; gap:8px; border-radius:8px; text-decoration:none; font-weight:700; transition:.2s; }
            .nav-btn,.logout-btn { padding:10px 20px; }
            .nav-btn { background:rgba(245,158,11,.1); color:#fbbf24; border:1px solid rgba(245,158,11,.28); }
            .logout-btn { background:linear-gradient(135deg,#22d3ee,#06b6d4); color:#082f49; border:none; cursor:pointer; }
            .hero,.card,.feature-card,.summary-card,.table-card { background:rgba(15,23,42,.78); border:1px solid rgba(148,163,184,.18); border-radius:16px; padding:24px; backdrop-filter:blur(18px); }
            .hero { margin-bottom:24px; display:grid; gap:20px; grid-template-columns:1.5fr 1fr; align-items:center; }
            .hero p,.card p,.feature-card p,.summary-card p,.table-card p { color:var(--muted); line-height:1.6; }
            .hero-pill { display:inline-flex; align-items:center; gap:8px; padding:8px 12px; border-radius:999px; background:rgba(245,158,11,.12); color:#fbbf24; font-weight:700; margin-bottom:12px; }
            .hero-actions { display:flex; gap:12px; flex-wrap:wrap; margin-top:18px; }
            .primary-btn,.ghost-btn { padding:12px 16px; border-radius:10px; font-weight:700; text-decoration:none; display:inline-flex; align-items:center; gap:8px; }
            .primary-btn { background:linear-gradient(135deg,#f59e0b,#f97316); color:#082f49; }
            .ghost-btn { background:rgba(148,163,184,.08); color:var(--heading); border:1px solid rgba(148,163,184,.18); }
            .stats-grid,.feature-grid { display:grid; gap:20px; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); }
            .stat-number { font-size:2rem; color:#fbbf24; font-weight:700; margin:8px 0 4px; }
            .label { color:#fbbf24; font-weight:700; letter-spacing:.04em; text-transform:uppercase; font-size:.78rem; }
            .section-title { font-size:1.75rem; color:var(--heading); margin:40px 0 24px; display:flex; align-items:center; gap:10px; }
            .feature-card h3,.card h2,.summary-card h3,.table-card h3 { color:var(--heading); margin-bottom:12px; }
            .metric { justify-content:space-between; gap:12px; }
            .metric .icon { width:48px; height:48px; border-radius:14px; display:grid; place-items:center; background:rgba(245,158,11,.12); color:#fbbf24; font-size:1.15rem; flex-shrink:0; }
            .metric .copy { color:var(--muted); font-size:.92rem; }
            .card.accent { background:linear-gradient(135deg, rgba(245,158,11,.16), rgba(15,23,42,.78)); border-color:rgba(245,158,11,.26); }
            .feature-card { background:rgba(245,158,11,.08); border-color:rgba(245,158,11,.18); }
            .feature-card ul { list-style:none; margin-top:12px; }
            .feature-card li { color:var(--muted); padding:4px 0; }
            .summary-card { display:flex; flex-direction:column; justify-content:space-between; min-height:100%; }
            .table-wrap { overflow-x:auto; }
            table { width:100%; border-collapse:collapse; min-width:900px; }
            th, td { padding:14px 12px; text-align:left; border-bottom:1px solid rgba(148,163,184,.1); vertical-align:top; }
            th { color:#dbeafe; font-size:.86rem; text-transform:uppercase; letter-spacing:.05em; }
            td { color:var(--text); }
            .pill { display:inline-flex; align-items:center; gap:6px; padding:6px 10px; border-radius:999px; font-size:.82rem; font-weight:700; }
            .pill.paid { background:rgba(52,211,153,.14); color:#6ee7b7; }
            .pill.partial { background:rgba(245,158,11,.14); color:#fbbf24; }
            .pill.unpaid { background:rgba(239,68,68,.14); color:#fca5a5; }
            .balance { font-weight:700; }
            .positive { color:#6ee7b7; }
            .negative { color:#fca5a5; }
            @media (max-width: 860px) {
                .hero { grid-template-columns:1fr; }
                header { flex-direction:column; align-items:flex-start; }
            }
        </style>
    </head>
    <body>
        <div class="container">
            <header>
                <div>
                    <h1><i class="fa-solid fa-calculator"></i> Finance Desk</h1>
                    <p style="color: var(--muted); margin-top: 8px;">Welcome, {{ auth()->user()->name }}</p>
                </div>
                <div class="toolbar">
                    <a href="{{ route('notifications.index') }}" class="nav-btn"><i class="fa-regular fa-bell"></i> Notifications</a>
                    <a href="{{ route('profile.show') }}" class="nav-btn"><i class="fa-regular fa-user"></i> Profile</a>
                    <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                        @csrf
                        <button type="submit" class="logout-btn"><i class="fa-solid fa-right-from-bracket"></i> Log Out</button>
                    </form>
                </div>
            </header>

            <section class="hero">
                <div>
                    <div class="hero-pill"><i class="fa-solid fa-sack-dollar"></i> Accountant Dashboard</div>
                    <h2 style="color: var(--heading); font-size: clamp(2rem, 4vw, 3.2rem); line-height:1.1;">Track fees, review collections, and keep school finances visible.</h2>
                    <p style="margin-top:14px; max-width:720px;">
                        Review which students have paid, how much has been collected, and which balances still need follow-up.
                        The dashboard is powered by the school fee payment records so analysis stays tied to real student accounts.
                    </p>
                    <div class="hero-actions">
                        <a href="{{ route('dashboard') }}" class="primary-btn"><i class="fa-solid fa-gauge-high"></i> Main Dashboard</a>
                        <a href="{{ route('notifications.index') }}" class="ghost-btn"><i class="fa-regular fa-bell"></i> Open Notifications</a>
                    </div>
                </div>

                <div class="summary-card">
                    <div>
                        <h3><i class="fa-solid fa-chart-column"></i> Payment snapshot</h3>
                        <p>Collections and balances are summarized from the fee records currently stored in the portal.</p>
                    </div>
                    <div class="stats-grid" style="margin-top:16px; grid-template-columns:1fr 1fr;">
                        <div class="card accent" style="padding:18px;">
                            <div class="label">Paid</div>
                            <div class="stat-number">{{ $paidStudents }}</div>
                            <p class="copy">Students fully settled</p>
                        </div>
                        <div class="card accent" style="padding:18px;">
                            <div class="label">Part-paid</div>
                            <div class="stat-number">{{ $partiallyPaidStudents }}</div>
                            <p class="copy">Students with balances</p>
                        </div>
                    </div>
                </div>
            </section>

            <div class="stats-grid">
                <div class="card">
                    <div class="metric">
                        <div>
                            <div class="label">Total collected</div>
                            <div class="stat-number" style="color:#22d3ee;">KSh {{ number_format($totalPaid, 2) }}</div>
                            <p class="copy">All fee payments recorded</p>
                        </div>
                        <div class="icon"><i class="fa-solid fa-money-bill-wave"></i></div>
                    </div>
                </div>
                <div class="card">
                    <div class="metric">
                        <div>
                            <div class="label">Outstanding</div>
                            <div class="stat-number" style="color:#f59e0b;">KSh {{ number_format($outstandingBalance, 2) }}</div>
                            <p class="copy">Balance still to be paid</p>
                        </div>
                        <div class="icon"><i class="fa-solid fa-hourglass-half"></i></div>
                    </div>
                </div>
                <div class="card">
                    <div class="metric">
                        <div>
                            <div class="label">Collection rate</div>
                            <div class="stat-number" style="color:#34d399;">{{ $collectionRate }}%</div>
                            <p class="copy">Paid vs expected totals</p>
                        </div>
                        <div class="icon"><i class="fa-solid fa-chart-line"></i></div>
                    </div>
                </div>
                <div class="card">
                    <div class="metric">
                        <div>
                            <div class="label">Students</div>
                            <div class="stat-number" style="color:#fbbf24;">{{ $students->count() }}</div>
                            <p class="copy">Student accounts reviewed</p>
                        </div>
                        <div class="icon"><i class="fa-solid fa-user-graduate"></i></div>
                    </div>
                </div>
                <div class="card">
                    <div class="metric">
                        <div>
                            <div class="label">Cash</div>
                            <div class="stat-number" style="color:#34d399;">KSh {{ number_format($cashReceived, 2) }}</div>
                            <p class="copy">Recorded cash payments</p>
                        </div>
                        <div class="icon"><i class="fa-solid fa-money-bill"></i></div>
                    </div>
                </div>
                <div class="card">
                    <div class="metric">
                        <div>
                            <div class="label">Pochi la Biashara</div>
                            <div class="stat-number" style="color:#f97316;">KSh {{ number_format($pochiReceived, 2) }}</div>
                            <p class="copy">Recorded mobile wallet payments</p>
                        </div>
                        <div class="icon"><i class="fa-solid fa-mobile-screen-button"></i></div>
                    </div>
                </div>
                <div class="card">
                    <div class="metric">
                        <div>
                            <div class="label">Bank transfer</div>
                            <div class="stat-number" style="color:#60a5fa;">KSh {{ number_format($bankReceived, 2) }}</div>
                            <p class="copy">Recorded bank payments</p>
                        </div>
                        <div class="icon"><i class="fa-solid fa-building-columns"></i></div>
                    </div>
                </div>
            </div>

            <div class="section-title"><i class="fa-solid fa-wallet"></i> Accountant Tools</div>
            <div class="feature-grid">
                <div class="feature-card">
                    <h3><i class="fa-solid fa-file-invoice"></i> Fee register</h3>
                    <p>See each student’s fee position at a glance, including totals due and totals paid.</p>
                </div>
                <div class="feature-card">
                    <h3><i class="fa-solid fa-receipt"></i> Receipts</h3>
                    <p>Review receipt numbers and payment methods for each fee transaction.</p>
                </div>
                <div class="feature-card">
                    <h3><i class="fa-solid fa-chart-line"></i> Reporting</h3>
                    <p>Use collection rates and balances to spot trends before month-end.</p>
                </div>
                <div class="feature-card">
                    <h3><i class="fa-solid fa-building-columns"></i> Statements</h3>
                    <p>Filter by student, class, academic year, or fee status to prepare financial statements.</p>
                </div>
            </div>

            <div class="section-title"><i class="fa-solid fa-receipt"></i> Recent Fee Payments</div>
            <div class="table-card">
                @if($recentPayments->count())
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Class</th>
                                    <th>Academic Year</th>
                                    <th>Term</th>
                                    <th>Due</th>
                                    <th>Paid</th>
                                    <th>Balance</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentPayments as $payment)
                                    @php
                                        $balance = max(((float) $payment->amount_due) - ((float) $payment->amount_paid), 0);
                                        $statusClass = $payment->status === 'paid' ? 'paid' : ($payment->status === 'partial' ? 'partial' : 'unpaid');
                                    @endphp
                                    <tr>
                                        <td>{{ $payment->student?->name ?? 'Unknown student' }}</td>
                                        <td>{{ $payment->student?->currentClass?->name ?? '-' }}</td>
                                        <td>{{ $payment->academic_year ?? '-' }}</td>
                                        <td>{{ $payment->term ?? '-' }}</td>
                                        <td>KSh {{ number_format($payment->amount_due, 2) }}</td>
                                        <td>KSh {{ number_format($payment->amount_paid, 2) }}</td>
                                        <td class="balance {{ $balance > 0 ? 'negative' : 'positive' }}">KSh {{ number_format($balance, 2) }}</td>
                                        <td><span class="pill {{ $statusClass }}">{{ ucfirst($payment->status) }}</span></td>
                                        <td><a href="{{ route('accountant.payments.edit', $payment->id) }}" class="ghost-btn" style="padding:8px 10px;"><i class="fa-solid fa-pen-to-square"></i> Edit</a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p>No fee payments have been recorded yet.</p>
                @endif
            </div>

            <div class="section-title"><i class="fa-solid fa-users"></i> Student Fee Analysis</div>
            <div class="table-card">
                @if($students->count())
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Admission No.</th>
                                    <th>Class</th>
                                    <th>Total Due</th>
                                    <th>Total Paid</th>
                                    <th>Balance</th>
                                    <th>Fee Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students as $student)
                                    @php
                                        $studentDue = (float) $student->feePayments->sum('amount_due');
                                        $studentPaid = (float) $student->feePayments->sum('amount_paid');
                                        $studentBalance = max($studentDue - $studentPaid, 0);
                                        $studentStatus = $studentDue <= 0
                                            ? 'unpaid'
                                            : ($studentPaid >= $studentDue ? 'paid' : ($studentPaid > 0 ? 'partial' : 'unpaid'));
                                    @endphp
                                    <tr>
                                        <td>{{ $student->name }}</td>
                                        <td>{{ $student->admission_number ?? '-' }}</td>
                                        <td>{{ $student->currentClass?->name ?? '-' }}</td>
                                        <td>KSh {{ number_format($studentDue, 2) }}</td>
                                        <td>KSh {{ number_format($studentPaid, 2) }}</td>
                                        <td class="balance {{ $studentBalance > 0 ? 'negative' : 'positive' }}">KSh {{ number_format($studentBalance, 2) }}</td>
                                        <td><span class="pill {{ $studentStatus }}">{{ ucfirst($studentStatus) }}</span></td>
                                        <td>
                                            @if($student->feePayments->first())
                                                <a href="{{ route('accountant.payments.edit', $student->feePayments->first()->id) }}" class="ghost-btn" style="padding:8px 10px;"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
                                            @else
                                                <span style="color: var(--muted);">No payment</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p>No student records are available yet.</p>
                @endif
            </div>
        </div>

        @include('partials.chat-fab')
        @include('partials.idle-timeout-modal')
        <script src="{{ asset('js/idle-timeout.js') }}"></script>
        <script>
            document.documentElement.dataset.idleTimeout = "{{ config('idle.idle_timeout', 15) }}";
            document.documentElement.dataset.warningTime = "{{ config('idle.warning_time', 1) }}";
        </script>
    </body>
</html>
