<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


 class Product extends Model
{
    protected $table = 'product';

    protected $fillable = [
        'shop_id', 'status', 'handle', 'title', 'body_html',
        'meta_title', 'meta_description', 'additional_info',
        'orders_count', 'min_price', 'max_price'
    ];

   public function options()
    {
        return $this->hasMany(ProductOption::class);
    }

    public function variants()
    {
        return $this->hasMany(Variant::class);
    }

    public function tags()
    {
        return $this->hasMany(ProductTag::class);
    }

    public function collections()
    {
        return $this->belongsToMany(Collection::class, 'product_collection');
    }

    public function uploads()
    {
        return $this->hasMany(Upload::class);
    }

 
}
