@php($sidebarRole = auth()->user()->role ?? 'accountant')
@extends('layouts.app-shell')

@section('title', 'Transactions | ' . config('app.name', 'School Portal'))

@section('content')
    <div class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">
        <div class="rounded-3xl border border-slate-200/10 bg-slate-950/70 p-6">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-cyan-300">Transactions</p>
            <h1 class="mt-2 text-3xl font-bold text-white">Manual journal entries</h1>
            <p class="mt-2 text-slate-300">Record income, expenses, asset transfers, and adjustments with balanced debit and credit lines.</p>
        </div>

        <form method="POST" action="{{ route('accounting.transactions.store') }}" enctype="multipart/form-data" class="rounded-3xl border border-slate-200/10 bg-slate-950/70 p-6">
            @csrf
            <div class="grid gap-4 md:grid-cols-3">
                <label class="grid gap-2 text-sm text-slate-300">Entry date
                    <input type="date" name="entry_date" value="{{ old('entry_date', now()->toDateString()) }}" class="rounded-xl border border-slate-700 bg-slate-900 px-4 py-3 text-white" required>
                </label>
                <label class="grid gap-2 text-sm text-slate-300">Reference
                    <input name="reference" value="{{ old('reference') }}" class="rounded-xl border border-slate-700 bg-slate-900 px-4 py-3 text-white">
                </label>
                <label class="grid gap-2 text-sm text-slate-300">Attachment
                    <input type="file" name="attachment" class="rounded-xl border border-slate-700 bg-slate-900 px-4 py-3 text-white">
                </label>
            </div>

            <label class="mt-4 grid gap-2 text-sm text-slate-300">Description
                <textarea name="description" rows="3" class="rounded-xl border border-slate-700 bg-slate-900 px-4 py-3 text-white">{{ old('description') }}</textarea>
            </label>

            <div class="mt-6 flex items-center justify-between">
                <h2 class="text-xl font-semibold text-white">Journal lines</h2>
                <button type="button" id="addJournalLine" class="rounded-xl border border-slate-700 px-4 py-2 font-semibold text-white transition hover:bg-slate-800">Add line</button>
            </div>

            <div id="journalLines" class="mt-4 grid gap-3">
                @for($i = 0; $i < 2; $i++)
                    <div class="journal-line grid gap-3 rounded-2xl border border-slate-800 bg-slate-900 p-4 md:grid-cols-5">
                        <label class="grid gap-2 text-xs text-slate-400 md:col-span-2">Account
                            <select name="lines[{{ $i }}][account_id]" class="rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-white" required>
                                <option value="">Select account</option>
                                @foreach($accounts as $account)
                                    <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="grid gap-2 text-xs text-slate-400 md:col-span-2">Description
                            <input name="lines[{{ $i }}][description]" class="rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-white">
                        </label>
                        <div class="grid gap-3 md:grid-cols-2">
                            <label class="grid gap-2 text-xs text-slate-400">Debit
                                <input type="number" step="0.01" min="0" name="lines[{{ $i }}][debit]" value="0" class="rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-white">
                            </label>
                            <label class="grid gap-2 text-xs text-slate-400">Credit
                                <input type="number" step="0.01" min="0" name="lines[{{ $i }}][credit]" value="0" class="rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-white">
                            </label>
                        </div>
                    </div>
                @endfor
            </div>

            <div class="mt-5 flex flex-wrap gap-3">
                <button type="submit" class="rounded-xl bg-cyan-400 px-4 py-3 font-semibold text-slate-950">Post transaction</button>
                <a href="{{ route('accounting.dashboard') }}" class="rounded-xl border border-slate-700 px-4 py-3 font-semibold text-white">Back to dashboard</a>
            </div>
        </form>

        <div class="rounded-3xl border border-slate-200/10 bg-slate-950/70 p-6">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-white">Recent transactions</h2>
                <span class="text-sm text-slate-400">{{ $journals->total() }} entries</span>
            </div>
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="text-slate-400">
                        <tr>
                            <th class="py-3 pr-4">Date</th>
                            <th class="py-3 pr-4">Journal</th>
                            <th class="py-3 pr-4">Description</th>
                            <th class="py-3 pr-4">Lines</th>
                            <th class="py-3 pr-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-slate-200">
                        @forelse($journals as $journal)
                            <tr>
                                <td class="py-3 pr-4">{{ $journal->entry_date?->format('Y-m-d') }}</td>
                                <td class="py-3 pr-4">{{ $journal->journal_number }}</td>
                                <td class="py-3 pr-4">{{ $journal->description ?? '-' }}</td>
                                <td class="py-3 pr-4">
                                    <details class="group">
                                        <summary class="cursor-pointer text-cyan-300">View lines</summary>
                                        <div class="mt-2 space-y-2 rounded-2xl border border-slate-800 bg-slate-900 p-3">
                                            @foreach($journal->lines as $line)
                                                <div class="flex flex-wrap justify-between gap-3 text-xs text-slate-300">
                                                    <span>{{ $line->account?->code }} - {{ $line->account?->name }}</span>
                                                    <span>Dr {{ number_format($line->debit, 2) }} / Cr {{ number_format($line->credit, 2) }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </details>
                                </td>
                                    <td class="py-3 pr-4">
                                        @unless($journal->source_type)
                                            <form method="POST" action="{{ route('accounting.transactions.destroy', $journal) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-sm font-semibold text-rose-300" onclick="return confirm('Delete this journal entry?')">Delete</button>
                                            </form>
                                        @else
                                            <span class="text-xs uppercase tracking-[0.18em] text-slate-500">Locked</span>
                                        @endunless
                                    </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-6 text-slate-400">No transactions yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-5">{{ $journals->links() }}</div>
        </div>
    </div>

    <script>
        (() => {
            const container = document.getElementById('journalLines');
            const button = document.getElementById('addJournalLine');
            if (!container || !button) return;

            button.addEventListener('click', () => {
                const index = container.querySelectorAll('.journal-line').length;
                const accounts = @json($accounts->map(fn ($account) => ['id' => $account->id, 'label' => $account->code . ' - ' . $account->name]));
                const options = ['<option value="">Select account</option>']
                    .concat(accounts.map((account) => `<option value="${account.id}">${account.label}</option>`))
                    .join('');

                const row = document.createElement('div');
                row.className = 'journal-line grid gap-3 rounded-2xl border border-slate-800 bg-slate-900 p-4 md:grid-cols-5';
                row.innerHTML = `
                    <label class="grid gap-2 text-xs text-slate-400 md:col-span-2">Account
                        <select name="lines[${index}][account_id]" class="rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-white" required>${options}</select>
                    </label>
                    <label class="grid gap-2 text-xs text-slate-400 md:col-span-2">Description
                        <input name="lines[${index}][description]" class="rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-white">
                    </label>
                    <div class="grid gap-3 md:grid-cols-2">
                        <label class="grid gap-2 text-xs text-slate-400">Debit
                            <input type="number" step="0.01" min="0" name="lines[${index}][debit]" value="0" class="rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-white">
                        </label>
                        <label class="grid gap-2 text-xs text-slate-400">Credit
                            <input type="number" step="0.01" min="0" name="lines[${index}][credit]" value="0" class="rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-white">
                        </label>
                    </div>
                `;
                container.appendChild(row);
            });
        })();
    </script>
@endsection
