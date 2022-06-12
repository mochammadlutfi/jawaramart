<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payment';
    protected $primaryKey = 'id';

    protected $fillable = [
        'paymenttable_type', 'paymenttable_id', 'amount', 'payment_method_id', 'change', 'note', 'status',
    ];

    
    protected $casts = [
        'amount' => 'float',
        'change' => 'float',
        'amount_received' => 'float',
    ];

    
    public function payment_method(){
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function paymenttable()
    {
        return $this->morphTo();
    }

}
