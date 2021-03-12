<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// 1.- Solo el servidor de API debería tener acceso a este endpoint, por lo que sería bueno poner una validación de CORS aquí
// 2.- El cliente debe mandarle su token al servidor de API a través del Authorization header como Bearer, este debe tener un middleware en todas sus rutas protegidas, este middleware debe hacer el request a este endpoint volviendo a mandar el token en el Authorization Header como Bearer, este endpoint responderá al middleware si el token es válido o no
Route::post('/oauth/verify-token', function (Request $request) {

    // Este chequeo internamente accede al Authorization header para verificar el token
    return response()->json([
        "is_valid_token" => Auth::guard('api')->check()
    ]);

});