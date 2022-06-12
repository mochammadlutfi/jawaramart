<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $table = 'sales';
    protected $primaryKey = 'id';

    protected $fillable = [
        'customer_id', 'date', 'is_pos', 'discount_type', 'discount_value', 'discount_amount', 'payment_status', 'status', 'total', 'staff_id'
    ];

    
    protected $casts = [
        'total' => 'float',
        'discount_amount' => 'float',
        'tax_amount' => 'float',
        'grand_total' => 'float',
        'shipping_cost' => 'float',
    ];

    protected $appends = [
        'total_paid', 'payment_fee'
    ];

    
    public function customer(){
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function line()
    {
        return $this->hasMany(SaleLine::class);
    }

    public function payment()
    {
        return $this->morphMany(Accounting\Payment::class, 'paymenttable');
    }

    public function getPaymentFeeAttribute()
    {
        $fee = $this->payment()->sum('code');
        return $fee;
    }

    public function delivery()
    {
        return $this->belongsTo(UserAddress::class, 'delivery_id');
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
