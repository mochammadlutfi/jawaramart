<?php

namespace App\Http\Controllers\Frontend;


use App\Models\UserAddress;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserAddressController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $user_id = auth()->guard('web')->user()->id;

        $data = UserAddress::where('user_id', $user_id)->orderBy('is_primary', 'DESC')->get();

        return Inertia::render('Frontend/User/Address',[
            'dataList' => $data,
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
            'reciever' => 'required',
            'phone' => 'required',
            'reciever' => 'required',
            'area_id' => 'required',
            'address' => 'required',
        ];

        $pesan = [
            'name.required' => 'Label Alamat Wajib Diisi!',
            'reciever.required' => 'Nama Penerima Wajib Diisi!',
            'phone.required' => 'No Handphone Penerima Wajib Diisi!',
            'area_id.required' => 'Wilayah Wajib Diisi!',
            'address.required' => 'Alamat Lengkap Wajib Diisi!',
        ];

        $validator = Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            if($request->axios){
                return response()->json([
                    'errors' => $validator->errors(),
                    'fail' => true,
                ], 200);
            }
            return back()->withErrors($validator->errors());
            
        }else{
            DB::beginTransaction();
            try{

                $user_id = auth()->guard('web')->user()->id;
                $check = UserAddress::where('user_id', $user_id)->get()->count();
                $is_main = ($check > 1 ) ? 0 : 1; 

                $data = new UserAddress();
                $data->user_id = auth()->guard('web')->user()->id;
                $data->name = $request->name;
                $data->reciever = $request->reciever;
                $data->phone = $request->phone;
                $data->area_id = $request->area_id;
                $data->area_text = $request->area_text;
                $data->address = $request->address;
                $data->postal_code = $request->postal_code;
                $data->is_primary = $is_main;
                $data->save();

            }catch(\QueryException $e){
                DB::rollback();
                return back();
            }
            DB::commit();
            if($request->axios){
                return response()->json([
                    'data' => $data,
                    'fail' => false,
                ], 200);
            }
            return redirect()->route('user.address.index');
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
            'reciever' => 'required',
            'phone' => 'required',
            'reciever' => 'required',
            'area_id' => 'required',
            'address' => 'required',
        ];

        $pesan = [
            'name.required' => 'Label Alamat Wajib Diisi!',
            'reciever.required' => 'Nama Penerima Wajib Diisi!',
            'phone.required' => 'No Handphone Penerima Wajib Diisi!',
            'area_id.required' => 'Wilayah Wajib Diisi!',
            'address.required' => 'Alamat Lengkap Wajib Diisi!',
        ];

        $validator = Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            return back()->withErrors($validator->errors());
        }else{
            DB::beginTransaction();
            try{
                    $data = UserAddress::find($request->id);
                    $data->user_id = auth()->guard('web')->user()->id;
                    $data->name = $request->name;
                    $data->reciever = $request->reciever;
                    $data->phone = $request->phone;
                    $data->area_id = $request->area_id;
                    $data->area_text = $request->area_text;
                    $data->address = $request->address;
                    $data->postal_code = $request->postal_code;
                    $data->save();

            }catch(\QueryException $e){
                DB::rollback();
                return back();
            }
            DB::commit();
            return redirect()->route('user.address.index');
        }
    }
    
    public function data(Request $request)
    {
        $search = $request->search;

        $user_id = auth()->guard('web')->user()->id;
        $data = UserAddress::where('user_id', $user_id)->orderBy('is_primary', 'DESC')
        ->when($search, function($query, $search){
            $query->where('name', 'LIKE', '%' . $search . '%')
            ->orWhere('address', 'LIKE', '%' . $search . '%')
            ->orWhere('reciever', 'LIKE', '%' . $search . '%')
            ->orWhere('phone', 'LIKE', '%' . $search . '%')
            ->orWhere('area_text', 'LIKE', '%' . $search . '%');
        })
        ->get();

        if($data){
            return response()->json([
                'data' => $data,
                'fail' => false,
            ], 200);
        }else{
            return response()->json([
                'message' => "Address Not Found",
                'fail' => false,
            ], 400);
        }
    }

    public function main(Request $request)
    {
        $id = $request->id;
        DB::beginTransaction();
        try{
            $user_id = auth()->guard('web')->user()->id;
            $default = UserAddress::where('user_id', $user_id)->update(['is_primary' => 0]);
    
            $primary = UserAddress::where('user_id', $user_id)->where('id', $id)->update(['is_primary' => 1]);

        }catch(\QueryException $e){
            DB::rollback();
            return back();
        }
        DB::commit();
        return redirect()->route('user.address.index');
    }

        /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try{
            $hapus_db = UserAddress::destroy($id);
        }catch(\QueryException $e){
            DB::rollback();
            return back();
        }
        DB::commit();
        return redirect()->route('user.address.index');
    }
}
