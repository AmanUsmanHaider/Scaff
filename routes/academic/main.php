<?php

use App\Http\Controllers\Academic\AcademicController;
use App\Http\Controllers\Academic\DashboardController;
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


Route::group(['middleware' => ['auth','verified','xss','user.status','user.module:academic'], 'prefix' => 'academic','as' => 'academic.'], function() {
    //Permission Group
    Route::get('dashboard',[DashboardController::class, 'index'])->name('dashboard');
    Route::resource('module',AcademicController::class);
    Route::get('get.academic-subject-select',[AcademicController::class,'getPermissionGroupIndexSelect'])->name('get.academic-subject-select');
    Route::get('get.academic-record',[AcademicController::class,'getIndex'])->name('get.academic-record');
//    Route::get('get-permission-group',[PermissionGroupController::class,'getIndex'])->name('get.permission-group');
    Route::get('get-subject-edit-select',[AcademicController::class,'getIndexSelect'])->name('get.subject-edit-select');
   Route::get('get-academic-created-activity/{id}',[AcademicController::class,'getActivity'])->name('get.academic-created-activity');
    Route::get('get-academic-created-group-activity-log/{id}',[AcademicController::class,'getActivityLog'])->name('get.academic-created-activity-log');
//    Route::get('get-permission-group-activity-trash',[PermissionGroupController::class,'getTrashActivity'])->name('get.permission-group-activity-trash');
//    Route::get('get-permission-group-activity-trash-log',[PermissionGroupController::class,'getTrashActivityLog'])->name('get.permission-group-activity-trash-log');
  Route::get('get-subject-attach-courses/{id}',[AcademicController::class,'getAttachCourses'])->name('get.subject-attach-courses');
    Route::get('get-academic-activity-trash',[AcademicController::class,'getTrashActivity'])->name('get.academic-activity-trash');
    Route::get('get-academic-activity-trash-log',[AcademicController::class,'getTrashActivityLog'])->name('get.academic-activity-trash-log');
//Filter
    Route::get('get-filter-subject-select',[AcademicController::class,'getfiltersubject'])->name('get.filter-subject-select');

//    //profile
    Route::get('profile/{id}',[ProfileController::class,'edit'])->name('profile');
    Route::put('profile/{id}',[ProfileController::class,'update'])->name('profile.update');
    Route::get('profile-image/{id}',[ProfileController::class,'getImage'])->name('profile.get.image');

});



