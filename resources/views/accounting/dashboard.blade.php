@php($sidebarRole = auth()->user()->role ?? 'accountant')
@extends('layouts.app-shell')

@section('title', 'Accounting Dashboard | ' . config('app.name', 'School Portal'))

@section('content')
    <div class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-4 rounded-3xl border border-slate-200/10 bg-slate-950/70 p-6 shadow-2xl backdrop-blur">
            <div class="flex flex-col justify-between gap-4 lg:flex-row lg:items-end">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-cyan-300">Accounting</p>
                    <h1 class="mt-2 text-3xl font-bold text-white">Financial overview</h1>
                    <p class="mt-2 max-w-3xl text-slate-300">
                        Track revenues, expenses, invoices, payables, receivables, and cash flow from one dashboard.
                    </p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('accounting.transactions.index') }}" class="rounded-xl bg-cyan-400 px-4 py-2 font-semibold text-slate-950 transition hover:bg-cyan-300">New transaction</a>
                    <a href="{{ route('accounting.invoices.index') }}" class="rounded-xl border border-slate-700 px-4 py-2 font-semibold text-white transition hover:bg-slate-800">New invoice</a>
                    <a href="{{ route('accounting.reports.index') }}" class="rounded-xl border border-slate-700 px-4 py-2 font-semibold text-white transition hover:bg-slate-800">Reports</a>
                    <a href="{{ route('accounting.reports.index') }}#paid-students-sheet" class="rounded-xl border border-slate-700 px-4 py-2 font-semibold text-white transition hover:bg-slate-800">Paid students sheet</a>
                </div>
            </div>

            <form method="GET" class="grid gap-4 md:grid-cols-3">
                <label class="grid gap-2 text-sm font-medium text-slate-300">
                    From
                    <input type="date" name="from" value="{{ optional($from)->format('Y-m-d') }}" class="rounded-xl border border-slate-700 bg-slate-900 px-4 py-3 text-white focus:border-cyan-400 focus:outline-none">
                </label>
                <label class="grid gap-2 text-sm font-medium text-slate-300">
                    To
                    <input type="date" name="to" value="{{ optional($to)->format('Y-m-d') }}" class="rounded-xl border border-slate-700 bg-slate-900 px-4 py-3 text-white focus:border-cyan-400 focus:outline-none">
                </label>
                <div class="flex items-end">
                    <button type="submit" class="rounded-xl bg-white px-4 py-3 font-semibold text-slate-950 transition hover:bg-slate-200">Filter range</button>
                </div>
            </form>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-3xl border border-emerald-400/20 bg-emerald-400/10 p-5">
                <p class="text-sm font-medium text-emerald-200">Revenue</p>
                <p class="mt-2 text-3xl font-bold text-white">KSh {{ number_format($revenues, 2) }}</p>
                <p class="mt-1 text-sm text-slate-300">Income recognized in the selected period.</p>
            </div>
            <div class="rounded-3xl border border-rose-400/20 bg-rose-400/10 p-5">
                <p class="text-sm font-medium text-rose-200">Expenses</p>
                <p class="mt-2 text-3xl font-bold text-white">KSh {{ number_format($expenses, 2) }}</p>
                <p class="mt-1 text-sm text-slate-300">Operating and direct costs booked in the period.</p>
            </div>
            <div class="rounded-3xl border border-cyan-400/20 bg-cyan-400/10 p-5">
                <p class="text-sm font-medium text-cyan-200">Profit / Loss</p>
                <p class="mt-2 text-3xl font-bold text-white">KSh {{ number_format($profit, 2) }}</p>
                <p class="mt-1 text-sm text-slate-300">Revenue minus expenses.</p>
            </div>
            <div class="rounded-3xl border border-amber-400/20 bg-amber-400/10 p-5">
                <p class="text-sm font-medium text-amber-200">Cash flow</p>
                <p class="mt-2 text-3xl font-bold text-white">KSh {{ number_format($cashFlow, 2) }}</p>
                <p class="mt-1 text-sm text-slate-300">Movement in cash, bank, and mobile money accounts.</p>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200/10 bg-slate-950/70 p-6 shadow-2xl backdrop-blur">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-white">
                        <i class="fa-regular fa-comment-dots"></i> Live Messages
                    </h2>
                    <p class="mt-2 max-w-3xl text-slate-400">
                        Incoming fee reminders and replies appear here automatically without refreshing the page.
                    </p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <span id="accountingUnreadCount" class="rounded-full border border-amber-400/20 bg-amber-400/10 px-3 py-2 text-sm font-semibold text-amber-100">
                        <i class="fa-regular fa-bell"></i> 0 unread
                    </span>
                    <span id="accountingMessageCount" class="rounded-full border border-cyan-400/20 bg-cyan-400/10 px-3 py-2 text-sm font-semibold text-cyan-100">
                        <i class="fa-solid fa-comments"></i> 0 messages
                    </span>
                </div>
            </div>

            <div id="accountingLiveMessages" class="mt-4 grid gap-3">
                <div class="rounded-2xl border border-dashed border-slate-700 bg-slate-900/50 px-4 py-5 text-center text-slate-400">
                    Loading messages...
                </div>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            <div class="rounded-3xl border border-slate-200/10 bg-slate-950/70 p-5">
                <p class="text-sm font-semibold uppercase tracking-[0.16em] text-slate-400">Receivables</p>
                <p class="mt-3 text-2xl font-bold text-white">KSh {{ number_format($receivablesOutstanding, 2) }}</p>
                <p class="mt-2 text-sm text-slate-400">Open customer balances.</p>
            </div>
            <div class="rounded-3xl border border-slate-200/10 bg-slate-950/70 p-5">
                <p class="text-sm font-semibold uppercase tracking-[0.16em] text-slate-400">Payables</p>
                <p class="mt-3 text-2xl font-bold text-white">KSh {{ number_format($payablesOutstanding, 2) }}</p>
                <p class="mt-2 text-sm text-slate-400">Outstanding supplier obligations.</p>
            </div>
            <div class="rounded-3xl border border-slate-200/10 bg-slate-950/70 p-5">
                <p class="text-sm font-semibold uppercase tracking-[0.16em] text-slate-400">Balance sheet</p>
                <p class="mt-3 text-sm text-slate-300">Assets</p>
                <p class="text-lg font-semibold text-white">KSh {{ number_format($assets, 2) }}</p>
                <p class="mt-2 text-sm text-slate-300">Liabilities: KSh {{ number_format($liabilities, 2) }}</p>
                <p class="text-sm text-slate-300">Equity: KSh {{ number_format($equity, 2) }}</p>
            </div>
        </div>

        <div class="grid gap-4 lg:grid-cols-2">
            <div class="rounded-3xl border border-slate-200/10 bg-slate-950/70 p-5">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-white">Recent journal entries</h2>
                    <a href="{{ route('accounting.transactions.index') }}" class="text-sm font-semibold text-cyan-300">View all</a>
                </div>
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="text-slate-400">
                            <tr>
                                <th class="py-3 pr-4">Date</th>
                                <th class="py-3 pr-4">Reference</th>
                                <th class="py-3 pr-4">Description</th>
                                <th class="py-3 pr-4">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800 text-slate-200">
                            @forelse($recentJournals as $journal)
                                <tr>
                                    <td class="py-3 pr-4">{{ $journal->entry_date?->format('Y-m-d') }}</td>
                                    <td class="py-3 pr-4">{{ $journal->reference ?? $journal->journal_number }}</td>
                                    <td class="py-3 pr-4">{{ $journal->description ?? '-' }}</td>
                                    <td class="py-3 pr-4"><span class="rounded-full bg-emerald-400/10 px-3 py-1 text-xs font-semibold text-emerald-200">{{ ucfirst($journal->status) }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="py-6 text-slate-400">No journal entries yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200/10 bg-slate-950/70 p-5">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-white">Recent invoices</h2>
                    <a href="{{ route('accounting.invoices.index') }}" class="text-sm font-semibold text-cyan-300">View all</a>
                </div>
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="text-slate-400">
                            <tr>
                                <th class="py-3 pr-4">Invoice</th>
                                <th class="py-3 pr-4">Party</th>
                                <th class="py-3 pr-4">Type</th>
                                <th class="py-3 pr-4">Balance</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800 text-slate-200">
                            @forelse($recentInvoices as $invoice)
                                <tr>
                                    <td class="py-3 pr-4">{{ $invoice->invoice_number }}</td>
                                    <td class="py-3 pr-4">{{ $invoice->party_name }}</td>
                                    <td class="py-3 pr-4">{{ ucfirst($invoice->direction) }}</td>
                                    <td class="py-3 pr-4">KSh {{ number_format($invoice->balance_due, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="py-6 text-slate-400">No invoices yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        const accountingLiveMessages = document.getElementById('accountingLiveMessages');
        const accountingUnreadCount = document.getElementById('accountingUnreadCount');
        const accountingMessageCount = document.getElementById('accountingMessageCount');

        function escapeHtml(value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        function renderAccountingMessages(messages) {
            if (!accountingLiveMessages) {
                return;
            }

            if (!messages.length) {
                accountingLiveMessages.innerHTML = `
                    <div class="rounded-2xl border border-dashed border-slate-700 bg-slate-900/50 px-4 py-5 text-center text-slate-400">
                        No messages yet. New messages will appear here automatically.
                    </div>
                `;
                return;
            }

            accountingLiveMessages.innerHTML = messages.map((item) => `
                <article class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <span class="inline-flex rounded-full border px-3 py-1 text-xs font-bold uppercase tracking-[0.08em] ${item.read_at ? 'border-slate-700 bg-slate-800 text-slate-300' : 'border-amber-400/20 bg-amber-400/10 text-amber-100'}">
                                ${item.read_at ? 'Read' : 'Unread'}
                            </span>
                            <h3 class="mt-3 text-base font-semibold text-white">${escapeHtml(item.title)}</h3>
                        </div>
                        <div class="text-sm text-slate-400">${escapeHtml(item.time ?? '')}</div>
                    </div>
                    <p class="mt-3 whitespace-pre-wrap text-sm leading-6 text-slate-300">${escapeHtml(item.message)}</p>
                </article>
            `).join('');
        }

        async function loadAccountingMessages() {
            try {
                const response = await fetch("{{ route('notifications.live') }}", {
                    headers: { 'Accept': 'application/json' },
                });

                if (!response.ok) {
                    return;
                }

                const payload = await response.json();
                const messages = (payload.notifications ?? []).filter((item) => item.type === 'message');

                if (accountingUnreadCount) {
                    accountingUnreadCount.innerHTML = `<i class="fa-regular fa-bell"></i> ${payload.unreadCount ?? 0} unread`;
                }

                if (accountingMessageCount) {
                    accountingMessageCount.innerHTML = `<i class="fa-solid fa-comments"></i> ${payload.messageCount ?? 0} messages`;
                }

                renderAccountingMessages(messages);
            } catch (error) {
                console.error('Failed to load accounting messages', error);
            }
        }

        loadAccountingMessages();
        window.setInterval(loadAccountingMessages, 6000);
    </script>
@endsection
