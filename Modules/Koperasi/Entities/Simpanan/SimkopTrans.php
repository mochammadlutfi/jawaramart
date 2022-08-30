<?php

namespace Modules\Koperasi\Entities\Simpanan;


use Illuminate\Database\Eloquent\Model;

class SimkopTrans extends Model
{
    protected $table = 'kop_wajib_trans';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','no_transaksi', 'anggota_id', 'debit', 'credit', 'next_payment'
    ];

    
    protected $casts = [
        'debit' => 'float',
        'credit' => 'float',
    ];

    public function transaksi()
    {
        return $this->belongsTo(\Transaksi::class, 'no_transaksi');
    }

    public function anggota()
    {
        return $this->belongsTo(\Anggota::class, 'anggota_id', 'anggota_id');
    }
}
