<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\http\controllers\MercadoLivreAuthController;

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
Route::post('/auth/get_link', [MercadoLivreAuthController::class, 'get_link']);
Route::get('/auth/code', [MercadoLivreAuthController::class, 'get_first_code']);
Route::get('/product', [MercadoLivreProductController::class, 'index']);
Route::get('/sale', [MercadoLivreSaleController::class, 'index']);
