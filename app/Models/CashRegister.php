<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashRegister extends Model
{
    protected $table = 'cash_register';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'opened_at', 'closed_at', 'admin_id', 'opened_amount', 'closed_amount'
    ];
    
    protected $casts = [
        'unit_price' => 'float',
    ];

}
