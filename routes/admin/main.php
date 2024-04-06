<?php

use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ModuleController;
use Illuminate\Support\Facades\Route;
require __DIR__.'/userManagement/main.php';
/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the prefix "admin" middleware group. Now create something great!
|
*/
//Admin Routes
Route::group(['middleware' => ['auth','verified','xss','user.status','user.module:admin'], 'prefix' => 'admin','as' => 'admin.'], function() {
    //Dashboard
    Route::get('dashboard',[DashboardController::class, 'index'])->name('dashboard');
    //Permission Group
    Route::resource('module',ModuleController::class);
    Route::get('get-module',[ModuleController::class,'getIndex'])->name('get.module');
    Route::get('get-module-select',[ModuleController::class,'getIndexSelect'])->name('get.module-select');
    Route::get('get-module-activity/{id}',[ModuleController::class,'getActivity'])->name('get.module-activity');
    Route::get('get-module-activity-log/{id}',[ModuleController::class,'getActivityLog'])->name('get.module-activity-log');
    Route::get('get-module-activity-trash',[ModuleController::class,'getTrashActivity'])->name('get.module-activity-trash');
    Route::get('get-module-activity-trash-log',[ModuleController::class,'getTrashActivityLog'])->name('get.module-activity-trash-log');
    //Backup
    Route::get('backup',[BackupController::class, 'index'])->name('backup.index');
    Route::get('backup/create',[BackupController::class, 'create'])->name('backup.create');
    Route::get('backup/download/{file_name}',[BackupController::class, 'download'])->name('backup.download');
    Route::get('backup/delete/{file_name}',[BackupController::class, 'delete'])->name('backup.delete');
});


