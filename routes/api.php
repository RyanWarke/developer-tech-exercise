<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ClassController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('/school')->controller(SchoolController::class)->group(function () {
    Route::get('/', 'getSchool');
    Route::post('/resync', 'resyncSchoolData');
});

Route::get('/employees', EmployeeController::class);
Route::get('/class/{employee}', [ClassController::class, 'getEmployeesClasses']);