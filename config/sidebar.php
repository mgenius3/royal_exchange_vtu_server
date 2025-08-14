<?php

return [

  /*
    |--------------------------------------------------------------------------
    | View Storage Paths
    |--------------------------------------------------------------------------
    |
    | Most templating systems load templates from disk. Here you may specify
    | an array of paths that should be checked for your views. Of course
    | the usual Laravel view path has already been registered for you.
    |
    */

  'menu' => [
    [
      'text' => 'Navigation',
      'is_header' => true
    ],
    [
      'url' => '/dashboard',
      'icon' => 'fa fa-laptop',
      'text' => 'Dashboard'
    ],
    // [
    //   'url' => '/analytics',
    //   'icon' => 'fa fa-chart-pie',
    //   'text' => 'Analytics'
    // ],
    [
      'is_divider' => true
    ],

    [
      'text' => 'User Management',
      'is_header' => true
    ],
    [
      'icon' => 'fa fa-user-circle',
      'text' => 'User List',
      // 'label' => '6',
      'children' => [
        [
          'url' => '/users',
          'action' => 'AllUsers',
          'text' => 'All Users'
        ],
        [
          'url' => '/users/create_user',
          'action' => 'Create Users',
          'text' => 'Create Users'
        ], 
        
        //[
        //   'url' => '/user/detail',
        //   'action' => 'Detail',
        //   'text' => 'Detail'
        // ]

      ]
    ],

    [
      'is_divider' => true
    ],
    [
      'text' => 'Gift Cards Management',
      'is_header' => true
    ],
    [
      'icon' => 'fa fa-envelope',
      'text' => 'Gift Card',
      // 'label' => '6',
      'children' => [[
        'url' => '/gift-cards',
        'action' => 'Gift Cards',
        'text' => 'Gift Cards'
      ], [
        'url' => '/gift-cards/create-transaction',
        'action' => 'New Transactions',
        'text' => 'New Transactions'
      ], [
        'url' => '/gift-cards/all-transactions',
        'action' => 'All Transactions',
        'text' => 'All Transactions'
      ]]
    ],
    [
      'is_divider' => true
    ],

    [
      'text' => 'Crypto Management',
      'is_header' => true
    ],
    [
      'icon' => 'fab fa-bitcoin',
      'text' => 'Crypto',
      // 'label' => '6',
      'children' => [[
        'url' => '/crypto',
        'action' => 'Crypto\'s',
        'text' => 'Crypto\'s'
      ], [
        'url' => '/crypto/create-transaction',
        'action' => 'New Transactions',
        'text' => 'New Transactions'
      ], [
        'url' => '/crypto/all-transactions',
        'action' => 'All Transactions',
        'text' => 'All Transactions'
      ]]
    ],
    [
      'is_divider' => true
    ],

    [
      'text' => 'Vtu Management',
      'is_header' => true
    ],
    [
      'icon' => 'fas fa-sim-card',
      'text' => 'Vtu',
      // 'label' => '6',
      'children' => [[
        'url' => '/vtu',
        'action' => 'Vtu\'s',
        'text' => 'Vtu\'s'
      ], [
        'url' => '/vtu/create-transaction',
        'action' => 'New Transactions',
        'text' => 'New Transactions'
      ], [
        'url' => '/vtu/all-transactions',
        'action' => 'All Transactions',
        'text' => 'All Transactions'
      ]]
    ],
    [
      'is_divider' => true
    ],

    [
      'text' => 'ADS Management',
      'is_header' => true
    ],
    [
      'icon' => 'fas fa-sim-card',
      'text' => 'ADS',
      // 'label' => '6',
      'children' => [[
        'url' => '/ads',
        'action' => 'ADS\'s',
        'text' => 'ADS\'s'
      ], [
        'url' => '/ads/create',
        'action' => 'New ADS',
        'text' => 'New ADS'
      ],
      //  [
      //   'url' => '/ads/all-transactions',
      //   'action' => 'All ADS',
      //   'text' => 'All ADS'
      // ]
      
      ]
    ],
    [
      'is_divider' => true
    ],
    [
      'text' => 'Payment Management',
      'is_header' => true
    ],
    [
      'icon' => 'fa fa-bank',
      'text' => 'Payment Methods',
      'children' => [
        [
          'url' => '/bank-details',
          'action' => 'Bank Settings',
          'text' => 'Bank Settings'
        ],
        [
          'url' => '/payment-settings',
          'action' => 'Payment Gateways',
          'text' => 'Payment Gateways'
        ],
      ]
    ],
    [
      'is_divider' => true
    ],
    [
      'text' => 'Exchange Rate',
      'is_header' => true
    ],
    [
      'url' => '/exchange-rates',
      'icon' => 'fa fa-exchange',
      'text' => 'Exchange Rates'
    ],

    [
      'is_divider' => true
    ],
    
    [
      'text' => 'Wallet Transactions',
      'is_header' => true
    ],
    [
      'url' => '/wallet-transactions',
      'icon' => 'fa fa-wallet',
      'text' => 'Wallet Transactions'
    ],

    [
      'is_divider' => true
    ],
    [
      'text' => 'Email Sender',
      'is_header' => true
    ],
    [
      'icon' => 'fa fa-paper-plane',
      'text' => 'Email',
      'children' => [
        
        [
          'url' => '/email/send-to-user',
          'action' => 'Send Email',
          'text' => 'Send Email'
        ],
        [
          'url' => '/email/broadcast',
          'action' => 'BroadCast Email',
          'text' => 'BroadCast Email'
        ],
      ]
    ],


    [
      'is_divider' => true
    ],
    [
      'text' => 'Support',
      'is_header' => true
    ],
    [
      'url' => '/chat',
      'icon' => 'fa fa-comments',
      'text' => 'Support'
    ],

    // [
    //   'text' => 'Components',
    //   'is_header' => true
    // ],
    // [
    //   'url' => '/widgets',
    //   'icon' => 'fa fa-qrcode',
    //   'text' => 'Widgets'
    // ],
    // [
    //   'icon' => 'fa fa-wallet',
    //   'text' => 'POS System',
    //   'children' => [[
    //     'url' => '/pos/customer-order',
    //     'text' => 'Customer Order'
    //   ], [
    //     'url' => '/pos/kitchen-order',
    //     'text' => 'Kitchen Order'
    //   ], [
    //     'url' => '/pos/counter-checkout',
    //     'text' => 'Counter Checkout'
    //   ], [
    //     'url' => '/pos/table-booking',
    //     'text' => 'Table Booking'
    //   ], [
    //     'url' => '/pos/menu-stock',
    //     'text' => 'Menu Stock'
    //   ]]
    // ],
    // [
    //   'icon' => 'fa fa-heart',
    //   'text' => 'UI Kits',
    //   'children' => [[
    //     'url' => '/ui/bootstrap',
    //     'action' => 'Bootstrap',
    //     'text' => 'Bootstrap'
    //   ], [
    //     'url' => '/ui/buttons',
    //     'text' => 'Buttons'
    //   ], [
    //     'url' => '/ui/card',
    //     'text' => 'Card'
    //   ], [
    //     'url' => '/ui/icons',
    //     'text' => 'Icons'
    //   ], [
    //     'url' => '/ui/modal-notifications',
    //     'text' => 'Modal & Notifications'
    //   ], [
    //     'url' => '/ui/typography',
    //     'text' => 'Typography'
    //   ], [
    //     'url' => '/ui/tabs-accordions',
    //     'text' => 'Tabs & Accordions'
    //   ]]
    // ],
    // [
    //   'icon' => 'fa fa-file-alt',
    //   'text' => 'Forms',
    //   'children' => [[
    //     'url' => '/form/elements',
    //     'text' => 'Form Elements'
    //   ], [
    //     'url' => '/form/plugins',
    //     'text' => 'Form Plugins'
    //   ], [
    //     'url' => '/form/wizards',
    //     'text' => 'Wizards'
    //   ]]
    // ],
    // [
    //   'icon' => 'fa fa-table',
    //   'text' => 'Tables',
    //   'children' => [
    //     [
    //       'url' => '/table/elements',
    //       'text' => 'Table Elements'
    //     ],
    //     [
    //       'url' => '/table/plugins',
    //       'text' => 'Table Plugins'
    //     ]
    //   ]
    // ],
    // [
    //   'icon' => 'fa fa-chart-bar',
    //   'text' => 'Charts',
    //   'children' => [[
    //     'url' => '/chart/chart-js',
    //     'text' => 'Chart.js'
    //   ], [
    //     'url' => '/chart/chart-apex',
    //     'text' => 'Apexcharts.js'
    //   ]]
    // ],
    // [
    //   'url' => '/map',
    //   'icon' => 'fa fa-map-marker-alt',
    //   'text' => 'Map'
    // ],
    // [
    //   'url' => 'Layout',
    //   'icon' => 'fa fa-code-branch',
    //   'text' => 'Layout',
    //   'children' => [[
    //     'url' => '/layout/starter-page',
    //     'text' => 'Starter Page'
    //   ], [
    //     'url' => '/layout/fixed-footer',
    //     'text' => 'Fixed Footer'
    //   ], [
    //     'url' => '/layout/full-height',
    //     'text' => 'Full Height'
    //   ], [
    //     'url' => '/layout/full-width',
    //     'text' => 'Full Width'
    //   ], [
    //     'url' => '/layout/boxed-layout',
    //     'text' => 'Boxed Layout'
    //   ], [
    //     'url' => '/layout/minified-sidebar',
    //     'text' => 'Minified Sidebar'
    //   ], [
    //     'url' => '/layout/top-nav',
    //     'text' => 'Top Nav'
    //   ], [
    //     'url' => '/layout/mixed-nav',
    //     'text' => 'Mixed Nav'
    //   ], [
    //     'url' => '/layout/mixed-nav-boxed-layout',
    //     'text' => 'Mixed Nav Boxed Layout'
    //   ]]
    // ],
    // [
    //   'icon' => 'fa fa-globe',
    //   'text' => 'Pages',
    //   'children' => [[
    //     'url' => '/page/scrum-board',
    //     'text' => 'Scrum Board'
    //   ], [
    //     'url' => '/page/products',
    //     'text' => 'Products'
    //   ], [
    //     'url' => '/page/product/details',
    //     'text' => 'Product Details'
    //   ], [
    //     'url' => '/page/orders',
    //     'text' => 'Orders'
    //   ], [
    //     'url' => '/page/order/details',
    //     'text' => 'Order Details'
    //   ], [
    //     'url' => '/page/gallery',
    //     'text' => 'Gallery'
    //   ], [
    //     'url' => '/page/search-results',
    //     'text' => 'Search Results'
    //   ], [
    //     'url' => '/page/coming-soon',
    //     'text' => 'Coming Soon Page'
    //   ], [
    //     'url' => '/page/error',
    //     'text' => 'Error Page'
    //   ], [
    //     'url' => '/page/login',
    //     'text' => 'Login'
    //   ], [
    //     'url' => '/page/register',
    //     'text' => 'Register'
    //   ], [
    //     'url' => '/page/messenger',
    //     'text' => 'Messenger'
    //   ], [
    //     'url' => '/page/data-management',
    //     'text' => 'Data Management'
    //   ], [
    //     'url' => '/page/file-manager',
    //     'text' => 'File Manager'
    //   ], [
    //     'url' => '/page/pricing',
    //     'text' => 'Pricing Page'
    //   ]]
    // ],
    // [
    //   'url' => '/landing',
    //   'icon' => 'fa fa-crown',
    //   'text' => 'Landing Page'
    // ],
    // [
    //   'is_divider' => true
    // ],
    // [
    //   'text' => 'Users',
    //   'is_header' => true
    // ],
    // [
    //   'url' => '/profile',
    //   'icon' => 'fa fa-user-circle',
    //   'text' => 'Profile'
    // ],
    // [
    //   'url' => '/calendar',
    //   'icon' => 'fa fa-calendar',
    //   'text' => 'Calendar'
    // ],
    // [
    //   'url' => '/settings',
    //   'icon' => 'fa fa-cog',
    //   'text' => 'Settings'
    // ],
    // [
    //   'url' => '/helper',
    //   'icon' => 'fa fa-question-circle',
    //   'text' => 'Helper'
    // ]
  ]
];
