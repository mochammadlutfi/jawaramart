<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;

use App\Helpers\MenuHelp;
class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request)
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request)
    {
        
        $data = [
            'flash' => [
                'message' => fn () => $request->session()->get('message')
            ],
            'auth' => [
                'staff' => fn () => $request->user('admin')
                ? $request->user('admin')->only('name', 'email', 'avatar', 'avatar_url')
                : null,
                'user' => fn () => $request->user()
                ? $request->user()->only('name', 'email', 'avatar', 'avatar_url')
                : null,
            ],
            'errors' => fn() => $this->sharedValidationErrors(),
            'settings' => fn() => settings()->all(),
        ];

        if(Auth::guard('web')->check()){
            $data['cart'] = Cart::with('product', 'variant')
            ->where('user_id', auth()->guard('web')->user()->id)
            ->orderBy('created_at', 'DESC')->get();
        }else{
            $data['cart'] = [];
        }

        if(Auth::guard('admin')->check()){
            $data['menu'] = MenuHelp::mainMenu();
            $data['modules'] = MenuHelp::permission();
        }

        return array_merge(parent::share($request), $data);
    }

    protected function sharedValidationErrors(){
        if($errors = session('errors')){
            return $errors->getBag('default');
        }
        return (object)[];
    }
}
