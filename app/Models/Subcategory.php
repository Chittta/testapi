<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    protected $primaryKey = 'SubcategoryID';

    protected $fillable = [
        'CategoryID',
        'SubcategoryName',
        'SubcategoryDescription'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'CategoryID', 'CategoryID');
    }


    public function products()
    {
        return $this->hasMany(Product::class, 'SubcategoryID', 'SubcategoryID');
    }
}
