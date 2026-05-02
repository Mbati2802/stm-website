<?php

return [
    'db' => [
        'host' => 'localhost',
        'name' => 'crm_college',
        'user' => 'stmarys2_stm',
        'pass' => 'stmarys2_stm',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    ],
    'session_name' => 'crm_session',
    'app_name' => 'CRM - St. Mary\'s MCH Medical Training College',
    'registration_fee' => 5000,
    'whatsapp' => [
        'enabled' => false,
        'api_key' => '',
        'phone_number' => ''
    ],
    'sms' => [
        'enabled' => true,
        'api_key' => '',
        'sender_id' => 'STMCH'
    ],
    'email' => [
        'enabled' => true,
        'from' => 'admissions@stmarysmchmcollege.ac.ke',
        'from_name' => 'Admissions Office'
    ]
];
