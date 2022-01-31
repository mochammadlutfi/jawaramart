<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductBrand extends Model
{
    protected $table = 'product_brands';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name', 'image', 'state'
    ];

    protected $appends = [
        'image_url',
    ];

    public function product()
    {
        return $this->hasOne(Product::Class, 'brand_id');
    }

    public function getImageUrlAttribute($value)
    {
        if($this->attributes['image']){
            return asset($this->attributes['image']);
        }else{
            return asset('media/placeholder/product.png');
        }
    }

}
