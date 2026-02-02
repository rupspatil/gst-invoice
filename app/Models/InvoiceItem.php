<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $fillable = ['invoice_id','description','hsn','qty','unit_price','tax_percent','tax_amount', 'cgst', 'sgst', 'igst','line_subtotal','line_total'];
    public function invoice(){ return $this->belongsTo(Invoice::class); }
    public function item()
{
    return $this->belongsTo(Item::class);
}
}
