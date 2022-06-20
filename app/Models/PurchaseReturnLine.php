<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseReturnLine extends Model
{
    protected $table = 'purchase_return_lines';
    protected $primaryKey = 'id';

    protected $fillable = [
        'return_id', 'product_id', 'variant_id', 'qty', 'unit_price', 'sub_total'
    ];
    
    protected $casts = [
        'unit_price' => 'float',
        'sub_total' => 'float',
    ];

    
    public function return(){
        return $this->belongsTo(PurchaseReturn::class, 'return_id');
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
