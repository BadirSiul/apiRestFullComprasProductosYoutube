<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Responses\ApiResponse;
use Illuminate\Support\Facades\Validator;
use App\Models\Producto;
use App\Models\Compra;
use GuzzleHttp\Psr7\Query;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Database\QueryException;


class CompraController extends Controller
{
    public function index()
    {
        
    }

    public function store(Request $request)
    {
        try {
            $productos = $request->input('productos');

            // Validar los productos 
            if (empty($productos)) {
                return Apiresponse::error(['error' => 'No se han proporcionado productos.'], 400);
            }

            // Validar cada producto
            $validator = Validator::make($request->all(), [
                'productos' => 'required|array',
                'productos.*.producto_id' => 'required|integer|exists:productos,id',
                'productos.*.cantidad' => 'required|integer|min:1',
            ]);

            if ($validator->fails()) {
                return ApiResponse::error('Datos Invalidtos en la lista de productos', 400, $validator->errors());
            }

            // Validar productos duplicados
            $productoIds = array_column($productos, 'producto_id');
            if (count($productoIds) !== count(array_unique($productoIds))) {
                return ApiResponse::error('No se permiten productos duplicados en la lista.', 400);
            }

            $totalPagar = 0;
            $subtotal = 0;
            $compraItems = [];


            // Iteraccion de los productos para calcular el total a pagar.
            foreach ($productos as $producto) {
                $productoB = Producto::find($producto['producto_id']);
                if (!$productoB) {
                    return ApiResponse::error('Producto no encontrado', 404);
                }

                // Validar la cantidad disponible de los productos

                if ($productoB->cantidad_disponible < $producto['cantidad']) {
                    return ApiResponse::error('No hay suficiente cantidad disponible para el producto: ', 400);

                }

                // Actualizacion de la cantidad disponible de cada producto
                $productoB->cantidad_disponible -= $producto['cantidad'];
                $productoB->save();

                // calculo de los importes
                $subtotal = $productoB->precio * $producto['cantidad'];
                $totalPagar += $subtotal;

                // Items de la compra
                $compraItems[] = [
                    'producto_id' => $productoB->id,
                    'precio' => $productoB->precio,
                    'cantidad' => $producto['cantidad'],
                    'subtotal' => $subtotal,
                ];

            }

            // Registro en la tabla de compras
            $compra = Compra::create([
                'subtotal' => $totalPagar,
                'total' => $totalPagar,
            ]);

            // Asignar los productos a la compraa con sus cantidades y sus subtotales
            $compra->productos()->attach($compraItems);

            return ApiResponse::success('Compra realizada exitosamente', 201, [
                'compra' => $compra,
                'productos' => $compraItems,
            ]);

        }catch (QueryException $e){
            // Error de consulta en la base de datos
            return ApiResponse::error('Error enn la consulta de base datos', 500);
        }catch (Exception $e){
            
            return ApiResponse::error('Error Inesperado', 500);
        }
    }

    public function show($id)
    {
        # code
    }
}
