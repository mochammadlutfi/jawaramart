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
        'opened_amount' => 'float',
        'closed_amount' => 'float',
    ];

    protected $appends = [
        'revenue',
    ];

    public function staff(){
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function getRevenueAttribute()
    {
        if($this->closed_amount){
            return $this->closed_amount - $this->opened_amount;
        }else{
            return 0;
        }
    }

}
