<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

use Illuminate\Support\Facades\Auth;

Route::get('/login', [AuthController::class, 'login'])->name('login');

