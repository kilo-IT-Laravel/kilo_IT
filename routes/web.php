<?php

use App\Http\Controllers\UserManagement;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/reset-password', [UserManagement::class, 'showResetForm'])->name('password.reset');