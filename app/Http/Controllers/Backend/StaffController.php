<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use App\Models\Admin;
use Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Storage;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class StaffController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
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
        $dataList = Admin::with('roles')
        ->when($request->search, function($query, $search){
            $query->where('name', 'LIKE', '%' . $search . '%')
            ->orWhere('email', 'LIKE', '%' . $search . '%');
        })
        ->orderBy('id', 'desc')->paginate(6);
 
        return Inertia::render('Backend/Settings/Staff/index', [
            'dataList' => $dataList
        ]);
    }

    
    public function profile(Request $request)
    {

        $data = Admin::where('id', auth()->guard('admin')->user()->id)->first();

        return Inertia::render('Backend/Settings/Staff/Profile', [
            'data' => $data,
        ]);
    }


    public function updateProfile(Request $request)
    {
        // dd($request->all());
        $rules = [
            'name' => 'required',
            'username' => 'required|unique:admins,username,'.$request->id,
            'email' => 'required|unique:admins,email,'.$request->id,
        ];

        $pesan = [
            'name.required' => 'Full Name must be filled.',
            'username.required' => 'Username must be filled.',
            'username.unique' => 'Username has already used.',
            'email.required' => 'Email must be filled.',
            'email.unique' => 'Email has already used.',
        ];

        $validator = Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            return back()->withErrors($validator->errors());
        }else{
            DB::beginTransaction();
            try{
                    $data = Admin::where('id', $request->id)->first();
                    $data->name = $request->name;
                    $data->username = $request->username;
                    $data->email = $request->email;
                    if($data->avatar != $request->avatar){
                        if(Storage::disk('public')->exists($data->avatar))
                        {
                            Storage::disk('public')->delete($data->avatar);
                        }
                        if($request->hasFile('avatar')){
                            $data->avatar = $this->uploadFiles($request->file('avatar'), Str::slug($request->username, '-'));
                        }else{
                            $data->avatar = null;
                        }
                    }
                    $data->save();

                    $data->assignRole($request->role);

            }catch(\QueryException $e){
                DB::rollback();
                return back();
            }
            DB::commit();
            return redirect()->route('admin.profile');
        }

    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $roles = Role::where('guard_name', 'admin')->latest()->get();

        return Inertia::render('Backend/Settings/Staff/Form',[
            'roles' => $roles,
            'editMode' => false
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
            'username' => 'required|unique:admins',
            'email' => 'required|unique:admins',
            'role' => 'required',
            'password' => 'required|min:6|required_with:password_confirm|same:password_confirm',
            'password_confirm' => 'required|min:6'
        ];

        $pesan = [
            'name.required' => 'Full Name must be filled.',
            'username.required' => 'Username must be filled.',
            'username.unique' => 'Username has already used.',
            'email.required' => 'Email must be filled.',
            'email.unique' => 'Email has already used.',
            'password.required' => 'Password must be filled.',
            'password.min' => 'Password must be 6-20 characters.',
            'password.same' => 'Password Confirmation does not match.',
            'password_confirm.required' => 'Password Confirmation must be filled.',
            'password_confirm.min' => 'Password must be 6-20 characters.',
            'role.required' => 'Staff Role must be filled',
        ];

        $validator = Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            return back()->withErrors($validator->errors());
        }else{
            DB::beginTransaction();
            try{
                    $data = new Admin();
                    $data->name = $request->name;
                    $data->username = $request->username;
                    $data->email = $request->email;
                    if(!empty($request->file('avatar'))){
                        $data->avatar = $this->uploadFiles($request->file('avatar'), Str::slug($request->username, '-'));
                    }
                    $data->email = $request->email;
                    $data->password = Hash::make($request->password);
                    $data->save();

                    $data->assignRole($request->role);

            }catch(\QueryException $e){
                DB::rollback();
                return back();
            }
            DB::commit();
            return redirect()->route('admin.settings.staff.index');
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $data = Admin::with(['roles'])->where('id', $id)->first();
        $roles = Role::where('guard_name', 'admin')->latest()->get();

        return Inertia::render('Backend/Settings/Staff/Form',[
            'roles' => $roles,
            'data' => $data,
            'editMode' => true
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
        $rules = [
            'name' => 'required',
            'username' => 'required|unique:admins,username,'.$request->id,
            'email' => 'required|unique:admins,email,'.$request->id,
            'role' => 'required',
        ];

        $pesan = [
            'name.required' => 'Full Name must be filled.',
            'username.required' => 'Username must be filled.',
            'username.unique' => 'Username has already used.',
            'email.required' => 'Email must be filled.',
            'email.unique' => 'Email has already used.',
            'role.required' => 'Staff Role must be filled',
        ];

        $validator = Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            return back()->withErrors($validator->errors());
        }else{
            DB::beginTransaction();
            try{
                    $data = Admin::where('id', $request->id)->first();
                    $data->name = $request->name;
                    $data->username = $request->username;
                    $data->email = $request->email;
                    if($data->avatar != $request->avatar){
                        if(Storage::disk('public')->exists($data->avatar))
                        {
                            Storage::disk('public')->delete($data->avatar);
                        }
                        if($request->hasFile('avatar')){
                            $data->avatar = $this->uploadFiles($request->file('avatar'), Str::slug($request->username, '-'));
                        }else{
                            $data->avatar = null;
                        }
                    }
                    $data->email = $request->email;
                    $data->save();

                    $data->assignRole($request->role);

            }catch(\QueryException $e){
                DB::rollback();
                return back();
            }
            DB::commit();
            return redirect()->route('admin.settings.staff.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        $data = Admin::where('id', $id)->first();
        $cek = Storage::disk('public')->exists($data->avatar);
        if($cek)
        {
            Storage::disk('public')->delete($data->avatar);
        }
        $hapus_db = Admin::destroy($data->id);
        if($hapus_db)
        {
            return redirect()->route('admin.settings.staff.index');
        }
    }

    
    private function uploadFiles($file, $name){
        $file_name = $name. '-' . uniqid() . '.' . $file->getClientOriginalExtension();
        Storage::disk('public')->putFileAs(
            'uploads/staff',
            $file,
            $file_name
        );
        
        return 'uploads/staff/'.$file_name;
    }

    
    public function password()
    {
        return Inertia::render('Backend/Settings/Staff/Password',[
        ]);
    }

    
    public function passwordUpdate(Request $request)
    {
        $rules = [
            'password' => 'required',
            'new_password' => 'required',
            'new_password_confirm' => 'required',
        ];

        $pesan = [
            'password.required' => 'Old password must be filled',
            'new_password.required' => 'New Password must be filled.',
            'new_password_confirm.required' => 'Password Confirmation must be filled.',
        ];

        $validator = Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            return back()->withErrors($validator->errors());
        }else{
            DB::beginTransaction();
            try{

                if (Hash::check($request->password, auth()->guard('web')->user()->password)) {
                    $data = Admin::find(auth()->guard('admin')->user()->id);
                    $data->password = Hash::make($request->new_password);
                    $data->save();
                }else{
                    return back()->withErrors([
                        'password' => ['Curent Password Wrong!']
                    ]);
                }

            }catch(\QueryException $e){
                DB::rollback();
                return back();
            }
            DB::commit();
            return redirect()->route('admin.password');
        }
    }
}
