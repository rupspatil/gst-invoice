<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VariantValue extends Model
{
    protected $table = 'variant_value';

    protected $fillable = [
        'option_value_id',
        'variant_id',
    ];

   public function optionValue()
    {
        return $this->belongsTo(ProductOptionValue::class, 'option_value_id');
    }

    public function variant()
    {
        return $this->belongsTo(Variant::class);
    }
}
