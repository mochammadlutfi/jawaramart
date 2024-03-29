<?php

namespace App\Helpers;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use DB;

class MenuHelp
{
    public static function mainMenu()
    {

        $menuData = Collect([]);

        $menuData->push([
            "icon" => "fi fi-rs-home",
            "name" => "Dashboard",
            "to" => "admin.dashboard",
        ]);

        $data = auth()->guard('admin')->user()->getAllPermissions()->toArray();
        $permission = array();
        foreach ($data as $element) {
            $permission[$element['module']][] = $element['name'];
        }

        if(array_key_exists('Customer', $permission)){
            $menuData->push([
                "icon" => "fi fi-rs-users",
                "name" => "Customers",
                "to" => "admin.customer.index",
            ]);
        }

        if(array_key_exists('Product', $permission) || array_key_exists('Product Category', $permission)  || array_key_exists('Product Category', $permission)){
            $product_menu = Collect([
                "name" => 'Products',
                "icon" => 'fi fi-rs-box-alt',
                "subActivePaths" => 'admin.product.*',
            ]);

            $sub_product_menu = [];

            if(array_key_exists('Product', $permission)){
                $sub_product_menu[] = [
                    "name" => 'Product',
                    "to" => 'admin.product.index',
                ];
            }

            if(array_key_exists('Product Category', $permission)){
                $sub_product_menu[] = [
                    "name" => 'Categories',
                    "to" => 'admin.product.category.index',
                ];
            }

            
            if(array_key_exists('Product Brand', $permission)){
                $sub_product_menu[] = [
                    "name" => 'Brands',
                    "to" => 'admin.product.brand.index',
                ];
            }

            $product_menu = $product_menu->put('sub', $sub_product_menu);
            $menuData->push($product_menu->all());
        }

        if(array_key_exists('Sale Order', $permission)){
            $menu = Collect([
                "name" => 'Sales',
                "icon" => 'fa fa-arrow-circle-up',
                "subActivePaths" => 'admin.sale.*',
            ]);

            $sub_menu = [];

            if(in_array("create", $permission['Sale Order'])){
                $sub_menu[] = [
                    "name" => 'Create Sale Order',
                    "to" => 'admin.sale.order.create',
                ];
            }

            $sub_menu[] = [
                "name" => 'List Sale Orders',
                "to" => 'admin.sale.order.index',
            ];
            
            $sub_menu[] = [
                "name" => 'List Web Orders',
                "to" => 'admin.sale.web',
            ];
            
            $sub_menu[] = [
                "name" => 'List POS',
                "to" => 'admin.sale.pos.index',
            ];

            $sub_menu[] = [
                "name" => 'List Sale Return',
                "to" => 'admin.sale.return.index',
            ];
            
            $sub_menu[] = [
                "name" => 'Sale Payment',
                "to" => 'admin.sale.payment.index',
            ];


            $menu = $menu->put('sub', $sub_menu);
            $menuData->push($menu->all());
        }

        if(array_key_exists('Purchase Order', $permission) || array_key_exists('Supplier', $permission)){
            $menu = Collect([
                "name" => 'Purchase',
                "icon" => 'fa fa-arrow-circle-down',
                "subActivePaths" => 'admin.purchase.*',
            ]);

            $sub_menu = [];

            if(array_key_exists('Supplier', $permission)){
                $sub_menu[] = [
                    "name" => 'Supplier',
                    "to" => 'admin.purchase.supplier.index',
                ];
            }

            if(in_array("create", $permission['Purchase Order'])){
                $sub_menu[] = [
                    "name" => 'Create Purchase Order',
                    "to" => 'admin.purchase.order.create',
                ];
            }

            $sub_menu[] = [
                "name" => 'List Purchase Orders',
                "to" => 'admin.purchase.order.index',
            ];

            
            $sub_menu[] = [
                "name" => 'List Purchase Return',
                "to" => 'admin.purchase.return.index',
            ];

            $menu = $menu->put('sub', $sub_menu);
            $menuData->push($menu->all());
        }

        if(array_key_exists('Payment', $permission) || array_key_exists('Payment Method', $permission)){
            $menu = Collect([
                "name" => 'Accounting',
                "icon" => 'fi fi-rs-calculator',
                "subActivePaths" => 'admin.accounting.*',
            ]);

            $sub_menu = [
                [
                    "name" => 'Config',
                    "subActivePaths" => 'admin.accounting.config.*',
                    "sub" => [
                      [
                          "name" => 'Payment Method',
                          "to" => 'admin.accounting.config.payment_method.index',
                      ],
                      [
                          "name" => 'Bank',
                          "to" => 'admin.accounting.config.bank.index',
                      ],
                    ]
                ],
            ];

            if(array_key_exists('Payment', $permission)){
                $sub_menu[] = [
                    "name" => 'Payment',
                    "to" => 'admin.accounting.payment.index',
                ];
            }

            if(array_key_exists('Payment Method', $permission)){
                $sub_menu[] = [
                    "name" => 'Payment Method',
                    "to" => 'admin.purchase.order.create',
                ];
            }
            
            $sub_menu[] = [
                "name" => 'Piutang',
                "to" => 'admin.accounting.piutang.index',
            ];

            $menu = $menu->put('sub', $sub_menu);
            $menuData->push($menu->all());
        }

        if(array_key_exists('Slider', $permission)){
            $menu = Collect([
                "name" => 'Apperance',
                "icon" => 'fi fi-rs-brush',
                "subActivePaths" => 'admin.appearance.*',
            ]);

            $sub_menu = [];

            if(array_key_exists('Slider', $permission)){
                $sub_menu[] = [
                    "name" => 'Slider',
                    "to" => 'admin.appearance.slider.index',
                ];
            }

            $menu = $menu->put('sub', $sub_menu);
            $menuData->push($menu->all());
        }
        // Report
        $menu = Collect([
            "name" => 'Report',
            "icon" => 'si si-printer',
            "subActivePaths" => 'admin.report.*',
        ]);

        $sub_menu = [];

        $sub_menu[] = [
            "name" => 'Profit & Loss',
            "to" => 'admin.report.profit_loss',
        ];

        $sub_menu[] = [
            "name" => 'Stock Report',
            "to" => 'admin.report.stock_report',
        ];

        $sub_menu[] = [
            "name" => 'Cash Register',
            "to" => 'admin.report.cash_register',
        ];

        $menu = $menu->put('sub', $sub_menu);
        $menuData->push($menu->all());

        
        $menu = Collect([
            "name" => 'Koperasi',
            "icon" => 'fa fa-university',
            "subActivePaths" => 'admin.kop.*',
        ]);
        

        
        $sub_menu = [
            [
                "name" => 'Simpanan',
                "subActivePaths" => 'admin.kop.simpanan.*',
                "sub" => [
                  [
                      "name" => 'Simpanan Wajib',
                      "to" => 'admin.kop.simpanan.wajib.index',
                  ],
                  [
                      "name" => 'Simpanan Sukarela',
                      "to" => 'admin.kop.simpanan.sukarela.index',
                  ],
                ]
            ],
        ];

        $sub_menu[] = [
            "name" => 'Anggota',
            "to" => 'admin.kop.anggota.index',
        ];

        $menu = $menu->put('sub', $sub_menu);
        $menuData->push($menu->all());

        $settingsMenu = ['Base', 'Staff', 'Roles', 'Warehouse', 'Tax'];
        if (count(array_intersect_key(array_flip($settingsMenu), $permission)) > 0){
            $menu = Collect([
                "name" => 'Settings',
                "icon" => 'fi fi-rs-settings',
                "subActivePaths" => 'admin.settings.*',
            ]);

            $sub_menu = [];

            if(array_key_exists('Base', $permission)){
                $sub_menu[] = [
                    "name" => 'General Settings',
                    "to" => 'admin.settings.general.index',
                ];
            }

            if(array_key_exists('Staff', $permission)){
                $sub_menu[] = [
                    "name" => 'Staff',
                    "to" => 'admin.settings.staff.index',
                ];
            }

            if(array_key_exists('Staff Role', $permission)){
                $sub_menu[] = [
                    "name" => 'Staff Role',
                    "to" => 'admin.settings.roles.index',
                ];
            }

            if(array_key_exists('Warehouse', $permission)){
                $sub_menu[] = [
                    "name" => 'Warehouse',
                    "to" => 'admin.settings.warehouse.index',
                ];
            }

            if(array_key_exists('Tax', $permission)){
                $sub_menu[] = [
                    "name" => 'Taxes',
                    "to" => 'admin.settings.tax.index',
                ];
            }

            $menu = $menu->put('sub', $sub_menu);
            $menuData->push($menu->all());
        }

        return $menuData->all();
    }

    public static function permission()
    {
        $data = auth()->guard('admin')->user()->getAllPermissions()->toArray();
        $permission = array();
        foreach ($data as $element) {
            $permission[$element['module']][] = $element['name'];
        }

        return $permission;
    }
}
