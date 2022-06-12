<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Helpers\Collection;

use App\Http\Resources\SearchProductResource;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function search(Request $request){
        
        
        $search = $request->q;
        if($search){
            if($request->sort == 1){
                $sort = 'sell_max_price';
                $sort_dir = 'asc';
            }elseif($request->sort == 2){
                $sort = 'sell_max_price';
                $sort_dir = 'desc';
            }else{
                $sort = 'name';
                $sort_dir = 'asc';
            }
    
            $res = Product::select('id', 'name', 'show_web', 'slug', 'category_id', 'brand_id')
            ->withCount([
                'stock' => function ($query) {
                    $query->select(DB::raw("SUM(stock)"));
                }
            ])
            ->with([
                'variant'
            ])
            ->when($search, function($query, $search){
                $query->where('name', 'LIKE', '%' . $search . '%');
            })->get();
    
            if($sort_dir == 'asc'){
                $sorted = $res->sortBy($sort);
            }else{
                $sorted = $res->sortByDesc($sort);
            }


            $product = (new Collection($sorted->values()))->paginate(18);
        }else{
            $product = null;
        }
        
        return Inertia::render('Frontend/Product/SearchListing',[
            'products' => $product
        ]);
    }

    public function autocomplete(Request $request){

        $search = $request->search;
        // dd($request->all());
        $res = Product::select('id', 'name', 'show_web', 'slug', 'category_id', 'brand_id')
        ->withCount([
            'stock' => function ($query) {
                $query->select(DB::raw("SUM(stock)"));
            }
        ])
        ->with([
            'variant:id,sell_price'
        ])
        ->when($search, function($query, $search){
            $query->where('name', 'LIKE', '%' . $search . '%');
        })->get();

        // dd($res);

        // $res = ProductCategory::where
        

        return response()->json([
            'products' => $res,
            'fail' => false,
        ], 200);

    }
}
