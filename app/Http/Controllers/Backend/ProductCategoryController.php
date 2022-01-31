<?php

namespace App\Http\Controllers\Backend;



use App\Models\ProductCategory;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Storage;

class ProductCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
    
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $items = ProductCategory::select('id', 'name as text', 'parent_id', 'thumbnail')->get();
        $children = array();

        foreach($items as $item)
            $children[$item->parent_id][] = $item;

        foreach($items as $item)
            if (isset($children[$item->id]))
            $item->children = $children[$item->id];

        $dataList = reset($children);
        // dd($children);

        return Inertia::render('Backend/Category/index', [
            'dataList' => fn () => $dataList
        ]);
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
        ];

        $pesan = [
            'name.required' => 'Nama Kategori Wajib Diisi!',
        ];

        $validator = Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            return back()->withErrors($validator->errors());
        }else{
            DB::beginTransaction();
            try{
                    $data = new ProductCategory();
                    $data->name = $request->name;
                    $data->parent_id = $request->parent_id;
                    
                    if(!empty($request->file('image'))){
                        $data->thumbnail = $this->uploadFiles($request->file('image'), Str::slug($request->name, '-'));
                    }
                    $data->save();
                    ProductCategory::fixTree();
            }catch(\QueryException $e){
                DB::rollback();
                return back();
            }
            DB::commit();
            return redirect()->route('admin.product.category.index');
        }
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
            'name' => 'required',
        ];

        $pesan = [
            'name.required' => 'Nama Kategori Wajib Diisi!',
        ];

        $validator = Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            return back()->withErrors($validator->errors());
        }else{
            DB::beginTransaction();
            try{
                    $data = ProductCategory::find($request->id);
                    $data->name = $request->name;
                    $data->parent_id = $request->parent_id;
                    if($data->thumbnail != $request->image){
                        if(Storage::disk('public')->exists($data->thumbnail))
                        {
                            Storage::disk('public')->delete($data->thumbnail);
                        }
                        if($request->hasFile('image')){
                            $data->thumbnail = $this->uploadFiles($request->file('image'), Str::slug($request->name, '-'));
                        }else{
                            $data->thumbnail = null;
                        }
                    }
                    $data->save();
                    ProductCategory::fixTree();
            }catch(\QueryException $e){
                DB::rollback();
                return response()->json([
                    'fail' => true,
                    'pesan' => $e,
                ]);
            }
            DB::commit();
            return redirect()->route('admin.product.category.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        $data = ProductCategory::find($id);
        $cek = Storage::disk('public')->exists($data->thumbnail);
        if($cek)
        {
            Storage::disk('public')->delete($data->thumbnail);
        }
        $hapus_db = ProductCategory::destroy($data->id);
        if($hapus_db)
        {
            return redirect()->route('admin.product.category.index');
        }
    }

    
    private function uploadFiles($file, $name){
        $file_name = $name. '-' . uniqid() . '.' . $file->getClientOriginalExtension();
        Storage::disk('public')->putFileAs(
            'uploads/product/category',
            $file,
            $file_name
        );
        
        return 'uploads/product/category/'.$file_name;
    }

    public function data(Request $request)
    {
        $data = ProductCategory::withCount('child')
        ->where('parent_id', $request->category_id)
        ->orderBy('name', 'ASC')
        ->get();

        return response()->json($data);
    }

    public function extractTree(Request $request)
    {
        $data = ProductCategory::withCount('child')
        ->where('id', $request->category_id)
        ->first();
        if($data->level == 2){
            $response = collect([
                'parent' => $data->parent->parent,
                'child' => $data->parent,
                'grandchild' => $data,
            ]);
        }else if($data->level == 1){
            $response = collect([
                'parent' => $data->parent,
                'child' => $data,
            ]);
        }else{
            $response = collect([
                'parent' => $data,
            ]);
        }

        return response()->json($response);
    }
}
