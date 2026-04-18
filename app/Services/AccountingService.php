<?php

namespace App\Services;

use App\Models\AccountingInvoice;
use App\Models\AccountingInvoiceItem;
use App\Models\AccountingJournal;
use App\Models\AccountingJournalLine;
use App\Models\AccountingPayment;
use App\Models\ChartAccount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class AccountingService
{
    private const DEFAULT_ACCOUNTS = [
        ['code' => '1000', 'name' => 'Cash on Hand', 'type' => 'asset', 'normal_balance' => 'debit'],
        ['code' => '1010', 'name' => 'Bank Account', 'type' => 'asset', 'normal_balance' => 'debit'],
        ['code' => '1020', 'name' => 'Mobile Money Clearing', 'type' => 'asset', 'normal_balance' => 'debit'],
        ['code' => '1200', 'name' => 'Accounts Receivable', 'type' => 'asset', 'normal_balance' => 'debit'],
        ['code' => '1210', 'name' => 'VAT Recoverable', 'type' => 'asset', 'normal_balance' => 'debit'],
        ['code' => '2000', 'name' => 'Accounts Payable', 'type' => 'liability', 'normal_balance' => 'credit'],
        ['code' => '2100', 'name' => 'VAT Payable', 'type' => 'liability', 'normal_balance' => 'credit'],
        ['code' => '3000', 'name' => 'Owner Equity', 'type' => 'equity', 'normal_balance' => 'credit'],
        ['code' => '4000', 'name' => 'Sales Revenue', 'type' => 'revenue', 'normal_balance' => 'credit'],
        ['code' => '4100', 'name' => 'Service Revenue', 'type' => 'revenue', 'normal_balance' => 'credit'],
        ['code' => '5000', 'name' => 'Operating Expenses', 'type' => 'expense', 'normal_balance' => 'debit'],
        ['code' => '5100', 'name' => 'Payroll Expense', 'type' => 'expense', 'normal_balance' => 'debit'],
    ];

    public function seedDefaultAccounts(): void
    {
        foreach (self::DEFAULT_ACCOUNTS as $defaults) {
            ChartAccount::firstOrCreate(
                ['code' => $defaults['code']],
                $defaults + ['currency' => 'KES', 'is_active' => true, 'is_system' => true]
            );
        }
    }

    public function createAccount(array $data): ChartAccount
    {
        $this->seedDefaultAccounts();

        return ChartAccount::create([
            'code' => $data['code'],
            'name' => $data['name'],
            'type' => $data['type'],
            'normal_balance' => $data['normal_balance'] ?? ($data['type'] === 'asset' || $data['type'] === 'expense' ? 'debit' : 'credit'),
            'parent_id' => $data['parent_id'] ?? null,
            'currency' => $data['currency'] ?? 'KES',
            'description' => $data['description'] ?? null,
            'is_active' => $data['is_active'] ?? true,
            'is_system' => $data['is_system'] ?? false,
            'balance' => $data['balance'] ?? 0,
        ]);
    }

    public function postJournal(array $data, array $lines, ?Model $source = null): AccountingJournal
    {
        $this->seedDefaultAccounts();
        $totalDebit = collect($lines)->sum(fn ($line) => (float) ($line['debit'] ?? 0));
        $totalCredit = collect($lines)->sum(fn ($line) => (float) ($line['credit'] ?? 0));

        if (round($totalDebit, 2) !== round($totalCredit, 2)) {
            throw new RuntimeException('Journal is not balanced.');
        }

        return DB::transaction(function () use ($data, $lines, $source, $totalDebit, $totalCredit) {
            $journal = AccountingJournal::create([
                'journal_number' => $data['journal_number'] ?? $this->generateJournalNumber(),
                'entry_date' => $data['entry_date'] ?? now()->toDateString(),
                'reference' => $data['reference'] ?? null,
                'source_type' => $source ? $source::class : ($data['source_type'] ?? null),
                'source_id' => $source?->getKey() ?? ($data['source_id'] ?? null),
                'description' => $data['description'] ?? null,
                'status' => $data['status'] ?? 'posted',
                'posted_by' => $data['posted_by'] ?? auth()->id(),
                'posted_at' => $data['posted_at'] ?? now(),
                'attachment_path' => $data['attachment_path'] ?? null,
            ]);

            foreach ($lines as $line) {
                $account = ChartAccount::findOrFail($line['account_id']);
                $debit = (float) ($line['debit'] ?? 0);
                $credit = (float) ($line['credit'] ?? 0);

                AccountingJournalLine::create([
                    'journal_id' => $journal->id,
                    'account_id' => $account->id,
                    'description' => $line['description'] ?? null,
                    'debit' => $debit,
                    'credit' => $credit,
                ]);

                $this->applyBalance($account, $debit, $credit);
            }

            return $journal->load(['lines.account', 'poster']);
        });
    }

    public function updateJournal(AccountingJournal $journal, array $data, array $lines): AccountingJournal
    {
        $this->validateJournalBalance($lines);

        return DB::transaction(function () use ($journal, $data, $lines) {
            $journal->load('lines.account');
            $this->reverseJournal($journal);

            $journal->lines()->delete();
            $journal->update([
                'entry_date' => $data['entry_date'] ?? $journal->entry_date,
                'reference' => $data['reference'] ?? $journal->reference,
                'description' => $data['description'] ?? $journal->description,
                'status' => $data['status'] ?? $journal->status,
                'attachment_path' => $data['attachment_path'] ?? $journal->attachment_path,
            ]);
            $this->storeJournalLines($journal, $lines);
            $journal->load(['lines.account', 'poster']);

            return $journal;
        });
    }

    public function deleteJournal(AccountingJournal $journal): void
    {
        DB::transaction(function () use ($journal) {
            $journal->load('lines.account');
            $this->reverseJournal($journal);
            $journal->delete();
        });
    }

    public function createInvoice(array $data, array $items, ?Model $source = null): AccountingInvoice
    {
        $this->seedDefaultAccounts();
        $subtotal = collect($items)->sum(fn ($item) => (float) ($item['line_total'] ?? ((float) ($item['quantity'] ?? 1) * (float) ($item['unit_price'] ?? 0))));
        $taxAmount = (float) ($data['tax_amount'] ?? collect($items)->sum(function ($item) {
            $lineTotal = (float) ($item['line_total'] ?? ((float) ($item['quantity'] ?? 1) * (float) ($item['unit_price'] ?? 0)));
            return $lineTotal * ((float) ($item['tax_rate'] ?? 0) / 100);
        }));
        $total = round($subtotal + $taxAmount, 2);
        $direction = $data['direction'] ?? 'receivable';

        return DB::transaction(function () use ($data, $items, $source, $subtotal, $taxAmount, $total, $direction) {
            $invoice = AccountingInvoice::create([
                'invoice_number' => $data['invoice_number'] ?? $this->generateInvoiceNumber(),
                'direction' => $direction,
                'party_name' => $data['party_name'],
                'party_email' => $data['party_email'] ?? null,
                'party_phone' => $data['party_phone'] ?? null,
                'issue_date' => $data['issue_date'] ?? now()->toDateString(),
                'due_date' => $data['due_date'] ?? null,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $total,
                'amount_paid' => 0,
                'balance_due' => $total,
                'status' => $data['status'] ?? 'sent',
                'notes' => $data['notes'] ?? null,
                'attachment_path' => $data['attachment_path'] ?? null,
                'created_by' => $data['created_by'] ?? auth()->id(),
            ]);

            foreach ($items as $item) {
                AccountingInvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'account_id' => $item['account_id'] ?? null,
                    'description' => $item['description'],
                    'quantity' => (float) ($item['quantity'] ?? 1),
                    'unit_price' => (float) ($item['unit_price'] ?? 0),
                    'tax_rate' => (float) ($item['tax_rate'] ?? 0),
                    'line_total' => (float) ($item['line_total'] ?? ((float) ($item['quantity'] ?? 1) * (float) ($item['unit_price'] ?? 0))),
                ]);
            }

            $this->postInvoiceJournal($invoice, $items, $source);

            return $invoice->load(['items.account', 'payments', 'creator']);
        });
    }

    public function updateInvoice(AccountingInvoice $invoice, array $data, array $items): AccountingInvoice
    {
        return DB::transaction(function () use ($invoice, $data, $items) {
            $invoice->load('payments');

            if ($invoice->payments()->exists()) {
                throw new RuntimeException('Invoice with payments cannot be edited safely.');
            }

            $existingJournal = AccountingJournal::where('source_type', AccountingInvoice::class)
                ->where('source_id', $invoice->id)
                ->first();

            if ($existingJournal) {
                $existingJournal->load('lines.account');
                $this->reverseJournal($existingJournal);
                $existingJournal->delete();
            }

            $invoice->items()->delete();

            $subtotal = collect($items)->sum(fn ($item) => (float) ($item['line_total'] ?? ((float) ($item['quantity'] ?? 1) * (float) ($item['unit_price'] ?? 0))));
            $taxAmount = (float) ($data['tax_amount'] ?? collect($items)->sum(function ($item) {
                $lineTotal = (float) ($item['line_total'] ?? ((float) ($item['quantity'] ?? 1) * (float) ($item['unit_price'] ?? 0)));
                return $lineTotal * ((float) ($item['tax_rate'] ?? 0) / 100);
            }));
            $total = round($subtotal + $taxAmount, 2);

            $invoice->update([
                'direction' => $data['direction'] ?? $invoice->direction,
                'party_name' => $data['party_name'] ?? $invoice->party_name,
                'party_email' => $data['party_email'] ?? $invoice->party_email,
                'party_phone' => $data['party_phone'] ?? $invoice->party_phone,
                'issue_date' => $data['issue_date'] ?? $invoice->issue_date,
                'due_date' => $data['due_date'] ?? $invoice->due_date,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $total,
                'amount_paid' => 0,
                'balance_due' => $total,
                'status' => $data['status'] ?? $invoice->status,
                'notes' => $data['notes'] ?? $invoice->notes,
                'attachment_path' => $data['attachment_path'] ?? $invoice->attachment_path,
            ]);

            foreach ($items as $item) {
                AccountingInvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'account_id' => $item['account_id'] ?? null,
                    'description' => $item['description'],
                    'quantity' => (float) ($item['quantity'] ?? 1),
                    'unit_price' => (float) ($item['unit_price'] ?? 0),
                    'tax_rate' => (float) ($item['tax_rate'] ?? 0),
                    'line_total' => (float) ($item['line_total'] ?? ((float) ($item['quantity'] ?? 1) * (float) ($item['unit_price'] ?? 0))),
                ]);
            }

            $invoice->refresh();

            $this->postInvoiceJournal($invoice, $items);

            return $invoice->load(['items.account', 'payments', 'creator']);
        });
    }

    public function recordPayment(AccountingInvoice $invoice, array $data, ?Model $source = null): AccountingPayment
    {
        $this->seedDefaultAccounts();

        return DB::transaction(function () use ($invoice, $data, $source) {
            $paymentAccount = $this->resolvePaymentAccount($data['payment_method'] ?? 'cash', $data['account_id'] ?? null);
            $amount = (float) $data['amount'];

            $payment = AccountingPayment::create([
                'invoice_id' => $invoice->id,
                'account_id' => $paymentAccount->id,
                'recorded_by' => $data['recorded_by'] ?? auth()->id(),
                'amount' => $amount,
                'payment_method' => $data['payment_method'] ?? 'cash',
                'reference' => $data['reference'] ?? null,
                'payer_name' => $data['payer_name'] ?? $invoice->party_name,
                'paid_at' => $data['paid_at'] ?? now(),
                'notes' => $data['notes'] ?? null,
                'attachment_path' => $data['attachment_path'] ?? null,
            ]);

            $arAccount = ChartAccount::where('code', '1200')->firstOrFail();
            $apAccount = ChartAccount::where('code', '2000')->firstOrFail();

            $lines = $invoice->direction === 'payable'
                ? [
                    ['account_id' => $apAccount->id, 'debit' => $amount, 'credit' => 0, 'description' => 'Reduce accounts payable'],
                    ['account_id' => $paymentAccount->id, 'debit' => 0, 'credit' => $amount, 'description' => 'Cash/bank outflow'],
                ]
                : [
                    ['account_id' => $paymentAccount->id, 'debit' => $amount, 'credit' => 0, 'description' => 'Cash/bank inflow'],
                    ['account_id' => $arAccount->id, 'debit' => 0, 'credit' => $amount, 'description' => 'Reduce accounts receivable'],
                ];

            $this->postJournal([
                'entry_date' => Carbon::parse($payment->paid_at)->toDateString(),
                'reference' => $payment->reference ?: 'PAY-' . $payment->id,
                'description' => 'Payment for invoice ' . $invoice->invoice_number,
                'source_type' => $source ? $source::class : AccountingPayment::class,
                'source_id' => $source?->getKey() ?? $payment->id,
                'posted_by' => $payment->recorded_by,
                'posted_at' => $payment->paid_at,
            ], $lines, $payment);

            $this->refreshInvoicePaymentStatus($invoice, $amount);

            return $payment->load(['invoice', 'account', 'recorder']);
        });
    }

    public function refreshInvoicePaymentStatus(AccountingInvoice $invoice, float $amount): void
    {
        $invoice->amount_paid = round((float) $invoice->payments()->sum('amount'), 2);
        $invoice->balance_due = round(max((float) $invoice->total_amount - (float) $invoice->amount_paid, 0), 2);

        $invoice->status = $invoice->balance_due <= 0
            ? 'paid'
            : ($invoice->amount_paid > 0 ? 'partial' : 'sent');

        $invoice->save();
    }

    public function dashboardMetrics(?Carbon $from = null, ?Carbon $to = null): array
    {
        $this->seedDefaultAccounts();

        $from = $from ?: now()->startOfMonth();
        $to = $to ?: now()->endOfMonth();

        $journals = AccountingJournal::with('lines.account')
            ->whereBetween('entry_date', [$from->toDateString(), $to->toDateString()])
            ->get();

        $invoices = AccountingInvoice::with('payments')
            ->whereBetween('issue_date', [$from->toDateString(), $to->toDateString()])
            ->get();

        $revenues = 0.0;
        $expenses = 0.0;
        $cashFlow = 0.0;

        foreach ($journals as $journal) {
            foreach ($journal->lines as $line) {
                $account = $line->account;
                if (! $account) {
                    continue;
                }

                $net = (float) $line->debit - (float) $line->credit;

                if (in_array($account->type, ['revenue'], true)) {
                    $revenues += max((float) $line->credit - (float) $line->debit, 0);
                }

                if (in_array($account->type, ['expense'], true)) {
                    $expenses += max((float) $line->debit - (float) $line->credit, 0);
                }

                if (in_array($account->code, ['1000', '1010', '1020'], true)) {
                    $cashFlow += $net;
                }
            }
        }

        $receivablesOutstanding = (float) $invoices->where('direction', 'receivable')->sum('balance_due');
        $payablesOutstanding = (float) $invoices->where('direction', 'payable')->sum('balance_due');
        $incomeCollected = (float) $invoices->where('direction', 'receivable')->sum('amount_paid');
        $outflowPaid = (float) $invoices->where('direction', 'payable')->sum('amount_paid');

        return [
            'from' => $from,
            'to' => $to,
            'revenues' => round($revenues, 2),
            'expenses' => round($expenses, 2),
            'profit' => round($revenues - $expenses, 2),
            'cashFlow' => round($cashFlow, 2),
            'receivablesOutstanding' => round($receivablesOutstanding, 2),
            'payablesOutstanding' => round($payablesOutstanding, 2),
            'incomeCollected' => round($incomeCollected, 2),
            'outflowPaid' => round($outflowPaid, 2),
            'assets' => round((float) ChartAccount::where('type', 'asset')->sum('balance'), 2),
            'liabilities' => round((float) ChartAccount::where('type', 'liability')->sum('balance'), 2),
            'equity' => round((float) ChartAccount::where('type', 'equity')->sum('balance'), 2),
            'revenueAccounts' => ChartAccount::where('type', 'revenue')->orderBy('code')->get(),
            'expenseAccounts' => ChartAccount::where('type', 'expense')->orderBy('code')->get(),
            'assetAccounts' => ChartAccount::where('type', 'asset')->orderBy('code')->get(),
            'liabilityAccounts' => ChartAccount::where('type', 'liability')->orderBy('code')->get(),
            'monthlySeries' => $this->monthlySeries($from, $to),
            'recentJournals' => AccountingJournal::with('lines.account')->latest('entry_date')->latest('id')->limit(10)->get(),
            'recentInvoices' => AccountingInvoice::with('payments')->latest('id')->limit(10)->get(),
            'recentPayments' => AccountingPayment::with(['invoice', 'account'])->latest('paid_at')->latest('id')->limit(10)->get(),
            'trialBalance' => ChartAccount::orderBy('code')->get(),
        ];
    }

    public function monthlySeries(Carbon $from, Carbon $to): Collection
    {
        $series = collect();
        $cursor = $from->copy()->startOfMonth();
        $end = $to->copy()->startOfMonth();

        while ($cursor->lte($end)) {
            $monthStart = $cursor->copy()->startOfMonth();
            $monthEnd = $cursor->copy()->endOfMonth();

            $monthlyJournals = AccountingJournal::with('lines.account')
                ->whereBetween('entry_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
                ->get();

            $income = 0.0;
            $expense = 0.0;
            $cash = 0.0;

            foreach ($monthlyJournals as $journal) {
                foreach ($journal->lines as $line) {
                    $account = $line->account;
                    if (! $account) {
                        continue;
                    }

                    if ($account->type === 'revenue') {
                        $income += max((float) $line->credit - (float) $line->debit, 0);
                    }

                    if ($account->type === 'expense') {
                        $expense += max((float) $line->debit - (float) $line->credit, 0);
                    }

                    if (in_array($account->code, ['1000', '1010', '1020'], true)) {
                        $cash += (float) $line->debit - (float) $line->credit;
                    }
                }
            }

            $series->push([
                'month' => $cursor->format('M Y'),
                'income' => round($income, 2),
                'expenses' => round($expense, 2),
                'cash' => round($cash, 2),
            ]);

            $cursor->addMonth();
        }

        return $series;
    }

    public function ledger(ChartAccount $account, ?Carbon $from = null, ?Carbon $to = null): Collection
    {
        $query = AccountingJournalLine::with(['journal.poster', 'account'])
            ->where('account_id', $account->id)
            ->orderByDesc('id');

        if ($from && $to) {
            $query->whereHas('journal', fn ($journalQuery) => $journalQuery->whereBetween('entry_date', [$from->toDateString(), $to->toDateString()]));
        }

        return $query->get();
    }

    public function invoiceSummary(): array
    {
        return [
            'receivable' => AccountingInvoice::where('direction', 'receivable')->count(),
            'payable' => AccountingInvoice::where('direction', 'payable')->count(),
            'open' => AccountingInvoice::whereIn('status', ['draft', 'sent', 'partial', 'overdue'])->count(),
            'paid' => AccountingInvoice::where('status', 'paid')->count(),
        ];
    }

    public function resolvePaymentAccount(string $method, ?int $accountId = null): ChartAccount
    {
        if ($accountId) {
            return ChartAccount::findOrFail($accountId);
        }

        return match ($method) {
            'bank' => ChartAccount::where('code', '1010')->firstOrFail(),
            'mobile_money' => ChartAccount::where('code', '1020')->firstOrFail(),
            default => ChartAccount::where('code', '1000')->firstOrFail(),
        };
    }

    protected function postInvoiceJournal(AccountingInvoice $invoice, array $items, ?Model $source = null): AccountingJournal
    {
        $arAccount = ChartAccount::where('code', '1200')->firstOrFail();
        $apAccount = ChartAccount::where('code', '2000')->firstOrFail();
        $vatPayable = ChartAccount::where('code', '2100')->firstOrFail();
        $vatRecoverable = ChartAccount::where('code', '1210')->firstOrFail();

        $total = round((float) $invoice->subtotal + (float) $invoice->tax_amount, 2);
        $lines = [];

        foreach ($items as $item) {
            $lineTotal = (float) ($item['line_total'] ?? ((float) ($item['quantity'] ?? 1) * (float) ($item['unit_price'] ?? 0)));
            $accountId = $item['account_id'] ?? null;

            if (! $accountId) {
                continue;
            }

            $lines[] = $invoice->direction === 'payable'
                ? ['account_id' => $accountId, 'debit' => $lineTotal, 'credit' => 0, 'description' => $item['description'] ?? null]
                : ['account_id' => $accountId, 'debit' => 0, 'credit' => $lineTotal, 'description' => $item['description'] ?? null];
        }

        if ((float) $invoice->tax_amount > 0) {
            $lines[] = $invoice->direction === 'payable'
                ? ['account_id' => $vatRecoverable->id, 'debit' => (float) $invoice->tax_amount, 'credit' => 0, 'description' => 'VAT recoverable']
                : ['account_id' => $vatPayable->id, 'debit' => 0, 'credit' => (float) $invoice->tax_amount, 'description' => 'VAT payable'];
        }

        $lines[] = $invoice->direction === 'payable'
            ? ['account_id' => $apAccount->id, 'debit' => 0, 'credit' => $total, 'description' => 'Accounts payable']
            : ['account_id' => $arAccount->id, 'debit' => $total, 'credit' => 0, 'description' => 'Accounts receivable'];

        return $this->postJournal([
            'entry_date' => $invoice->issue_date?->toDateString() ?? now()->toDateString(),
            'reference' => $invoice->invoice_number,
            'description' => 'Invoice ' . $invoice->invoice_number . ' for ' . $invoice->party_name,
            'source_type' => $source ? $source::class : AccountingInvoice::class,
            'source_id' => $source?->getKey() ?? $invoice->id,
            'posted_by' => $invoice->created_by,
        ], $lines, $invoice);
    }

    protected function validateJournalBalance(array $lines): void
    {
        $totalDebit = collect($lines)->sum(fn ($line) => (float) ($line['debit'] ?? 0));
        $totalCredit = collect($lines)->sum(fn ($line) => (float) ($line['credit'] ?? 0));

        if (round($totalDebit, 2) !== round($totalCredit, 2)) {
            throw new RuntimeException('Journal is not balanced.');
        }
    }

    protected function storeJournalLines(AccountingJournal $journal, array $lines): void
    {
        foreach ($lines as $line) {
            $account = ChartAccount::findOrFail($line['account_id']);
            $debit = (float) ($line['debit'] ?? 0);
            $credit = (float) ($line['credit'] ?? 0);

            AccountingJournalLine::create([
                'journal_id' => $journal->id,
                'account_id' => $account->id,
                'description' => $line['description'] ?? null,
                'debit' => $debit,
                'credit' => $credit,
            ]);

            $this->applyBalance($account, $debit, $credit);
        }
    }

    protected function applyBalance(ChartAccount $account, float $debit, float $credit): void
    {
        $delta = $account->normal_balance === 'credit'
            ? ($credit - $debit)
            : ($debit - $credit);

        $account->balance = round((float) $account->balance + $delta, 2);
        $account->save();
    }

    protected function reverseJournal(AccountingJournal $journal): void
    {
        foreach ($journal->lines as $line) {
            if ($line->account) {
                $this->applyBalance($line->account, (float) $line->credit, (float) $line->debit);
            }
        }
    }

    protected function generateInvoiceNumber(): string
    {
        return 'INV-' . now()->format('Ymd') . '-' . Str::upper(Str::random(5));
    }

    protected function generateJournalNumber(): string
    {
        return 'JRN-' . now()->format('Ymd') . '-' . Str::upper(Str::random(5));
    }
}
