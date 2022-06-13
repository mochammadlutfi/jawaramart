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
            Route::get('/detail/{id}', 'CustomerController@show')->name('customer.show');
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
            Route::get('/data', 'ProductController@data')->name("product.data");
            Route::get('/import', 'ProductController@import')->name("product.import");
    
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

        // Route::prefix('settings')->group(function() {
        Route::group(['prefix' => 'settings', 'as'=>'settings.'], function () {
            Route::get('/', 'SettingsController@index')->name("general.index");

            Route::group(['prefix' => 'staff', 'as'=>'staff.'], function () {
                Route::get('/', 'StaffController@index')->name('index');
                Route::get('/create', 'StaffController@create')->name('create');
                Route::post('/store','StaffController@store')->name('store');
                Route::get('/edit/{id}', 'StaffController@edit')->name('edit');
                Route::post('/update','StaffController@update')->name('update');
                Route::delete('/delete/{id}','StaffController@destroy')->name('delete');
            });

            Route::group(['prefix' => 'roles'], function () {
                Route::get('/', 'RolesController@index')->name('roles.index');
                Route::get('/create', 'RolesController@create')->name('roles.create');
                Route::post('/store','RolesController@store')->name('roles.store');
                Route::get('/edit/{id}', 'RolesController@edit')->name('roles.edit');
                Route::post('/update','RolesController@update')->name('roles.update');
                Route::delete('/delete/{id}','RolesController@destroy')->name('roles.delete');
            });

            
            Route::group(['prefix' => 'tax'], function () {
                Route::get('/', 'TaxController@index')->name('tax.index');
                Route::get('/data', 'TaxController@data')->name('tax.data');
                Route::post('/store','TaxController@store')->name('tax.store');
                Route::post('/update','TaxController@update')->name('tax.update');
                Route::delete('/delete/{id}','TaxController@destroy')->name('tax.delete');
                Route::get('/data','TaxController@data')->name('tax.data');
            });

            
            Route::group(['prefix' => 'warehouse', 'as' => 'warehouse.'], function () {
                Route::get('/', 'WarehouseController@index')->name('index');
                Route::get('/data', 'WarehouseController@data')->name('data');
                Route::post('/store','WarehouseController@store')->name('store');
                Route::post('/update','WarehouseController@update')->name('update');
                Route::delete('/delete/{id}','WarehouseController@destroy')->name('delete');
            });
        });

        Route::group(['prefix' => 'sale', 'as'=>'sale.'], function () {

            Route::group(['prefix' => 'pos', 'as' => 'pos.'], function () {
                Route::get('/', 'POSController@index')->name('index');
                Route::get('/create', 'POSController@create')->name('create');
                Route::post('/store','POSController@store')->name('store');
                Route::get('/edit/{id}', 'POSController@edit')->name('edit');
                Route::post('/update','POSController@update')->name('update');
                Route::delete('/delete/{id}','POSController@destroy')->name('delete');
                Route::get('/invoice-print/{id}','POSController@invoice_print')->name('invoice_print');
                Route::get('/report', 'POSController@report')->name('report');
                Route::get('/transaction', 'POSController@transaction')->name('transaction');
                Route::post('/open','POSController@open')->name('open');
                Route::post('/close','POSController@close')->name('close');
            });

            
            Route::group(['prefix' => 'order', 'as' => 'order.'], function () {
                Route::get('/', 'SaleOrderController@index')->name('index');
                Route::get('/create', 'SaleOrderController@create')->name('create');
                Route::post('/store','SaleOrderController@store')->name('store');
                Route::get('/show/{id}', 'SaleOrderController@show')->name('show');
                Route::get('/edit/{id}', 'SaleOrderController@edit')->name('edit');
                Route::post('/update','SaleOrderController@update')->name('update');
                Route::delete('/delete/{id}','SaleOrderController@destroy')->name('delete');
                Route::post('/update-status','SaleOrderController@update_status')->name('update_status');
            });

            
            Route::group(['prefix' => 'payment', 'as' => 'payment.'], function () {
                Route::get('/', 'SalePaymentController@index')->name('index');
                Route::get('/create', 'SalePaymentController@create')->name('create');
                Route::post('/store','SalePaymentController@store')->name('store');
                Route::get('/show/{id}', 'SalePaymentController@show')->name('show');
                Route::get('/edit/{id}', 'SalePaymentController@edit')->name('edit');
                Route::post('/update','SalePaymentController@update')->name('update');
                Route::post('/validate','SalePaymentController@validate')->name('validate');
                Route::delete('/delete/{id}','SalePaymentController@destroy')->name('delete');
            });
            
        });

        Route::group(['prefix' => 'purchase', 'as'=>'purchase.'], function () {

            Route::group(['prefix' => 'supplier', 'as' => 'supplier.'], function () {
                Route::get('/', 'SupplierController@index')->name('index');
                Route::get('/create', 'SupplierController@create')->name('create');
                Route::post('/store','SupplierController@store')->name('store');
                Route::get('/edit/{id}', 'SupplierController@edit')->name('edit');
                Route::post('/update','SupplierController@update')->name('update');
                Route::delete('/delete/{id}','SupplierController@destroy')->name('delete');
                Route::get('/data','SupplierController@data')->name('data');
            });


            Route::group(['prefix' => 'order', 'as' => 'order.'], function () {
                Route::get('/', 'PurchaseOrderController@index')->name('index');
                Route::get('/create', 'PurchaseOrderController@create')->name('create');
                Route::post('/store','PurchaseOrderController@store')->name('store');
                Route::get('/show/{id}', 'PurchaseOrderController@show')->name('show');
                Route::get('/edit/{id}', 'PurchaseOrderController@edit')->name('edit');
                Route::post('/update','PurchaseOrderController@update')->name('update');
                Route::post('/updateStatus','PurchaseOrderController@updateStatus')->name('update_status');
                Route::delete('/delete/{id}','PurchaseOrderController@destroy')->name('delete');
                Route::post('/payment','PurchaseOrderController@payment')->name('payment');
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

        Route::prefix('/accounting')->name('accounting.')->namespace('Accounting')->group(function() {

            Route::group(['prefix' => 'payment', 'as' => 'payment.'], function () {
                Route::get('/', 'PaymentController@index')->name('index');
                Route::get('/create', 'PaymentController@create')->name('create');
                Route::post('/store','PaymentController@store')->name('store');
                Route::get('/edit/{id}', 'PaymentController@edit')->name('edit');
                Route::post('/update','PaymentController@update')->name('update');
                Route::delete('/delete/{id}','PaymentController@destroy')->name('delete');
            });

            Route::group(['prefix' => 'config', 'as' => 'config.'], function () {

                Route::group(['prefix' => 'payment-method', 'as' => 'payment_method.'], function () {
                    Route::get('/', 'PaymentMethodController@index')->name('index');
                    Route::get('/create', 'PaymentMethodController@create')->name('create');
                    Route::post('/store','PaymentMethodController@store')->name('store');
                    Route::get('/edit/{id}', 'PaymentMethodController@edit')->name('edit');
                    Route::get('/detail/{id}', 'PaymentMethodController@show')->name('show');
                    Route::get('/delete/{id}', 'PaymentMethodController@delete')->name('delete');
                    Route::get('/data', 'PaymentMethodController@data')->name('data');
                });


                Route::group(['prefix' => 'bank', 'as' => 'bank.'], function () {
                    Route::get('/', 'BankController@index')->name('index');
                    Route::get('/create', 'BankController@create')->name('create');
                    Route::post('/store','BankController@store')->name('store');
                    Route::post('/update','BankController@update')->name('update');
                    Route::get('/edit/{id}', 'BankController@edit')->name('edit');
                    Route::get('/detail/{id}', 'BankController@show')->name('show');
                    Route::delete('/delete/{id}', 'BankController@destroy')->name('delete');
                });

                
                Route::group(['prefix' => 'account', 'as' => 'account.'], function () {
                    Route::get('/', 'AccountController@index')->name('index');
                    Route::get('/create', 'AccountController@create')->name('create');
                    Route::post('/store','AccountController@store')->name('store');
                    Route::get('/edit/{id}', 'AccountController@edit')->name('edit');
                    Route::post('/update','AccountController@update')->name('update');
                    Route::get('/detail/{id}', 'AccountController@show')->name('show');
                    Route::post('/updateState','AccountController@updateState')->name('updateState');
                });
            });

            Route::group(['prefix' => 'reports', 'as' => 'report.'], function () {
                Route::get('/simpanan', 'ReportController@simpanan')->name('simpanan');
                Route::get('/neraca-saldo', 'ReportController@balance_sheet')->name('balance_sheet');
            });
            
            Route::group(['prefix' => 'potong-gaji', 'as' => 'potong_gaji.'], function () {
                Route::get('/', 'PotongGajiController@index')->name('index');
                Route::get('/export-excel', 'PotongGajiController@exportExcel')->name('exportExcel');
                Route::get('/export-pdf', 'PotongGajiController@exportPDF')->name('exportPDF');
                Route::post('/confirmPayment','PotongGajiController@confirmPayment')->name('confirm');
                Route::get('/{anggota_id}', 'PotongGajiController@show')->name('show');
                Route::get('/{anggota_id}/pdf', 'PotongGajiController@printPdf')->name('pdf');
            });

            
            Route::group(['prefix' => 'cash', 'as' => 'cash.'], function () {
                Route::get('/', 'CashController@index')->name('index');
                Route::get('/create', 'CashController@create')->name('create');
                Route::post('/store','CashController@store')->name('store');
                Route::get('/edit/{id}', 'CashController@edit')->name('edit');
                Route::post('/update','CashController@update')->name('update');
                Route::get('/detail/{id}', 'CashController@show')->name('show');
            });



        });
        

        Route::get('/profile', 'StaffController@profile')->name('profile');
        Route::post('/updateProfile', 'StaffController@updateProfile')->name('profile.update');
    });
});