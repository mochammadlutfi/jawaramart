<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use DB;
use Str;
class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    public function showResetForm($token, Request $request)
    {
        return view("website.auth.passwords.reset", compact("token"));
    }


    public function reset(Request $request)
    {
        // dd($request->all());
        $rules = [
            'password' => 'required|string|confirmed'
        ];

        $pesan = [
            'password.required' => 'Password Baru Wajib Diisi!',
            'password.confirmed' => 'Password baru yang dimasukan tidak sama!',
        ];
        $validator = Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            return response()->json([
                'fail' => true,
                'msg' => 'Terdapat Kesalahan Di Form!',
                'errors' => $validator->errors(),
            ]);
        }else{
            
            // DB::beginTransaction();
            // try{

                $credentials = array(
                    'token' => $request->token,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                );

                $status = Password::reset($credentials,
                    function ($user, $password) {
                        $user->forceFill([
                            'password' => Hash::make($password)
                        ])->setRememberToken(Str::random(60));
                        $user->save();
                    }
                );
                
                if($status === Password::PASSWORD_RESET){
                    return response()->json([
                        'fail' => false,
                    ]);
                }else{
                    return response()->json([
                        'fail' => true,
                        'status' => $status,
                    ]);
                }
                // ? redirect()->route('login')->with('status', __($status))
                // : back()->withErrors(['email' => [__($status)]]);
                // dd($status);

                // $reset_password_status = Password::reset($credentials, function ($user, $password) {
                    // $user->password = $password;
                    // $user->save();
                // });
            // }catch(\QueryException $e){
            //     DB::rollback();
            //     return response()->json([
            //         'fail' => true,
            //         'pesan' => 'Error Menyimpan Data',
            //         'log' => $e,
            //     ]);
            // }

            // DB::commit();
            // return response()->json([
            //     'fail' => false,
            // ]);
        }
    }
}
