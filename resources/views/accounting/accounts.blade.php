@extends('layouts.app-shell')

@section('title', 'Chart of Accounts | ' . config('app.name', 'School Portal'))

@section('content')

@php
    $sidebarRole = auth()->user()->role ?? 'accountant';
    $hideChat = true;
@endphp

    <div class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">

    <!-- HEADER -->
    <div class="rounded-3xl border border-slate-200/10 bg-slate-950/70 p-6">
        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-cyan-300">
            Chart of accounts
        </p>
        <h1 class="mt-2 text-3xl font-bold text-white">
            Manage assets, liabilities, equity, revenue, and expenses
        </h1>
        <p class="mt-2 text-slate-300">
            Create account categories, assign parent accounts, and maintain opening balances.
        </p>
    </div>

    <div class="grid gap-6 xl:grid-cols-3">

        <!-- CREATE ACCOUNT -->
        <form method="POST"
              action="{{ route('accounting.accounts.store') }}"
              class="rounded-3xl border border-slate-200/10 bg-slate-950/70 p-6 xl:col-span-1">

            @csrf

            <h2 class="text-xl font-semibold text-white">New account</h2>

            <div class="mt-4 grid gap-4">

                <label class="grid gap-2 text-sm text-slate-300">
                    Code
                    <input name="code" value="{{ old('code') }}"
                        class="rounded-xl border border-slate-700 bg-slate-900 px-4 py-3 text-white" required>
                </label>

                <label class="grid gap-2 text-sm text-slate-300">
                    Name
                    <input name="name" value="{{ old('name') }}"
                        class="rounded-xl border border-slate-700 bg-slate-900 px-4 py-3 text-white" required>
                </label>

                <label class="grid gap-2 text-sm text-slate-300">
                    Type
                    <select name="type"
                        class="rounded-xl border border-slate-700 bg-slate-900 px-4 py-3 text-white" required>

                        @foreach($types as $type)
                            <option value="{{ $type }}" class="bg-slate-900 text-white">
                                {{ ucfirst($type) }}
                            </option>
                        @endforeach

                    </select>
                </label>

                <label class="grid gap-2 text-sm text-slate-300">
                    Normal balance
                    <select name="normal_balance"
                        class="rounded-xl border border-slate-700 bg-slate-900 px-4 py-3 text-white">
                        <option value="debit" class="bg-slate-900 text-white">Debit</option>
                        <option value="credit" class="bg-slate-900 text-white">Credit</option>
                    </select>
                </label>

                <label class="grid gap-2 text-sm text-slate-300">
                    Parent account
                    <select name="parent_id"
                        class="rounded-xl border border-slate-700 bg-slate-900 px-4 py-3 text-white">

                        <option value="">None</option>

                        @foreach($parentAccounts as $parent)
                            <option value="{{ $parent->id }}" class="bg-slate-900 text-white">
                                {{ $parent->code }} - {{ $parent->name }}
                            </option>
                        @endforeach

                    </select>
                </label>

                <label class="grid gap-2 text-sm text-slate-300">
                    Currency
                    <input name="currency" value="KES"
                        class="rounded-xl border border-slate-700 bg-slate-900 px-4 py-3 text-white">
                </label>

                <label class="grid gap-2 text-sm text-slate-300">
                    Description
                    <textarea name="description" rows="3"
                        class="rounded-xl border border-slate-700 bg-slate-900 px-4 py-3 text-white">{{ old('description') }}</textarea>
                </label>

            </div>

            <button type="submit"
                class="mt-5 w-full rounded-xl bg-cyan-400 px-4 py-3 font-semibold text-slate-950">
                Save account
            </button>
        </form>

        <!-- ACCOUNTS TABLE -->
        <div class="rounded-3xl border border-slate-200/10 bg-slate-950/70 p-6 xl:col-span-2">

            <h2 class="text-xl font-semibold text-white">Accounts</h2>
            <p class="text-sm text-slate-400">
                System accounts are protected from deletion.
            </p>

            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full text-left text-sm">

                    <thead class="text-slate-400">
                        <tr>
                            <th class="py-3 pr-4">Code</th>
                            <th class="py-3 pr-4">Name</th>
                            <th class="py-3 pr-4">Type</th>
                            <th class="py-3 pr-4">Balance</th>
                            <th class="py-3 pr-4">Status</th>
                            <th class="py-3 pr-4">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-800 text-slate-200">

                        @foreach($accounts as $account)

                        <tr>
                            <td class="py-3 pr-4">{{ $account->code }}</td>
                            <td class="py-3 pr-4">{{ $account->name }}</td>
                            <td class="py-3 pr-4">{{ ucfirst($account->type) }}</td>
                            <td class="py-3 pr-4">
                                KSh {{ number_format($account->balance ?? 0, 2) }}
                            </td>

                            <td class="py-3 pr-4">
                                <span class="rounded-full px-3 py-1 text-xs font-semibold
                                    {{ $account->is_system ? 'bg-amber-400/10 text-amber-200'
                                    : ($account->is_active ? 'bg-emerald-400/10 text-emerald-200'
                                    : 'bg-slate-700 text-slate-200') }}">
                                    {{ $account->is_system ? 'System'
                                    : ($account->is_active ? 'Active' : 'Inactive') }}
                                </span>
                            </td>

                            <td class="py-3 pr-4">

                                <!-- EDIT -->
                                <details class="group">
                                    <summary class="cursor-pointer text-cyan-300">Edit</summary>

                                    <form method="POST"
                                          action="{{ route('accounting.accounts.update', $account) }}"
                                          class="mt-3 grid gap-3 rounded-2xl border border-slate-800 bg-slate-900 p-4">

                                        @csrf
                                        @method('PUT')

                                        <div class="grid gap-3 md:grid-cols-2">

                                            <input name="code" value="{{ $account->code }}"
                                                class="rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-white">

                                            <input name="name" value="{{ $account->name }}"
                                                class="rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-white">

                                            <select name="type"
                                                class="rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-white">

                                                @foreach($types as $type)
                                                    <option value="{{ $type }}"
                                                        {{ $account->type == $type ? 'selected' : '' }}>
                                                        {{ ucfirst($type) }}
                                                    </option>
                                                @endforeach

                                            </select>

                                            <select name="normal_balance"
                                                class="rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-black">
                                                <option value="debit"
                                                    {{ $account->normal_balance == 'debit' ? 'selected' : '' }}>
                                                    Debit
                                                </option>
                                                <option value="credit"
                                                    {{ $account->normal_balance == 'credit' ? 'selected' : '' }}>
                                                    Credit
                                                </option>
                                            </select>

                                        </div>

                                        <textarea name="description" rows="2"
                                            class="rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-white">{{ $account->description }}</textarea>

                                        <button type="submit"
                                            class="rounded-xl bg-cyan-400 px-4 py-2 font-semibold text-slate-950">
                                            Update
                                        </button>

                                    </form>
                                </details>

                                <!-- DELETE -->
                                @if(!$account->is_system)
                                <form method="POST"
                                      action="{{ route('accounting.accounts.destroy', $account) }}"
                                      class="mt-2">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="text-sm font-semibold text-rose-300"
                                        onclick="return confirm('Delete this account?')">
                                        Delete
                                    </button>
                                </form>
                                @endif

                            </td>
                        </tr>

                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

@endsection
