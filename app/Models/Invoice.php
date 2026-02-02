<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{

    protected $fillable = [
        'invoice_number','customer_id','invoice_date','due_date', 
        'sub_total','discount','tax_total','grand_total','cgst','sgst','igst','notes','round_off'
    ];
    
//     protected $dates = [
//     'invoice_date' => 'datetime',
//      'due_date' => 'datetime',
// ];
protected $casts = [
        'invoice_date' => 'datetime',
        'due_date' => 'datetime',
    ];

    public function customer(){ return $this->belongsTo(Customer::class); }
    public function items(){ return $this->hasMany(InvoiceItem::class); }
}
