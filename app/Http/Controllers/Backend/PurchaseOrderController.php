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
use Barryvdh\DomPDF\Facade\Pdf;


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
     * 
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
                $data->status = 'pending';
                $data->payment_status = 'unpaid';
                $data->tax_id = $request->tax_id;
                $data->tax_amount = $request->tax_amount;
                $data->save();

                foreach($request->lines as $i){
                    $line = new PurchaseLine();
                    $line->product_id = $i['product_id'];
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
            $q->with(['payment_method']);
        }, 'supplier', 'staff'])
        ->where('id', $id)->first();

        return Inertia::render('Backend/Purchases/Form', [
            'data' => $data,
            'editMode' => true
        ]);
    }


    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request)
    {
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

                $data = Purchase::where('id', $request->id)->first();
                $data->date = Carbon::now();
                $data->ref = GeneralHelp::generate_ref_purchase();
                $data->supplier_id = $request->supplier_id;
                $data->discount_type = $request->discount_type;
                $data->discount_amount = $request->discount_amount;
                $data->discount_value = $request->discount_value;
                $data->total = $request->total;
                $data->grand_total = $request->grand_total;
                $data->staff_id = auth()->guard('admin')->user()->id;
                $data->tax_id = $request->tax_id;
                $data->tax_amount = $request->tax_amount;
                $data->save();

                foreach($request->lines as $i){
                    if(array_key_exists("id", $i)){
                        $line_id = $i['id'];
                    }else{
                        $line_id = null;
                    }

                    $line = PurchaseLine::firstOrNew(['id' =>  $line_id]);
                    $line->product_id = $i['product_id'];
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
                // if(count($request->removed)){
                $remove = PurchaseLine::where('purchase_id', $data->id)->whereIn('id', $request->removed)->delete();
                // }

            }catch(\QueryException $e){
                DB::rollback();
                return back();
            }
            DB::commit();
            return redirect()->route('admin.purchase.order.show', $data->id);
        }
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
            $q->with(['payment_method:id,name,image']);
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
        $rules = [
            'amount_received' => 'required',
            'paymenttable_type' => 'required',
        ];

        $pesan = [
            'amount_received.required' => 'Amount Received Must Be Filled!',
            'paymenttable_type.required' => 'Payment Type Must Be Filled!',
        ];

        $validator = Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            return back()->withErrors($validator->errors());
        }else{
            DB::beginTransaction();
            try{
                    
                    $data = new Payment();
                    $data->paymenttable_type = $request->paymenttable_type;
                    $data->paymenttable_id = $request->paymenttable_id;
                    $data->type = 'outbound';
                    $data->amount = $request->amount;
                    $data->amount_received = $request->amount_received;
                    $data->payment_method_id = $request->payment_method_id;
                    $data->change = $request->change;
                    $data->date = Carbon::now();
                    $data->validated_at = Carbon::now();
                    $data->validated_by = auth()->guard('admin')->user()->id;
                    $data->save();

                    $return = $request->paymenttable_type::where('id', $request->paymenttable_id)->first();
                    if($return->to_pay == 0){
                        $return->payment_status = 'paid';
                    }else if($return->to_pay < $return->grand_total){
                        $return->payment_status = 'partial';
                    }else{
                        $return->payment_status = 'unpaid';
                    }
                    $return->save();

            }catch(\QueryException $e){
                DB::rollback();
                return back();
            }
            DB::commit();
            return redirect()->route('admin.purchase.order.show', $return->id);
        }
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
        DB::beginTransaction();
        try{
            $data = Purchase::where('id', $id)->delete();
            $line = PurchaseLine::where('purchase_id', $id)->delete();

        }catch(\QueryException $e){
            DB::rollback();
            return back();
        }
        DB::commit();
        return redirect()->route('admin.purchase.order.index');
    }

    
    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function pdf($id)
    {
        $data = Purchase::with(['line' => function($q){
            $q->with(['product:id,name']);
        }, 'payment' => function($q){
            $q->with(['payment_method:id,name,image']);
        }, 'supplier', 'staff'])
        ->where('id', $id)->first();
        
        $pdf = PDF::loadView('report.purchase', compact([
            'data'
        ]));

        return $pdf->stream();
        // return view('report.purchase', compact([
        //         'data'
        //     ]));
    }

    private function uploadFiles($file){
        $file_name = uniqid() . '.' . $file->getClientOriginalExtension();
        Storage::disk('public')->putFileAs(
            'uploads/sliders',
            $file,
            $file_name
        );
        $pdf->setPaper('a4', 'landscape');
        
        return 'uploads/sliders/'.$file_name;
    }
}
