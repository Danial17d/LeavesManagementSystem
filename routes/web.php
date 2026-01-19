<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\auth\LoginController;
use App\Http\Controllers\auth\RegisterController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

//=====================
//public links
//=====================
Route::group([],function(){
   Route::get('/',WelcomeController::class);
   Route::get('/about',AboutController::class);
});
Route::group(['middleware' => 'guest'],function(){
    Route::get('/login',[LoginController::class,'create'])->name('login');
    Route::post('/login',[LoginController::class,'store']);
    Route::get('/register',[RegisterController::class,'create'])->name('register');
    Route::post('/register',[RegisterController::class,'store']);
});


