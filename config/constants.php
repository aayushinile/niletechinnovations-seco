<?php
return [
    'CONSTANT_KEY' => 'constant value',
    'DEFAULT_DATE_FORMAT' => 'm-d-Y',
    'DEFAULT_DATETIME_FORMAT' => 'm-d-Y h:i a',
    'DEFAULT_CURRENCY' => '$',
    'DEFAULT_CURRENCY_CODE' => 'USD',

    // Date formats for specific countries
    'COUNTRY_DATE_FORMATS' => [
        'United States' => 'm/d/Y',       // Example for USA
        'India' => 'd-m-Y',     // Example for India
        // Add more as needed
    ],

    // Datetime formats for specific countries
    'COUNTRY_DATETIME_FORMATS' => [
        'United States' => 'm/d/Y h:i a',       // Example for USA
        'India' => 'd-m-Y H:i',       // Example for India
        // Add more as needed
    ],
    'COUNTRY_PHONE_FORMATS' => [
        'United States' => '+1',       // Example for USA
        'India' => '+91',       // Example for India
        // Add more as needed
    ],
    'COUNTRY_CURRENCY' => [
        'United States' => '$',       // Example for USA
        'India' => '₹',       // Example for India
        // Add more as needed
    ],
];
?>
