@extends('layouts.app-shell')

@section('title', 'Reports | ' . config('app.name', 'School Portal'))

@section('content')

@php
    $sidebarRole = auth()->user()->role ?? 'accountant';
@endphp

<div class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">

    <!-- HEADER -->
    <div class="rounded-3xl border border-slate-200/10 bg-slate-950/70 p-6">
        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-cyan-300">Reports</p>
        <h1 class="mt-2 text-3xl font-bold text-white">Financial statements and exports</h1>
        <p class="mt-2 text-slate-300">
            Generate period-based reports for profit and loss, balance sheet, cash flow, and ledger review.
        </p>
    </div>

    <!-- FILTER FORM -->
    <form method="GET" class="grid gap-4 rounded-3xl border border-slate-200/10 bg-slate-950/70 p-6 md:grid-cols-4">

        <label class="grid gap-2 text-sm text-slate-300">
            From
            <input type="date" name="from"
                value="{{ isset($from) ? $from->format('Y-m-d') : '' }}"
                class="rounded-xl border border-slate-700 bg-slate-900 px-4 py-3 text-white">
        </label>

        <label class="grid gap-2 text-sm text-slate-300">
            To
            <input type="date" name="to"
                value="{{ isset($to) ? $to->format('Y-m-d') : '' }}"
                class="rounded-xl border border-slate-700 bg-slate-900 px-4 py-3 text-white">
        </label>

        <label class="grid gap-2 text-sm text-slate-300">
            Ledger account
            <select name="account_id"
                class="rounded-xl border border-slate-700 bg-slate-900 px-4 py-3 text-white">
                <option value="">Select account</option>

                @if(isset($trialBalance))
                    @foreach($trialBalance as $account)
                        <option value="{{ $account->id }}"
                            {{ isset($ledgerAccount) && $ledgerAccount && $ledgerAccount->id == $account->id ? 'selected' : '' }}>
                            {{ $account->code }} - {{ $account->name }}
                        </option>
                    @endforeach
                @endif
            </select>
        </label>

        <div class="flex items-end">
            <button type="submit"
                class="w-full rounded-xl bg-cyan-400 px-4 py-3 font-semibold text-slate-950">
                Apply filters
            </button>
        </div>
    </form>

    <!-- SUMMARY CARDS -->
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">

        <div class="rounded-3xl border border-emerald-400/20 bg-emerald-400/10 p-5">
            <p class="text-sm text-emerald-200">Revenue</p>
            <p class="mt-2 text-3xl font-bold text-white">
                KSh {{ number_format($revenues ?? 0, 2) }}
            </p>
        </div>

        <div class="rounded-3xl border border-rose-400/20 bg-rose-400/10 p-5">
            <p class="text-sm text-rose-200">Expenses</p>
            <p class="mt-2 text-3xl font-bold text-white">
                KSh {{ number_format($expenses ?? 0, 2) }}
            </p>
        </div>

        <div class="rounded-3xl border border-cyan-400/20 bg-cyan-400/10 p-5">
            <p class="text-sm text-cyan-200">Profit / Loss</p>
            <p class="mt-2 text-3xl font-bold text-white">
                KSh {{ number_format($profit ?? 0, 2) }}
            </p>
        </div>

        <div class="rounded-3xl border border-amber-400/20 bg-amber-400/10 p-5">
            <p class="text-sm text-amber-200">Cash flow</p>
            <p class="mt-2 text-3xl font-bold text-white">
                KSh {{ number_format($cashFlow ?? 0, 2) }}
            </p>
        </div>
    </div>

    <!-- BALANCE SHEET + EXPORTS -->
    <div class="grid gap-6 lg:grid-cols-2">

        <!-- BALANCE SHEET -->
        <div class="rounded-3xl border border-slate-200/10 bg-slate-950/70 p-6">
            <h2 class="text-xl font-semibold text-white">Balance sheet</h2>

            <div class="mt-4 space-y-3 text-sm text-slate-300">
                <div class="flex justify-between border-b border-slate-800 py-2">
                    <span>Assets</span>
                    <span>KSh {{ number_format($assets ?? 0, 2) }}</span>
                </div>

                <div class="flex justify-between border-b border-slate-800 py-2">
                    <span>Liabilities</span>
                    <span>KSh {{ number_format($liabilities ?? 0, 2) }}</span>
                </div>

                <div class="flex justify-between border-b border-slate-800 py-2">
                    <span>Equity</span>
                    <span>KSh {{ number_format($equity ?? 0, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- EXPORTS -->
        <div class="rounded-3xl border border-slate-200/10 bg-slate-950/70 p-6">
            <h2 class="text-xl font-semibold text-white">Quick exports</h2>

            <div class="mt-4 grid gap-3">

                <a href="{{ route('accounting.reports.export', ['type'=>'profit-loss']) }}"
                    class="rounded-xl border border-slate-700 px-4 py-3 text-white hover:bg-slate-800">
                    Profit & Loss CSV
                </a>

                <a href="{{ route('accounting.reports.export', ['type'=>'cash-flow']) }}"
                    class="rounded-xl border border-slate-700 px-4 py-3 text-white hover:bg-slate-800">
                    Cash Flow CSV
                </a>

                @if(isset($ledgerAccount))
                    <a href="{{ route('accounting.reports.export', ['type'=>'ledger','account_id'=>$ledgerAccount->id]) }}"
                        class="rounded-xl border border-slate-700 px-4 py-3 text-white hover:bg-slate-800">
                        Ledger CSV
                    </a>
                @endif

            </div>
        </div>
    </div>

    <!-- LEDGER TABLE -->
    @if(isset($ledgerAccount) && isset($ledger))

        <div class="rounded-3xl border border-slate-200/10 bg-slate-950/70 p-6">

            <h2 class="text-xl font-semibold text-white">
                General ledger: {{ $ledgerAccount->code }} - {{ $ledgerAccount->name }}
            </h2>

            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full text-sm text-left">
                    <thead class="text-slate-400">
                        <tr>
                            <th class="py-3 pr-4">Date</th>
                            <th class="py-3 pr-4">Journal</th>
                            <th class="py-3 pr-4">Description</th>
                            <th class="py-3 pr-4">Debit</th>
                            <th class="py-3 pr-4">Credit</th>
                            <th class="py-3 pr-4">Balance</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-800 text-slate-200">

                        @php
                            $running = 0;
                        @endphp

                        @foreach($ledger as $line)

                            @php
                                if ($ledgerAccount->normal_balance == 'credit') {
                                    $running += ($line->credit - $line->debit);
                                } else {
                                    $running += ($line->debit - $line->credit);
                                }
                            @endphp

                            <tr>
                                <td class="py-3 pr-4">
                                    {{ isset($line->journal->entry_date) ? $line->journal->entry_date->format('Y-m-d') : '' }}
                                </td>

                                <td class="py-3 pr-4">
                                    {{ $line->journal->journal_number ?? '' }}
                                </td>

                                <td class="py-3 pr-4">
                                    {{ $line->description ?? ($line->journal->description ?? '-') }}
                                </td>

                                <td class="py-3 pr-4">
                                    KSh {{ number_format($line->debit ?? 0, 2) }}
                                </td>

                                <td class="py-3 pr-4">
                                    KSh {{ number_format($line->credit ?? 0, 2) }}
                                </td>

                                <td class="py-3 pr-4">
                                    KSh {{ number_format($running, 2) }}
                                </td>
                            </tr>

                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>

    @endif

    <!-- TRIAL BALANCE -->
    <div class="rounded-3xl border border-slate-200/10 bg-slate-950/70 p-6">
        <h2 class="text-xl font-semibold text-white">Trial balance snapshot</h2>

        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full text-sm text-left">

                <thead class="text-slate-400">
                    <tr>
                        <th class="py-3 pr-4">Code</th>
                        <th class="py-3 pr-4">Account</th>
                        <th class="py-3 pr-4">Type</th>
                        <th class="py-3 pr-4">Balance</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-800 text-slate-200">

                    @if(isset($trialBalance))
                        @foreach($trialBalance as $account)
                            <tr>
                                <td class="py-3 pr-4">{{ $account->code }}</td>
                                <td class="py-3 pr-4">{{ $account->name }}</td>
                                <td class="py-3 pr-4">{{ ucfirst($account->type) }}</td>
                                <td class="py-3 pr-4">
                                    KSh {{ number_format($account->balance ?? 0, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    @endif

                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection