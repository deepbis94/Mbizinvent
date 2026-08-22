<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceProduct extends Model
{
    public $timestamps = true;
    const UPDATED_AT = null;

    protected $table = 'invoice_products';
    protected $fillable = [
        'invoice_id',
        'product_id',
        'quantity',
        'mrp',
        'rate',
        'discount',
        'discount_percentage',
        'discount_amt',
        'tax_rate',
        'total',
        'profit',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
}
