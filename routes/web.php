<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\ApprovalRequestController;
use App\Http\Controllers\auth\ForgotPasswordController;
use App\Http\Controllers\auth\LoginController;
use App\Http\Controllers\auth\RegisterController;
use App\Http\Controllers\auth\ResetPasswordController;
use App\Http\Controllers\auth\VerifyEmailController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeaveRequestAttachmentController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\LeaveRequestRevocation;
use App\Http\Controllers\LeaveTypeController;
use App\Http\Controllers\LeaveTypeLevelController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleAssignmentController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StructureAssignmentController;
use App\Http\Controllers\StructureController;
use App\Http\Controllers\StructureRequestController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserStatusController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;
Route::group([],function(){
   Route::get('/',WelcomeController::class)->name('welcome');
   Route::get('/about',AboutController::class)->name('about');
    Route::get('/forgot-password' , [ForgotPasswordController::class,'create'])->name('password.forgot');
    Route::post('/forgot-password' , [ForgotPasswordController::class,'store'])->name('password.forgot.store');
    Route::get('/reset-password' , [ResetPasswordController::class,'create'])->name('reset.password');
    Route::post('/reset-password' , [ResetPasswordController::class,'store'])->name('reset.password.store');
});
Route::group(['middleware' => 'guest'],function(){
    Route::get('/login',[LoginController::class,'create'])->name('login');
    Route::post('/login',[LoginController::class,'store'])->name('login.store');
    Route::get('/register',[RegisterController::class,'create'])->name('register');
    Route::post('/register',[RegisterController::class,'store'])->name('register.store');
});

Route::group(['middleware' => 'auth'],function(){
    Route::delete('/logout',[LoginController::class,'destroy'])->name('logout');
    Route::get('/verify-email', [VerifyEmailController::class, 'create'])->name('verification.notice');
    Route::post('/email/verification-notification', [VerifyEmailController::class, 'store'])->name('verification.send')->middleware('throttle:6,1');
    Route::get('/verify-email/{id}/{hash}', [VerifyEmailController::class, 'update'])->name('verification.verify')->middleware('signed');
});

Route::group(['middleware' => ['auth', 'verified', 'has.structure']],function(){
    Route::get('/dashboard',DashboardController::class)->name('dashboard');
    Route::post('/user-status', UserStatusController::class)->name('user-status.update');
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
    Route::get('/calendar/{year}/{month}/{day}', [CalendarController::class, 'show'])->name('calendar.show');
    Route::post('/notifications', [NotificationController::class, 'store'])->name('notifications.read');
    Route::patch('/notifications/{notification}', [NotificationController::class, 'update'])->name('notifications.update');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/users/create' , [UserController::class,'create'])->name('users.create');
    Route::post('/users' , [UserController::class,'store'])->name('users.store');
    Route::get('/users' , [UserController::class,'index'])->name('users.index');
    Route::get('/users/{user}' , [UserController::class,'show'])->name('users.show');
    Route::get('/users/{user}/edit' , [UserController::class,'edit'])->name('users.edit');
    Route::patch('/users/{user}' , [UserController::class,'update'])->name('users.update');
    Route::delete('/users/{user}' , [UserController::class,'destroy'])->name('users.destroy');

    Route::get('/roles',[RoleController::class,'index'])->name('roles.index');
    Route::get('/roles/create',[RoleController::class,'create'])->name('roles.create');
    Route::post('/roles',[RoleController::class,'store'])->name('roles.store');
    Route::get('/roles/{role}/edit',[RoleController::class,'edit'])->name('roles.edit');
    Route::get('/roles/{role}',[RoleController::class,'show'])->name('roles.show');
    Route::patch('/roles/{role}',[RoleController::class,'update'])->name('roles.update');
    Route::delete('/roles/{role}',[RoleController::class,'destroy'])->name('roles.destroy');

    Route::post('/role-assignment',[RoleAssignmentController::class,'store'])->name('role.assignment');

    Route::get('/leave-types', [LeaveTypeController::class, 'index'])->name('leave-types.index');
    Route::get('/leave-types/create', [LeaveTypeController::class, 'create'])->name('leave-types.create');
    Route::post('/leave-types', [LeaveTypeController::class, 'store'])->name('leave-types.store');Route::get('/leave-types/{leaveType}', [LeaveTypeController::class, 'show'])->name('leave-types.show');
    Route::get('/leave-types/{leaveType}/edit', [LeaveTypeController::class, 'edit'])->name('leave-types.edit');
    Route::patch('/leave-types/{leaveType}', [LeaveTypeController::class, 'update'])->name('leave-types.update');
    Route::delete('/leave-types/{leaveType}', [LeaveTypeController::class, 'destroy'])->name('leave-types.destroy');

    Route::get('/structures',[StructureController::class,'index'])->name('structures.index');
    Route::get('/structures/create',[StructureController::class,'create'])->name('structures.create');
    Route::get('/structures/{structure}',[StructureController::class,'show'])->name('structures.show');
    Route::post('/structures',[StructureController::class,'store'])->name('structures.store');
    Route::delete('/structures/{structure}',[StructureController::class,'destroy'])->name('structures.destroy');


    Route::get('/structure-assignment/{structure}/create',[StructureAssignmentController::class,'create'])->name('structure.assignment.create');
    Route::post('/structure-assignment',[StructureAssignmentController::class,'store'])->name('structure.assignment.store');
    Route::get('/structure-assignment/{structure}/edit',[StructureAssignmentController::class,'edit'])->name('structure.assignment.edit');
    Route::patch('/structure-assignment',[StructureAssignmentController::class,'update'])->name('structure.assignment.update');

    Route::get('/leave-requests/create', [LeaveRequestController::class, 'create'])->name('leave-requests.create');
    Route::get('/leave-requests', [LeaveRequestController::class, 'index'])->name('leave-requests.index');
    Route::post('/leave-requests', [LeaveRequestController::class, 'store'])->name('leave-requests.store');
    Route::get('/leave-requests/{leaveRequest}', [LeaveRequestController::class, 'show'])->name('leave-requests.show');
    Route::delete('/leave-requests/{leaveRequest}', [LeaveRequestController::class, 'destroy'])->name('leave-requests.cancel');
    Route::patch('/leave-requests/{leaveRequest}/revoke', [LeaveRequestRevocation::class, 'update'])->name('leave-requests.revoke');
    Route::get('/leave-requests/{leaveRequest}/attachment', LeaveRequestAttachmentController::class)->name('leave-requests.attachment');

    Route::get('/leave-approvals', [ApprovalRequestController::class, 'index'])->name('leave-approvals.index');
    Route::patch('/leave-approvals/{approvalRequest}', [ApprovalRequestController::class, 'update'])->name('leave-approvals.update');

    Route::post('leave-types/level',[LeaveTypeLevelController::class,'store'])->name('leave-types-level.store');
});

Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::get('/structure-requests/create', [StructureRequestController::class, 'create'])->name('structure-requests.create');
    Route::get('/structure-requests', [StructureRequestController::class, 'index'])->name('structure-requests.index');
    Route::get('/structure-requests/{structureRequest}', [StructureRequestController::class, 'show'])->name('structure-requests.show');
    Route::post('/structure-requests', [StructureRequestController::class, 'store'])->name('structure-requests.store');
});
