<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductOptionValue extends Model
{
    protected $table = 'product_option_value';

    protected $fillable = [
        'option_id',
        'title',
    ];

   public function option()
    {
        return $this->belongsTo(ProductOption::class, 'option_id');
    }

    public function variantValues()
    {
        return $this->hasMany(VariantValue::class, 'option_value_id');
    }

}
