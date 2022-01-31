<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    protected $table = 'sliders';
    protected $primaryKey = 'id';

    protected $fillable = [
        'image', 'state', 'link',
    ];

    protected $appends = [
        'image_url',
    ];
    
    public function getImageUrlAttribute($value)
    {
        if($this->attributes['image']){
            return asset($this->attributes['image']);
        }else{
            return asset('img/placeholder/product.png');
        }
    }

}
