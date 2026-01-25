<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\auth\ForgotPasswordController;
use App\Http\Controllers\auth\LoginController;
use App\Http\Controllers\auth\RegisterController;
use App\Http\Controllers\auth\ResetPasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

//=====================
//public links
//=====================
Route::group([],function(){
   Route::get('/',WelcomeController::class);
   Route::get('/about',AboutController::class);
    Route::get('/forgot-password' , [ForgotPasswordController::class,'create']);
    Route::post('/forgot-password' , [ForgotPasswordController::class,'store']);
    Route::get('/reset-password' , [ResetPasswordController::class,'create'])->name('reset.password');
    Route::post('/reset-password' , [ResetPasswordController::class,'store']);
});
Route::group(['middleware' => 'guest'],function(){
    Route::get('/login',[LoginController::class,'create'])->name('login');
    Route::post('/login',[LoginController::class,'store']);
    Route::get('/register',[RegisterController::class,'create'])->name('register');
    Route::post('/register',[RegisterController::class,'store']);
});

Route::group(['middleware' => 'auth'],function(){
    Route::get('/dashboard',DashboardController::class);

    Route::get('/roles',[RoleController::class,'index']);
    Route::get('/roles/create',[RoleController::class,'create']);
    Route::post('/roles',[RoleController::class,'store']);
    Route::get('/roles/{role}/edit',[RoleController::class,'edit']);
    Route::get('/roles/{role}',[RoleController::class,'show']);
    Route::patch('/roles/{role}',[RoleController::class,'update']);
    Route::delete('/roles/{role}',[RoleController::class,'destroy']);

    Route::delete('/logout',[LoginController::class,'destroy'])->name('logout');
});


