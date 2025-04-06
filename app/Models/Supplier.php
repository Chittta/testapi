<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $primaryKey = 'SupplierID';

    protected $fillable = [
        'SupplierName',
        'SupplierDescription',
        'SupplierEmailId',
        'SupplierPhone',
        'SupplierAddress'
    ];
}
