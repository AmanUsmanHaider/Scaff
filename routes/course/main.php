<?php

use App\Http\Controllers\Academic\AcademicController;
use App\Http\Controllers\Academic\DashboardController;
use App\Http\Controllers\Courses\CourseController;
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
    Route::resource('module2',CourseController::class);
    Route::get('get.course-subject-select',[CourseController::class,'getPermissionGroupIndexSelect'])->name('get.course-subject-select');
    Route::get('get.course-record',[CourseController::class,'getIndex'])->name('get.course-record');
//    Route::get('get-permission-group',[PermissionGroupController::class,'getIndex'])->name('get.permission-group');
    Route::get('get-subject-edit-select',[CourseController::class,'getIndexSelect'])->name('get.subject-edit-select');
   Route::get('get-course-created-activity/{id}',[CourseController::class,'getActivity'])->name('get.course-created-activity');
    Route::get('get-course-created-group-activity-log/{id}',[CourseController::class,'getActivityLog'])->name('get.course-created-activity-log');
//    Route::get('get-permission-group-activity-trash',[PermissionGroupController::class,'getTrashActivity'])->name('get.permission-group-activity-trash');
//    Route::get('get-permission-group-activity-trash-log',[PermissionGroupController::class,'getTrashActivityLog'])->name('get.permission-group-activity-trash-log');
  Route::get('get-subject-attach-courses/{id}',[CourseController::class,'getAttachCourses'])->name('get.subject-attach-courses');
    Route::get('get-course-activity-trash',[CourseController::class,'getTrashActivity'])->name('get.course-activity-trash');
    Route::get('get-course-activity-trash-log',[CourseController::class,'getTrashActivityLog'])->name('get.course-activity-trash-log');
//Filter
    Route::get('get-filter-course-select',[CourseController::class,'getfiltersubject'])->name('get.filter-course-select');

//    //profile
    Route::get('profile/{id}',[ProfileController::class,'edit'])->name('profile');
    Route::put('profile/{id}',[ProfileController::class,'update'])->name('profile.update');
    Route::get('profile-image/{id}',[ProfileController::class,'getImage'])->name('profile.get.image');

});



