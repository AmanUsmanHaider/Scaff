<?php

use Illuminate\Support\Facades\Route;
//Import Admin Routes
require __DIR__.'/auth.php';
require __DIR__.'/course/main.php';
require __DIR__.'/academic/main.php';
require __DIR__.'/admin/main.php';
require __DIR__.'/manager/main.php';
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

