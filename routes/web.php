<?php

use App\Http\Controllers\AssignTypeController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\LeadStatusController;
use App\Http\Controllers\LeadTypeController;
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
    Route::put('/branch-update', [BranchController::class, 'BranchUpdate']);
    Route::delete('/branch-delete', [BranchController::class, 'BranchDelete']);
    Route::get('/single-branch', [BranchController::class, 'SingleBranch']);
});

// Role
Route::post('/create-role', [RoleController::class, 'CreateRole']);
Route::get('/role-list', [RoleController::class, 'RoleList']);
Route::get('/single-role', [RoleController::class, 'SingleRole']);
Route::put('/role-update', [RoleController::class, 'RoleUpdate']);
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

// Event

Route::middleware([TokenVerificationMiddleware::class])->group(function () {
    Route::post('/create-event', [EventController::class, 'CreateEvent']);
    Route::get('/event-list', [EventController::class, 'EventList']);
    Route::put('/event-update', [EventController::class, 'EventUpdate']);
    Route::delete('/event-delete', [EventController::class, 'EventDelete']);
    Route::get('/single-event', [EventController::class, 'SingleEvent']);
});


// Lead Type


Route::middleware([TokenVerificationMiddleware::class])->group(function () {
    Route::post('/create-type', [LeadTypeController::class, 'CreateLeadType']);
    Route::get('/type-list', [LeadTypeController::class, 'LeadTypeList']);
    Route::put('/type-update', [LeadTypeController::class, 'LeadTypeUpdate']);
    Route::delete('/type-delete', [LeadTypeController::class, 'LeadTypeDelete']);
    Route::get('/single-type', [LeadTypeController::class, 'SingleLeadType']);
});


// Assign Type 

Route::middleware([TokenVerificationMiddleware::class])->group(function () {
    Route::post('/create-assign', [AssignTypeController::class, 'CreateAssignType']);
    Route::get('/assign-list', [AssignTypeController::class, 'AssignTypeList']);
    Route::put('/assign-update', [AssignTypeController::class, 'AssignTypeUpdate']);
    Route::delete('/assign-delete', [AssignTypeController::class, 'AssignTypeDelete']);
    Route::get('/single-assign', [AssignTypeController::class, 'SingleAssignType']);
});

// County

Route::middleware([TokenVerificationMiddleware::class])->group(function () {
    Route::post('/create-country', [CountryController::class, 'CreateCountry']);
    Route::get('/country-list', [CountryController::class, 'CountryList']);
    Route::put('/country-update', [CountryController::class, 'CountryUpdate']);
    Route::delete('/country-delete', [CountryController::class, 'CountryDelete']);
    Route::get('/single-country', [CountryController::class, 'SingleCountry']);
});