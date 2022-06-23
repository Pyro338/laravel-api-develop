<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'namespace' => 'Gamebetr\Api\Controllers\Web',
], function () {
    Route::get('avatar/{avatar}', 'Avatar')->name('avatar');
    Route::get('user/confirmreset', 'ConfirmReset')->name('confirmreset');
    Route::get('confirmwithdrawal/{uuid}/{token}', 'ConfirmWithdrawal')->name('confirmwithdrawal');
    Route::get('cancelwithdrawal/{uuid}/{token}', 'CancelWithdrawal')->name('cancelwithdrawal');
    
    // user form routes
    Route::get('user/register', 'Register')->name('register');
    Route::get('user/login', 'Login')->name('login');
    Route::get('user/forgotpassword', 'ForgotPassword')->name('forgotpassword');
    Route::get('{locale?}/user/register', 'Register');
    Route::get('{locale?}/user/login', 'Login');
    Route::get('{locale?}/user/forgotpassword', 'ForgotPassword');
});
