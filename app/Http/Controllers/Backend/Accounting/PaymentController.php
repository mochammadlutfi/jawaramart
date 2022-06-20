<?php

namespace App\Http\Controllers\Backend\Accounting;

use App\Models\Accounting\Payment;
use App\Models\Accounting\PaymentMethod;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Storage;

use Carbon\Carbon;
class PaymentController extends Controller
{
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
        $data = ProductBrand::withCount(['product'])
        ->when($request->search, function($query, $search){
            $query->where('name', 'LIKE', '%' . $search . '%');
        })
        ->orderBy('id', 'desc')->paginate(6);
       

        return Inertia::render('Backend/Brand/index', [
            'dataList' => fn () => $data
        ]);
    }

        /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function data(Request $request)
    {
        $data = Payment::where('paymenttable_type', $request->type)
        ->where('paymenttable_id', $request->id)
        ->with(['payment_method'])
        ->orderBy('id', 'desc')->get();
        
        $payment_methods = PaymentMethod::where('pos_ok', 1)->get();

        return response()->json([
            'data' => $data,
            'payment_methods' => $payment_methods,
            'fail' => false,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $rules = [
            'paymenttable_type' => 'required',
        ];

        $pesan = [
            'paymenttable_type.required' => 'Nama Brand Wajib Diisi!',
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
                    $data->amount = $request->amount;
                    $data->amount_received = $request->amount_received;
                    $data->payment_method_id = $request->payment_method_id;
                    $data->change = $request->change;
                    $data->date = Carbon::now();
                    $data->validated_at = Carbon::now();
                    $data->validated_by = auth()->guard('admin')->user()->id;

                    $data->save();

            }catch(\QueryException $e){
                DB::rollback();
                return back();
            }
            DB::commit();

            return redirect()->back();
        }
    }

        /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function update(Request $request)
    {
        $rules = [
            'name' => 'required',
        ];

        $pesan = [
            'name.required' => 'Nama Brand Wajib Diisi!',
        ];

        $validator = Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            return back()->withErrors($validator->errors());
        }else{
            DB::beginTransaction();
            try{
                    $data = ProductBrand::find($request->id);
                    $data->name = $request->name;
                    $data->state = $request->state;
                    if($data->image != $request->image){
                        if(Storage::disk('public')->exists($data->image))
                        {
                            Storage::disk('public')->delete($data->image);
                        }
                        if($request->hasFile('image')){
                            $data->image = $this->uploadFiles($request->file('image'), Str::slug($request->name, '-'));
                        }else{
                            $data->image = null;
                        }
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

    private function uploadFiles($file, $name){
        $file_name = $name. '-' . uniqid() . '.' . $file->getClientOriginalExtension();
        Storage::disk('public')->putFileAs(
            'uploads/product/brands',
            $file,
            $file_name
        );
        
        return 'uploads/product/brands/'.$file_name;
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
            $data = Payment::where('id', $id)->first();
            $parent_id = $data->paymenttable_id;
            $model = $data->paymenttable_type;
            $data->delete();

            $parent = $model::where('id', $parent_id)->first();
            if($parent->to_pay == 0){
                $parent->payment_status = 'paid';
            }else if($parent->to_pay < $parent->grand_total){
                $parent->payment_status = 'partial';
            }else{
                $parent->payment_status = 'unpaid';
            }
            $parent->save();


        }catch(\QueryException $e){
            DB::rollback();
            return back();
        }
        DB::commit();
            return redirect()->back();
    }
}