<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::namespace('API')->name('api.')->group(function(){
    Route::namespace('v1')->group(function(){
        Route::get('/slider', 'SliderController@index')->name("slider");

        Route::prefix('products')->group(function() {
            Route::get('/', 'ProductController@index')->name("product.index");
    
            Route::group(['prefix' => 'category'], function () {
                Route::get('/', 'ProductCategoryController@index')->name('product.category');
            });
    
            // Route::group(['prefix' => 'brands'], function () {
            //     Route::get('/', 'ProductBrandController@index')->name('product.brand.index');
            // });
    
        });

        Route::group(['prefix' => 'cart'], function () {
            Route::get('/', 'CartController@index')->name('cart.index');
            Route::post('/store','CartController@store')->name('cart.store');
            Route::post('/update','CartController@update')->name('cart.update');
            Route::delete('/delete/{id}','CartController@destroy')->name('cart.delete');
        });

    });
});