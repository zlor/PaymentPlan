<?php

return [

    /*
     * Laravel-admin name.
     */
    'name' => env('APP_ADMIN_NAME', 'Ranto-Payment'),

    /*
     * Logo in admin panel header.
     */
    'logo' => env('APP_ADMIN_LOGO_TXT', '<b>Ranto</b>Payment'),

    /*
     * Mini-logo in admin panel header.
     */
    'logo-mini' => env('APP_ADMIN_LOGO_MINI', '<b>Rt</b>'),

    /*
     * Route configuration.
     */
    'route' => [

        'prefix' => env('APP_ADMIN_ROUTE_PREFIX', ''),

        'namespace' => 'App\\Admin\\Controllers',

        'middleware' => ['web', 'admin'],
    ],

    /*
     * Laravel-admin install directory.
     */
    'directory' => app_path('Admin'),

    /*
     * Laravel-admin html title.
     */
    'title' => env('APP_ADMIN_TITLE', 'Admin'),

    /*
     * Use `https`.
     */
    'secure' => false,

    /*
     * Laravel-admin auth setting.
     */
    'auth' => [
        'guards' => [
            'admin' => [
                'driver'   => 'session',
                'provider' => 'admin',
            ],
        ],

        'providers' => [
            'admin' => [
                'driver' => 'eloquent',
                'model'  => Encore\Admin\Auth\Database\Administrator::class,
            ],
        ],
    ],

    /*
     * Laravel-admin upload setting.
     */
    'upload' => [

        'disk' => 'admin',

        'directory' => [
            'image' => 'images',
            'file'  => 'files',
        ],
    ],

    'import' => [
        'disk' => 'import',
        'directory'=> [
            'payment'  => 'payments',
            'supplier' => 'suppliers',
        ]
    ],

    /*
     * Laravel-admin database setting.
     */
    'database' => [

        // Database connection for following tables.
        'connection' => '',

        // User tables and model.
        'users_table' => 'admin_users',
        'users_model' => Encore\Admin\Auth\Database\Administrator::class,

        // Role table and model.
        'roles_table' => 'admin_roles',
        'roles_model' => Encore\Admin\Auth\Database\Role::class,

        // Permission table and model.
        'permissions_table' => 'admin_permissions',
        'permissions_model' => Encore\Admin\Auth\Database\Permission::class,

        // Menu table and model.
        'menu_table' => 'admin_menu',
        'menu_model' => Encore\Admin\Auth\Database\Menu::class,

        // Pivot table for table above.
        'operation_log_table'    => 'admin_operation_log',
        'user_permissions_table' => 'admin_user_permissions',
        'role_users_table'       => 'admin_role_users',
        'role_permissions_table' => 'admin_role_permissions',
        'role_menu_table'        => 'admin_role_menu',
    ],

    /*
     * By setting this option to open or close operation log in laravel-admin.
     */
    'operation_log' => [

        'enable' => true,

        /*
         * Routes that will not log to database.
         *
         * All method to path like: admin/auth/logs
         * or specific method to path like: get:admin/auth/logs
         */
        'except' => [
            'admin/auth/logs*',
        ],
    ],

    /*
     * @see https://adminlte.io/docs/2.4/layout
     */
    'skin' => 'skin-blue-light',

    /*
    |---------------------------------------------------------|
    |LAYOUT OPTIONS | fixed                                   |
    |               | layout-boxed                            |
    |               | layout-top-nav                          |
    |               | sidebar-collapse                        |
    |               | sidebar-mini                            |
    |---------------------------------------------------------|
     */
    'layout' => ['sidebar-mini', 'sidebar-collapse'],

    /*
     * Version displayed in footer.
     */
    'version' => '0.1',

    /**
     * Power By
     */
    'power_by' => 'Ranto',//'<a href="https://github.com/z-song/laravel-admin" target="_blank">laravel-admin</a>',

    /*
     * Settings for extensions.
     */
    'extensions' => [
        'media-manager' => [

            // Select a local disk that you configured in `config/filesystem.php`
            'disk' => 'public'
        ],
    ],
];
