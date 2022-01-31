<?php

namespace Modules\Product\Entities;
use Illuminate\Database\Eloquent\Model;
use Rennokki\QueryCache\Traits\QueryCacheable;

class ProductVariant extends Model
{
    protected $table = 'product_variant';
    protected $primaryKey = 'id';

    protected $casts = [
        'purchase_price' => 'float',
        'sell_price' => 'float',
    ];

    protected $fillable = [
        'variant', 'sku', 'purchase_price', 'sell_price', 'stock', 'product_id',
    ];

    public function product()
    {
        return $this->hasMany(Product::class, 'product_id');
    }

    // public function setHargaAttribute($value)
    // {
    //     return "Rp" .number_format($value,0,',','.');
    // }

}