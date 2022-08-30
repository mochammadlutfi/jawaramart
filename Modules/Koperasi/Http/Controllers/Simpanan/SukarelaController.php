<?php

namespace Modules\Koperasi\Http\Controllers\Simpanan;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use DB;
use Carbon\Carbon;
use App\Helpers\GeneralHelp;



use App\Models\Transaksi;
use App\Models\TransaksiLine;
use App\Models\Accounting\Payment;

use App\Models\Simpanan\SimkopTransaksi;
use App\Models\Simpanan\SimlaTransaksi;
use App\Models\Simpanan\Wallet;

class SukarelaController extends Controller
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
            ->select('a.id', 'a.anggota_id', 'a.name', 'b.nama as anggota_name', 'a.date', 'a.service', 'a.type', 'a.payment_status', 'a.total', 'c.id as simla_id')
            ->join('kop_anggota as b', 'b.anggota_id', '=', 'a.anggota_id')
            ->join('kop_sukarela_trans as c', 'c.trans_id', '=', 'a.id')
            ->whereIn('a.service', ['setoran sukarela', 'penarikan sukarela', 'transfer sukarela']);
        $data = $eloq->orderBy($sort, $sortDir)
        ->paginate($limit);

            
        $all = clone $eloq;
        $pending = clone $eloq;
        $done = clone $eloq;

        $statistic = [
            'all' => $all->count(),
            'pending' => $pending->where('b.status', 0)->count(),
            'done' =>  $done->where('b.status', 1)->count(),
        ];

        return Inertia::render('Koperasi::Simpanan/Sukarela/Index',[
            'dataList' => $data,
            'statistic' => $statistic
        ]);
    }


        /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function create(Request $request)
    {
        return Inertia::render('Simpanan/Sukarela/Form',[
            'transaksi_ref' => GeneralHelp::generate_transaksi_ref('simla'),
            'type' => 'isi saldo'
        ]);
    }

        /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function store(Request $request)
    {
        $rules = [
            'anggota_id' => 'required',
            'amount' => 'required',
            'tgl' => 'required',
        ];

        $pesan = [
            'anggota_id.required' => 'ID Anggota Wajib Diisi!',
            'kas_id.required' => 'Kas Wajib Diisi!',
            'amount.required' => 'Jumlah Simpanan Wajib Diisi!',
            'tgl.required' => 'Tanggal Transaksi Wajib Diisi!',
        ];

        $validator = Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            return back()->withErrors($validator->errors());
        }else{

            $item = collect([
                array(
                    'keterangan' => 'Simpanan Sukarela',
                    'jumlah' => $request->jumlah,
                    'akun' => 14,
                ),
            ]);

            DB::beginTransaction();
            try{

                $transaksi = new Transaksi();
                $transaksi->nomor = $request->kd_transaksi;
                $transaksi->anggota_id = $request->anggota_id;
                $transaksi->service = 'simpanan';
                $transaksi->sub_service = 'sukarela';
                $transaksi->jenis = ($request->type == 'withdrawn') ? 'penarikan sukarela' : 'setoran sukarela';
                $transaksi->teller_id   = auth()->guard('admin')->user()->id;
                $transaksi->keterangan = $request->note;
                $transaksi->total = $request->amount ;
                $transaksi->tgl = Carbon::parse($request->tgl)->format('Y-m-d');
                $transaksi->status = 1;
                $transaksi->save();


                $line = new TransaksiLine();
                $line->jumlah = $request->amount;
                $line->nama = 'Simpanan Sukarela';
                $line->akun_id = 14;
                $line->user_id = auth()->guard('admin')->user()->id;
                $line->created_at = Carbon::parse($request->tgl)->format('Y-m-d H:i:s');
                $transaksi->line()->save($line);
                
                $payment = new Payment();
                $payment->payment_method_id = $request->payment_method_id;
                $payment->tgl_bayar = Carbon::parse($request->tgl)->format('Y-m-d H:i:s');
                $payment->jumlah = (int)$request->amount;
                $payment->type = 'inbound';
                $payment->status = $request->payment_method_id == 1 ? 'paid' : 'unpaid';
                $transaksi->payment()->save($payment);

                $simla = new SimlaTransaksi();
                $simla->anggota_id = $request->anggota_id;
                $simla->type = $request->type;
                $simla->debit = $request->amount;
                $simla->credit = 0;
                $transaksi->simla()->save($simla);

            }catch(\QueryException $e){
                DB::rollback();
                return response()->json([
                    'fail' => true,
                    'pesan' => 'Terjadi Error Pada Penyimpanan Data',
                    'error' => $e,
                ]);
            }

            DB::commit();
            return redirect()->route('simpanan.sukarela.show', $transaksi->id);
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
        ->select('a.id', 'a.name', 'a.service', 'a.date', 'a.total', 'a.anggota_id', 'b.nama as anggota_name', 'c.id as simla_id', 'a.payment_status', 'c.type')
        ->join('kop_anggota as b', 'b.anggota_id', '=', 'a.anggota_id')
        ->join('kop_sukarela_trans as c', 'c.trans_id', '=', 'a.id')
        ->where('c.id', $id)->first();

        return Inertia::render('Koperasi::Simpanan/Sukarela/Show',[
            'data' => $data
        ]);
    }

    

    public function edit($id, Request $request)
    {

        $data = Transaksi::with(['anggota', 'teller', 'simla', 'line', 'payment' => function($q){
            $q->with(['payment_method']); 
         }])
         ->where('id', $id)->first();

        return Inertia::render('Simpanan/Sukarela/Form', [
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
            'amount' => 'required',
            'tgl' => 'required',
        ];

        $pesan = [
            'anggota_id.required' => 'ID Anggota Wajib Diisi!',
            'kas_id.required' => 'Kas Wajib Diisi!',
            'amount.required' => 'Jumlah Simpanan Wajib Diisi!',
            'tgl.required' => 'Tanggal Transaksi Wajib Diisi!',
        ];


        $validator = Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            return back()->withErrors($validator->errors());
        }else{
            DB::beginTransaction();
            Carbon::today()->format('H:i:s');
            try{
                $transaksi = Transaksi::find($request->id);
                $transaksi->nomor = $request->kd_transaksi;
                $transaksi->anggota_id = $request->anggota_id;
                $transaksi->total = (int)$request->amount;
                $transaksi->tgl = Carbon::parse($request->tgl)->format('Y-m-d H:i:s');
                $transaksi->teller_id  = auth()->guard('admin')->user()->id;
                $transaksi->save();

                $line = TransaksiLine::where('transaksi_id', $transaksi->id)
                ->where('akun_id', settings()->get('simpanan_sukarela_journal'))
                ->first();
                $line->jumlah = (int)$request->amount;
                $line->save();
                
                $simla = SimlaTransaksi::where('transaksi_id', $transaksi->id)->first();
                $simla->anggota_id = $request->anggota_id;
                $simla->type = $request->type;
                $simla->debit = $request->amount;
                $simla->credit = 0;
                $simla->save();

                $payment = Payment::where('paymenttable_type', 'App\Models\Transaksi')->where('paymenttable_id', $transaksi->id)->first();
                $payment->payment_method_id = $request->payment_method_id;
                $payment->tgl_bayar = Carbon::parse($request->tgl)->format('Y-m-d H:i:s');
                $payment->jumlah = (int)$request->amount;
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
            return redirect()->route('simpanan.sukarela.show', $transaksi->id);
        }
    }


    public function delete($id){

    }


}
