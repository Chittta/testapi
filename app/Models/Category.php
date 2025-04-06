<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $primaryKey = 'CategoryID';

    protected $fillable = [
        'CategoryName',
        'CategoryDescription',
    ];

    public function subcategories()
    {
        return $this->hasMany(Subcategory::class, 'CategoryID', 'CategoryID');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'CategoryID', 'CategoryID');
    }
}
