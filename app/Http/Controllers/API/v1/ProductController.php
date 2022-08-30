<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use App\Models\Product;
use DB;
class ProductController extends Controller
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
        $data = Product::with('variant')
        ->withCount([
            'stock' => function ($query) {
                $query->select(DB::raw("IFNULL(SUM(stock), 0 )"));
            }
        ])->orderBy('id', 'ASC')->limit(18)->get();
        if($data){
            return response()->json([
                'data' => $data,
                'fail' => false,
            ], 200);
        }else{
            return response()->json([
                'message' => "Product Not Found",
                'fail' => false,
            ], 400);
        }
    }


    
    /**
     * List Product Brand.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function listBrand(Request $request)
    {
        $data = ProductBrand::orderBy('id', 'ASC')->limit(18)->get();
        if($data){
            return response()->json([
                'data' => $data,
                'fail' => false,
            ], 200);
        }else{
            return response()->json([
                'message' => "Product Not Found",
                'fail' => false,
            ], 400);
        }
    }
}
