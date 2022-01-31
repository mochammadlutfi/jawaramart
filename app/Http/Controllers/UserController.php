<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use Auth;
use Illuminate\Support\Facades\Redirect;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $dataList = User::when($request->search, function($query, $search){
            $query->where('name', 'LIKE', '%' . $search . '%')
            ->orWhere('email', 'LIKE', '%' . $search . '%');
        })
        ->orderBy('id', 'desc')->paginate(6);
 
        return Inertia::render('Users/Index', [
            'dataList' => $dataList
        ]);
    }


    public function profile(Request $request)
    {

        $user = Auth::user();

        return Inertia::render('Users/Profile', [
            'user' => $user->only('name', 'email'),
        ]);
    }


    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        if($user){
            return redirect()->back()->with('message', 'Data Berhasil Diupdate!');
        }

    }

}
