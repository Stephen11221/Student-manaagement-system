@php($sidebarRole = auth()->user()->role ?? 'accountant')
@extends('layouts.app-shell')

@section('title', 'Invoices | ' . config('app.name', 'School Portal'))

@section('content')
    <div class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">
        <div class="rounded-3xl border border-slate-200/10 bg-slate-950/70 p-6">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-cyan-300">Invoices and bills</p>
            <h1 class="mt-2 text-3xl font-bold text-white">Track accounts receivable and payable</h1>
            <p class="mt-2 text-slate-300">Create customer invoices, supplier bills, and apply payments against them.</p>
        </div>

        <form method="POST" action="{{ route('accounting.invoices.store') }}" enctype="multipart/form-data" class="rounded-3xl border border-slate-200/10 bg-slate-950/70 p-6">
            @csrf
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <label class="grid gap-2 text-sm text-slate-300">Direction
                    <select name="direction" class="rounded-xl border border-slate-700  px-4 py-3 text-white">
                        <option value="receivable">Receivable</option>
                        <option value="payable">Payable</option>
                    </select>
                </label>
                <label class="grid gap-2 text-sm text-slate-300">Party name
                    <input name="party_name" value="{{ old('party_name') }}" class="rounded-xl border border-slate-700 bg-slate-900 px-4 py-3 text-white" required>
                </label>
                <label class="grid gap-2 text-sm text-slate-300">Party email
                    <input name="party_email" type="email" value="{{ old('party_email') }}" class="rounded-xl border border-slate-700 bg-slate-900 px-4 py-3 text-white">
                </label>
                <label class="grid gap-2 text-sm text-slate-300">Party phone
                    <input name="party_phone" value="{{ old('party_phone') }}" class="rounded-xl border border-slate-700 bg-slate-900 px-4 py-3 text-white">
                </label>
                <label class="grid gap-2 text-sm text-slate-300">Issue date
                    <input type="date" name="issue_date" value="{{ old('issue_date', now()->toDateString()) }}" class="rounded-xl border border-slate-700 bg-slate-900 px-4 py-3 text-white" required>
                </label>
                <label class="grid gap-2 text-sm text-slate-300">Due date
                    <input type="date" name="due_date" value="{{ old('due_date') }}" class="rounded-xl border border-slate-700 bg-slate-900 px-4 py-3 text-white">
                </label>
                <label class="grid gap-2 text-sm text-slate-300">Tax amount
                    <input type="number" step="0.01" min="0" name="tax_amount" value="{{ old('tax_amount', 0) }}" class="rounded-xl border border-slate-700 bg-slate-900 px-4 py-3 text-white">
                </label>
                <label class="grid gap-2 text-sm text-slate-300">Attachment
                    <input type="file" name="attachment" class="rounded-xl border border-slate-700 bg-slate-900 px-4 py-3 text-white">
                </label>
            </div>

            <label class="mt-4 grid gap-2 text-sm text-slate-300">Notes
                <textarea name="notes" rows="3" class="rounded-xl border border-slate-700 bg-slate-900 px-4 py-3 text-white">{{ old('notes') }}</textarea>
            </label>

            <div class="mt-6 flex items-center justify-between">
                <h2 class="text-xl font-semibold text-white">Invoice items</h2>
                <button type="button" id="addInvoiceItem" class="rounded-xl border border-slate-700 px-4 py-2 font-semibold text-white transition hover:bg-slate-800">Add item</button>
            </div>

            <div id="invoiceItems" class="mt-4 grid gap-3">
                @for($i = 0; $i < 2; $i++)
                    <div class="invoice-item grid gap-3 rounded-2xl border border-slate-800 bg-slate-900 p-4 xl:grid-cols-6">
                        <label class="grid gap-2 text-xs text-slate-400 xl:col-span-2">Description
                            <input name="items[{{ $i }}][description]" class="rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-white" required>
                        </label>
                        <label class="grid gap-2 text-xs text-slate-400">Account
                            <select name="items[{{ $i }}][account_id]" class="rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-white">
                                <option value="">Optional</option>
                                @foreach($accounts as $account)
                                    <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="grid gap-2 text-xs text-slate-400">Qty
                            <input type="number" step="0.01" min="0.01" name="items[{{ $i }}][quantity]" value="1" class="rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-white">
                        </label>
                        <label class="grid gap-2 text-xs text-slate-400">Unit price
                            <input type="number" step="0.01" min="0" name="items[{{ $i }}][unit_price]" value="0" class="rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-white">
                        </label>
                        <label class="grid gap-2 text-xs text-slate-400">Tax %
                            <input type="number" step="0.01" min="0" name="items[{{ $i }}][tax_rate]" value="0" class="rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-white">
                        </label>
                    </div>
                @endfor
            </div>

            <div class="mt-5 flex flex-wrap gap-3">
                <button type="submit" class="rounded-xl bg-cyan-400 px-4 py-3 font-semibold text-slate-950">Save invoice</button>
                <a href="{{ route('accounting.dashboard') }}" class="rounded-xl border border-slate-700 px-4 py-3 font-semibold text-white">Back to dashboard</a>
            </div>
        </form>

        <div class="rounded-3xl border border-slate-200/10 bg-slate-950/70 p-6">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-white">Invoices and bills</h2>
                <span class="text-sm text-slate-400">{{ $invoices->total() }} records</span>
            </div>
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="text-slate-400">
                        <tr>
                            <th class="py-3 pr-4">Number</th>
                            <th class="py-3 pr-4">Party</th>
                            <th class="py-3 pr-4">Direction</th>
                            <th class="py-3 pr-4">Status</th>
                            <th class="py-3 pr-4">Total</th>
                            <th class="py-3 pr-4">Balance</th>
                            <th class="py-3 pr-4">Payment</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-slate-200">
                        @forelse($invoices as $invoice)
                            <tr>
                                <td class="py-3 pr-4">{{ $invoice->invoice_number }}</td>
                                <td class="py-3 pr-4">{{ $invoice->party_name }}</td>
                                <td class="py-3 pr-4">{{ ucfirst($invoice->direction) }}</td>
                                <td class="py-3 pr-4">{{ ucfirst($invoice->status) }}</td>
                                <td class="py-3 pr-4">KSh {{ number_format($invoice->total_amount, 2) }}</td>
                                <td class="py-3 pr-4">KSh {{ number_format($invoice->balance_due, 2) }}</td>
                                <td class="py-3 pr-4">
                                    <details>
                                        <summary class="cursor-pointer text-cyan-300">Record payment</summary>
                                        <form method="POST" action="{{ route('accounting.invoices.payments.store', $invoice) }}" enctype="multipart/form-data" class="mt-3 grid gap-3 rounded-2xl border border-slate-800 bg-slate-900 p-4">
                                            @csrf
                                            <div class="grid gap-3 md:grid-cols-2">
                                                <label class="grid gap-2 text-xs text-slate-400">Amount
                                                    <input type="number" step="0.01" min="0.01" name="amount" value="{{ $invoice->balance_due }}" class="rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-white">
                                                </label>
                                                <label class="grid gap-2 text-xs text-slate-400">Method
                                                    <select name="payment_method" class="rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-white">
                                                        <option value="cash">Cash</option>
                                                        <option value="bank">Bank</option>
                                                        <option value="mobile_money">Mobile money</option>
                                                        <option value="card">Card</option>
                                                        <option value="cheque">Cheque</option>
                                                        <option value="other">Other</option>
                                                    </select>
                                                </label>
                                                <label class="grid gap-2 text-xs text-slate-400">Reference
                                                    <input name="reference" class="rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-white">
                                                </label>
                                                <label class="grid gap-2 text-xs text-slate-400">Paid at
                                                    <input type="datetime-local" name="paid_at" value="{{ now()->format('Y-m-d\TH:i') }}" class="rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-white">
                                                </label>
                                                <label class="grid gap-2 text-xs text-slate-400 md:col-span-2">Attachment
                                                    <input type="file" name="attachment" class="rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-white">
                                                </label>
                                            </div>
                                            <label class="grid gap-2 text-xs text-slate-400">Notes
                                                <textarea name="notes" rows="2" class="rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-white"></textarea>
                                            </label>
                                            <button type="submit" class="rounded-xl bg-cyan-400 px-4 py-2 font-semibold text-slate-950">Save payment</button>
                                        </form>
                                    </details>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="py-6 text-slate-400">No invoices or bills yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-5">{{ $invoices->links() }}</div>
        </div>
    </div>

    <script>
        (() => {
            const container = document.getElementById('invoiceItems');
            const button = document.getElementById('addInvoiceItem');
            if (!container || !button) return;

            const accounts = @json($accounts->map(fn ($account) => ['id' => $account->id, 'label' => $account->code . ' - ' . $account->name]));
            const options = ['<option value="">Optional</option>']
                .concat(accounts.map((account) => `<option value="${account.id}">${account.label}</option>`))
                .join('');

            button.addEventListener('click', () => {
                const index = container.querySelectorAll('.invoice-item').length;
                const row = document.createElement('div');
                row.className = 'invoice-item grid gap-3 rounded-2xl border border-slate-800 bg-slate-900 p-4 xl:grid-cols-6';
                row.innerHTML = `
                    <label class="grid gap-2 text-xs text-slate-400 xl:col-span-2">Description
                        <input name="items[${index}][description]" class="rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-white" required>
                    </label>
                    <label class="grid gap-2 text-xs text-slate-400">Account
                        <select name="items[${index}][account_id]" class="rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-white">${options}</select>
                    </label>
                    <label class="grid gap-2 text-xs text-slate-400">Qty
                        <input type="number" step="0.01" min="0.01" name="items[${index}][quantity]" value="1" class="rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-white">
                    </label>
                    <label class="grid gap-2 text-xs text-slate-400">Unit price
                        <input type="number" step="0.01" min="0" name="items[${index}][unit_price]" value="0" class="rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-white">
                    </label>
                    <label class="grid gap-2 text-xs text-slate-400">Tax %
                        <input type="number" step="0.01" min="0" name="items[${index}][tax_rate]" value="0" class="rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-white">
                    </label>
                `;
                container.appendChild(row);
            });
        })();
    </script>
@endsection
