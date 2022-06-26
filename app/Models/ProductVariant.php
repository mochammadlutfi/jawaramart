<?php

namespace App\Models;

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
        'variant', 'sku', 'purchase_price', 'sell_price', 'product_id',
    ];

    
    protected $appends = [
        'current_stock',
    ];

    public function product()
    {
        return $this->hasMany(Product::class, 'product_id');
    }

    
    public function stock()
    {
        return $this->hasOne(ProductStock::class, 'variant_id');
    }
    
    public function sale()
    {
        return $this->hasOne(SaleLine::class, 'variant_id');
    }

    // public function setHargaAttribute($value)
    // {
    //     return "Rp" .number_format($value,0,',','.');
    // }

    public function getCurrentStockAttribute(){
        return (int)$this->stock()->sum('stock');
    }

}
