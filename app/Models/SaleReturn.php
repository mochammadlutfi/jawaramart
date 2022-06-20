<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleReturn extends Model
{
    protected $table = 'sale_return';
    protected $primaryKey = 'id';

    protected $fillable = [
        'sale_id', 'date', 'discount_type', 'discount_value', 'discount_amount', 'payment_status', 'status', 'total', 'staff_id'
    ];

    
    protected $casts = [
        'total' => 'float',
        'discount_amount' => 'float',
        'discount_value' => 'float',
        'grand_total' => 'float',
    ];

    protected $appends = [
        'total_paid', 'payment_fee'
    ];

    
    public function sale(){
        return $this->belongsTo(Sale::class, 'sale_id');
    }

    public function line()
    {
        return $this->hasMany(SaleReturnLine::class, 'return_id');
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
        $data = $this->payment()->sum('amount_received');
        return (float)$data;
    }

    public function getAmountDueAttribute()
    {
        $data = $this->payment()->sum('amount_received');
        return (float)$data;
    }


}
