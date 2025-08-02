<?php

use App\Http\Controllers\BranchController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\LeadStatusController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\TokenVerificationMiddleware;
use App\Models\Role;
use Illuminate\Support\Facades\Route;

Route::post('/test', function () {
    return response()->json(['status' => 'ok']);
});

Route::post('/registration', [UserController::class, 'UserRegistration']);
Route::post('/login', [UserController::class, 'UserLogin']);
Route::post('/logout', [UserController::class, 'UserLogout']);
Route::post('/reset-password', [UserController::class, 'ResetPassword']);
Route::get('/user-list', [UserController::class, 'UserList']);

Route::middleware([TokenVerificationMiddleware::class])->group(function () {
    Route::get('profile', [UserController::class, 'UserProfile']);
});

// Branch

Route::middleware([TokenVerificationMiddleware::class])->group(function () {
    Route::post('/create-branch', [BranchController::class, 'CreateBranch']);
    Route::get('/branch-list', [BranchController::class, 'BranchList']);
    Route::post('/branch-update', [BranchController::class, 'BranchUpdate']);
    Route::delete('/branch-delete', [BranchController::class, 'BranchDelete']);
    Route::get('/single-branch', [BranchController::class, 'SingleBranch']);
});

// Role
Route::post('/create-role', [RoleController::class, 'CreateRole']);
Route::get('/role-list', [RoleController::class, 'RoleList']);
Route::get('/single-role', [RoleController::class, 'SingleRole']);
Route::post('/role-update', [RoleController::class, 'RoleUpdate']);
Route::delete('/role-delete', [RoleController::class, 'RoleDelete']);

// Lead Status

Route::middleware([TokenVerificationMiddleware::class])->group(function () {
    Route::post('/create-status', [LeadStatusController::class, 'CreateLeadStatus']);
    Route::get('/status-list', [LeadStatusController::class, 'LeadStatusList']);
    Route::post('/status-update', [LeadStatusController::class, 'LeadStatusUpdate']);
    Route::delete('/status-delete', [LeadStatusController::class, 'LeadStatusDelete']);
    Route::get('/single-status', [LeadStatusController::class, 'SingleLeadStatus']);
});

// Lead

Route::middleware([TokenVerificationMiddleware::class])->group(function () {
    Route::post('/create-lead', [LeadController::class, 'CreateLead']);
    Route::get('/lead-list', [LeadController::class, 'LeadList']);
    Route::post('/lead-update', [LeadController::class, 'LeadUpdate']);
    Route::delete('/lead-delete', [LeadController::class, 'DeleteLead']);
    Route::get('/single-lead', [LeadController::class, 'SingleLead']);
});
