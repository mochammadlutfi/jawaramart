<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Model;

class PaymentProof extends Model
{
    protected $table = 'account_payment_proof';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'payment_id', 'path',
    ];

    // public function getTypeAttribute($value)
    // {
    //     return ucwords($value);
    // }

    protected $appends = [
        'url',
    ];

    public function getUrlAttribute($value)
    {
        if($this->attributes['path']){
            return asset($this->attributes['path']);
        }
        return false;
    }

}
