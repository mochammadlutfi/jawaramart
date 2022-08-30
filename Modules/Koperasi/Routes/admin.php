<?php
use Inertia\Inertia;
/*
|--------------------------------------------------------------------------
| Web Routes For Staff / Backend
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('/admin/koperasi')->name('admin.kop.')->group(function() {

    Route::prefix('anggota')->name('anggota.')->group(function() {
        Route::get('/', 'AnggotaController@index')->name('index');
        Route::get('/create', 'AnggotaController@create')->name('create');
        Route::post('/store','AnggotaController@store')->name('store');
        Route::get('/edit/{id}', 'AnggotaController@edit')->name('edit');
        Route::post('/update','AnggotaController@update')->name('update');
        Route::get('/json','AnggotaController@json')->name('json');
    });

    
    Route::group(['prefix' => 'simpanan','namespace' => 'Simpanan', 'as' => 'simpanan.'], function () {

        Route::group(['prefix' => 'wajib', 'as' => 'wajib.'], function () {
            Route::get('/', 'WajibController@index')->name('index');
            Route::get('/create', 'WajibController@create')->name('create');
            Route::post('/store','WajibController@store')->name('store');
            Route::get('/tunggakan', 'WajibController@tunggakan')->name('tunggakan');
            Route::get('/edit/{id}', 'WajibController@edit')->name('edit');
            Route::post('/update','WajibController@update')->name('update');
            Route::get('/detail/{id}', 'WajibController@show')->name('show');
            Route::get('/paid/{id}', 'WajibController@paid')->name('paid');
        });


        Route::group(['prefix' => 'sukarela', 'as' => 'sukarela.'], function () {
            Route::get('/', 'SukarelaController@index')->name('index');
            Route::get('/create', 'SukarelaController@create')->name('create');
            Route::post('/store','SukarelaController@store')->name('store');
            Route::get('/edit/{id}', 'SukarelaController@edit')->name('edit');
            Route::post('/update','SukarelaController@update')->name('update');
            Route::get('/show/{id}', 'SukarelaController@show')->name('show');
        });
        
    });
});
