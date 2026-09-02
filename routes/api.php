<?php

use Illuminate\Support\Facades\Route;

Route::middleware([\App\Http\Middleware\StatelessLegacyAuth::class])
    ->get('/', function(){
        return response()->json([
            'hello' => 'world',
        ]);
    });
