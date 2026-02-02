<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    protected $table = 'upload';

    protected $fillable = [
        'shop_id',
        'product_id',
        'collection_id',
        'user_id',
        'store_id',
        'favicon_id',
        'file_system',
        'object_key',
        'size',
        'discr',
        'position',
        'width',
        'height',
        'storeLogo_id',
        'themeImage_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }
}
