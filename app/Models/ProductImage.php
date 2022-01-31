<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $table = 'product_images';
    protected $primaryKey = 'id';
    protected $fillable = [
        'product_id', 'path', 'is_utama'
    ];

    protected $appends = [
        'image_url',
    ];

    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'product_id');
    }

    public function getImageUrlAttribute($value)
    {
        if($this->attributes['path']){
            return asset($this->attributes['path']);
        }else{
            return asset('img/placeholder/product.png');
        }
    }

}
