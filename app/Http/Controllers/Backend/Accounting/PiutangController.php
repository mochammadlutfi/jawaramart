<?php

namespace App\Http\Controllers\Backend\Accounting;

use App\Models\SaleLine;
use App\Models\Sale;
use App\Models\User;

use App\Models\Accounting\Payment;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Storage;

use App\Exports\PiutangExcel;
use Maatwebsite\Excel\Facades\Excel;

use App\Helpers\Collection;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class PiutangController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function index(Request $request)
    {
        $today = Carbon::now()->format('m');

        $query = Sale::select(DB::raw('SUM(grand_total) as grand_total, customer_id, status, payment_status, max(date) as date'))
        ->with([
            'payment' => function($q){
                $q->where('payment_method_id', 2)
                ->where('validated_at', Null);
            },
            'customer:id,name,avatar'
        ])
        ->groupBy('customer_id')
        ->where('payment_status', 'pending')
        ->get();

        $data = (new Collection($query))->paginate(10);


        return Inertia::render('Backend/Accounting/Piutang/Index',[
            'dataList' => $data
        ]);
    }


    
    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function show($id, Request $request)
    {
        $today = Carbon::now()->format('m');
        $customer = User::where('id', $id)->first();
        
        $data = Sale::whereHas(
            'payment', function($q){
                return $q->where('payment_method_id', 2);
            },
        )
        ->where('customer_id', $id)
        ->where('payment_status', 'pending')
        ->get();
        

        return Inertia::render('Backend/Accounting/Piutang/Show',[
            'customer' => $customer,
            'data' => $data
        ]);
    }

    
    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function export(Request $request)
    {
        $today = Carbon::now()->format('F');

        return Excel::download(new PiutangExcel, 'Piutang Customer '. $today.'.xlsx');
    }

    
    public function exportPDF(Request $request)
    {
        $today = Carbon::now()->format('m');

        $anggota = Anggota::where('tipe', 1)->latest()->get();
        $items = [];
        foreach($anggota as $ang){
            $transaksi = Transaksi::whereHas(
                'payment', function($q){
                    return $q->where('payment_method_id', 2)
                    ->where('status', 'unpaid');
                },
            )
            ->where('anggota_id', $ang->anggota_id)
            ->whereMonth('tgl', $today)->get();
            
            if($transaksi->count() > 0){
                $items[] = [
                    'anggota_id' => $ang->anggota_id,
                    'nama' => $ang->nama,
                    'nip' => $ang->nip,
                    'golongan' => $ang->golongan,
                    'jumlah' => $transaksi->sum('total'),
                    'list' => $transaksi
                ];
            }
        }
        
        $pdf = PDF::loadView('exports/pdf/piutangMany', compact([
            'items'
        ]));
        return $pdf->stream("Slip Tagihan Anggota.pdf");
    }
    
    public function printPdf($anggota_id, Request $request)
    {
        $today = Carbon::now()->format('m');
        $anggota = Anggota::where('anggota_id', $anggota_id)->first();
        
        $transaksi = Transaksi::whereHas(
            'payment', function($q){
                return $q->where('payment_method_id', 2);
            },
        )
        ->where('anggota_id', $anggota_id)
        ->whereMonth('tgl', $today)->get();

        $pdf = PDF::loadView('exports/pdf/piutang', compact([
            'anggota',
            'transaksi'
        ]));
        return $pdf->stream("Slip Tagihan". "-". $anggota->anggota_id . $anggota->nama .".pdf");
    }


    public function confirmPayment(Request $request){
        $rules = [
            "ids.*" => 'required',
        ];

        $pesan = [
            'ids.*.required' => 'Customer not selected!',
        ];

        $validator = Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            return back()->withErrors($validator->errors());
        }else{
            DB::beginTransaction();
            $today = Carbon::now()->format('m');

            try{
                foreach($request->ids as $id){
                    $transaksi = Sale::whereHas(
                        'payment', function($q){
                            return $q->where('payment_method_id', 2);
                        },
                    )
                    ->where('customer_id', $id)
                    ->where('payment_status', 'pending')->get();

                    foreach($transaksi as $t){
                        $sale = Sale::where('id', $t->id)->first();
                        $sale->payment_status = 'paid';
                        $sale->save();

                        $payment = Payment::where('paymenttable_type', 'App\Models\Sale')->where('paymenttable_id', $t->id)->first();
                        $payment->validated_at = Carbon::now();
                        $payment->validated_by = auth()->guard('admin')->user()->id;
                        $payment->save();
                    }
                }
            }catch(\QueryException $e){
                DB::rollback();
                return back();
            }

            DB::commit();
            return redirect()->route('admin.accounting.piutang.index');
        }

    }


    
    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        $data = ProductBrand::find($id);
        $cek = Storage::disk('public')->exists($data->thumbnail);
        if($cek)
        {
            Storage::disk('public')->delete($data->thumbnail);
        }
        $hapus_db = ProductBrand::destroy($data->id);
        if($hapus_db)
        {
            return redirect()->route('admin.product.brand.index');
        }
    }
}