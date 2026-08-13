<?php

return [
    'clear_current' => 'Clear existing data of this type before copy',
    'clear_current_all' => 'Clear existing data before each copy step',
    'clearing' => 'Clearing current data: :type',
    'copy' => 'Copy :type',
    'no_absent_users' => 'No absent users',
    'elapsed' => 'Elapsed: :seconds s',

    'types' => [
        'archive' => 'archive',
        'users' => 'users',
        'eatings' => 'eatings',
        'factors' => 'factors',
        'settings' => 'settings',
        'products' => 'products',
        'menus' => 'menus',
        'diary' => 'diary',
    ],

    'commands' => [
        'all' => 'Run all migrations from the old Diacalc program to this one',
        'archive' => 'Copy archive groups and products from the old Diacalc database',
        'users' => 'Copy missing users from the old Diacalc database',
        'eatings' => 'Copy eatings from the old Diacalc database',
        'factors' => 'Copy factors from the old Diacalc database',
        'settings' => 'Copy settings from the old Diacalc database',
        'products' => 'Copy product groups and products from the old Diacalc database',
        'menus' => 'Copy menus from the old Diacalc database',
        'diary' => 'Copy diary records from the old Diacalc database',
    ],
];
