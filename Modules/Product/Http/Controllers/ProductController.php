<?php

namespace Modules\Product\Http\Controllers;


use Modules\Product\Entities\Product;
use Modules\Product\Entities\ProductVariant;
use Modules\Product\Entities\ProductImage;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Storage;

class ProductController extends Controller
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
        $dataList = Product::when($request->search, function($query, $search){
            $query->where('name', 'LIKE', '%' . $search . '%');
        })
        ->orderBy('id', 'desc')->paginate(6);
 
        return Inertia::render('Product::Product/index', [
            'dataList' => $dataList
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return Inertia::render('Product::Product/form');
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
            'name' => 'required',
            // 'description' => 'required',
            'category_id' => 'required',
        ];

        $pesan = [
            'name.required' => 'Nama Produk Wajib Diisi!',
            // 'description.required' => 'Deksripsi Produk Wajib Diisi!',
            // 'description.min' => 'Deksripsi Produk Terlalu Sedikit!',
            'category_id.required' => 'Kategori Produk Wajib Diisi!',
        ];

        if($request->has_variant == 0)
        {
            $rules['sell_price'] = 'required';
            $rules['purchase_price'] = 'required';

            $pesan['sell_price.required'] = 'Harga Jual Produk Wajib Diisi!';
            $pesan['purchase_price.required'] = 'Harga Beli Produk Wajib Diisi!';
        }else{
            $rules['var1_name'] = 'required';
            $rules['var1_value'] = 'required';

            $pesan['var1_nama.required'] = 'Nama Variasi Produk Wajib Diisi!';
            $pesan['var1_value.required'] = 'Pilihan Variasi Produk Wajib Diisi!';
            if($request->var2_name)
            {
                $rules['var2_name'] = 'required';
                $rules['var2_value'] = 'required';

                $pesan['var2_name.required'] = 'Nama Variasi Produk Wajib Diisi!';
                $pesan['var2_value.required'] = 'Pilihan Variasi Produk Wajib Diisi!';
            }
        }

        $validator = Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            return back()->withErrors($validator->errors());
        }else{
            DB::beginTransaction();
            try{
                $product = new Product();
                $product->name = $request->name;
                $product->description = $request->description;
                $product->category_id = $request->category_id;
                $product->has_variant = $request->has_variant;
                $product->save();

                if($request->has_variant == 0){
                    $variant = new ProductVariant();
                    $variant->sell_price = $request->sell_price;
                    $variant->purchase_price = $request->purchase_price;
                    $variant->sku = $request->sku;
                    $variant->product_id = $product->id;
                    $variant->save();
                }else{
                    $product->var1_name = $request->var1_name;
                    $product->var1_value = json_encode($request->var1_value);
                    if(!empty($request->var2_value)){
                        $product->var2_name = $request->var2_name;
                        $product->var2_value = json_encode($request->var2_value);
                    }
                    $product->save();
                    foreach($request->variant as $v){
                        $variant = new ProductVariant();
                        $variant->variant = $v["var1"];
                        if(!empty($request->var2_value)){
                            $variant->variant2 = $v["var2"];
                        }
                        $variant->sell_price = $v["sell_price"];
                        $variant->purchase_price = $v["purchase_price"];
                        $variant->sku = $v["sku"];
                        $variant->product_id = $product->id;
                        $variant->save();
                    }
                }
                $count = 0;
                foreach($request->images as $images){
                    $image = new ProductImage();
                    $image->product_id = $product->id;
                    $image->path =  $this->uploadImage($images, Str::slug($product->name, '-'), $product->id);
                    if($count == 0){
                        $image->is_utama = 1;
                    }else{
                        $image->is_utama = 0;
                    }
                    $image->save();
                    $count++;
                }
                
            }catch(\QueryException $e){
                DB::rollback();
                return back();
            }
            DB::commit();
            return back();
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        $data = Product::with(['images', 'category'])
        ->where('id', $id)->first();

        return Inertia::render('Product::Product/show', [
            'data' => $data
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $data = Product::with(['images', 'variant'])
        ->where('id', $id)->first();

        return Inertia::render('Product::Product/form', [
            'data' => $data,
            'editMode' => true,
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
        // dd($request->images);
        $rules = [
            'name' => 'required',
            // 'description' => 'required',
            'category_id' => 'required',
        ];

        $pesan = [
            'name.required' => 'Nama Produk Wajib Diisi!',
            // 'description.required' => 'Deksripsi Produk Wajib Diisi!',
            // 'description.min' => 'Deksripsi Produk Terlalu Sedikit!',
            'category_id.required' => 'Kategori Produk Wajib Diisi!',
        ];

        if($request->has_variant == 0)
        {
            $rules['sell_price'] = 'required';
            $rules['purchase_price'] = 'required';

            $pesan['sell_price.required'] = 'Harga Jual Produk Wajib Diisi!';
            $pesan['purchase_price.required'] = 'Harga Beli Produk Wajib Diisi!';
        }else{
            $rules['var1_name'] = 'required';
            $rules['var1_value'] = 'required';

            $pesan['var1_nama.required'] = 'Nama Variasi Produk Wajib Diisi!';
            $pesan['var1_value.required'] = 'Pilihan Variasi Produk Wajib Diisi!';
            if($request->var2_name)
            {
                $rules['var2_name'] = 'required';
                $rules['var2_value'] = 'required';

                $pesan['var2_name.required'] = 'Nama Variasi Produk Wajib Diisi!';
                $pesan['var2_value.required'] = 'Pilihan Variasi Produk Wajib Diisi!';
            }
        }

        $validator = Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            return back()->withErrors($validator->errors());
        }else{
            DB::beginTransaction();
            try{
                $product = Product::find($request->id);
                $product->name = $request->name;
                $product->description = $request->description;
                $product->category_id = $request->category_id;
                $product->has_variant = $request->has_variant;
                $product->save();

                if($request->has_variant == 0){
                    if($product->variant->count() > 1){
                        $product->variant()->delete();
                    }
                    $variant = new ProductVariant();
                    $variant->sell_price = $request->sell_price;
                    $variant->purchase_price = $request->purchase_price;
                    $variant->sku = $request->sku;
                    $variant->product_id = $product->id;
                    $variant->save();
                }else{
                    if($product->variant()->count() == 1){
                        $product->variant()->delete();
                    }
                    $product->var1_name = $request->var1_name;
                    $product->var1_value = json_encode($request->var1_value);
                    if(!empty($request->var2_value)){
                        $product->var2_name = $request->var2_name;
                        $product->var2_value = json_encode($request->var2_value);
                    }
                    $product->save();

                    foreach($request->variant as $v){
                        $params = [
                            'product_id' => $product->id,
                            'variant' => $v['var1'],
                        ];
                        if(!empty($request->var2_value)){
                            $params['variant2'] = $v["var2"];
                        }
                        ProductVariant::updateOrCreate($params,[
                            'sell_price' => $v["sell_price"],
                            'purchase_price' => $v["purchase_price"], 
                            'sku' => $v["sku"]
                        ]);
                    }
                }
                $count = 0;
                $old_images = [];
                $new_images = [];
                foreach($request->images as $images){
                    if(is_string($images)){
                        $old_images[] = $images;
                    }else{
                        $new_images[] = $images;
                    }
                }
                
                $old_image_del = ProductImage::where('product_id', $product->id)->whereNotIn('path', $old_images)->get();
                foreach($old_image_del as $del_image){
                    if($this->deleteImage($del_image->path));
                    {
                        $del_image->delete();
                    }
                }

                foreach($new_images as $images){
                    $image = new ProductImage();
                    $image->product_id = $product->id;
                    $image->path =  $this->uploadImage($images, Str::slug($product->name, '-'), $product->id);
                    if(ProductImage::where('product_id', $product->id)->where('is_utama', 1)->get()->count() > 0){
                        $image->is_utama = 0;
                    }else{
                        $image->is_utama = 1;
                    }
                    $image->save();
                    $count++;
                }
                
                
            }catch(\QueryException $e){
                DB::rollback();
                return back();
            }
            DB::commit();
            return back();
        }
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

    
    
    private function uploadImage($file, $name, $id){
        $file_name = $name. '-' . uniqid() . '.' . $file->getClientOriginalExtension();
        Storage::disk('public')->putFileAs(
            'uploads/product/images/'.$id,
            $file,
            $file_name
        );
        
        return 'uploads/product/images/'.$id.'/'.$file_name;
    }

    private function deleteImage($files){
        $cek = Storage::disk('public')->exists($files);
        if($cek)
        {
            Storage::disk('public')->delete($files);
            return true;
        }
        return false;
    }

}
