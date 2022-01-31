<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
require __DIR__.'/auth.php';
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// Route::any('{all}', function () {
//     return view('layouts.app');
// })
// ->where(['all' => '.*']);

Route::get('/', function () {
    return Inertia::render('Frontend/home');
})->name('home');

Route::get('/p/{product}', 'Frontend\ProductController@show')->name('product.show');

Route::get('/cart', function () {
    return Inertia::render('Frontend/cart');
})->name('cart');

// Route::get('/dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware('auth')->name('dashboard');

// Route::get('/pages/1', function () {
//     return Inertia::render('Dashboard');
// })->middleware('auth')->name('pages.1.1');

// Route::get('/pages/2', function () {
//     return Inertia::render('Dashboard');
// })->middleware('auth')->name('pages.2.1');
// Route::get('/profile', 'UserController@profile')->middleware('auth')->name('user.profile');

// Route::post('/updateProfile', 'UserController@updateProfile')->middleware('auth')->name('user.profile.update');

// Route::group(['prefix' => 'settings', 'middleware' => 'auth','as'=>'settings.'], function () {
//     Route::group(['prefix' => 'users'], function () {
//         Route::get('/', 'UserController@index')->name('user.index');
//     });
// });

Route::namespace('Frontend')->group(function(){
    // Auth::routes(); 
    Auth::routes(['verify'=>true]);

    

    Route::group(['prefix' => 'cart'], function () {
        Route::get('/', 'CartController@index')->name('cart.index');
        Route::get('/data', 'CartController@data')->name('cart.data');
        Route::post('/store','CartController@store')->name('cart.store');
        Route::post('/update','CartController@update')->name('cart.update');
        Route::delete('/delete/{id}','CartController@destroy')->name('cart.delete');

        Route::match(['get', 'post'], '/shipment','CheckoutController@index')->name('checkout');
    });
});


// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// MediaManager