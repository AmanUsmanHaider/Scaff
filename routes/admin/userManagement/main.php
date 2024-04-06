<?php

use App\Http\Controllers\Admin\UserManagement\DashboardController;
use App\Http\Controllers\Admin\UserManagement\LogoutController;
use App\Http\Controllers\Admin\UserManagement\PermissionController;
use App\Http\Controllers\Admin\UserManagement\PermissionGroupController;
use App\Http\Controllers\Admin\UserManagement\ProfileController;
use App\Http\Controllers\Admin\UserManagement\RoleController;
use App\Http\Controllers\Admin\UserManagement\UserController;
use Illuminate\Support\Facades\Route;
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
Route::get('/dashboard',[DashboardController::class, 'index'])->name('dashboard');
Route::get('/switch-module',[DashboardController::class, 'getSwitchModule'])->name('get.switch-module');
Route::get('/logout',[LogoutController::class, 'logout' ])->name('get.logout');

Route::group(['middleware' => ['auth','verified','xss','user.status','user.module:admin'], 'prefix' => 'admin','as' => 'admin.'], function() {
    //Permission Group
    Route::resource('permission-group',PermissionGroupController::class);
    Route::get('get-permission-group',[PermissionGroupController::class,'getIndex'])->name('get.permission-group');
    Route::get('get-permission-group-select',[PermissionGroupController::class,'getIndexSelect'])->name('get.permission-group-select');
    Route::get('get-permission-group-activity/{id}',[PermissionGroupController::class,'getActivity'])->name('get.permission-group-activity');
    Route::get('get-permission-group-activity-log/{id}',[PermissionGroupController::class,'getActivityLog'])->name('get.permission-group-activity-log');
    Route::get('get-permission-group-activity-trash',[PermissionGroupController::class,'getTrashActivity'])->name('get.permission-group-activity-trash');
    Route::get('get-permission-group-activity-trash-log',[PermissionGroupController::class,'getTrashActivityLog'])->name('get.permission-group-activity-trash-log');
    //Permission
    Route::resource('permissions',PermissionController::class);
    Route::get('get-permissions',[PermissionController::class,'getIndex'])->name('get.permissions');
    Route::get('get-permission-permission-group-select',[PermissionController::class,'getPermissionGroupIndexSelect'])->name('get.permission-permission-group-select');
    Route::get('get-permission-module-select',[PermissionController::class,'getPermissionModuleIndexSelect'])->name('get.permission-module-select');
    //Role
    Route::resource('role',RoleController::class);
    Route::get('get-role',[RoleController::class,'getIndex'])->name('get.role');
    //User
    Route::resource('user',UserController::class);
    Route::get('get-user',[UserController::class,'getIndex'])->name('get.user');
    Route::get('get-user-role-select',[UserController::class,'getIndexSelect'])->name('get.user-role-select');
    Route::get('get-user-activity/{id}',[UserController::class,'getActivity'])->name('get.user-activity');
    Route::get('get-user-activity-log/{id}',[UserController::class,'getActivityLog'])->name('get.user-activity-log');
    Route::get('get-user-activity-trash',[UserController::class,'getTrashActivity'])->name('get.user-activity-trash');
    Route::get('get-user-activity-trash-log',[UserController::class,'getTrashActivityLog'])->name('get.user-activity-trash-log');
    //Profile
    Route::get('profile/{id}',[ProfileController::class,'edit'])->name('profile');
    Route::put('profile/{id}',[ProfileController::class,'update'])->name('profile.update');
    Route::get('profile-image/{id}',[ProfileController::class,'getImage'])->name('profile.get.image');
});


