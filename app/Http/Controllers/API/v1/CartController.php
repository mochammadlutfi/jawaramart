<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use App\Models\Cart;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
class CartController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $data = ProductCategory::where('parent_id', null)->get();
        if($data){
            return response()->json([
                'data' => $data,
                'fail' => false,
            ], 200);
        }else{
            return response()->json([
                'message' => "No Hp Tidak Ditemukan",
                'fail' => false,
            ], 400);
        }
    }


        /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        dd(auth()->guard('web')->user()->id);
        $rules = [
            'variant_id' => 'required',
        ];

        $pesan = [
            'variant_id.required' => 'Nama Brand Wajib Diisi!',
        ];

        $validator = Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            return back()->withErrors($validator->errors());
        }else{
            DB::beginTransaction();
            try{
                    $cart = new Cart();
                    $cart->user_id = auth()->user()->id;
                    $cart->product_id = $request->product_id;
                    $data->variant_id = $request->variant_id;
                    $data->unit_price = $request->price;
                    $data->qty = $request->qty;
                    $data->save();

            }catch(\QueryException $e){
                DB::rollback();
                return back();
            }
            DB::commit();
            return redirect()->route('admin.product.brand.index');
        }
    }
}
