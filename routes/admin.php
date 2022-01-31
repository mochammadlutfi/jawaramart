<?php 

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::prefix('/admin')->name('admin.')->namespace('Backend')->group(function(){


    Route::namespace('Auth')->group(function(){

        //Login Routes
        Route::get('/','LoginController@showLoginForm');
        Route::get('/login','LoginController@showLoginForm')->name('login');
        Route::post('/login','LoginController@login')->name('loginPost');
        Route::post('/logout','LoginController@logout')->name('logout');

        //Forgot Password Routes
        Route::get('/password/reset','ForgotPasswordController@showLinkRequestForm')->name('password.request');
        Route::post('/password/email','ForgotPasswordController@sendResetLinkEmail')->name('password.email');

        //Reset Password Routes
        Route::get('/password/reset/{token}','ResetPasswordController@showResetForm')->name('password.reset');
        Route::post('/password/reset','ResetPasswordController@reset')->name('password.update');

        // // Email Verification Route(s)
        Route::get('email/verify','VerificationController@show')->name('verification.notice');
        Route::get('email/verify/{id}','VerificationController@verify')->name('verification.verify');
        Route::get('email/resend','VerificationController@resend')->name('verification.resend');
    });

    Route::group(['middleware' => 'auth:admin'], function () {
        Route::get('/dashboard','HomeController@index')->name('dashboard');

        Route::group(['prefix' => 'customer'], function () {
            Route::get('/', 'CustomerController@index')->name('customer.index');
            Route::get('/data', 'CustomerController@data')->name('customer.data');
            Route::post('/store','CustomerController@store')->name('customer.store');
            Route::get('/edit/{id}','CustomerController@edit')->name('customer.edit');
            Route::post('/update','CustomerController@update')->name('customer.update');
            Route::delete('/delete/{id}','CustomerController@destroy')->name('customer.delete');
        });

        Route::prefix('products')->group(function() {
            Route::get('/', 'ProductController@index')->name("product.index");
            Route::get('/create', 'ProductController@create')->name("product.create");
            Route::post('/store','ProductController@store')->name('product.store');
            Route::get('/show/{id}','ProductController@show')->name('product.show');
            Route::get('/edit/{id}','ProductController@edit')->name('product.edit');
            Route::post('/update','ProductController@update')->name('product.update');
            Route::delete('/delete/{id}','ProductController@destroy')->name('product.delete');
    
            Route::group(['prefix' => 'category'], function () {
                Route::get('/', 'ProductCategoryController@index')->name('product.category.index');
                Route::get('/data', 'ProductCategoryController@data')->name('product.category.data');
                Route::post('/store','ProductCategoryController@store')->name('product.category.store');
                Route::get('/edit/{id}','ProductCategoryController@edit')->name('product.category.edit');
                Route::post('/update','ProductCategoryController@update')->name('product.category.update');
                Route::delete('/delete/{id}','ProductCategoryController@destroy')->name('product.category.delete');
                Route::delete('/delete/{id}/destroy_files','ProductCategoryController@deleteFiles')->name('product.category.deleteFiles');
                Route::get('/extract','ProductCategoryController@extractTree')->name('product.category.extractTree');
            });
    
            Route::group(['prefix' => 'brands'], function () {
                Route::get('/', 'ProductBrandController@index')->name('product.brand.index');
                Route::get('/data', 'ProductBrandController@data')->name('product.brand.data');
                Route::post('/store','ProductBrandController@store')->name('product.brand.store');
                Route::post('/update','ProductBrandController@update')->name('product.brand.update');
                Route::delete('/delete/{id}','ProductBrandController@destroy')->name('product.brand.delete');
            });
    
        });

        Route::prefix('settings')->group(function() {
            Route::get('/', 'SettingsController@index')->name("settings.general.index");

            Route::group(['prefix' => 'staff'], function () {
                Route::get('/', 'StaffController@index')->name('settings.staff.index');
            });

            Route::group(['prefix' => 'roles'], function () {
                Route::get('/', 'RolesController@index')->name('settings.roles.index');
                Route::get('/create', 'RolesController@create')->name('settings.roles.create');
                Route::post('/store','RolesController@store')->name('settings.roles.store');
                Route::get('/edit/{id}', 'RolesController@edit')->name('settings.roles.edit');
                Route::post('/update','RolesController@update')->name('settings.roles.update');
                Route::delete('/delete/{id}','RolesController@destroy')->name('settings.roles.delete');
            });
        });


        Route::prefix('appearance')->group(function() {
            Route::group(['prefix' => 'sliders'], function () {
                Route::get('/', 'SliderController@index')->name('appearance.slider.index');
                Route::get('/create', 'SliderController@create')->name('appearance.slider.create');
                Route::post('/store','SliderController@store')->name('appearance.slider.store');
                Route::get('/edit/{id}', 'SliderController@edit')->name('appearance.slider.edit');
                Route::post('/update','SliderController@update')->name('appearance.slider.update');
                Route::delete('/delete/{id}','SliderController@destroy')->name('appearance.slider.delete');
            });
        });
        

        Route::get('/profile', 'StaffController@profile')->name('profile');
        Route::post('/updateProfile', 'StaffController@updateProfile')->name('profile.update');
    });
});