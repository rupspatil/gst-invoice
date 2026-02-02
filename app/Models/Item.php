<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'sku',
        'name',
        'type',
        'hsn_sac',
        'unit',
        'price',
        'cost_price',
        'tax_percent',
        'is_inventory',
        'stock',
        'description',
        'meta'
    ];

    protected $casts = [
        'is_inventory' => 'boolean',
        'stock' => 'decimal:2',
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'tax_percent' => 'decimal:2',
        'meta' => 'array'
    ];

    public function scopeProducts($q)
    {
        return $q->where('type', 'product');
    }
    public function scopeServices($q)
    {
        return $q->where('type', 'service');
    }

    // If you want to link invoice items to catalog items
    public function invoiceItems()
    {
        return $this->hasMany(\App\Models\InvoiceItem::class, 'item_id');
    }
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
