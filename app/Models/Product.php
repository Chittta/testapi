<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $primaryKey = 'ProductId';

    protected $fillable = [
        'ProductName',
        'ProductDescription',
        'CategoryID',
        'SubcategoryID',
        'Currency',
        'UnitPrice',
        'SupplierID',
        'QuntityOnStock',
        'QuntityOnOrcer',
        'IsActive',
    ];
    public function category()
    {
        return $this->belongsTo(Category::class, 'CategoryID', 'CategoryID');
    }
    
    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class, 'SubcategoryID', 'SubcategoryID');
    }
    
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'SupplierID', 'SupplierID');
    }

    
}
