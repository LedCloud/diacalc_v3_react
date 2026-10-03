<?php

return [
    'keep' => 'Emails of users whose current data is left unchanged. Comma-separated or repeated.',
    'keep_all' => 'Emails of users left unchanged. Every other mapped user is cleared and recreated.',
    'confirm_recreate' => 'All current user data will be cleared and the users will be recreated. Continue?',
    'cancelled' => 'Operation cancelled. No data was changed.',
    'keep_unknown' => 'Keep email not found among users being copied: :email',
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
