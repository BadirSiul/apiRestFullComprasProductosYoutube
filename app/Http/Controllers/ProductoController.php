<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Http\Controllers\Responses\ApiResponse;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class ProductoController extends Controller
{
    public function index()
    {
        try {
            // Todo el codigo Funciona Bien.
            //todo el codigo inicial.
            $productos = Producto::all(); // message statuscode error data
            return Apiresponse::success('Productos obtenidos', 200, $productos);
        } catch (Exception $e) {
            return ApiResponse::error('Ocurrio un error al obtener los productos: ' . $e->getMessage(), 500);
        }
    }

    public function store(Request $request)
    {
        try{

            $request->validate([
                'nombre' => 'required|unique:productos',
                'precio' => 'required|numeric|between:0,999999.99',
                'cantidad_disponible' => 'required|integer|min:0',
                'categoria_id' => 'required|exists:categorias,id',
                'marca_id' => 'required|exists:marcas,id',
            ]);

            $producto = Producto::create($request->all());
            return ApiResponse::success('Producto creado exitosamente', 201, $producto);

        }catch(ValidationException $e){
            $errors = $e->validator->errors()->toArray();

            if (isset($errors['categoria_id'])) {
                $errors['categoria'] = $errors['categoria_id'];
                unset($errors['categoria_id']);

            }

            return ApiResponse::error('Error de validacion' .$e->getMessage(), 422,$errors);
        }
    }

    public function show($id)
    {
        try {
            $producto = Producto::findOrfail($id);
            return ApiResponse::success('Producto obtenido exitosamente', 200, $producto);
        }catch(ModelNotFoundException $e){
            return ApiResponse::error('¡Producto no encontrado!', 404);

        }
    }

    public function update(Request $request, $id)
    {
        try {
            $producto = Producto::findOrFail($id);
            $producto->update($request->all());
            $request->validate([    
                'nombre' => 'required|unique:productos,nombre,'.$producto->id,
                'precio' => 'required|numeric|between:0,999999.99',
                'cantidad_disponible' => 'required|integer|min:0',
                'categoria_id' => 'required|exists:categorias,id',
                'marca_id' => 'required|exists:marcas,id',
            ]);
            return ApiResponse::success('Producto actualizado exitosamente', 200, $producto);

        }catch(ValidationException $e){
            $errors = $e->validator->errors()->toArray();

            if (isset($errors['categoria_id'])) {
                $errors['categoria'] = $errors['categoria_id'];
                unset($errors['categoria_id']);

            }

            if (isset($errors['marca_id'])) {
                $errors['marca'] = $errors['marca_id'];
                unset($errors['marca_id']);

            }
            return ApiResponse::error('Error de validacion' .$e->getMessage(), 422,$errors);
        } catch(ModelNotFoundException $e){
            return ApiResponse::error('¡Producto no encontrado!', 404);

        } catch(Exception $e){
            return ApiResponse::error('Ocurrio un error al actualizar el producto: ' . $e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        # code
    }
}
