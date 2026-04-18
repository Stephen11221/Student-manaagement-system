<?php

namespace App\Http\Controllers;

use App\Models\AccountingInvoice;
use App\Models\AccountingJournal;
use App\Models\AccountingPayment;
use App\Models\ChartAccount;
use App\Services\AccountingService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AccountingController extends Controller
{
    public function dashboard(Request $request, AccountingService $service)
    {
        [$from, $to] = $this->resolveDateRange($request);
        $metrics = $service->dashboardMetrics($from, $to);

        return view('accounting.dashboard', $metrics);
    }

    public function accounts(Request $request, AccountingService $service)
    {
        $service->seedDefaultAccounts();

        $accounts = ChartAccount::with('parent')
            ->orderBy('type')
            ->orderBy('code')
            ->get();

        $types = ['asset', 'liability', 'equity', 'revenue', 'expense'];
        $parentAccounts = ChartAccount::orderBy('code')->get();

        return view('accounting.accounts', compact('accounts', 'types', 'parentAccounts'));
    }

    public function storeAccount(Request $request, AccountingService $service)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:chart_accounts,code'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:asset,liability,equity,revenue,expense'],
            'normal_balance' => ['nullable', 'in:debit,credit'],
            'parent_id' => ['nullable', 'exists:chart_accounts,id'],
            'currency' => ['nullable', 'string', 'size:3'],
            'description' => ['nullable', 'string'],
        ]);

        $service->createAccount($validated);

        return back()->with('status', 'Account created successfully.');
    }

    public function updateAccount(Request $request, ChartAccount $account)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:chart_accounts,code,' . $account->id],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:asset,liability,equity,revenue,expense'],
            'normal_balance' => ['required', 'in:debit,credit'],
            'parent_id' => ['nullable', 'exists:chart_accounts,id'],
            'currency' => ['nullable', 'string', 'size:3'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($account->is_system) {
            return back()->withErrors(['code' => 'System accounts cannot be edited.']);
        }

        $account->update([
            ...$validated,
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('status', 'Account updated successfully.');
    }

    public function destroyAccount(ChartAccount $account)
    {
        if ($account->is_system || $account->journalLines()->exists()) {
            return back()->withErrors(['account' => 'This account cannot be deleted because it is in use.']);
        }

        $account->delete();

        return back()->with('status', 'Account deleted successfully.');
    }

    public function transactions(Request $request, AccountingService $service)
    {
        $journals = AccountingJournal::with('lines.account', 'poster')
            ->orderByDesc('entry_date')
            ->orderByDesc('id')
            ->paginate(15);

        $accounts = ChartAccount::orderBy('code')->get();
        $editingJournal = null;

        return view('accounting.transactions', compact('journals', 'accounts', 'editingJournal'));
    }

    public function storeJournal(Request $request, AccountingService $service)
    {
        $validated = $this->validateJournalRequest($request);
        $attachmentPath = $this->storeAttachment($request, 'attachment');

        $journal = $service->postJournal([
            'entry_date' => $validated['entry_date'],
            'reference' => $validated['reference'] ?? null,
            'description' => $validated['description'] ?? null,
            'attachment_path' => $attachmentPath,
        ], $validated['lines']);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Transaction posted successfully.', 'journal' => $journal], 201);
        }

        return redirect()->route('accounting.transactions.index')->with('status', 'Transaction posted successfully.');
    }

    public function updateJournal(Request $request, AccountingJournal $journal, AccountingService $service)
    {
        $validated = $this->validateJournalRequest($request);
        $attachmentPath = $this->storeAttachment($request, 'attachment');

        $updated = $service->updateJournal($journal, [
            'entry_date' => $validated['entry_date'],
            'reference' => $validated['reference'] ?? null,
            'description' => $validated['description'] ?? null,
            'attachment_path' => $attachmentPath ?: $journal->attachment_path,
        ], $validated['lines']);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Transaction updated successfully.', 'journal' => $updated]);
        }

        return redirect()->route('accounting.transactions.index')->with('status', 'Transaction updated successfully.');
    }

    public function destroyJournal(AccountingJournal $journal, AccountingService $service)
    {
        $service->deleteJournal($journal);

        return back()->with('status', 'Transaction deleted successfully.');
    }

    public function invoices(Request $request, AccountingService $service)
    {
        $invoices = AccountingInvoice::with(['items.account', 'payments', 'creator'])
            ->orderByDesc('issue_date')
            ->orderByDesc('id')
            ->paginate(15);

        $accounts = ChartAccount::orderBy('code')->get();

        return view('accounting.invoices', compact('invoices', 'accounts'));
    }

    public function storeInvoice(Request $request, AccountingService $service)
    {
        $validated = $this->validateInvoiceRequest($request);
        $attachmentPath = $this->storeAttachment($request, 'attachment');

        $invoice = $service->createInvoice([
            'direction' => $validated['direction'],
            'party_name' => $validated['party_name'],
            'party_email' => $validated['party_email'] ?? null,
            'party_phone' => $validated['party_phone'] ?? null,
            'issue_date' => $validated['issue_date'],
            'due_date' => $validated['due_date'] ?? null,
            'status' => $validated['status'] ?? 'sent',
            'notes' => $validated['notes'] ?? null,
            'tax_amount' => $validated['tax_amount'] ?? 0,
            'attachment_path' => $attachmentPath,
        ], $validated['items']);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Invoice created successfully.', 'invoice' => $invoice], 201);
        }

        return redirect()->route('accounting.invoices.index')->with('status', 'Invoice created successfully.');
    }

    public function updateInvoice(Request $request, AccountingInvoice $invoice, AccountingService $service)
    {
        $validated = $this->validateInvoiceRequest($request);
        $attachmentPath = $this->storeAttachment($request, 'attachment');

        $updated = $service->updateInvoice($invoice, [
            'direction' => $validated['direction'],
            'party_name' => $validated['party_name'],
            'party_email' => $validated['party_email'] ?? null,
            'party_phone' => $validated['party_phone'] ?? null,
            'issue_date' => $validated['issue_date'],
            'due_date' => $validated['due_date'] ?? null,
            'status' => $validated['status'] ?? $invoice->status,
            'notes' => $validated['notes'] ?? null,
            'tax_amount' => $validated['tax_amount'] ?? 0,
            'attachment_path' => $attachmentPath ?: $invoice->attachment_path,
        ], $validated['items']);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Invoice updated successfully.', 'invoice' => $updated]);
        }

        return redirect()->route('accounting.invoices.index')->with('status', 'Invoice updated successfully.');
    }

    public function recordPayment(Request $request, AccountingInvoice $invoice, AccountingService $service)
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'in:cash,bank,mobile_money,card,cheque,other'],
            'reference' => ['nullable', 'string', 'max:255'],
            'payer_name' => ['nullable', 'string', 'max:255'],
            'paid_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'account_id' => ['nullable', 'exists:chart_accounts,id'],
        ]);

        $attachmentPath = $this->storeAttachment($request, 'attachment');

        $payment = $service->recordPayment($invoice, [
            ...$validated,
            'paid_at' => $validated['paid_at'] ?? now(),
            'attachment_path' => $attachmentPath,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Payment recorded successfully.', 'payment' => $payment], 201);
        }

        return back()->with('status', 'Payment recorded successfully.');
    }

    public function reports(Request $request, AccountingService $service)
    {
        [$from, $to] = $this->resolveDateRange($request);
        $metrics = $service->dashboardMetrics($from, $to);
        $ledgerAccountId = $request->integer('account_id');
        $ledgerAccount = $ledgerAccountId ? ChartAccount::find($ledgerAccountId) : null;
        $ledger = $ledgerAccount ? $service->ledger($ledgerAccount, $from, $to) : collect();

        return view('accounting.reports', array_merge($metrics, [
            'ledgerAccount' => $ledgerAccount,
            'ledger' => $ledger,
        ]));
    }

    public function export(Request $request, string $type, AccountingService $service): StreamedResponse
    {
        [$from, $to] = $this->resolveDateRange($request);
        $metrics = $service->dashboardMetrics($from, $to);

        $filename = match ($type) {
            'balance-sheet' => 'balance-sheet',
            'cash-flow' => 'cash-flow',
            'ledger' => 'general-ledger',
            default => 'profit-loss',
        };

        $rows = match ($type) {
            'balance-sheet' => [
                ['Section', 'Amount'],
                ['Assets', $metrics['assets']],
                ['Liabilities', $metrics['liabilities']],
                ['Equity', $metrics['equity']],
            ],
            'cash-flow' => array_merge(
                [['Section', 'Amount']],
                [
                    ['Cash Inflow', $metrics['incomeCollected']],
                    ['Cash Outflow', $metrics['outflowPaid']],
                    ['Net Cash Flow', $metrics['cashFlow']],
                ]
            ),
            'ledger' => $this->ledgerRowsFromRequest($request, $service, $from, $to),
            default => array_merge(
                [['Section', 'Amount']],
                [
                    ['Revenue', $metrics['revenues']],
                    ['Expenses', $metrics['expenses']],
                    ['Profit', $metrics['profit']],
                ]
            ),
        };

        return response()->streamDownload(function () use ($rows) {
            $output = fopen('php://output', 'w');
            foreach ($rows as $row) {
                fputcsv($output, $row);
            }
            fclose($output);
        }, $filename . '.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function apiSummary(Request $request, AccountingService $service)
    {
        [$from, $to] = $this->resolveDateRange($request);

        return response()->json($service->dashboardMetrics($from, $to));
    }

    public function apiAccounts(AccountingService $service)
    {
        $service->seedDefaultAccounts();

        return response()->json([
            'data' => ChartAccount::with('parent')->orderBy('type')->orderBy('code')->get(),
        ]);
    }

    public function apiInvoices()
    {
        return response()->json([
            'data' => AccountingInvoice::with(['items.account', 'payments'])->latest('id')->paginate(20),
        ]);
    }

    public function apiLedger(Request $request, ChartAccount $account, AccountingService $service)
    {
        [$from, $to] = $this->resolveDateRange($request);

        return response()->json([
            'account' => $account,
            'lines' => $service->ledger($account, $from, $to),
        ]);
    }

    protected function validateJournalRequest(Request $request): array
    {
        $validated = $request->validate([
            'entry_date' => ['required', 'date'],
            'reference' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'lines' => ['required', 'array', 'min:2'],
            'lines.*.account_id' => ['required', 'exists:chart_accounts,id'],
            'lines.*.description' => ['nullable', 'string'],
            'lines.*.debit' => ['nullable', 'numeric', 'min:0'],
            'lines.*.credit' => ['nullable', 'numeric', 'min:0'],
        ]);

        $validated['lines'] = collect($validated['lines'])->map(function ($line) {
            return [
                'account_id' => (int) $line['account_id'],
                'description' => $line['description'] ?? null,
                'debit' => (float) ($line['debit'] ?? 0),
                'credit' => (float) ($line['credit'] ?? 0),
            ];
        })->all();

        $debit = collect($validated['lines'])->sum('debit');
        $credit = collect($validated['lines'])->sum('credit');

        if (round($debit, 2) !== round($credit, 2)) {
            abort(422, 'Debit and credit totals must balance.');
        }

        return $validated;
    }

    protected function validateInvoiceRequest(Request $request): array
    {
        $validated = $request->validate([
            'direction' => ['required', 'in:receivable,payable'],
            'party_name' => ['required', 'string', 'max:255'],
            'party_email' => ['nullable', 'email', 'max:255'],
            'party_phone' => ['nullable', 'string', 'max:255'],
            'issue_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:issue_date'],
            'status' => ['nullable', 'in:draft,sent,partial,paid,overdue,cancelled'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.account_id' => ['nullable', 'exists:chart_accounts,id'],
            'items.*.quantity' => ['nullable', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['nullable', 'numeric', 'min:0'],
            'items.*.tax_rate' => ['nullable', 'numeric', 'min:0'],
            'items.*.line_total' => ['nullable', 'numeric', 'min:0'],
        ]);

        $validated['items'] = collect($validated['items'])->map(function ($item) {
            $quantity = (float) ($item['quantity'] ?? 1);
            $unitPrice = (float) ($item['unit_price'] ?? 0);
            $lineTotal = isset($item['line_total']) ? (float) $item['line_total'] : round($quantity * $unitPrice, 2);

            return [
                'description' => $item['description'],
                'account_id' => isset($item['account_id']) ? (int) $item['account_id'] : null,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'tax_rate' => (float) ($item['tax_rate'] ?? 0),
                'line_total' => $lineTotal,
            ];
        })->all();

        return $validated;
    }

    protected function storeAttachment(Request $request, string $field): ?string
    {
        if (! $request->hasFile($field)) {
            return null;
        }

        return $request->file($field)->store('accounting', 'public');
    }

    protected function ledgerRowsFromRequest(Request $request, AccountingService $service, Carbon $from, Carbon $to): array
    {
        $accountId = $request->integer('account_id');

        if (! $accountId) {
            return [['Date', 'Account', 'Description', 'Debit', 'Credit', 'Balance']];
        }

        $account = ChartAccount::find($accountId);

        if (! $account) {
            return [['Date', 'Account', 'Description', 'Debit', 'Credit', 'Balance']];
        }

        $rows = [['Date', 'Account', 'Description', 'Debit', 'Credit', 'Balance']];
        $running = 0.0;

        foreach ($service->ledger($account, $from, $to) as $line) {
            $running += $account->normal_balance === 'credit'
                ? ((float) $line->credit - (float) $line->debit)
                : ((float) $line->debit - (float) $line->credit);

            $rows[] = [
                optional($line->journal->entry_date)->format('Y-m-d') ?? $line->journal->entry_date,
                $account->code . ' ' . $account->name,
                $line->description ?: $line->journal->description,
                number_format((float) $line->debit, 2, '.', ''),
                number_format((float) $line->credit, 2, '.', ''),
                number_format($running, 2, '.', ''),
            ];
        }

        return $rows;
    }

    protected function resolveDateRange(Request $request): array
    {
        $from = $request->filled('from') ? Carbon::parse($request->string('from')) : now()->startOfMonth();
        $to = $request->filled('to') ? Carbon::parse($request->string('to')) : now()->endOfMonth();

        if ($to->lt($from)) {
            [$from, $to] = [$to, $from];
        }

        return [$from, $to];
    }
}
