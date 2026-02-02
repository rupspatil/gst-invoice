<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Variant extends Model
{
    protected $table = 'variant';

    protected $fillable = [
        'product_id',
        'shipping_package_id',
        'image_id',
        'price',
        'sale_price',
        'cost_per_item',
        'inventory_management',
        'inventory_policy',
        'inventory_status',
        'sku',
        'barcode',
        'weight',
        'weight_unit',
        'inventoryItem_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function values()
    {
        return $this->hasMany(VariantValue::class);
    }

    public function image()
    {
        return $this->belongsTo(Upload::class, 'image_id');
    }
}
