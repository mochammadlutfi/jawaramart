<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseReturn extends Model
{
    protected $table = 'purchase_return';
    protected $primaryKey = 'id';

    protected $fillable = [
        'purchase_id', 'date', 'discount_type', 'discount_value', 'discount_amount', 'payment_status', 'status', 'total', 'staff_id'
    ];

    
    protected $casts = [
        'total' => 'float',
        'discount_amount' => 'float',
        'discount_value' => 'float',
        'grand_total' => 'float',
    ];

    protected $appends = [
        'total_paid', 'payment_fee', 'to_pay'
    ];

    
    public function purchase(){
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }

    public function line()
    {
        return $this->hasMany(PurchaseReturnLine::class, 'return_id');
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

    public function staff(){
        return $this->belongsTo(Admin::class, 'staff_id');
    }

    public function getTotalPaidAttribute()
    {
        $data = $this->payment()->sum('amount');
        return (float)$data;
    }

    public function getToPayAttribute()
    {
        $payment = $this->payment()->sum('amount');
        $amount_due = $this->grand_total;

        return abs((float)$payment - (float)$amount_due);
    }


}
