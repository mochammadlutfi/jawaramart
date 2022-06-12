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
use Carbon\Carbon;
use App\Helpers\GeneralHelp;
use App\Models\Purchase;
use App\Models\PurchaseLine;
use App\Models\Payment;
use App\Models\ProductStock;


class PurchaseOrderController extends Controller
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
        $dataList = Purchase::with(["payment" => function($q){
            $q->with('payment_method');
        }, 'staff', 'supplier'])
        ->withCount(['line'])
        ->when($request->search, function($query, $search){
            $query->where('name', 'LIKE', '%' . $search . '%')
            ->orWhere('email', 'LIKE', '%' . $search . '%');
        })
        ->orderBy('id', 'desc')->paginate(10);

        return Inertia::render('Backend/Purchases/Index', [
            'dataList' => $dataList
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return Inertia::render('Backend/Purchases/Form');
    }


    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $rules = [
            'supplier_id' => 'required',
            'date' => 'required',
            'status' => 'required',
        ];

        $pesan = [
            'supplier_id.required' => 'Supplier Must Be Filled!',
            'date.required' => 'Supplier Must Be Filled!',
            'status.required' => 'Supplier Must Be Filled!',
        ];

        $validator = Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            return back()->withErrors($validator->errors());
        }else{
            DB::beginTransaction();
            try{

                $data = new Purchase();
                $data->date = Carbon::now();
                $data->ref = GeneralHelp::generate_ref_purchase();
                $data->supplier_id = $request->supplier_id;
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
                    $line = new PurchaseLine();
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
            return redirect()->route('admin.purchase.order.show', $data->id);
        }
    }

    

    /**
     * Show the form for editing a resource.
     * @return Renderable
     */
    public function edit($id)
    {
        $data = Purchase::with(['line' => function($q){
            $q->with(['product:id,name']);
        }, 'payment' => function($q){
            $q->with(['payment_method:id,name']);
        }, 'supplier', 'staff'])
        ->where('id', $id)->first();

        return Inertia::render('Backend/Purchases/Form', [
            'data' => $data,
            'editMode' => true
        ]);
    }

    /**
     * Show Sale Detail.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Purchase::with(['line' => function($q){
            $q->with(['product:id,name']);
        }, 'payment' => function($q){
            $q->with(['payment_method:id,name']);
        }, 'supplier', 'staff'])
        ->where('id', $id)->first();

        return Inertia::render('Backend/Purchases/Show',[
            'data' => $data
        ]);
    }
    
    /**
     * Store a newly payment created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function payment(Request $request)
    {
        // dd($request->all());
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
                

                $purchase = Purchase::find($request->purchase_id);

                $payment = new Payment();
                $payment->date = Carbon::parse($request->date)->format('Y-m-d');
                $payment->amount = $request->amount;
                $payment->amount_received = $request->amount_received;
                $payment->change = $request->change;
                $payment->ref = $request->ref;
                $payment->payment_method_id = 1;
                $purchase->payment()->save($payment);

                if($purchase->total_paid > 0){
                    if($purchase->grand_total == $purchase->total_paid){
                        $purchase->payment_status = 'paid';
                    }else{
                        $purchase->payment_status = 'partial';
                    }
                }
                $purchase->save();

            }catch(\QueryException $e){
                DB::rollback();
                return back();
            }
            DB::commit();
            return redirect()->route('admin.purchase.order.show', $purchase->id);
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
    public function updateStatus(Request $request)
    {
        
        DB::beginTransaction();
        try{

            $data = Purchase::where('id', $request->id)->first();
            $data->status = $request->status;
            $data->save();

            if($request->status === 'done'){
                foreach($data->line as $line){
                    $s = ProductStock::firstOrNew(['product_id' =>  $line->product_id, 'variant_id' =>  $line->variant_id]);
                    $s->stock = ($s->stock != null) ? $s->stock + $line->qty : $line->qty;
                    $s->save();
                }
            }

        }catch(\QueryException $e){
            DB::rollback();
            return back();
        }
        DB::commit();
        return redirect()->route('admin.purchase.order.show', $data->id);
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
