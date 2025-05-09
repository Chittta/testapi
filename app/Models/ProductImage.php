<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class ProductImage extends Model
{
    
    protected $fillable = ['ProductId', 'ImagePath'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'ProductId', 'ProductId');
    }
}
