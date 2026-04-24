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
            .live-panel { margin: 24px 0 12px; padding: 22px; border-radius: 18px; border: 1px solid rgba(148,163,184,.18); background: rgba(15,23,42,.78); backdrop-filter: blur(18px); }
            .live-panel__head { display:flex; justify-content:space-between; gap:16px; align-items:flex-start; flex-wrap:wrap; margin-bottom:18px; }
            .live-panel__title { color:var(--heading); font-size:1.3rem; margin-bottom:6px; }
            .live-panel__copy { color:var(--muted); line-height:1.5; max-width:720px; }
            .live-panel__meta { display:flex; gap:10px; flex-wrap:wrap; }
            .live-pill { display:inline-flex; align-items:center; gap:8px; padding:8px 12px; border-radius:999px; border:1px solid rgba(245,158,11,.24); background:rgba(245,158,11,.1); color:#fef3c7; font-weight:700; font-size:.85rem; }
            .live-list { display:grid; gap:12px; }
            .live-message { border-radius:16px; border:1px solid rgba(51,65,85,.95); background:rgba(2,6,23,.4); padding:14px 16px; }
            .live-message__top { display:flex; justify-content:space-between; gap:12px; flex-wrap:wrap; align-items:flex-start; }
            .live-message__title { color:#f8fafc; font-weight:800; margin-bottom:4px; }
            .live-message__body { color:#cbd5e1; line-height:1.6; margin-top:8px; white-space:pre-wrap; }
            .live-message__time { color:#94a3b8; font-size:.82rem; white-space:nowrap; }
            .live-message__badge { display:inline-flex; align-items:center; gap:6px; padding:5px 10px; border-radius:999px; background:rgba(245,158,11,.12); border:1px solid rgba(245,158,11,.24); color:#fde68a; font-size:.76rem; font-weight:800; text-transform:uppercase; letter-spacing:.08em; }
            .live-empty { color:#cbd5e1; text-align:center; padding:22px 16px; border-radius:16px; border:1px dashed rgba(148,163,184,.24); background:rgba(15,23,42,.45); }
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
                        <a href="{{ route('accounting.dashboard') }}" class="ghost-btn"><i class="fa-solid fa-calculator"></i> Accounting Hub</a>
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

            <section class="live-panel">
                <div class="live-panel__head">
                    <div>
                        <div class="live-panel__title"><i class="fa-regular fa-comment-dots"></i> Live Messages</div>
                        <div class="live-panel__copy">Messages you send and any incoming student replies appear here automatically without reloading the page.</div>
                    </div>
                    <div class="live-panel__meta">
                        <span class="live-pill" id="accountantUnreadCount"><i class="fa-regular fa-bell"></i> 0 unread</span>
                        <span class="live-pill" id="accountantMessageCount"><i class="fa-solid fa-comments"></i> 0 messages</span>
                    </div>
                </div>
                <div class="live-list" id="accountantLiveMessages">
                    <div class="live-empty">Loading messages...</div>
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
                                        <td style="display:flex; gap:8px; flex-wrap:wrap;">
                                            <a href="{{ route('accountant.payments.edit', $payment->id) }}" class="ghost-btn" style="padding:8px 10px;"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
                                            <button
                                                type="button"
                                                class="ghost-btn"
                                                style="padding:8px 10px;"
                                                onclick="openMessageModal({{ $payment->id }}, @js($payment->student?->name ?? 'Unknown student'), @js(route('accountant.payments.message', $payment->id)))"
                                            >
                                                <i class="fa-solid fa-comment-dots"></i> Message
                                            </button>
                                        </td>
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
                                                <div style="display:flex; gap:8px; flex-wrap:wrap;">
                                                    <a href="{{ route('accountant.payments.edit', $student->feePayments->first()->id) }}" class="ghost-btn" style="padding:8px 10px;"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
                                                    <button
                                                        type="button"
                                                        class="ghost-btn"
                                                        style="padding:8px 10px;"
                                                        onclick="openMessageModal({{ $student->feePayments->first()->id }}, @js($student->name), @js(route('accountant.payments.message', $student->feePayments->first()->id)))"
                                                    >
                                                        <i class="fa-solid fa-comment-dots"></i> Message
                                                    </button>
                                                </div>
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

        <div id="messageModal" class="message-modal" style="display:none;">
            <div class="message-modal__backdrop" onclick="closeMessageModal()"></div>
            <div class="message-modal__panel" role="dialog" aria-modal="true" aria-labelledby="messageModalTitle">
                <div class="message-modal__header">
                    <div>
                        <div class="message-modal__eyebrow">Quick message</div>
                        <h3 id="messageModalTitle">Send Student Message</h3>
                    </div>
                    <button type="button" class="message-modal__close" onclick="closeMessageModal()" aria-label="Close message modal">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <form id="messageModalForm" method="POST">
                    @csrf
                    <div id="messageModalFeedback" class="message-modal__feedback" style="display:none;"></div>
                    <div class="message-modal__grid">
                        <div class="message-modal__field">
                            <label for="messageModalRecipient">Recipient</label>
                            <input id="messageModalRecipient" type="text" disabled>
                        </div>
                        <div class="message-modal__field">
                            <label for="messageModalSubject">Subject</label>
                            <input id="messageModalSubject" type="text" name="subject" value="Fee update" required>
                        </div>
                    </div>
                    <div class="message-modal__field">
                        <label for="messageModalBody">Message</label>
                        <textarea id="messageModalBody" name="message" rows="6" required placeholder="Write a short message to the student..."></textarea>
                    </div>
                    <div class="message-modal__actions">
                        <button type="button" class="ghost-btn" style="padding:10px 14px;" onclick="closeMessageModal()">
                            Cancel
                        </button>
                        <button type="submit" class="btn primary" style="padding:10px 16px;">
                            <i class="fa-solid fa-paper-plane"></i> Send Message
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <script src="{{ asset('js/idle-timeout.js') }}"></script>
        <script>
            document.documentElement.dataset.idleTimeout = "{{ config('idle.idle_timeout', 15) }}";
            document.documentElement.dataset.warningTime = "{{ config('idle.warning_time', 1) }}";

            const accountantLiveMessages = document.getElementById('accountantLiveMessages');
            const accountantUnreadCount = document.getElementById('accountantUnreadCount');
            const accountantMessageCount = document.getElementById('accountantMessageCount');

            function escapeHtml(value) {
                return String(value ?? '')
                    .replaceAll('&', '&amp;')
                    .replaceAll('<', '&lt;')
                    .replaceAll('>', '&gt;')
                    .replaceAll('"', '&quot;')
                    .replaceAll("'", '&#039;');
            }

            function renderAccountantLiveMessages(messages) {
                if (!accountantLiveMessages) {
                    return;
                }

                if (!messages.length) {
                    accountantLiveMessages.innerHTML = '<div class="live-empty">No messages yet. New messages will appear here automatically.</div>';
                    return;
                }

                accountantLiveMessages.innerHTML = messages.map((item) => `
                    <article class="live-message">
                        <div class="live-message__top">
                            <div>
                                <div class="live-message__badge">${item.read_at ? 'Read' : 'Unread'}</div>
                                <div class="live-message__title">${escapeHtml(item.title)}</div>
                            </div>
                            <div class="live-message__time">${escapeHtml(item.time ?? '')}</div>
                        </div>
                        <div class="live-message__body">${escapeHtml(item.message)}</div>
                    </article>
                `).join('');
            }

            async function loadAccountantLiveMessages() {
                try {
                    const response = await fetch("{{ route('notifications.live') }}", {
                        headers: { 'Accept': 'application/json' },
                    });

                    if (!response.ok) {
                        return;
                    }

                    const payload = await response.json();
                    const messages = (payload.notifications ?? []).filter((item) => item.type === 'message');

                    if (accountantUnreadCount) {
                        accountantUnreadCount.innerHTML = `<i class="fa-regular fa-bell"></i> ${payload.unreadCount ?? 0} unread`;
                    }

                    if (accountantMessageCount) {
                        accountantMessageCount.innerHTML = `<i class="fa-solid fa-comments"></i> ${payload.messageCount ?? 0} messages`;
                    }

                    renderAccountantLiveMessages(messages);
                } catch (error) {
                    console.error('Failed to load accountant live messages', error);
                }
            }

            loadAccountantLiveMessages();
            window.setInterval(loadAccountantLiveMessages, 6000);

            const messageModal = document.getElementById('messageModal');
            const messageModalForm = document.getElementById('messageModalForm');
            const messageModalRecipient = document.getElementById('messageModalRecipient');
            const messageModalSubject = document.getElementById('messageModalSubject');
            const messageModalBody = document.getElementById('messageModalBody');
            const messageModalFeedback = document.getElementById('messageModalFeedback');

            function showMessageModalFeedback(message, type = 'success') {
                if (!messageModalFeedback) {
                    return;
                }

                messageModalFeedback.className = `message-modal__feedback is-${type}`;
                messageModalFeedback.textContent = message;
                messageModalFeedback.style.display = 'block';
            }

            function openMessageModal(paymentId, studentName, actionUrl) {
                messageModalForm.action = actionUrl;
                messageModalRecipient.value = studentName || 'Unknown student';
                messageModalSubject.value = `Fee update for ${studentName || 'student'}`;
                messageModalBody.value = 'Please review your fee balance and contact the accountant if you need any clarification.';
                messageModalFeedback.style.display = 'none';
                messageModal.style.display = 'block';
                document.body.style.overflow = 'hidden';
                messageModalSubject.focus();
            }

            function closeMessageModal() {
                messageModal.style.display = 'none';
                document.body.style.overflow = '';
            }

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && messageModal.style.display === 'block') {
                    closeMessageModal();
                }
            });

            messageModalForm.addEventListener('submit', async (event) => {
                event.preventDefault();

                const submitButton = messageModalForm.querySelector('button[type="submit"]');
                const submitLabel = submitButton?.innerHTML ?? '';
                const formData = new FormData(messageModalForm);
                const csrfToken = messageModalForm.querySelector('input[name="_token"]')?.value ?? '';

                try {
                    if (submitButton) {
                        submitButton.disabled = true;
                        submitButton.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Sending...';
                    }

                    const response = await fetch(messageModalForm.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: formData,
                    });

                    const payload = await response.json().catch(() => ({}));

                    if (!response.ok) {
                        const firstError = payload?.errors ? Object.values(payload.errors).flat().filter(Boolean)[0] : null;
                        showMessageModalFeedback(payload.message || firstError || 'Could not send the message right now.', 'error');
                        return;
                    }

                    showMessageModalFeedback(payload.message || 'Message sent successfully.', 'success');
                    setTimeout(() => closeMessageModal(), 700);
                    messageModalForm.reset();
                } catch (error) {
                    showMessageModalFeedback('Could not send the message right now.', 'error');
                    console.error('Failed to send fee message', error);
                } finally {
                    if (submitButton) {
                        submitButton.disabled = false;
                        submitButton.innerHTML = submitLabel;
                    }
                }
            });
        </script>

        <style>
            .message-modal {
                position: fixed;
                inset: 0;
                z-index: 80;
                display: none;
            }
            .message-modal__backdrop {
                position: absolute;
                inset: 0;
                background: rgba(2, 6, 23, 0.72);
                backdrop-filter: blur(8px);
            }
            .message-modal__panel {
                position: relative;
                z-index: 1;
                width: min(680px, calc(100vw - 32px));
                margin: 8vh auto 0;
                border-radius: 24px;
                border: 1px solid rgba(148, 163, 184, 0.18);
                background: linear-gradient(180deg, rgba(15, 23, 42, 0.98), rgba(17, 24, 39, 0.98));
                box-shadow: 0 32px 90px rgba(2, 6, 23, 0.45);
                padding: 24px;
            }
            .message-modal__header {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 16px;
                margin-bottom: 20px;
            }
            .message-modal__eyebrow {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 6px 12px;
                border-radius: 999px;
                background: rgba(56, 189, 248, 0.12);
                border: 1px solid rgba(56, 189, 248, 0.2);
                color: #bae6fd;
                font-size: 12px;
                font-weight: 800;
                letter-spacing: 0.08em;
                text-transform: uppercase;
            }
            .message-modal h3 {
                margin-top: 10px;
                color: #f8fafc;
                font-size: 1.4rem;
            }
            .message-modal__close {
                border: 1px solid rgba(148, 163, 184, 0.18);
                background: rgba(148, 163, 184, 0.08);
                color: #e2e8f0;
                width: 42px;
                height: 42px;
                border-radius: 12px;
                cursor: pointer;
            }
            .message-modal__grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 14px;
            }
            .message-modal__field {
                display: grid;
                gap: 8px;
                margin-bottom: 14px;
            }
            .message-modal__field label {
                color: #dbeafe;
                font-weight: 700;
                font-size: 0.92rem;
            }
            .message-modal__field input,
            .message-modal__field textarea {
                width: 100%;
                padding: 12px 14px;
                border-radius: 12px;
                border: 1px solid rgba(148, 163, 184, 0.18);
                background: rgba(2, 6, 23, 0.58);
                color: #f8fafc;
                font: inherit;
            }
            .message-modal__field input:disabled {
                color: #cbd5e1;
                opacity: 0.85;
            }
            .message-modal__feedback {
                margin-bottom: 14px;
                padding: 12px 14px;
                border-radius: 14px;
                font-size: 0.92rem;
                font-weight: 600;
                line-height: 1.5;
            }
            .message-modal__feedback.is-success {
                border: 1px solid rgba(34, 197, 94, 0.28);
                background: rgba(34, 197, 94, 0.12);
                color: #bbf7d0;
            }
            .message-modal__feedback.is-error {
                border: 1px solid rgba(239, 68, 68, 0.28);
                background: rgba(239, 68, 68, 0.12);
                color: #fecaca;
            }
            .message-modal__actions {
                display: flex;
                justify-content: flex-end;
                gap: 12px;
                flex-wrap: wrap;
            }
            @media (max-width: 640px) {
                .message-modal__panel {
                    margin-top: 4vh;
                    padding: 18px;
                }
                .message-modal__grid {
                    grid-template-columns: 1fr;
                }
            }
        </style>
    </body>
</html>
