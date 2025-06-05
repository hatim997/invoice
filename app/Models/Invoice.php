<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    public function invoiceDetails()
    {
        return $this->hasMany(InvoiceDetail::class, 'invoice_id');
    }

    public function invoiceAgencyCategories()
    {
        return $this->hasMany(InvoiceAgencyCategory::class, 'invoice_id');
    }
}
