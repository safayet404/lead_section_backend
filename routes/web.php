<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\ApplicationStatusController;
use App\Http\Controllers\AssignAoController;
use App\Http\Controllers\AssignCoController;
use App\Http\Controllers\AssignTypeController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\ChannelPartnerController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseTypeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ExpressApplicationController;
use App\Http\Controllers\IntakeController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\LeadStatusController;
use App\Http\Controllers\LeadTypeController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UniversityController;
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
Route::put('/user-update', [UserController::class, 'UserUpdate']);

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
    Route::post('/create-lead', [LeadController::class, 'importLeads']);
    Route::post('/upload-lead', [LeadController::class, 'uploadLeads']);
    Route::post('/file-upload', [LeadController::class, 'CreateLead']);
    Route::get('/lead-list', [LeadController::class, 'LeadList']);
    Route::post('/lead-update', [LeadController::class, 'LeadUpdate']);
    Route::delete('/lead-delete', [LeadController::class, 'DeleteLead']);
    Route::get('/single-lead', [LeadController::class, 'SingleLead']);

    Route::post('/lead-preview', [LeadController::class, 'AssignPreview']);
    Route::post('/lead-assign', [LeadController::class, 'AssignLeads']);
    Route::post('/assign-lead', [LeadController::class, 'AssignSave']);

    Route::get('/summary', [LeadController::class, 'branchManager']);
    Route::get('/admin-lead-summary', [LeadController::class, 'AdminReport']);

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
    Route::get('/country-list', [CountryController::class, 'CountryList']);
    Route::post('/create-country', [CountryController::class, 'CreateCountry']);
    Route::put('/country-update', [CountryController::class, 'CountryUpdate']);
    Route::delete('/country-delete', [CountryController::class, 'CountryDelete']);
    Route::get('/single-country', [CountryController::class, 'SingleCountry']);
});

// Manager Note

Route::middleware([TokenVerificationMiddleware::class])->group(function () {
    Route::post('/create-note', [NoteController::class, 'CreateNote']);
    Route::get('/note-list', [NoteController::class, 'NoteList']);
    Route::put('/note-update', [NoteController::class, 'NoteUpdate']);
    Route::delete('/note-delete', [NoteController::class, 'NoteDelete']);
    Route::get('/single-note', [NoteController::class, 'SingleNote']);
});

// Express Application

Route::post('/express-application', [ExpressApplicationController::class, 'CreateApplication']);
Route::get('/express-list', [ExpressApplicationController::class, 'ExpressApplicationList']);
Route::get('/single-express', [ExpressApplicationController::class, 'SingleExpressApplication']);

// University

Route::post('/create-university', [UniversityController::class, 'CreateUniversity']);
Route::get('/all-university', [UniversityController::class, 'AllUniversity']);

// Course Type

Route::post('/create-type', [CourseTypeController::class, 'CreateCourseType']);
Route::get('/all-course-type', [CourseTypeController::class, 'AllCourseType']);

// Intake

Route::post('/create-intake', [IntakeController::class, 'CreateIntake']);
Route::get('/all-intake', [IntakeController::class, 'AllIntake']);

// Course

Route::post('/create-course', [CourseController::class, 'CreateCourse']);
Route::get('/all-course', [CourseController::class, 'AllCourse']);

// Course Filtering

Route::get('/countries', [CourseController::class, 'Countries']);
Route::get('/intakes/{countryId}', [CourseController::class, 'IntakeByCountry']);
Route::get('/course-types/{countryId}/{intakeId}', [CourseController::class, 'CourseTypes']);

Route::get('/universities/{countryId}/{intakeId}/{courseTypeId}', [CourseController::class, 'Universities']);

Route::get('/courses/{countryId}/{intakeId}/{universityId}/{courseTypeId}', [CourseController::class, 'Courses']);

// Channel Partner

Route::post('/create-channel', [ChannelPartnerController::class, 'CreateChannelPartner']);
Route::get('/all-channel', [ChannelPartnerController::class, 'AllChannelPartner']);

// Application Status

Route::post('/create-application-status', [ApplicationStatusController::class, 'CreateApplicationStatus']);
Route::get('/all-application-status', [ApplicationStatusController::class, 'AllStatus']);

// Students

Route::post('/create-student', [StudentController::class, 'CreateStudent']);
Route::get('/all-students', [StudentController::class, 'AllStudents']);

// Applications

Route::post('/create-application', [ApplicationController::class, 'CreateApplication']);
Route::get('/all-application', [ApplicationController::class, 'ApplicationList']);

Route::middleware([TokenVerificationMiddleware::class])->group(function () {

    Route::post('/create-application', [ApplicationController::class, 'CreateApplication']);
    Route::post('/student-application', [ApplicationController::class, 'CreateStudentWithApplication']);
    Route::get('/all-application', [ApplicationController::class, 'ApplicationList']);
    Route::get('/single-application/{id}', [ApplicationController::class, 'SingleApplication']);

});

Route::post('/assign-ao', [AssignAoController::class, 'AssignAo']);
Route::get('/assign-ao-list', [AssignAoController::class, 'AssignList']);
Route::get('/single-assign/{applicationId}',[AssignAoController::class,'SingleApplication']);


Route::post('/assign-co', [AssignCoController::class, 'AssignCo']);
Route::get('/assign-co-list', [AssignAoController::class, 'AssignList']);
