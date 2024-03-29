<?php

namespace App\Models;

use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;
use Rennokki\QueryCache\Traits\QueryCacheable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasSlug, SoftDeletes;
    
    // Cache Duration
    public $cacheFor = 3600;

    protected $table = 'product';


    protected $fillable = [
        'name', 'slug', 'category_id', 'has_variasi', 'var1_nama', 'var2_nama', 'var1_value', 'var2_value', 'harga',
        'berat',
    ];

    protected $appends = [
        'main_image', 'sell_price', 'sell_max_price', 'purchase_price'
    ];

    
    public $sortable = ['id', 'nama', 'created_at', 'updated_at'];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function variant()
    {
        return $this->hasMany(ProductVariant::class, 'product_id');
    }

    public function stock()
    {
        return $this->hasMany(ProductStock::class, 'product_id');
    }
    
    public function sale()
    {
        return $this->hasOne(SaleLine::class, 'product_id');
    }

    public function purchase()
    {
        return $this->hasOne(Purchase::class, 'product_id');
    }

    public function getPurchasePriceAttribute()
    {
        $variant = $this->hasMany(ProductVariant::class, 'product_id');
        if($variant->count() > 1)
        {
            return (int)$variant->min('purchase_price');

        }else{
            return (int)$variant->first()->purchase_price;
        }
    }

    public function getSellPriceAttribute()
    {
        $variant = $this->hasMany(ProductVariant::class, 'product_id');
        if($variant->count() > 1)
        {
            return (int)$variant->min('sell_price');

        }else{
            return (int)$variant->first()->sell_price;
        }
    }

    public function getSellMaxPriceAttribute()
    {
        $variant = $this->hasMany(ProductVariant::class, 'product_id');
        if($variant->count() > 1)
        {
            return (int)$variant->max('sell_price');
        }else{
            return (int)$variant->first()->sell_price;
        }
    }

    public function images() {
        return $this->hasMany(ProductImage::class, 'product_id', 'id');
    }

    public function reviews() {
        return $this->hasMany(ProductReview::class, 'product_id', 'id');
    }

    public function getMainImageAttribute() {
        $get = $this->hasMany(ProductImage::class, 'product_id', 'id')->where('is_utama', 1)->first();
        if($get && file_exists( public_path() . '/' . $get->path) && $get->path)
        {
            return asset($get->path);
        }else{
            return asset('media/placeholder/product.png');
        }
    }

    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }
    

}
