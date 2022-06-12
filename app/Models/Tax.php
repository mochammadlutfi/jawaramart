<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    protected $table = 'tax_rates';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name', 'image', 'state'
    ];

}
