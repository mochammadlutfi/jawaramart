<?php

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

Route::prefix('product')->group(function() {
    Route::get('/', 'ProductController@index');
});

// Route::prefix('/admin')->name('admin.')->group(function(){
//     Route::prefix('products')->group(function() {
//         Route::get('/', 'ProductController@index')->name("product.index");
//         Route::get('/create', 'ProductController@create')->name("product.create");
//         Route::post('/store','ProductController@store')->name('product.store');
//         Route::get('/show/{id}','ProductController@show')->name('product.show');
//         Route::get('/edit/{id}','ProductController@edit')->name('product.edit');
//         Route::post('/update','ProductController@update')->name('product.update');
//         Route::delete('/delete/{id}','ProductController@destroy')->name('product.delete');



//         Route::group(['prefix' => 'category'], function () {
//             Route::get('/', 'ProductCategoryController@index')->name('product.category.index');
//             Route::get('/data', 'ProductCategoryController@data')->name('product.category.data');
//             Route::post('/store','ProductCategoryController@store')->name('product.category.store');
//             Route::get('/edit/{id}','ProductCategoryController@edit')->name('product.category.edit');
//             Route::post('/update','ProductCategoryController@update')->name('product.category.update');
//             Route::delete('/delete/{id}','ProductCategoryController@destroy')->name('product.category.delete');
//             Route::delete('/delete/{id}/destroy_files','ProductCategoryController@deleteFiles')->name('product.category.deleteFiles');
//             Route::get('/extract','ProductCategoryController@extractTree')->name('product.category.extractTree');
//         });



//         Route::group(['prefix' => 'brands'], function () {
//             Route::get('/', 'ProductBrandController@index')->name('product.brand.index');
//             Route::get('/data', 'ProductBrandController@data')->name('product.brand.data');
//             Route::post('/store','ProductBrandController@store')->name('product.brand.store');
//             Route::post('/update','ProductBrandController@update')->name('product.brand.update');
//             Route::delete('/delete/{id}','ProductBrandController@destroy')->name('product.brand.delete');
//         });

//     });
    
// });