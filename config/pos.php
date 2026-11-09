<?php

return [
    'name' => env('POS_NAME', 'Kopi Kita Cafe & Roastery'),
    'short_name' => env('POS_SHORT_NAME', 'Kopi Kita'),
    'outlet_name' => env('POS_OUTLET_NAME', 'Pati - Sentral 01'),
    'address' => env('POS_ADDRESS', 'Jl. Pemuda No. 88, Pati, Jawa Tengah'),
    'phone' => env('POS_PHONE', '(0295) 381920'),
    'tax_rate' => (float) env('POS_TAX_RATE', 0.10), // PB1 10%
    'currency' => env('POS_CURRENCY', 'Rp'),
    'low_stock_threshold' => (int) env('POS_LOW_STOCK_THRESHOLD', 15),
    'version' => env('POS_VERSION', '2.4.0'),
    'institution' => env('POS_INSTITUTION', 'SMK Tunas Harapan Pati'),
];
