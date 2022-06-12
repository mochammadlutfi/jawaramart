<?php

namespace App\Http\Controllers\Backend;

use App\Models\Warehouse;


use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Storage;

class WarehouseController extends Controller
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
        $search = $request->search;
        $data = Warehouse::
        when($search, function($query, $search){
            $query->where('name', 'LIKE', '%'. $search .'%');
        })
        ->orderBy('id', 'desc')->paginate(10);
       

        return Inertia::render('Backend/Settings/Warehouse', [
            'dataList' => fn () => $data
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
        ];

        $pesan = [
            'name.required' => 'Warehouse Name Must Be Filled!',
        ];

        $validator = Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            return back()->withErrors($validator->errors());
        }else{
            DB::beginTransaction();
            try{
                    $data = new Warehouse();
                    $data->name = $request->name;
                    $data->save();

            }catch(\QueryException $e){
                DB::rollback();
                return back();
            }
            DB::commit();
            return redirect()->route('admin.settings.warehouse.index');
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
            'name.required' => 'Tax Name Must Be Filled!',
        ];

        $validator = Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            return back()->withErrors($validator->errors());
        }else{
            DB::beginTransaction();
            try{
                    $data = Warehouse::find($request->id);
                    $data->name = $request->name;
                    $data->status = $request->status;
                    $data->save();

            }catch(\QueryException $e){
                DB::rollback();
                return back();
            }
            DB::commit();
            return redirect()->route('admin.settings.warehouse.index');
        }
    }


    
    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        $data = Warehouse::find($id);
        $remove_data = Warehouse::destroy($data->id);
        if($remove_data)
        {
            return redirect()->route('admin.settings.warehouse.index');
        }
    }

    public function data(Request $request)
    {
        $keyword = $request->q;
        $data = Warehouse::when($keyword, function($query, $search){
            $query->where('name', 'LIKE', '%' . $search . '%');
        })
        ->orderBy('created_at', 'DESC')->get();
        
        return response()->json($data);
    }
}