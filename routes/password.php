<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ForgetController;

header('Access-Control-Allow-Origin: *');

header('Access-Control-Allow-Credentials: true');

header('Access-Control-Allow-Headers: *');

header('Access-Control-Allow-Method: *');

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


//Route::get("emailConfirmation/{email}",[UserController::class,"emailConfirmation"]);
Route::post("forgetpassword",[ForgetController::class,"forgetPassword"]);

