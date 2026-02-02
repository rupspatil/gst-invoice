<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    protected $table = 'collection';

    protected $fillable = [
        'shop_id','position','title','description','meta_title','meta_description',
        'handle','collection_type','condition_type','status','parent_id','level',
        'path','path_levels','is_subcategory','hierarchy_depth','display_order',
        'show_products_at_level'
    ];

   public function products()
    {
        return $this->belongsToMany(Product::class, 'product_collection');
    }

    public function parent()
    {
        return $this->belongsTo(Collection::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Collection::class, 'parent_id');
    }

}
