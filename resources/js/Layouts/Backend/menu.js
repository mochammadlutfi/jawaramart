export default {
    'main': [
      {
        name: 'Dashboard',
        to: 'admin.dashboard',
        icon: 'si si-speedometer'
      },
      {
        name: 'Customer',
        to: 'admin.customer.index',
        icon: 'si si-user'
      },
      {
        name: 'Product',
        icon: 'fa fa-archive',
        subActivePaths: 'admin.product.*',
        sub: [
            {
              name: 'Products',
              to: 'admin.product.index',
            },
            {
              name: 'Categories',
              to: 'admin.product.category.index',
            },
            {
              name: 'Brands',
              to: 'admin.product.brand.index',
            },
        ]
      },
      {
        name: 'Apperance',
        icon: 'fi-rs-computer',
        subActivePaths: 'admin.appearance.*',
        sub: [
            {
              name: 'Sliders',
              to: 'admin.appearance.slider.index',
            },
        ]
      },
      {
        name: 'Settings',
        icon: 'si si-wrench',
        subActivePaths: 'admin.settings.*',
        sub: [
            {
              name: 'Staff',
              to: 'admin.settings.staff.index',
            },
            {
              name: 'Roles',
              to: 'admin.settings.roles.index',
              subActivePaths: 'admin.settings.roles.*',
            },
            {
              name: 'General',
              to: 'admin.settings.general.index',
            },
        ]
      }
    ],
}
  