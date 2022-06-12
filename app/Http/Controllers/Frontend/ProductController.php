<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Helpers\Collection;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\Models\Wishlist;
class ProductController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function show($product)
    {
        $data = Product::with(['images', 'category', 'reviews', 'variant'])
        ->withCount(['sale' => function($q){
            $q->whereHas('sale', function ($query) {
                return $query->where('is_pos', 0);
            });
        }])
        ->where('slug', $product)->first();

        if(auth()->guard('web')->user()){
            $wc = Wishlist::where('user_id', auth()->guard('web')->user()->id)->where('product_id', $data->id)->count();
            
            $data->is_wishlist = ($wc) ? 1 : 0;
        }else{
            $data->is_wishlist = 0;
        }

        return Inertia::render('Frontend/Product/Show',[
            'data' => $data
        ]);
    }


    public function category(Request $request){

        
        

        return Inertia::render('Frontend/Product/Category');
    }

    public function listingByCategory($slug, Request $request)
    {
        $search = $request->search;
        
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

        $category = ProductCategory::select('id', 'name', 'cover', 'slug')->where('slug', $slug)->first();
        $res = Product::select('id', 'name', 'show_web', 'slug', 'category_id', 'brand_id')
        ->withCount([
            'stock' => function ($query) {
                $query->select(DB::raw("SUM(stock)"));
            }
        ])
        ->with([
            'variant'
        ])
        ->where('category_id', $category->id)
        ->when($search, function($query, $search){
            $query->where('name', 'LIKE', '%' . $search . '%');
        })->get();

        if($sort_dir == 'asc'){
            $sorted = $res->sortBy($sort);
        }else{
            $sorted = $res->sortByDesc($sort);
        }
        $product = (new Collection($sorted->values()))->paginate(18);
        
        return Inertia::render('Frontend/Product/CategoryShow',[
            'category' => $category,
            'products' => $product
        ]);
    }
}
