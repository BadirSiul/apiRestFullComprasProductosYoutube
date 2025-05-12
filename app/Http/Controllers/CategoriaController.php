<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use App\Http\Controllers\Responses\ApiResponse;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;


class CategoriaController extends Controller
{
    //Este metodo se encarga de obtener todas las categorias.
    public function index()
    {
        try{
            // Todo el codigo Funciona Bien.
            //todo el codigo inicial.
            $categorias = Categoria::all(); // message statuscode error data
            return Apiresponse::success('Categorias obtenidas', 200, $categorias);
            // throw new Exception('Error de prueba');

        }catch(Exception $e){

            // Si hay un error en el try, se ejecuta este bloque
            // Puedes manejar el error aquí, por ejemplo, registrarlo o devolver una respuesta de error

            return ApiResponse::error('Ocurrio un error al obtener las categorias: '.$e->getMessage(), 500);
        }
    }
    //Este metodo se encarga de crear una nueva categoria.

    public function store(Request $request)
    {
        try{
            $request->validate([
                'nombre' => 'required|unique:categorias'
            ]);

            $categoria = Categoria::create($request->all());
            return ApiResponse::success('Categoria creada exitosamente', 201, $categoria);
        }catch(ValidationException $e){

            return ApiResponse::error('Error de validacion', 422);

        }
    }

    public function show($id)
    {
        try{
            $categoria = Categoria::findOrFail($id);
            return ApiResponse::success('Categoria obtenida', 200, $categoria);

        }catch(ModelNotFoundException $e){

            return ApiResponse::error('¡Categoria no encontrada!', 404);

        }
    }

    public function update(Request $request, $id)
    {
        try{
            $categoria = Categoria::findOrFail($id); // nombre = 'Lacteos' $request->nombre = 'Lacteos'
            $request->validate([
                'nombre' => ['required', Rule::unique('categorias')->ignore($categoria)]
            ]);
            $categoria->update($request->all());
            return ApiResponse::success('Categoria actualizada exitosamente', 200, $categoria);
        }catch(Exception $e){

            return ApiResponse::error('Error', 422);

        }catch(ModelNotFoundException $e){

            return ApiResponse::error('¡Categoria no encontrada!', 404);

        }
    }

    public function destroy($id)
    {
        try{
            $categoria = Categoria::findOrFail($id);
            $categoria->delete();
            return ApiResponse::success('Categoria eliminada exitosamente', 200);
        }catch(ModelNotFoundException $e){

            return ApiResponse::error('¡Categoria no encontrada!', 404);

        }
    }

    public function productosPorCategoria($id)
    {
        try {
            $categoria = Categoria::with('productos')->findOrFail($id);
            return ApiResponse::success('Productos obtenidos exitosamente', 200, $categoria);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('¡Categoria no encontrada!', 404);        
        }
}
}
