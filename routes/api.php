<?php

use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\SystemsController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChargingController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\ProjectExportController;
use App\Http\Controllers\ExportTemplateController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::middleware("auth:sanctum")->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('/role', RoleController::class);
    Route::apiResource('/team', TeamController::class); 
    Route::apiResource('/user', UserController::class);
    Route::apiResource('/category', CategoryController::class);
    Route::apiResource('/systems', SystemsController::class);
    Route::get('/progress', [ProgressController::class, 'index']);
    Route::post('/progress', [ProgressController::class, 'store']);
    Route::get('/progress/{id}', [ProgressController::class, 'show']);
    Route::put('/progress', [ProgressController::class, 'update']); 
    Route::get('/compute_per_team/{teamId}', [ProgressController::class, 'compute_per_team']);
    Route::post('/sync_charging', [ChargingController::class, 'sync_charging']);
    Route::get('/export_template', [ExportTemplateController::class, 'export']);
    Route::get('/export', [ProjectExportController::class, 'export']);
    Route::post('/import', [ImportController::class, 'import']);
    Route::put('/change_password', [AuthController::class, 'changePassword']);
    Route::patch('/reset_password/{id} ', [AuthController::class, 'resetPassword']);
});


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);



