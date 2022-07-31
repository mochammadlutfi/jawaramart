<?php
header('Access-Control-Allow-Origin:  *');
header('Access-Control-Allow-Methods:  POST, GET, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Origin, Authorization');
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
require __DIR__.'/auth.php';
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

Route::get('/', function () {
    return Inertia::render('Frontend/home');
})->name('home');

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

    Route::get('/search', 'HomeController@search')->name('search');
    Route::post('/search-autocomplete', 'HomeController@autocomplete')->name('search.autocomplete');

    Route::name('product.')->group(function () {
        Route::get('/p/{product}', 'ProductController@show')->name('show');
        Route::get('/category/{category_slug}', 'ProductController@listingByCategory')->name('category');
    });

    Route::name('cart.')->prefix('cart')->group(function () {
        Route::get('/', 'CartController@index')->name('index');
        Route::get('/data', 'CartController@data')->name('data');
        Route::post('/store','CartController@store')->name('store');
        Route::post('/update','CartController@update')->name('update');
        Route::delete('/delete/{id}','CartController@destroy')->name('remove');
        Route::post('/removeSelected','CartController@destroySelected')->name('removeSelected');
    });


    Route::name('checkout.')->prefix('checkout')->group(function () {
        Route::match(['get', 'post'], '/shipment','CheckoutController@index')->name('shipping');
        Route::match(['get', 'post'], '/payment','CheckoutController@payment')->name('payment');
        Route::post('/store','CheckoutController@store')->name('store');
    });
    
    Route::name('user.')->prefix('user')->group(function () {

        Route::get('/', function () {
            return redirect()->route('user.profile');
        });

        Route::get('/dashboard','UserController@dashboard')->name('dashboard');

        // Route::get('/order','OrderController@index')->name('pesanan');
        // Route::get('/pesanan/invoice/{invoice_no}','OrderController@invoice')->name('invoice');

        Route::name('address.')->prefix('address')->group(function () {
            Route::get('/','UserAddressController@index')->name('index');
            Route::get('/create','UserAddressController@create')->name('create');
            Route::post('/store', 'UserAddressController@store')->name('store');
            Route::get('/edit/{id}', 'UserAddressController@edit')->name('edit');
            Route::post('/update', 'UserAddressController@update')->name('update');
            Route::delete('/delete/{id}', 'UserAddressController@destroy')->name('delete');
            Route::get('/data','UserAddressController@data')->name('data');
            Route::post('/set-main', 'UserAddressController@main')->name('main');
        });

        Route::name('wishlist.')->prefix('wishlist')->group(function () {
            Route::get('/','WishlistController@index')->name('index');
            Route::post('/store', 'WishlistController@store')->name('store');
            Route::get('/edit/{id}', 'WishlistController@edit')->name('edit');
            Route::post('/update', 'WishlistController@update')->name('update');
            Route::delete('/delete/{id}', 'WishlistController@destroy')->name('delete');
        });

        Route::name('order.')->prefix('order')->middleware(['cors'])->group(function () {
            Route::get('/list','UserOrderController@index')->name('index');
            Route::get('/detail/{order}','UserOrderController@show')->name('show');
            Route::get('/payment', 'UserOrderController@payment')->name('payment');
            Route::post('/confirm', 'UserOrderController@confirm')->name('confirm');
            Route::post('/payment-update', 'UserOrderController@payment_update')->name('payment.update');
            Route::get('/payment/{order}', 'UserOrderController@payment_show')->name('payment.show');
            Route::post('/store', 'UserOrderController@store')->name('store');
        });

        Route::name('settings.')->prefix('settings')->group(function () {
            Route::get('/', 'UserController@settings')->name('index');

            Route::get('/profile', 'UserController@profile')->name('profile');
            Route::post('/profile/update', 'UserController@updateProfil')->name('profile.update');

            Route::get('/email','UserController@email')->name('email');
            Route::post('/validate', 'UserController@validate')->name('validate');
            Route::post('/email/update', 'UserController@emailUpdate')->name('email.update');

            Route::get('/phone','UserController@phone')->name('phone');
            Route::post('/phone/update', 'UserController@phoneUpdate')->name('phone.update');

            Route::get('/password','UserController@password')->name('password');
            Route::post('/password/update', 'UserController@passwordUpdate')->name('password.update');
        });
    });

    Route::name('shipper.')->prefix('shipper')->group(function () {
        Route::get('/sub_urban','ShipperController@getSubUrban')->name('sub_urban');
        Route::post('/store', 'ShipperController@store')->name('store');
    });

});