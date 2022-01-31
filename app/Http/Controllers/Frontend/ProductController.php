<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

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
        ->where('slug', $product)->first();

        return Inertia::render('Frontend/Product/show',[
            'data' => $data
        ]);
    }
}
