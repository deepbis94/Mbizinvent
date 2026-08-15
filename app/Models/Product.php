<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public $timestamps = true;
    const UPDATED_AT = null;

    protected $fillable = ['product_description', 'hsn_code', 'rate', 'gst_percentage'];

    public function stock()
    {
        return $this->hasOne(Inventory::class, 'product_id');
    }

    public function invoiceProducts()
    {
        return $this->hasMany(InvoiceProduct::class, 'product_id');
    }
}
