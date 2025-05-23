<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CompraController;
use App\Models\Categoria;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::apiresource('marcas', MarcaController::class);
Route::apiResource('categorias', CategoriaController::class);
Route::apiresource('productos', ProductoController::class);
Route::apiresource('compras', CompraController::class);

Route::get('categorias/{categoria}/productos', [CategoriaController::class, 'productosPorCategoria']);
Route::get('marcas/{marca}/productos', [MarcaController::class, 'productosPorMarca']);
