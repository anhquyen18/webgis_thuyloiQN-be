<?php

use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\ShapefileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PolicyController;
use App\Http\Controllers\ReservoirController;
use App\Http\Controllers\ReservoirSafetyController;

use App\Models\Department;
use App\Models\Organization;
use App\Models\Policy;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::get('get-lakes', [ShapefileController::class, 'index']);

// Cần  đổi 2 phướng thức này thành phương thức GET
Route::post('get-feature-info', [ShapefileController::class, 'getFeatureInfo']);
Route::post('search-feature-name', [ShapefileController::class, 'searchFeatureName']);


Route::post('login', [UserController::class, 'login']);

Route::group(['middleware' => 'jwt'], function () {
    Route::get('get-authenticated-user', [UserController::class, 'getAuthenticatedUser']);
    Route::put('update-user-info/{id}', [UserController::class, 'updateUserInfo']);
    Route::put('update-user-password/{id}', [UserController::class, 'updateUserPassword']);
    Route::post('upload-user-avatar/{id}', [UserController::class, 'uploadUserAvatar']);

    Route::get('/departments/{id}', [DepartmentController::class, 'getDepartment']);
});


Route::group(['middleware' => ['jwt', 'jwt.AllowAccessOrganization:1,2']], function () {
    Route::get('get-no-department-user', [UserController::class, 'getNoDepartmentUser']);

    Route::get('organization/{organizationId}/departments', [DepartmentController::class, 'index']);
    Route::post('departments/create', [DepartmentController::class, 'create']);
    Route::delete('departments/delete', [DepartmentController::class, 'delete']);
    Route::put('departments/{departmentId}/update-info', [DepartmentController::class, 'updateInfo']);
    Route::put('departments/{departmentId}/add-users', [DepartmentController::class, 'addUsers']);
    Route::put('departments/{departmentId}/remove-users', [DepartmentController::class, 'removeUsers']);
    Route::post('departments/{departmentId}/add-policies', [DepartmentController::class, 'addPolicies']);
    Route::delete('departments/{departmentId}/remove-policies', [DepartmentController::class, 'removePolicies']);

    Route::get('get-policies', [PolicyController::class, 'index']);
    Route::get('departments/{departmentId}/get-policies-not-in-department', [PolicyController::class, 'getPoliciesNotInDepartment']);

    Route::get('/get-users', [UserController::class, 'getUsers']);

    Route::get('/get-user-by-id/{id}', [UserController::class, 'getUserById']);
    Route::put('/users/update-user-profile/{id}', [UserController::class, 'updateUserProfile']);
});

Route::group(['middleware' => ['jwt', 'jwt.AllowAccessOrganizations:2']], function () {
    Route::get('get-organizations', [OrganizationController::class, 'index']);
    Route::get('get-no-organization-departments', [DepartmentController::class, 'getNoOrganizationDepartments']);
});

// Những quyền có thể tạo và chỉnh sửa các báo cáo
$fullAccessReports = 'jwt.AcceptedPolicies:' . '6,8,10';
Route::group(['middleware' => ['jwt', $fullAccessReports]], function () {
    Route::post('upload-temporary-image', [ReservoirSafetyController::class, 'uploadTemporaryImage']);
    Route::delete('delete-temporary-image', [ReservoirSafetyController::class, 'deleteTemporaryImage']);
    Route::post('reservoirs/{id}/safety-report', [ReservoirSafetyController::class, 'createSafetyReport']);
    Route::get('reservoirs/index', [ReservoirController::class, 'index']);
    Route::get('reservoirs/{id}/info', [ReservoirController::class, 'getReservoir']);
});

// Quyền có thể đọc các báo cáo an toàn
$readOnlySafetyReports = 'jwt.AcceptedPolicies:' . '9,10';
Route::group(['middleware' => ['jwt', $readOnlySafetyReports]], function () {
    Route::get('reservoirs/safety-reports', [ReservoirSafetyController::class, 'index']);
});
// Toàn quyền với các báo cáo an toàn
$fullAccessSafetyReports = 'jwt.AcceptedPolicies:' . '10';
Route::group(['middleware' => ['jwt', $readOnlySafetyReports]], function () {
    Route::delete('reservoirs/safety-reports/delete', [ReservoirSafetyController::class, 'deleteSafetyReports']);
});




// Route::group(['middleware' => ['jwt', 'jwt.role:2']], function () {
//     Route::post('update-feature-info', [ShapefileController::class, 'updateFeatureInfo']);
//     Route::put('update-feature-geom', [ShapefileController::class, 'updateFeatureGeom']);
// });

Route::post('test-somethings', [UserController::class, 'testSomethings']);
