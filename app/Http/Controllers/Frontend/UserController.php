<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Storage;

use App\Models\User;
use App\Models\Sale;
use App\Models\UserAddress;
class UserController extends Controller
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

    
    public function dashboard()
    {
        $user_id = auth()->guard('web')->user()->id;

        $sale_active = Sale::where('customer_id', $user_id)->where('status', '!=', 'done')->get()->count();
        $sale_done = Sale::where('customer_id', $user_id)->where('status', 'done')->get()->count();
        $total_belanja = Sale::where('customer_id', $user_id)->whereIn('status', array('confirmed', 'delivery', 'done'))->sum('grand_total');

        $overview = collect([
            [
                'title' => 'Pesanan Berlangsung',
                'data' => $sale_active,
                'icon' => 'fa fa-spin fa-spinner',
                'color' => 'text-warning',
                'type' => 'count',
                'url' => route('user.order.index')
            ],
            [
                'title' => 'Pesanan Selesai',
                'data' => $sale_done,
                'icon' => 'fa fa-check',
                'color' => 'text-success',
                'type' => 'count',
                'url' => route('user.order.index', ['status' => 'selesai'])
            ],
            [
                'title' => 'Total Belanja',
                'data' => $total_belanja,
                'color' => 'text-primary',
                'icon' => 'fa fa-money-bill',
                'type' => 'money',
                'url' => null,
            ],
        ]);

        $address = UserAddress::where('user_id', $user_id)->where('is_primary', 1)->first();

        return Inertia::render('Frontend/User/Dashboard',[
            'overview' => $overview,
            'address' => $address
        ]);

    }

    
    public function settings()
    {

        $data = Collect([
            [
                'title' => 'Profil',
                'sub_title' => 'Pengaturan informasi data diri',
                'icon_type' => 'image',
                'icon_src' => 'https://blue.kumparan.com/uikit-assets/assets/icons/profile-a511c2f486f99dfa2abdf99e78a848a1.svg',
                'route' => route('user.settings.profile'),
            ],
            [
                'title' => 'Alamat Email',
                'sub_title' => auth()->guard('web')->user()->email,
                'icon_type' => 'image',
                'icon_src' => 'https://blue.kumparan.com/uikit-assets/assets/icons/mail-5c0af36ee287b48288238ba4cbbe7da4.svg',
                'route' => route('user.settings.email'),
            ],
            [
                'title' => 'No Handphone',
                'sub_title' => 'Belum Ditambahkan',
                'icon_type' => 'image',
                'icon_src' => 'https://blue.kumparan.com/uikit-assets/assets/icons/smartphone-black-56938dcd68fd5ff772e52d4ebf7f6161.svg',
                'route' => route('user.settings.phone'),
            ],
            [
                'title' => 'Password',
                'sub_title' => '*****',
                'icon_type' => 'image',
                'icon_src' => 'https://blue.kumparan.com/uikit-assets/assets/icons/lock-73423076303023c063ea367b5823ca3e.svg',
                'route' => route('user.settings.password'),
            ],
        ]);

        return Inertia::render('Frontend/User/Settings/Index',[
            'dataList' => $data,
        ]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function profile()
    {
        $data = User::select('name', 'email', 'phone', 'avatar')
        ->where('id', auth()->guard('web')->user()->id)->first();

        return Inertia::render('Frontend/User/Settings/Profile',[
            'data' => $data,
        ]);
    }
    
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function updateProfil(Request $request)
    {
        $rules = [
            'name' => 'required',
        ];

        $pesan = [
            'name.required' => 'Full Name must be filled.',
        ];

        $validator = Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            return back()->withErrors($validator->errors());
        }else{
            DB::beginTransaction();
            try{
                    $data = User::where('id', auth()->guard('web')->user()->id)->first();
                    $data->name = $request->name;
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

            }catch(\QueryException $e){
                DB::rollback();
                return back();
            }
            DB::commit();
            return redirect()->route('user.settings.profile');
        }
    }

    
    private function uploadFiles($file, $name){
        $file_name = $name. '-' . uniqid() . '.' . $file->getClientOriginalExtension();
        Storage::disk('public')->putFileAs(
            'uploads/customer',
            $file,
            $file_name
        );
        
        return 'uploads/customer/'.$file_name;
    }


    public function email()
    {
        $data = User::select('name', 'email', 'phone', 'avatar')
        ->where('id', auth()->guard('web')->user()->id)->first();

        return Inertia::render('Frontend/User/Settings/Email',[
            'data' => $data,
        ]);
    }

    public function validate(Request $request)
    {
        $rules = [
            'password' => 'required|min:6',
        ];

        $pesan = [
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password kurang dari 6 karakter!',
        ];

        $validator = Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            return response()->json([
                'success'=> false,
                'message' => $validator->errors()
            ]);
        }else{
            $user = User::where('email', auth()->guard('web')->user()->email)->first();

            if (!Hash::check($request->password, $user->password)) {
                $error['password'] = array('Password salah!');
                return response()->json([
                    'success'=> false,
                    'message' => $error
                ]);
             }
            return response()->json([
                'success' => true, 
                'message'=>'success'
            ]);
        }
    }

    
    public function emailUpdate(Request $request)
    {
        // dd($request->email);
        $rules = [
            'email' => 'required|unique:users,email|email',
        ];

        $pesan = [
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Alamat email sudah digunakan.',
            'email.email' => 'Format alamat email salah.',
        ];

        $validator = Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            return back()->withErrors($validator->errors());
        }else{
            DB::beginTransaction();
            try{

                    $data = User::where('email', auth()->guard('web')->user()->email)->first();
                    $data->email = $request->email;
                    $data->email_verified_at = null;
                    $data->save();
                    
                    $data->sendEmailVerificationNotification();

            }catch(\QueryException $e){
                DB::rollback();
                return back();
            }
            DB::commit();
            return redirect()->route('user.profile');
        }
    }

    
    public function phone()
    {
        $data = User::select('name', 'email', 'phone', 'avatar')
        ->where('id', auth()->guard('web')->user()->id)->first();

        return Inertia::render('Frontend/User/Settings/Phone',[
            'data' => $data,
        ]);
    }


    public function password()
    {
        return Inertia::render('Frontend/User/Settings/Password',[
        ]);
    }



}
