<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use Illuminate\Console\Command;

class MarkInvoicesOverdue extends Command
{
    protected $signature = 'invoices:mark-overdue';

    protected $description = 'Audit and report invoices that have passed their due date';

    public function handle(): int
    {
        $overdueQuery = Invoice::whereIn('status', [Invoice::STATUS_ISSUED, Invoice::STATUS_PARTIALLY_PAID])
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', now());

        $count = $overdueQuery->count();

        $this->info("Found {$count} overdue invoices.");

        return Command::SUCCESS;
    }
}
