<?php

namespace App\Http\Controllers\Backend;

use App\Models\Tax;


use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Storage;

class TaxController extends Controller
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
        $data = Tax::
        when($request->search, function($query, $search){
            $query->where('name', 'LIKE', '%' . $search . '%');
        })
        ->orderBy('id', 'desc')->paginate(6);
       

        return Inertia::render('Backend/Settings/Tax', [
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
            'amount' => 'required',
        ];

        $pesan = [
            'name.required' => 'Tax Name Must Be Filled!',
            'amount.required' => 'Tax Rates Must Be Filled!',
        ];

        $validator = Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            return back()->withErrors($validator->errors());
        }else{
            DB::beginTransaction();
            try{
                    $data = new Tax();
                    $data->name = $request->name;
                    $data->amount = $request->amount;
                    $data->save();

            }catch(\QueryException $e){
                DB::rollback();
                return back();
            }
            DB::commit();
            return redirect()->route('admin.settings.tax.index');
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
            'amount' => 'required',
        ];

        $pesan = [
            'name.required' => 'Tax Name Must Be Filled!',
            'amount.required' => 'Tax Rates Must Be Filled!',
        ];

        $validator = Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            return back()->withErrors($validator->errors());
        }else{
            DB::beginTransaction();
            try{
                    $data = Tax::find($request->id);
                    $data->name = $request->name;
                    $data->amount = $request->amount;
                    $data->save();

            }catch(\QueryException $e){
                DB::rollback();
                return back();
            }
            DB::commit();
            return redirect()->route('admin.settings.tax.index');
        }
    }


    
    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        $data = Tax::find($id);
        $remove_data = Tax::destroy($data->id);
        if($remove_data)
        {
            return redirect()->route('admin.settings.tax.index');
        }
    }

    public function data(Request $request)
    {
        $keyword = $request->q;
        $data = Tax::when($keyword, function($query, $search){
            $query->where('name', 'LIKE', '%' . $search . '%');
        })
        ->orderBy('created_at', 'DESC')->get();
        
        return response()->json($data);
    }
}