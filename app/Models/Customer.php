<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    public $timestamps = true;
    const UPDATED_AT = null;

    protected $fillable = ['customer_name', 'address', 'state', 'state_code', 'city', 'phone', 'gstin_number', 'pan_number'];

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'customer_id');
    }
}
