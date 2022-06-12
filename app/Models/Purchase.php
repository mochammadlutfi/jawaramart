<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $table = 'purchases';
    protected $primaryKey = 'id';

    protected $fillable = [
        'supplier_id', 'status', 'ref', 'tax_id', 'discount_type', 'discount_value', 'total_without_discount', 'total',
    ];

    
    protected $casts = [
        'total' => 'float',
        'discount_amount' => 'float',
        'tax_amount' => 'float',
        'grand_total' => 'float',
    ];

    protected $appends = [
        'total_paid',
    ];


    public function supplier(){
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function line()
    {
        return $this->hasMany(PurchaseLine::class);
    }

    public function payment()
    {
        return $this->morphMany(Payment::class, 'paymenttable');
    }

    public function staff(){
        return $this->belongsTo(Admin::class, 'staff_id');
    }

    public function getTotalPaidAttribute()
    {
        $data = $this->payment()->sum('amount_received');
        return (float)$data;
    }

}
