<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | حدد هنا المواقع (الأصول) المسموح لها تطلب من API تبعك.
    | لو تشتغل محليًا، حط عنوان Vue (مثلاً http://localhost:5173).
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:5173',   // Vue dev server
        'http://127.0.0.1:5173',   // احتمال تستخدم 127 بدل localhost
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
