<?php

namespace App\Helpers;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use DB;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Payment;
class GeneralHelp
{
    public static function generate_ref_sale()
    {
        $q = Sale::select(DB::raw('MAX(RIGHT(ref,5)) AS kd_max'));
        $urut = "";

        $code = 'INV/';
        $no = 1;
        date_default_timezone_set('Asia/Jakarta');

        if($q->count() > 0){
            foreach($q->get() as $k){
                return $code . date('ymd') .'/'.sprintf("%05s", abs(((int)$k->kd_max) + 1));
            }
        }else{
            return $code . date('ymd') .'/'. sprintf("%05s", $no);
        }
    }

    public static function generate_ref_purchase()
    {
        $q = Purchase::select(DB::raw('MAX(RIGHT(ref,5)) AS kd_max'));
        $urut = "";

        $code = 'PO/';
        $no = 1;
        date_default_timezone_set('Asia/Jakarta');

        if($q->count() > 0){
            foreach($q->get() as $k){
                return $code . date('ymd') .'/'.sprintf("%05s", abs(((int)$k->kd_max) + 1));
            }
        }else{
            return $code . date('ymd') .'/'. sprintf("%05s", $no);
        }
    }

    public static function generate_payment_code($method)
    {
        
        $date = Carbon::now()->format('Y-m-d');
        $q = Payment::select(DB::raw('MAX(code) AS kd_max'))->whereNotNull('code')
        ->where('payment_method_id', 3)->whereDate('created_at', $date);

        $no = 100;
        date_default_timezone_set('Asia/Jakarta');

        if($q->count() > 0){
            foreach($q->get() as $k){
                return abs(((int)$k->kd_max) + 1);
            }
        }else{
            return $no;
        }
    }
}