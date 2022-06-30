<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Storage;
use Inertia\Inertia;
use Carbon\Carbon;

use App\Helpers\GeneralHelp;


use App\Models\Sale;
use App\Models\SaleLine;
use App\Models\Accounting\Payment;
use App\Models\Accounting\PaymentMethod;
use App\Models\ProductStock;
use App\Models\CashRegister;
class POSController extends Controller
{
    /**
     * Only Authenticated users for "admin" guard
     * are allowed.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Show Admin Dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        $dataList = Sale::with('customer:id,name,avatar')->withCount(['line'])
        ->where('is_pos', 1)
        ->when($request->search, function ($q) {
            return $q->where('ref', 'LIKE', '%' . $search . '%');
        })->orderBy('id', 'desc')->paginate(10);

        return Inertia::render('Backend/POS/Index', [
            'dataList' => $dataList,
        ]);
    }

        /**
     * Show Admin Dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $payment_methods = PaymentMethod::where('pos_ok', 1)->get();

        $today = Carbon::today();
        $cash_register = CashRegister::select('cash_register.id', 'admin_id', 'opened_at', 'closed_at', 'opened_amount', 'closed_amount', 'a.name')
        ->leftJoin('admins as a', 'a.id', '=', 'cash_register.admin_id')
        ->where('admin_id', auth()->guard('admin')->user()->id)
        ->whereDate('opened_at', $today)
        ->where('closed_at', NULL)
        ->first();

        return Inertia::render('Backend/POS/Create',[
            'payment_methods' => $payment_methods,
            'cash_register' => $cash_register,
        ]);
    }



    public function open(Request $request)
    {
        DB::beginTransaction();
        try{
            $data = new CashRegister();
            $data->opened_at = Carbon::now();
            $data->opened_amount = $request->amount;
            $data->admin_id = auth()->guard('admin')->user()->id;
            $data->save();

        }catch(\QueryException $e){
            DB::rollback();
            return back();
        }

        DB::commit();
        return redirect()->route('admin.sale.pos.create');
    }

    
    public function close(Request $request)
    {
        DB::beginTransaction();
        try{
            $data = CashRegister::where('id', $request->id)->first();
            $data->closed_at = Carbon::now();
            $data->closed_amount = $request->amount;
            $data->save();

        }catch(\QueryException $e){
            DB::rollback();
            return back();
        }

        DB::commit();
        return redirect()->route('admin.sale.pos.create');
    }

    
    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try{
                $data = new Sale();
                $data->date = Carbon::now();
                $data->is_pos = 1;
                $data->ref = GeneralHelp::generate_ref_sale();
                $data->customer_id = $request->customer_id;
                $data->discount_type = $request->discount_type;
                $data->discount_amount = $request->discount_amount;
                $data->discount_value = $request->discount_value;
                $data->total = $request->total;
                $data->grand_total = $request->grand_total;
                $data->staff_id = auth()->guard('admin')->user()->id;
                $data->status = 'done';
                if($request->payment_method == 2){
                    $data->payment_status = 'pending';
                    $data->payment_due = Carbon::today()->addMonth(1)->firstOfMonth();
                }else{
                    $data->payment_status = 'paid';
                }
                $data->save();

                foreach($request->lines as $i){
                    $line = new SaleLine();
                    $line->product_id = $i['id'];
                    $line->variant_id = $i['variant_id'];
                    $line->unit_price = $i['price'];
                    $line->net_price = $i['net_price'];
                    $line->qty = $i['qty'];
                    $line->discount_type = $i['discount_type'];
                    $line->discount_value = $i['discount_value'];
                    $line->discount_amount = $i['discount_amount'] != NULL ? $i['discount_amount'] : 0 ;
                    $line->sub_total = $i['subtotal'];
                    $data->line()->save($line);

                    $s = ProductStock::where('product_id', $i['id'])->where('variant_id', $i['variant_id'])
                    ->decrement('stock', $i["qty"]);
                    
                    // $s = ProductStock::firstOrNew(['product_id' =>  $line->product_id, 'variant_id' =>  $line->variant_id]);
                    // $s->stock = ($s->stock != null) ? $s->stock + $line->qty : $line->qty;
                    // $s->save();
                }

                $payment = new Payment();
                $payment->amount = $request->grand_total;
                $payment->amount_received = $request->payment_received;
                $payment->change = $request->payment_change;
                $payment->type = 'inbound';
                $payment->payment_method_id = $request->payment_method;
                if($request->payment_method != 2){
                    $payment->validated_at = Carbon::now();
                }
                $data->payment()->save($payment);

        }catch(\QueryException $e){
            DB::rollback();
            return back();
        }
        DB::commit();
        return response()->json([
            'fail' => false,
            'id' => $data->id
        ]);
    }

        /**
     * Print the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function invoice_print($id)
    {
        $data = Sale::with(['line' => function($q){
            $q->with(['product:id,name']);
        }, 'payment' => function($q){
            $q->with(['payment_method:id,name,image']);
        }, 'customer', 'staff'])
        ->where('id', $id)->first();

        return response()->json($data);
    }


    public function report(){

        $admin_id = auth()->guard('admin')->user()->id;

        $today = Carbon::today();
        $cash_register = CashRegister::select('cash_register.id', 'admin_id', 'opened_at', 'closed_at', 'opened_amount', 'closed_amount', 'a.name')
        ->leftJoin('admins as a', 'a.id', '=', 'cash_register.admin_id')
        ->where('admin_id', auth()->guard('admin')->user()->id)
        ->whereDate('opened_at', $today)
        ->where('closed_at', NULL)
        ->first();

        $query = Payment::select('payment.id', 'payment.amount', 'payment.payment_method_id', 'payment.validated_at', 'payment.validated_by', 'payment.amount_received',
        'a.id as sale_id', 'a.ref', 'a.date', 'a.payment_status', 'a.payment_due', 'a.grand_total', 'a.is_pos')
        ->where('paymenttable_type','App\Models\Sale')
        ->where('a.is_pos', 1)
        ->whereDate('a.date', '>=', Carbon::parse($cash_register->opened_at))
        ->whereTime('a.date', '>=', Carbon::parse($cash_register->opened_at))
        ->join('sales as a', 'a.id', '=', 'payment.paymenttable_id')
        ->with(['payment_method' => function($q){
            $q->where('pos_ok', 1);
        }, 'payment_proof'])->get();

        // dd($query);
        $total_sales = 0;
        $total_payment = 0;
        $on_hand = 0;
        foreach($query as $q){
            $total_sales += $q->grand_total;
            $total_payment += $q->amount;
            if($q->payment_method_id == 1){
                $on_hand += $q->amount;
            }
        }

        $payment = PaymentMethod::where('pos_ok', 1)
        ->withCount([
            'payment' => function($q) use($cash_register){
                $q->select(DB::raw("SUM(amount_received) as paidsum", 'a.is_pos', 'a.date'))
                ->leftJoin('sales as a', 'a.id', '=', 'payment.paymenttable_id')
                ->where('a.is_pos', 1)
                ->whereDate('a.date', '>=', Carbon::parse($cash_register->opened_at))
                ->whereTime('a.date', '>=', Carbon::parse($cash_register->opened_at));
            }
        ])->get();

        $data = Collect([
            'payment' => $payment,
            'total_sales' => $total_sales,
            'total_payment' => $total_payment,
            'on_hand' => $on_hand,
        ]);

        return response()->json($data);
    }

    public function transaction(Request $request){
        $admin_id = auth()->guard('admin')->user()->id;

        $today = Carbon::today();
        $cash_register = CashRegister::select('cash_register.id', 'admin_id', 'opened_at', 'closed_at', 'opened_amount', 'closed_amount', 'a.name')
        ->leftJoin('admins as a', 'a.id', '=', 'cash_register.admin_id')
        ->where('admin_id', auth()->guard('admin')->user()->id)
        ->whereDate('opened_at', $today)
        ->where('closed_at', NULL)
        ->first();
        
        $data = Sale::with('customer:id,name,avatar')->withCount(['line'])
        ->where('is_pos', 1)
        ->where('staff_id', $admin_id)
        ->whereDate('date', '>=', Carbon::parse($cash_register->opened_at))
        ->whereTime('date', '>=', Carbon::parse($cash_register->opened_at))
        ->when($request->search, function ($q) {
            return $q->where('ref', 'LIKE', '%' . $search . '%');
        })->orderBy('id', 'desc')->paginate(10);


        return response()->json($data);
    }

}
