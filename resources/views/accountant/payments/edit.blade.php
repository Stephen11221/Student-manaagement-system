<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Edit Fee Payment | {{ config('app.name', 'School Portal') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            :root { color-scheme: dark; --text:#e2e8f0; --muted:#94a3b8; --heading:#f8fafc; --primary:#f59e0b; }
            * { box-sizing:border-box; margin:0; padding:0; }
            body { font-family:"Instrument Sans",sans-serif; color:var(--text); background:linear-gradient(135deg,#020617 0%,#0f172a 54%,#111827 100%); min-height:100vh; }
            .shell { max-width:1100px; margin:0 auto; padding:40px 20px; }
            .header { display:flex; justify-content:space-between; gap:16px; flex-wrap:wrap; margin-bottom:24px; padding-bottom:20px; border-bottom:1px solid rgba(148,163,184,.12); }
            h1,h2,h3,p { margin:0; }
            h1 { color:var(--heading); font-size:2.2rem; }
            .subtitle { color:var(--muted); margin-top:8px; }
            .layout { display:grid; grid-template-columns:1.15fr .85fr; gap:20px; align-items:start; }
            .card { background:rgba(15,23,42,.78); border:1px solid rgba(148,163,184,.18); border-radius:18px; padding:24px; backdrop-filter:blur(18px); }
            .summary { display:grid; gap:16px; }
            .summary-box { background:rgba(245,158,11,.08); border:1px solid rgba(245,158,11,.22); border-radius:14px; padding:18px; }
            .summary-box .label { text-transform:uppercase; letter-spacing:.08em; font-size:.75rem; color:#fbbf24; font-weight:700; }
            .summary-box .value { margin-top:6px; font-size:1.5rem; color:var(--heading); font-weight:700; }
            .mini { font-size:1rem; font-weight:600; color:var(--text); }
            label { display:block; margin-bottom:8px; color:#dbeafe; font-weight:600; font-size:.95rem; }
            input, select, textarea { width:100%; padding:12px 14px; border:1px solid rgba(148,163,184,.2); border-radius:10px; background:rgba(2,6,23,.56); color:#f8fafc; font:inherit; }
            textarea { min-height:110px; resize:vertical; }
            .grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:16px; }
            .field { margin-bottom:16px; }
            .help { margin-top:6px; color:var(--muted); font-size:.85rem; line-height:1.5; }
            .actions { display:flex; gap:12px; flex-wrap:wrap; margin-top:24px; }
            .btn { border:none; cursor:pointer; text-decoration:none; font-weight:700; padding:12px 18px; border-radius:10px; display:inline-flex; align-items:center; gap:8px; }
            .primary { background:linear-gradient(135deg,#f59e0b,#f97316); color:#082f49; }
            .secondary { background:rgba(148,163,184,.1); color:var(--heading); border:1px solid rgba(148,163,184,.18); }
            .error { background:rgba(239,68,68,.12); border:1px solid rgba(239,68,68,.24); color:#fecaca; padding:14px 16px; border-radius:12px; margin-bottom:18px; }
            .badge { display:inline-flex; align-items:center; gap:6px; padding:6px 10px; border-radius:999px; font-weight:700; font-size:.82rem; }
            .badge.paid { background:rgba(52,211,153,.14); color:#6ee7b7; }
            .badge.partial { background:rgba(245,158,11,.14); color:#fbbf24; }
            .badge.unpaid { background:rgba(239,68,68,.14); color:#fca5a5; }
            @media (max-width: 900px) {
                .layout { grid-template-columns:1fr; }
                .grid { grid-template-columns:1fr; }
            }
        </style>
    </head>
    <body>
        <div class="shell">
            <div class="header">
                <div>
                    <h1><i class="fa-solid fa-pen-to-square"></i> Edit Fee Payment</h1>
                    <p class="subtitle">Update cash, check, deposit, bursary, or any other fee record.</p>
                </div>
                <div class="actions" style="margin-top:0;">
                    <button type="button" class="btn primary" onclick="openQuickMessageModal()">
                        <i class="fa-solid fa-comment-dots"></i> Quick Message
                    </button>
                    <a href="{{ route('accountant.dashboard') }}" class="btn secondary"><i class="fa-solid fa-arrow-left"></i> Back</a>
                </div>
            </div>

            @if ($errors->any())
                <div class="error">
                    <strong><i class="fa-solid fa-triangle-exclamation"></i> Please fix the highlighted issues.</strong>
                    <ul style="margin-top:8px; margin-left:20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @php
                $balance = max(((float) $payment->amount_due) - ((float) $payment->amount_paid), 0);
                $statusClass = $payment->status === 'paid' ? 'paid' : ($payment->status === 'partial' ? 'partial' : 'unpaid');
            @endphp

            <div class="layout">
                <div class="card">
                    <h2 style="color:var(--heading); margin-bottom:18px;">Payment Details</h2>
                    <form method="POST" action="{{ route('accountant.payments.update', $payment->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid">
                            <div class="field">
                                <label>Student</label>
                                <select name="student_id" required>
                                    @foreach ($students as $student)
                                        <option value="{{ $student->id }}" @selected((string) old('student_id', $payment->student_id) === (string) $student->id)>
                                            {{ $student->name }}{{ $student->admission_number ? ' • '.$student->admission_number : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="field">
                                <label>Academic Year</label>
                                <input type="text" name="academic_year" value="{{ old('academic_year', $payment->academic_year) }}" placeholder="2025/2026">
                            </div>

                            <div class="field">
                                <label>Term</label>
                                <input type="text" name="term" value="{{ old('term', $payment->term) }}" placeholder="Term 1">
                            </div>

                            <div class="field">
                                <label>Receipt Number</label>
                                <input type="text" name="receipt_number" value="{{ old('receipt_number', $payment->receipt_number) }}" placeholder="Receipt reference">
                            </div>

                            <div class="field">
                                <label>Amount Due</label>
                                <input type="number" min="0" step="0.01" name="amount_due" value="{{ old('amount_due', $payment->amount_due) }}" required>
                            </div>

                            <div class="field">
                                <label>Amount Paid</label>
                                <input type="number" min="0" step="0.01" name="amount_paid" value="{{ old('amount_paid', $payment->amount_paid) }}" required>
                            </div>

                            <div class="field">
                                <label>Payment Method</label>
                                <select name="payment_method" required>
                                    @foreach ([
                                        'cash' => 'Cash',
                                        'check' => 'Check',
                                        'cheque' => 'Cheque (legacy)',
                                        'deposit' => 'Deposit',
                                        'bursary' => 'Bursary',
                                        'mpesa' => 'M-Pesa',
                                        'pochi_la_biashara' => 'Pochi la Biashara',
                                        'bank_transfer' => 'Bank Transfer',
                                        'card' => 'Card',
                                        'other' => 'Other',
                                    ] as $value => $label)
                                        <option value="{{ $value }}" @selected(old('payment_method', $payment->payment_method) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <div class="help">Use this when correcting cash, check, deposit, bursary, or mobile payment records.</div>
                            </div>

                            <div class="field">
                                <label>Phone Number</label>
                                <input type="text" name="phone_number" value="{{ old('phone_number', $payment->phone_number ?? $payment->student?->phone) }}" placeholder="07XX XXX XXX">
                                <div class="help">Used for Daraja STK pushes and M-Pesa follow-up.</div>
                            </div>

                            <div class="field">
                                <label>Status</label>
                                <select name="status" required>
                                    <option value="paid" @selected(old('status', $payment->status) === 'paid')>Paid</option>
                                    <option value="partial" @selected(old('status', $payment->status) === 'partial')>Partial</option>
                                    <option value="unpaid" @selected(old('status', $payment->status) === 'unpaid')>Unpaid</option>
                                </select>
                            </div>

                            <div class="field">
                                <label>Paid At</label>
                                <input type="datetime-local" name="paid_at" value="{{ old('paid_at', optional($payment->paid_at)->format('Y-m-d\\TH:i')) }}">
                            </div>
                        </div>

                        <div class="field">
                            <label>Notes</label>
                            <textarea name="notes" placeholder="Add a short finance note or correction reason.">{{ old('notes', $payment->notes) }}</textarea>
                        </div>

                        <div class="actions">
                            <button type="submit" class="btn primary"><i class="fa-regular fa-floppy-disk"></i> Save Payment</button>
                            <a href="{{ route('accountant.dashboard') }}" class="btn secondary"><i class="fa-solid fa-xmark"></i> Cancel</a>
                        </div>
                    </form>

                    <div style="margin-top:28px; padding-top:24px; border-top:1px solid rgba(148,163,184,.12);">
                        <h3 style="color:var(--heading); margin-bottom:12px;"><i class="fa-solid fa-mobile-screen-button"></i> Daraja STK Push</h3>
                        <p style="color:var(--muted); margin-bottom:16px;">Send a payment prompt to the student’s phone through Safaricom Daraja.</p>
                        <form method="POST" action="{{ route('accountant.payments.daraja.push', $payment->id) }}">
                            @csrf
                            <div class="grid">
                                <div class="field">
                                    <label>STK Phone Number</label>
                                    <input type="text" name="phone_number" value="{{ old('phone_number', $payment->phone_number ?? $payment->student?->phone) }}" placeholder="2547XXXXXXXX" required>
                                </div>
                                <div class="field">
                                    <label>Request Amount</label>
                                    <input type="number" name="amount" min="1" step="1" value="{{ old('amount', max(((float) $payment->amount_due) - ((float) $payment->amount_paid), 1)) }}" required>
                                </div>
                            </div>
                            <div class="actions" style="margin-top:16px;">
                                <button type="submit" class="btn primary"><i class="fa-solid fa-paper-plane"></i> Send STK Push</button>
                            </div>
                        </form>
                    </div>

                    <div id="student-message" style="margin-top:28px; padding-top:24px; border-top:1px solid rgba(148,163,184,.12);">
                        <h3 style="color:var(--heading); margin-bottom:12px;"><i class="fa-solid fa-comment-dots"></i> Send Student Message</h3>
                        <p style="color:var(--muted); margin-bottom:16px;">Send a private note to this student about fees, reminders, or follow-up.</p>
                        <form method="POST" action="{{ route('accountant.payments.message', $payment->id) }}">
                            @csrf
                            <div class="grid">
                                <div class="field">
                                    <label>Subject</label>
                                    <input type="text" name="subject" value="{{ old('subject', 'Fee update for ' . ($payment->student?->name ?? 'student')) }}" required>
                                </div>
                                <div class="field">
                                    <label>Recipient</label>
                                    <input type="text" value="{{ $payment->student?->name ?? 'Unknown student' }}" disabled>
                                </div>
                            </div>
                            <div class="field">
                                <label>Message</label>
                                <textarea name="message" placeholder="Write a short message to the student..." required>{{ old('message', $payment->status === 'unpaid' ? 'Please review your fee balance and settle the pending amount.' : 'Your fee record has been updated. Please check the details.') }}</textarea>
                            </div>
                            <div class="actions" style="margin-top:16px;">
                                <button type="submit" class="btn primary"><i class="fa-solid fa-paper-plane"></i> Send Message</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card summary">
                    <div>
                        <h2 style="color:var(--heading);">Current Summary</h2>
                        <p style="color:var(--muted); margin-top:8px;">Quick reference while editing the payment record.</p>
                    </div>

                    <div class="summary-box">
                        <div class="label">Student</div>
                        <div class="value">{{ $payment->student?->name ?? 'Unknown student' }}</div>
                        <div class="help">{{ $payment->student?->admission_number ?? 'No admission number' }}</div>
                    </div>

                    <div class="summary-box">
                        <div class="label">Balance</div>
                        <div class="value">KSh {{ number_format($balance, 2) }}</div>
                        <div class="help">Based on current due and paid amounts.</div>
                    </div>

                    <div class="summary-box">
                        <div class="label">Status</div>
                        <div class="value"><span class="badge {{ $statusClass }}">{{ ucfirst($payment->status) }}</span></div>
                        <div class="help">You can switch this to paid, partial, or unpaid.</div>
                    </div>

                    <div class="summary-box">
                        <div class="label">Current Method</div>
                        <div class="value">
                            {{ $payment->payment_method === 'check' ? 'Check' : ucfirst(str_replace('_', ' ', $payment->payment_method ?? 'other')) }}
                        </div>
                        <div class="help">Cash, check, deposit, bursary, and mobile payment methods are supported here.</div>
                    </div>

                    <div class="summary-box">
                        <div class="label">Daraja Request</div>
                        <div class="mini">{{ $payment->checkout_request_id ?? 'No request sent yet' }}</div>
                        <div class="help">
                            @if($payment->daraja_response_description)
                                {{ $payment->daraja_response_description }}
                            @else
                                Send an STK push to create a live request.
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('partials.idle-timeout-modal')
        <div id="quickMessageModal" class="message-modal" style="display:none;">
            <div class="message-modal__backdrop" onclick="closeQuickMessageModal()"></div>
            <div class="message-modal__panel" role="dialog" aria-modal="true" aria-labelledby="quickMessageModalTitle">
                <div class="message-modal__header">
                    <div>
                        <div class="message-modal__eyebrow">Quick message</div>
                        <h3 id="quickMessageModalTitle">Send Student Message</h3>
                    </div>
                    <button type="button" class="message-modal__close" onclick="closeQuickMessageModal()" aria-label="Close message modal">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <form id="quickMessageModalForm" method="POST" action="{{ route('accountant.payments.message', $payment->id) }}">
                    @csrf
                    <div id="quickMessageFeedback" class="message-modal__feedback" style="display:none;"></div>
                    <div class="message-modal__grid">
                        <div class="message-modal__field">
                            <label for="quickMessageRecipient">Recipient</label>
                            <input id="quickMessageRecipient" type="text" value="{{ $payment->student?->name ?? 'Unknown student' }}" disabled>
                        </div>
                        <div class="message-modal__field">
                            <label for="quickMessageSubject">Subject</label>
                            <input id="quickMessageSubject" type="text" name="subject" value="{{ old('subject', 'Fee update for ' . ($payment->student?->name ?? 'student')) }}" required>
                        </div>
                    </div>
                    <div class="message-modal__field">
                        <label for="quickMessageBody">Message</label>
                        <textarea id="quickMessageBody" name="message" rows="6" required placeholder="Write a short message to the student...">{{ old('message', $payment->status === 'unpaid' ? 'Please review your fee balance and settle the pending amount.' : 'Your fee record has been updated. Please check the details.') }}</textarea>
                    </div>
                    <div class="message-modal__actions">
                        <button type="button" class="ghost-btn" style="padding:10px 14px;" onclick="closeQuickMessageModal()">
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

            const quickMessageModal = document.getElementById('quickMessageModal');
            const quickMessageForm = document.getElementById('quickMessageModalForm');
            const quickMessageFeedback = document.getElementById('quickMessageFeedback');

            function showQuickMessageFeedback(message, type = 'success') {
                if (!quickMessageFeedback) {
                    return;
                }

                quickMessageFeedback.className = `message-modal__feedback is-${type}`;
                quickMessageFeedback.textContent = message;
                quickMessageFeedback.style.display = 'block';
            }

            function openQuickMessageModal() {
                quickMessageFeedback.style.display = 'none';
                quickMessageModal.style.display = 'block';
                document.body.style.overflow = 'hidden';
                document.getElementById('quickMessageSubject')?.focus();
            }

            function closeQuickMessageModal() {
                quickMessageModal.style.display = 'none';
                document.body.style.overflow = '';
            }

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && quickMessageModal.style.display === 'block') {
                    closeQuickMessageModal();
                }
            });

            quickMessageForm.addEventListener('submit', async (event) => {
                event.preventDefault();

                const submitButton = quickMessageForm.querySelector('button[type="submit"]');
                const submitLabel = submitButton?.innerHTML ?? '';
                const csrfToken = quickMessageForm.querySelector('input[name="_token"]')?.value ?? '';

                try {
                    if (submitButton) {
                        submitButton.disabled = true;
                        submitButton.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Sending...';
                    }

                    const response = await fetch(quickMessageForm.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: new FormData(quickMessageForm),
                    });

                    const payload = await response.json().catch(() => ({}));

                    if (!response.ok) {
                        const firstError = payload?.errors ? Object.values(payload.errors).flat().filter(Boolean)[0] : null;
                        showQuickMessageFeedback(payload.message || firstError || 'Could not send the message right now.', 'error');
                        return;
                    }

                    showQuickMessageFeedback(payload.message || 'Message sent successfully.', 'success');
                    setTimeout(() => closeQuickMessageModal(), 700);
                    quickMessageForm.reset();
                } catch (error) {
                    showQuickMessageFeedback('Could not send the message right now.', 'error');
                    console.error('Failed to send quick fee message', error);
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
