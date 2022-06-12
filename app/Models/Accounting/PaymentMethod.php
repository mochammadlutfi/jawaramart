<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $table = 'account_payment_methods';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'name', 'type', 'bank_id',
    ];

    protected $appends = ['image_url'];

    public function payment()
    {
        return $this->hasOne(Payment::class, 'payment_method_id',);
    }
    
    public function bank()
    {
        return $this->belongsTo(Bank::class, 'bank_id');
    }

    public function getTypeAttribute($value)
    {
        return ucwords($value);
    }

    public function getImageUrlAttribute($value)
    {
        if(file_exists( public_path() . '/' . $this->attributes['image']) && $this->attributes['image'] !== null){
            return asset($this->attributes['image']);
        }
        
        return asset('media/placeholder/product.png');
    }

}
