<?php

declare(strict_types=1);

return [
    'school_name' => 'MI Plus Raudlatul Muta\'allimin',
    'school_tagline' => 'Membina Generasi Qur\'ani, Berakhlak Mulia, dan Unggul Akademik',
    'school_address' => 'Kalipucung, Sanankulon, Blitar, Jawa Timur',
    'contact_phone' => '081234567890',
    'contact_email' => 'info@miplus-kalipucung.sch.id',
    'nsm' => '111235050000',
    'npsn' => '60700000',

    /*
    |--------------------------------------------------------------------------
    | Biaya Pendaftaran / Infaq Formulir PPDB
    |--------------------------------------------------------------------------
    */
    'ppdb_fee' => 50000,
    'ppdb_fee_formatted' => 'Rp 50.000',

    /*
    |--------------------------------------------------------------------------
    | Blitar Coordinates for Islamic Prayer Times
    |--------------------------------------------------------------------------
    */
    'blitar_coordinates' => [
        'latitude' => -8.0983,
        'longitude' => 112.1681,
        'timezone' => 'Asia/Jakarta',
    ],

    /*
    |--------------------------------------------------------------------------
    | Bank Account Info for SPP & Infaq
    |--------------------------------------------------------------------------
    */
    'bank_accounts' => [
        [
            'bank_name' => 'Bank Syariah Indonesia (BSI)',
            'account_number' => '7123456789',
            'account_holder' => 'YAYASAN RAUDLATUL MUTAALLIMIN',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | WhatsApp Notification Gateway Settings
    |--------------------------------------------------------------------------
    */
    'whatsapp' => [
        'enabled' => env('WA_NOTIFICATIONS_ENABLED', true),
        'auto_send' => env('WA_AUTO_SEND', false),
        'provider' => env('WA_GATEWAY_PROVIDER', 'fonnte'), // 'fonnte', 'wablas', 'generic'
        'token' => env('WA_GATEWAY_TOKEN', ''),
        'endpoint' => env('WA_GATEWAY_URL', 'https://api.fonnte.com/send'),
        'sender_name' => 'MI Plus Raudlatul Muta\'allimin',
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Gateway Configuration (QRIS / Virtual Account)
    |--------------------------------------------------------------------------
    */
    'payment' => [
        'enabled' => env('PAYMENT_GATEWAY_ENABLED', true),
        'provider' => env('PAYMENT_GATEWAY_PROVIDER', 'simulator'), // 'simulator', 'tripay', 'midtrans'
        'is_production' => env('PAYMENT_GATEWAY_IS_PRODUCTION', false),
        'absorb_fee' => env('PAYMENT_GATEWAY_ABSORB_FEE', true), // true: madrasah menyerap 0.7%, false: dibebankan ke wali
        'qris_fee_percent' => 0.7, // 0.7% standard Bank Indonesia
        'tripay' => [
            'merchant_code' => env('TRIPAY_MERCHANT_CODE', ''),
            'api_key' => env('TRIPAY_API_KEY', ''),
            'private_key' => env('TRIPAY_PRIVATE_KEY', ''),
            'api_url' => env('TRIPAY_API_URL', 'https://tripay.co.id/api-sandbox/'),
        ],
        'midtrans' => [
            'merchant_id' => env('MIDTRANS_MERCHANT_ID', ''),
            'client_key' => env('MIDTRANS_CLIENT_KEY', ''),
            'server_key' => env('MIDTRANS_SERVER_KEY', ''),
            'snap_url' => env('MIDTRANS_SNAP_URL', 'https://app.sandbox.midtrans.com/snap/snap.js'),
        ],
    ],
];
