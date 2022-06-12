<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseLine extends Model
{
    protected $table = 'purchase_lines';
    protected $primaryKey = 'id';

    protected $fillable = [
        'purchase_id', 'product_id', 'variant_id', 'discount_type', 'discount_value', 'discount_amount', 'unit_price', 'tax_id', 'tax_amount', 'net_price', 'sub_total'
    ];
    
    protected $casts = [
        'discount_amount' => 'float',
        'unit_price' => 'float',
        'net_price' => 'float',
        'tax_amount' => 'float',
        'sub_total' => 'float',
    ];

    
    public function purchase(){
        return $this->belongsTo(Sale::class, 'purchase_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }


    

}
