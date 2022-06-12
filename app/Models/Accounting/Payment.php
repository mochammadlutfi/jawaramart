<?php

namespace App\Models\Accounting;

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

    public function transaksi()
    {
        return $this->belongsTo(\Transaksi::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class, 'bank_id');
    }

    public function payment_method(){
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function payment_proof(){
        return $this->hasOne(PaymentProof::class, 'payment_id');
    }

}
