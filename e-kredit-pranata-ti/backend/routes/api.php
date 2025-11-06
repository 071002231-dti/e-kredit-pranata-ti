<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/credit-schema', function () {
    return response()->json([
        [
            'id' => 1,
            'category' => 'Pendidikan',
            'subcategory' => 'Pelatihan',
            'description' => 'Mengikuti pelatihan teknis',
            'credit_value' => 2
        ],
        [
            'id' => 2,
            'category' => 'Pengembangan',
            'subcategory' => 'Proyek TI',
            'description' => 'Mengembangkan aplikasi',
            'credit_value' => 5
        ]
    ]);
});
