<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PlanningController;
use App\Http\Controllers\HouseController;

use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (Auth::check()) {
        if (Auth::user()->role === 1) {
            return redirect()->route('planning');
        } elseif (Auth::user()->role === 0) {
            return redirect()->route('planning');
        }
    } else {
        return redirect()->route('login');
    }
});

Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/loginPost', [AuthController::class, 'loginPost'])->name('loginPost');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');


Route::get('/planning', [PlanningController::class, 'planning'])->name('planning');
Route::get('/createPlanning', [PlanningController::class, 'createPlanning'])->name('createPlanning');
Route::post('/createPlanningPost', [PlanningController::class, 'createPlanningPost'])->name('createPlanningPost');

Route::get('/editPlanning/{planningId}', [PlanningController::class, 'editPlanning'])->name('editPlanning');
Route::put('/updatePlanning/{planningId}', [PlanningController::class, 'updatePlanning'])->name('updatePlanning');

Route::get('/editHouse/{houseId}', [HouseController::class, 'editHouse'])->name('editHouse');
Route::post('/updateHouse/{houseId}', [HouseController::class, 'updateHouse'])->name('updateHouse');


Route::get('/gebruikers', [AuthController::class, 'gebruikers'])->name('users');
Route::get('/createAccount', [AuthController::class, 'createAccount'])->name('createAccount');

Route::post('/createAccountPost', [AuthController::class, 'createAccountPost'])->name('createAccountPost');

Route::get('/activateAccount', [AuthController::class, 'viewActivateAccount'])->name('activateAccount');
Route::post('/activateAccount', [AuthController::class, 'activateAccountPost'])->name('activateAccountPost');

Route::get('/houses', [HouseController::class, 'houses'])->name('houses');

Route::get('/createHouse', [HouseController::class, 'createHouse'])->name('createHouse');
Route::post('/createHousePost', [HouseController::class, 'createHousePost'])->name('createHousePost');
