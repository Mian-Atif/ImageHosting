<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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

Route::post("uploadimage",[ImageController::class,"upLoadImage"])->middleware('UserAuthentication');
Route::post("showall",[ImageController::class,"showAll"])->middleware('UserAuthentication');
Route::post("deletephoto",[ImageController::class,"deletePhoto"])->middleware('UserAuthentication');
Route::post("searchphoto",[ImageController::class,"searchPhoto"])->middleware('UserAuthentication');
Route::post("listhiddenphoto",[ImageController::class,"listHiddenPhoto"])->middleware('UserAuthentication');
Route::post("listprivatephoto",[ImageController::class,"listPrivatePhoto"])->middleware('UserAuthentication');
Route::post("listpublicphoto",[ImageController::class,"listPublicPhoto"])->middleware('UserAuthentication');
Route::post("sharedphoto",[ImageController::class,"sharedPhoto"])->middleware('UserAuthentication');
Route::post("checkSharedPhoto",[ImageController::class,"checkSharedPhoto"])->middleware('UserAuthentication');


