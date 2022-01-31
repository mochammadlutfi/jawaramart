<?php

namespace App\Http\Controllers\Admin\Auth;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Auth;
use Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Inertia\Inertia;
use Route;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating admin users for the application and
    | redirecting them to your admin dashboard.
    |
    */
    public function __construct()
    {
        $this->middleware('guest:admin')->except('logout');
    }

    /**
     * Show the login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        return Inertia::render('Admin/Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Login the admin.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $input = $request->all();

        $rules = [
            'email' => 'required|exists:admins,email|string',
            'password' => 'required|string'
        ];

        $pesan = [
            'email.required' => 'Alamat Email Wajib Diisi!',
            'email.exists' => 'Alamat Email Belum Terdaftar!',
            'password.required' => 'Password Wajib Diisi!',
        ];
        $validator = Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            // return 
            return back()->withErrors($validator->errors());
        }else{
            $fieldType = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
            if(auth()->guard('admin')->attempt($request->only('email','password')))
            {
                return redirect()->route('admin.dashboard');
            }else{
                $gagal['password'] = array('Password salah!');

                return back()->withErrors($gagal);
            }
        }
    }

    /**
     * Logout the admin.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }


    /**
     * Redirect back after a failed login.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    private function loginFailed(){
        return redirect()
            ->back()
            ->withInput()
            ->with('error','Login failed, please try again!');
    }

}

