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
                "name" => 'List POS',
                "to" => 'admin.sale.pos.index',
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
        
        $menuList = [
            [
                "icon" => "si si-speedometer",
                "name" => "Dashboard",
                "to" => "admin.dashboard",
            ],
            [
                "icon" => "si si-user",
                "name" => "Customers",
                "to" => "admin.customer.index",
            ],
            [
                "name" => 'Products',
                "icon" => 'si si-wallet',
                "subActivePaths" => 'admin.product.*',
                "sub" => [
                    [
                        "name" => 'Product',
                        "to" => 'admin.product.index',
                    ],
                    [
                        "name" => 'Categories',
                        "to" => 'admin.product.category.index',
                    ],
                    [
                        "name" => 'Brands',
                        "to" => 'admin.product.brand.index',
                    ],
                ]
            ],
            [
                "name" => 'Sales',
                "icon" => 'fa fa-arrow-circle-up',
                "subActivePaths" => 'admin.sale.*',
                "sub" => [
                    [
                        "name" => "POS",
                        "to" => "admin.sale.pos.index",
                    ],
                    [
                        "name" => 'Create Sale Order',
                        "to" => 'admin.sale.order.create',
                    ],
                    [
                        "name" => 'List Sale Orders',
                        "to" => 'admin.sale.order.index',
                    ],
                ]
            ],
            [
                "name" => 'Purchase',
                "icon" => 'fa fa-arrow-circle-down',
                "subActivePaths" => 'admin.purchase.*',
                "sub" => [
                    [
                        "name" => 'Supplier',
                        "to" => 'admin.purchase.supplier.index',
                    ],
                    [
                        "name" => 'Create Purchase Order',
                        "to" => 'admin.purchase.order.create',
                    ],
                    [
                        "name" => 'List Purchase Orders',
                        "to" => 'admin.purchase.order.index',
                    ],
                ]
            ],
            [
                "name" => 'Accounting',
                "icon" => 'si si-wallet',
                "subActivePaths" => 'admin.product.*',
                "sub" => [
                    [
                        "name" => 'Payment',
                        "to" => 'admin.product.index',
                    ],
                    [
                        "name" => 'Payment Method',
                        "to" => 'admin.product.category.index',
                    ],
                ]
            ],
            [
                "name" => 'Appearance',
                "icon" => 'si si-calculator',
                "subActivePaths" => 'admin.appearance.*',
                "sub" => [
                    [
                        "name" => 'Sliders',
                        "to" => 'admin.appearance.slider.index',
                    ],
                ]
            ],
            [
                "name" => 'Settings',
                "icon" => 'si si-wrench',
                "subActivePaths" => 'admin.settings.*',
                "sub" => [
                    [
                        "name" => 'General',
                        "to" => 'admin.settings.general.index',
                    ],
                    [
                        "name" => 'Staff',
                        "to" => 'admin.settings.staff.index',
                    ],
                    [
                        "name" => 'Staff Role',
                        "to" => 'admin.settings.roles.index',
                    ],
                    [
                        "name" => 'Tax',
                        "to" => 'admin.settings.tax.index',
                    ],
                    [
                        "name" => 'Warehouses',
                        "to" => 'admin.settings.warehouse.index',
                    ],
                ],
            ]
        ];

        $data = Collect($menuList);

        return $menuData->all();
    }

    public static function permission(){
        
        $data = auth()->guard('admin')->user()->getAllPermissions()->toArray();
        $permission = array();
        foreach ($data as $element) {
            $permission[$element['module']][] = $element['name'];
        }

        return $permission;
    }
}
