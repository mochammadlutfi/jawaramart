<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use App\Models\ProductCategory;
class ProductCategoryController extends Controller
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
}
