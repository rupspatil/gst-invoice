<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['name','company','email','phone','gstin','address','state','pincode','city'];
    public function invoices(){ return $this->hasMany(Invoice::class); }
}
