<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PlanningController;

use Illuminate\Support\Facades\Auth;

Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/loginPost', [AuthController::class, 'loginPost'])->name('loginPost');

Route::get('/planning', [PlanningController::class, 'planning'])->name('planning');

Route::get('/gebruikers', [AuthController::class, 'gebruikers'])->name('users');
Route::get('/createAccount', [AuthController::class, 'createAccount'])->name('createAccount');

Route::post('/createAccountPost', [AuthController::class, 'createAccountPost'])->name('createAccountPost');