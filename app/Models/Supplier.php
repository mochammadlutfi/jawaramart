<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $table = 'supplier';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name', 'no_hp', 'no_phone', 'email', 'address', 'postal_code', 'teritory_id',
    ];

}
