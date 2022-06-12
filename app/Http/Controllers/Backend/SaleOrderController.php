<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Storage;
use App\Helpers\GeneralHelp;

use Carbon\Carbon;
use App\Models\Sale;
use App\Models\SaleLine;
use App\Models\Payment;


class SaleOrderController extends Controller
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
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {

        $overview = Collect([
            'pending' => Sale::where('status', 'pending')->where('is_pos', 0)->where('payment_status', 'paid')->count(),
            'confirmed' => Sale::where('status', 'confirmed')->where('is_pos', 0)->where('payment_status', 'paid')->count(),
            'dikirim' => Sale::where('status', 'delivery')->where('is_pos', 0)->where('payment_status', 'paid')->count(),
            'selesai' => Sale::whereIn('status', array('done', 'cancel'))->where('is_pos', 0)->where('payment_status', 'paid')->count(),
        ]);

        $query = Sale::with('customer:id,name,avatar')->withCount(['line'])
        ->where('is_pos', 0);

        $query->when($request->status == 'pending' || empty($request->status), function ($q) {
            return $q->where('status', 'pending')->where('payment_status', 'paid');
        });

        $query->when($request->status == 'confirmed', function ($q) {
            return $q->where('status', 'confirmed')->where('payment_status', 'paid');
        });

        $query->when($request->status == 'dikirim', function ($q) {
            return $q->where('status', 'delivery')->where('payment_status', 'paid');
        });

        $query->when($request->status == 'selesai', function ($q) {
            return $q->where('status', 'done')->orwhere('status', 'cancel');
        });

        $query->when($request->search, function ($q) {
            return $q->where('ref', 'LIKE', '%' . $search . '%');
        });
        
        $dataList = $query->orderBy('id', 'desc')->paginate(10);
 
        return Inertia::render('Backend/Sales/Index', [
            'dataList' => $dataList,
            'overview' => $overview
        ]);
    }


    public function create(){
        
        return Inertia::render('Backend/Sales/Form');
    }


    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $rules = [
            'customer_id' => 'required',
            'date' => 'required',
            'status' => 'required',
        ];

        $pesan = [
            'customer_id.required' => 'Customer Must Be Filled!',
            'date.required' => 'Supplier Must Be Filled!',
            'status.required' => 'Supplier Must Be Filled!',
        ];

        $validator = Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            return back()->withErrors($validator->errors());
        }else{
            DB::beginTransaction();
            try{

                $data = new Sale();
                $data->date = Carbon::now();
                $data->ref = GeneralHelp::generate_ref_sale();
                $data->customer_id = $request->customer_id;
                $data->discount_type = $request->discount_type;
                $data->discount_amount = $request->discount_amount;
                $data->discount_value = $request->discount_value;
                $data->total = $request->total;
                $data->grand_total = $request->grand_total;
                $data->staff_id = auth()->guard('admin')->user()->id;
                $data->status = 'draft';
                $data->payment_status = 'unpaid';
                $data->tax_id = $request->tax_id;
                $data->tax_amount = $request->tax_amount;
                $data->save();

                foreach($request->lines as $i){
                    $line = new SaleLine();
                    $line->product_id = $i['id'];
                    $line->variant_id = $i['variant_id'];
                    $line->unit_price = $i['price'];
                    $line->net_price = $i['price'];
                    $line->qty = $i['qty'];
                    $line->discount_type = $i['discount_type'];
                    $line->discount_value = $i['discount_value'];
                    $line->discount_amount = $i['discount_amount'] != NULL ? $i['discount_amount'] : 0 ;
                    $line->tax_id = $i['tax_id'];
                    $line->tax_amount = $i['tax_amount'] != NULL ? $i['tax_amount'] : 0 ;
                    $line->sub_total = $i['subtotal'];
                    $data->line()->save($line);
                }

                // $payment = new Payment();
                // $payment->amount = $request->grand_total;
                // $payment->amount_received = $request->payment_received;
                // $payment->change = $request->payment_change;
                // $payment->payment_method_id = 1;
                // $data->payment()->save($payment);

            }catch(\QueryException $e){
                DB::rollback();
                return back();
            }
            DB::commit();
            return redirect()->route('admin.sale.order.show', $data->id);
        }
    }

    /**
     * Show Sale Detail.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Sale::with(['line' => function($q){
            $q->with(['product:id,name']);
        }, 'payment' => function($q){
            $q->with(['payment_method:id,name,image']);
        }, 'customer', 'staff', 'delivery'])
        ->where('id', $id)->first();

        return Inertia::render('Backend/Sales/Show',[
            'data' => $data
        ]);
    }

    
    public function edit($id, Request $request){
        
        $data = Sale::with(['line' => function($q){
            $q->with(['product:id,name']);
        }, 'payment' => function($q){
            $q->with(['payment_method:id,name,image']);
        }, 'customer', 'staff'])
        ->where('id', $id)->first();

        return Inertia::render('Backend/Sales/Form', [
            'data' => $data,
            'editMode' => true
        ]);
    }

    
    /**
     * Store a newly payment created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function payment(Request $request)
    {
        $rules = [
            'payment_method' => 'required',
            'amount_received' => 'required',
        ];

        $pesan = [
            'payment_method.required' => 'Payment Method Must be Filled!',
            'amount_received.required' => 'Payment Method Must be Filled!',
        ];

        $validator = Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            return back()->withErrors($validator->errors());
        }else{
            DB::beginTransaction();
            try{
                    
                    $data = new Payment();
                    $data->url = $request->link;
                    $data->state = $request->state;
                    if(!empty($request->file('image'))){
                        $data->image = $this->uploadFiles($request->file('image'));
                    }
                    $data->save();

            }catch(\QueryException $e){
                DB::rollback();
                return back();
            }
            DB::commit();
            return redirect()->route('admin.product.brand.index');
        }
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

        /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update_status(Request $request)
    {
        DB::beginTransaction();
        try{
            $sale = Sale::where('id', $request->id)->first();
            $sale->status = $request->status;
            $sale->save();

        }catch(\QueryException $e){
            DB::rollback();
            return back();
        }
        DB::commit();
        return redirect()->route('admin.sale.order.index', $sale->id);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }

    private function uploadFiles($file){
        $file_name = uniqid() . '.' . $file->getClientOriginalExtension();
        Storage::disk('public')->putFileAs(
            'uploads/sliders',
            $file,
            $file_name
        );
        
        return 'uploads/sliders/'.$file_name;
    }

}
