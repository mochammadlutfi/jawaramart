<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $table = 'payment_methods';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
    ];

    
    public function payment(){
        return $this->hasOne(Payment::class, 'payment_method_id');
    }

}
