<?php

use App\Http\Controllers\LeadsFormController;
use App\Http\Middleware\ZohoCRMMiddleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

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

Route::post('/leads', function (Request $request) {
    return app(LeadsFormController::class)($request);
})->middleware(ZohoCRMMiddleware::class);
