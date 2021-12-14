<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UpdateUserController;
use App\Http\Controllers\ImageController;

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


Route::post("updateuser",[UpdateUserController::class,"updateUser"])->middleware('UserAuthentication');
Route::post("profilepic",[UpdateUserController::class,"profilePic"])->middleware('UserAuthentication');
Route::post("changepassword",[UpdateUserController::class,"changePassword"])->middleware('UserAuthentication');
Route::post("setprivacy",[UpdateUserController::class,"setPrivacy"])->middleware('UserAuthentication');


//Route::post("getuserdata",[UpdateUserController::class,"getUserData"]);

