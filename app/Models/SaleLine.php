<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleLine extends Model
{
    protected $table = 'sale_lines';
    protected $primaryKey = 'id';

    protected $fillable = [
        'sale_id', 'product_id', 'variant_id', 'discount_type', 'discount_value', 'discount_amount', 'unit_price', 'sub_total'
    ];
    
    protected $casts = [
        'discount_amount' => 'float',
        'unit_price' => 'float',
        'tax_amount' => 'float',
        'sub_total' => 'float',
    ];

    
    public function sale(){
        return $this->belongsTo(Sale::class, 'sale_id');
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
