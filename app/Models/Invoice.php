<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    public $timestamps = true;
    const UPDATED_AT = null;

    protected $table = 'invoices';
    protected $fillable = [
        'invoice_number',
        'invoice_date',
        'customer_id',
        'round_off',
        'total',
        'discount_percentage',
        'discount_amt',
        'csv',
    ];

    protected $casts = [
        'invoice_date' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function products()
    {
        return $this->hasMany(InvoiceProduct::class, 'invoice_id');
    }
}
