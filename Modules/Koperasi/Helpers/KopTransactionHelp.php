<?php

use Carbon\Carbon;

/**
 * Generate ID Anggota
 * Rumus : Kode Cabang + Tahun + Bulan + No Urut
 * @return Renderable
 */




if(!function_exists('generate_transaksi_ref')){
    function generate_transaksi_ref($type)
    {
        if($type == 'simkop'){
            $sub_service = 'wajib';
            $service = 'simpanan';
            $code = 'SK/';
        }else if($type == 'simla'){
            $sub_service = 'sukarela';
            $service = 'simpanan';
            $code = 'SL/';
        }

        $today = Carbon::today();
        
        $q = Transaksi::select(DB::raw('MAX(RIGHT(nomor,5)) AS kd_max'))->whereDate('tgl', $today)->where('sub_service', $sub_service);
        $kd_cabang = 1;
        $no = 1;

        date_default_timezone_set('Asia/Jakarta');
        if($q->count() > 0){
            foreach($q->get() as $k){
                return $code. Carbon::now()->format('ymd') .'/'. sprintf("%05s", abs(((int)$k->kd_max) + 1));
            }
        }else{
            return $code. Carbon::now()->format('ymd').'/'. sprintf("%05s", 1);
        }
    }
}

if(!function_exists('get_simkop_nomor')){
    function get_simkop_nomor(){
        $q = Transaksi::select(DB::raw('MAX(RIGHT(nomor,5)) AS kd_max'));
        $kd_cabang = 1;
        $no = 1;
        date_default_timezone_set('Asia/Jakarta');
        if($q->count() > 0){
            foreach($q->get() as $k){
                return 'SK/'. Date::now()->format('ymd') .'/'. sprintf("%05s", abs(((int)$k->kd_max) + 1));
            }
        }else{
            return 'SK/'. Date::now()->format('ymd').'/'. sprintf("%05s", 1);
        }
    }
}