<?php

  

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
 

use App\Http\Controllers\PlanController;

  

/*

|--------------------------------------------------------------------------

| Web Routes

|--------------------------------------------------------------------------

|

| Here is where you can register web routes for your application. These

| routes are loaded by the RouteServiceProvider within a group which

| contains the "web" middleware group. Now create something great!

|

*/

  

Route::get('/', function () {

    return view('welcome');

});

 

Auth::routes();

  

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

  

Route::middleware("auth")->group(function () {

    Route::get('plans', [PlanController::class, 'index'])
        ->name('plans.index');   //  THIS LINE WAS MISSING

    Route::get('plans/{plan}', [PlanController::class, 'show'])
        ->name('plans.show');

    Route::post('subscription', [PlanController::class, 'subscription'])
        ->name('subscription.create');

       // NEW: Cancel subscription
    Route::post('subscription/cancel', [PlanController::class, 'cancel'])
        ->name('subscription.cancel');       
});
