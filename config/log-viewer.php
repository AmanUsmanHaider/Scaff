<?php

use Arcanedev\LogViewer\Contracts\Utilities\Filesystem;

return [

    /* -----------------------------------------------------------------
     |  Log files storage path
     | -----------------------------------------------------------------
     */

    'storage-path'  => storage_path('logs'),

    /* -----------------------------------------------------------------
     |  Log files pattern
     | -----------------------------------------------------------------
     */

    'pattern'       => [
        'prefix'    => Filesystem::PATTERN_PREFIX,    // 'laravel-'
        'date'      => Filesystem::PATTERN_DATE,      // '[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9]'
        'extension' => Filesystem::PATTERN_EXTENSION, // '.log'
    ],

    /* -----------------------------------------------------------------
     |  Locale
     | -----------------------------------------------------------------
     |  Supported locales :
     |    'auto', 'ar', 'bg', 'de', 'en', 'es', 'et', 'fa', 'fr', 'hu', 'hy', 'id', 'it', 'ja', 'ko', 'nl',
     |    'pl', 'pt-BR', 'ro', 'ru', 'sv', 'th', 'tr', 'zh-TW', 'zh'
     */

    'locale'        => 'auto',

    /* -----------------------------------------------------------------
     |  Theme
     | -----------------------------------------------------------------
     |  Supported themes :
     |    'bootstrap-3', 'bootstrap-4'
     |  Make your own theme by adding a folder to the views directory and specifying it here.
     */

    'theme'         => 'logs',

    /* -----------------------------------------------------------------
     |  Route settings
     | -----------------------------------------------------------------
     */

    'route'         => [
        'enabled'    => true,

        'attributes' => [
            'prefix' => 'admin/log-viewer',
            'middleware' => ['web', 'auth','permission:admin_user-management_log-dashboard'],
        ],
    ],

    /* -----------------------------------------------------------------
     |  Log entries per page
     | -----------------------------------------------------------------
     |  This defines how many logs & entries are displayed per page.
     */

    'per-page'      => 30,

    /* -----------------------------------------------------------------
     |  Download settings
     | -----------------------------------------------------------------
     */

    'download'      => [
        'prefix'    => 'laravel-',

        'extension' => 'log',
    ],

    /* -----------------------------------------------------------------
     |  Menu settings
     | -----------------------------------------------------------------
     */

    'menu'  => [
        'filter-route'  => 'log-viewer::logs.filter',

        'icons-enabled' => true,
    ],

    /* -----------------------------------------------------------------
     |  Icons
     | -----------------------------------------------------------------
     */

    'icons' =>  [
        /**
         * Font awesome >= 4.3
         * http://fontawesome.io/icons/
         */
        'all'       => 'cil-list',                 // http://fontawesome.io/icon/list/
        'emergency' => 'cil-bug',                  // http://fontawesome.io/icon/bug/
        'alert'     => 'cil-bullhorn',             // http://fontawesome.io/icon/bullhorn/
        'critical'  => 'cil-triangle',            // http://fontawesome.io/icon/heartbeat/
        'error'     => 'cil-x-circle',         // http://fontawesome.io/icon/times-circle/
        'warning'   => 'cil-warning', // http://fontawesome.io/icon/exclamation-triangle/
        'notice'    => 'cil-bell-exclamation',   // http://fontawesome.io/icon/exclamation-circle/
        'info'      => 'cil-info',          // http://fontawesome.io/icon/info-circle/
        'debug'     => 'cil-life-ring',            // http://fontawesome.io/icon/life-ring/
    ],

    /* -----------------------------------------------------------------
     |  Colors
     | -----------------------------------------------------------------
     */

    'colors' =>  [
        'levels'    => [
            'empty'     => '#D1D1D1',
            'all'       => '#8A8A8A',
            'emergency' => '#B71C1C',
            'alert'     => '#D32F2F',
            'critical'  => '#F44336',
            'error'     => '#FF5722',
            'warning'   => '#FF9100',
            'notice'    => '#4CAF50',
            'info'      => '#1976D2',
            'debug'     => '#90CAF9',
        ],
    ],

    /* -----------------------------------------------------------------
     |  Strings to highlight in stack trace
     | -----------------------------------------------------------------
     */

    'highlight' => [
        '^#\d+',
        '^Stack trace:',
    ],

];
