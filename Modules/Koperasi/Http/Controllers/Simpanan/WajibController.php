<?php

namespace Modules\Koperasi\Http\Controllers\Simpanan;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use DB;
use Carbon\Carbon;
use App\Helpers\GeneralHelp;
use App\Helpers\SimpananHelp;
use App\Helpers\Collection;


use App\Models\Anggota;

use App\Models\Transaksi;
use App\Models\Accounting\Payment;
use App\Models\TransaksiLine;

use Modules\Koperasi\Entities\Simpanan\SimkopTrans;
use Modules\Koperasi\Entities\Simpanan\SimlaTransaksi;
use App\Models\Simpanan\Wallet;

class WajibController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $startDate = !empty($request->startDate) ? Carbon::parse($request->startDate)->format('Y-m-d') : Carbon::now()->startofmonth()->format('Y-m-d');
        $endDate = !empty($request->endDate) ? Carbon::parse($request->endDate)->format('Y-m-d') : Carbon::now()->format('Y-m-d');
        $sort = !empty($request->sort) ? $request->sort : 'date';
        $sortDir = !empty($request->sortDir) ? $request->sortDir : 'desc';
        $limit = ($request->limit) ? $request->limit : 25;

        $eloq = DB::table('kop_trans as a')
        ->select('a.id', 'a.name', 'a.date', 'a.total', 'a.anggota_id', 'b.nama as anggota_name', 'c.periode', 'a.payment_status')
        ->join('kop_anggota as b', 'b.anggota_id', '=', 'a.anggota_id')
        ->join('kop_wajib_trans as c', 'c.transaksi_id', '=', 'a.id')
        ->when($request->search, function($q, $search){
            return $q->where('a.name', 'LIKE', '%' . $search . '%')
            ->orWhere('b.name', 'LIKE', '%' . $search . '%')
            ->orWhere('a.anggota_id', 'LIKE', '%' . $search . '%')
            ->orWhere('a.date', 'LIKE', '%' . $search . '%');
        })
        ->where(function ($query) use ($startDate, $endDate) {
            $query->whereDate("a.date", ">=", $startDate)
                ->orWhereDate("a.date", "=", $endDate);
        });

        $data = $eloq->orderBy($sort, $sortDir)
        ->paginate($limit);

        $count = clone $eloq;
        $sum = clone $eloq;

        $statistic = [
            'count' => $count->count(),
            'sum' => $sum->sum('total'),
        ];

        return Inertia::render('Koperasi::Simpanan/Wajib/Index',[
            'dataList' => $data,
            'overview' => $statistic
        ]);
    }


        /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function create(Request $request)
    {
        
        return Inertia::render('Koperasi::Simpanan/Wajib/Form',[
            'name' => '12',
        ]);
    }


        /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function store(Request $request)
    {
        dd($request->all());
        $rules = [
            'anggota_id' => 'required',
            'tgl' => 'required',
            'periode' => 'required',
        ];

        $pesan = [
            'anggota_id.required' => 'Anggota Koperasi Wajib Diisi!',
            'kas_id.required' => 'Kas Koperasi Wajib Diisi!',
            'periode.required' => 'Periode Pembayaran Wajib Diisi!',
            'tgl.required' => 'Tanggal Setoran Wajib Diisi!',
        ];

        $validator = Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            return response()->json([
                'fail' => true,
                'errors' => $validator->errors()
            ]);
        }else{
            $pay_check = SimkopTransaksi::where('anggota_id', $request->anggota_id)
            ->whereMonth('periode', Carbon::createFromFormat('d F Y', '1 '.$request->periode)->format('m'))
            ->whereYear('periode', Carbon::createFromFormat('d F Y', '1 '.$request->periode)->format('Y'))
            ->first();

            if($pay_check)
            {
                return response()->json([
                    'fail' => true,
                    'errors' => $validator->errors()
                ]);
            }

            $item = collect([
                array(
                    'name' => 'Simpanan Wajib',
                    'jumlah' => 100000,
                    'akun' => 4,
                ),
            ]);

            DB::beginTransaction();
            Carbon::today()->format('H:i:s');
            try{
                $transaksi = new Transaksi();
                $transaksi->nomor = $request->kd_transaksi;
                $transaksi->anggota_id = $request->anggota_id;
                $transaksi->service = 'simpanan';
                $transaksi->sub_service = 'wajib';
                $transaksi->jenis = 'setoran wajib';
                $transaksi->total = (int)$request->wajib;
                $transaksi->tgl = Carbon::parse($request->tgl)->format('Y-m-d H:i:s');
                $transaksi->created_at = Carbon::parse($request->tgl)->format('Y-m-d H:i:s');
                $transaksi->teller_id  = auth()->guard('admin')->user()->id;
                $transaksi->save();
                
                $pay_wajib = new SimkopTransaksi();
                $pay_wajib->anggota_id  = $request->anggota_id;
                $pay_wajib->periode = Carbon::createFromFormat('d F Y', '1 '. $request->periode)->format('Y-m-d');
                $pay_wajib->jumlah = $request->wajib;
                $pay_wajib->created_at = Carbon::parse($request->tgl)->format('Y-m-d H:i:s');

                $transaksi->simkop()->save($pay_wajib);

                // $transaksi->payment()->create([
                //     'jumlah' => (int)$request->wajib,
                //     'status' => 'confirm',
                //     'payment_method_id' => $request->payment_method_id,
                //     'tgl_bayar' => Carbon::parse($request->tgl)->format('Y-m-d H:i:s')
                // ]);

                $payment = new Payment();
                $payment->payment_method_id = $request->payment_method_id;
                $payment->tgl_bayar = Carbon::parse($request->tgl)->format('Y-m-d H:i:s');
                $payment->jumlah = (int)$request->wajib;
                $payment->status = $request->payment_method_id == 1 ? 'paid' : 'unpaid';
                $transaksi->payment()->save($payment);

                foreach($item as $i){
                    $line = new TransaksiLine();
                    $line->nama = $i['name'];
                    $line->akun_id = $i['akun'];
                    $line->jumlah = $i['jumlah'];
                    $line->user_id = auth()->guard('admin')->user()->id;
                    $transaksi->line()->save($line);
                }

            }catch(\QueryException $e){
                DB::rollback();
                return response()->json([
                    'fail' => true,
                    'pesan' => 'Terjadi Error Pada Penyimpanan Data',
                    'error' => $e,
                ]);
            }
            DB::commit();
            return response()->json([
                'fail' => false,
                'invoice' => $transaksi->id,
            ]);
        }
    }
    
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function show($id, Request $request)
    {

        $data = DB::table('kop_trans as a')
        ->select('a.id', 'a.name', 'a.service', 'a.date', 'a.total', 'a.anggota_id', 'b.nama as anggota_name', 'c.periode', 'a.payment_status')
        ->join('kop_anggota as b', 'b.anggota_id', '=', 'a.anggota_id')
        ->join('kop_wajib_trans as c', 'c.transaksi_id', '=', 'a.id')
        ->where('a.id', $id)->first();

        $line = DB::table('kop_trans_line as a')
        ->where('trans_id', $data->id)->get();

        return Inertia::render('Koperasi::Simpanan/Wajib/Show', [
            'data' => $data,
            'line' => $line,
        ]);
    }

    public function edit($id, Request $request)
    {

        $data = Transaksi::with(['anggota', 'teller', 'line', 'simkop',
        'payment' => function($q){
            $q->with('payment_method');
        },])
        ->where('id', $id)->first();

        return Inertia::render('Simpanan/Wajib/Form', [
            'data' => $data,
            'editMode' => true,
        ]);
    }


            /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function update(Request $request)
    {

        $rules = [
            'anggota_id' => 'required',
            'tgl' => 'required',
            'periode' => 'required',
        ];

        $pesan = [
            'anggota_id.required' => 'Anggota Koperasi Wajib Diisi!',
            'kas_id.required' => 'Kas Koperasi Wajib Diisi!',
            'periode.required' => 'Periode Pembayaran Wajib Diisi!',
            'tgl.required' => 'Tanggal Setoran Wajib Diisi!',
        ];

        $validator = Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            return response()->json([
                'fail' => true,
                'errors' => $validator->errors()
            ]);
        }else{
            DB::beginTransaction();
            Carbon::today()->format('H:i:s');
            try{
                $transaksi = Transaksi::find($request->id);
                $transaksi->nomor = $request->kd_transaksi;
                $transaksi->anggota_id = $request->anggota_id;
                $transaksi->total = (int)$request->wajib;
                $transaksi->tgl = Carbon::parse($request->tgl)->format('Y-m-d H:i:s');
                $transaksi->teller_id  = auth()->guard('admin')->user()->id;
                $transaksi->save();
                
                $pay_wajib = SimkopTransaksi::where('transaksi_id', $transaksi->id)->first();
                $pay_wajib->anggota_id  = $request->anggota_id;
                $pay_wajib->periode = Carbon::createFromFormat('d F Y', '1 '. $request->periode)->format('Y-m-d');
                $pay_wajib->jumlah = $request->wajib;
                $pay_wajib->created_at = Carbon::parse($request->tgl)->format('Y-m-d H:i:s');
                $pay_wajib->save();
                
                $payment = Payment::where('paymenttable_type', 'App\Models\Transaksi')->where('paymenttable_id', $transaksi->id)->first();
                $payment->payment_method_id = $request->payment_method_id;
                $payment->tgl_bayar = Carbon::parse($request->tgl)->format('Y-m-d H:i:s');
                $payment->jumlah = (int)$request->wajib;
                $payment->status = $request->payment_method_id == 1 ? 'paid' : 'unpaid';
                $payment->save();

            }catch(\QueryException $e){
                DB::rollback();
                return response()->json([
                    'fail' => true,
                    'pesan' => 'Terjadi Error Pada Penyimpanan Data',
                    'error' => $e,
                ]);
            }
            DB::commit();
            return response()->json([
                'fail' => false,
                'invoice' => $transaksi->id,
            ]);
        }
    }


        /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function tunggakan(Request $request)
    {
        $keyword  = $request->keyword;
        $today = Carbon::now()->format('m');
        $anggota = Anggota::with('last_simkop')->latest()->get();
        $items = [];
        foreach($anggota as $ang){           

            $dari = Carbon::parse($ang->tgl_gabung)->startOfMonth();
            $now = Carbon::now()->endOfMonth();
            $diff_in_months = $dari->diffInMonths($now);

            $nominal = 0;
            $jumlah = 0;
            $list = array();
            for($i = 0; $i <= $diff_in_months; $i++)
            {
                $bulan = SimkopTransaksi::where('anggota_id', $ang->anggota_id)
                ->whereMonth('periode', $dari->format('m'))
                ->whereYear('periode', $dari->format('Y'))
                ->first();
                if(!$bulan)
                {
                    $nominal += SimpananHelp::getAmountWajib($ang->golongan);
                    $jumlah += 1;
                    $list[$dari->format('Y')][] = $dari->format('F Y');
                }
                $dari->addMonth(1);
            }
            
            if($jumlah != 0 && $nominal != 0){
                $items[] = [
                    'anggota_id' => $ang->anggota_id,
                    'nama' => $ang->nama,
                    'tipe' => $ang->tipe,
                    'last_payment' => $ang->last_simkop->created_at,
                    'golongan' => $ang->golongan,
                    'jumlah' => $jumlah,
                    'nominal' => $nominal,
                    'list' => $list,
                ];
            }
        }

        $data = (new Collection($items))->paginate(10);

        return Inertia::render('Simpanan/Wajib/Tunggakan',[
            'dataList' => $data
        ]);
    }

    public function paid($id){
        $year = Carbon::now()->format('Y');
        $data = SimkopTrans::where('anggota_id', $id)
        ->whereYear('periode', '>=', $year)->pluck('periode');
        return response()->json([
            'fail' => false,
            'date' => $data,
        ]);
    }


}
