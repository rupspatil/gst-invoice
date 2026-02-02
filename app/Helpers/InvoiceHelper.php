<?php 

namespace App\Helpers;

use App\Models\Invoice;
use Carbon\Carbon;

class InvoiceHelper {
    public static function generateNumber() {
        $year = Carbon::now()->format('Y');
        $prefix = "INV";

        $latest = Invoice::whereYear('created_at', $year)
            ->orderBy('id', 'DESC')
            ->first();

        $next = $latest ? intval(substr($latest->invoice_number, -4)) + 1 : 1;

        return $prefix . "_" . $year . "_" . str_pad($next, 4, "0", STR_PAD_LEFT);
    }
}