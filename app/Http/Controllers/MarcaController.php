<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Marca;
use App\Http\Controllers\Responses\ApiResponse;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class MarcaController extends Controller
{
    public function index()
    {
        try{
            $marcas = Marca::all();
            return ApiResponse::success('Marcas obtenidas', 200, $marcas);
        } catch (Exception $e) {
            return ApiResponse::error('Ocurrió un error al obtener las marcas: ' . $e->getMessage(), 500);
        }
        
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nombre' => 'required|unique:marcas,nombre',
                'descripcion' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return ApiResponse::error('Error de validación', 422, $validator->errors());
            }

            $marca = Marca::create($request->all());
            return ApiResponse::success('Marca creada exitosamente', 201, $marca);
        } catch (ValidationException $e) {
            return ApiResponse::error('Error de validación', 422);
        }
    }

    public function show($id)
    {
        try {
            $marca = Marca::findOrFail($id);
            return ApiResponse::success('Marca obtenida', 200, $marca);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('¡Marca no encontrada!', 404);
        } catch (Exception $e) {
            return ApiResponse::error('Ocurrió un error al obtener la marca: ' . $e->getMessage(), 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nombre' => [
                    'required',
                    Rule::unique('marcas')->ignore($id),
                ],
                'descripcion' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return ApiResponse::error('Error de validación', 422, $validator->errors());
            }

            $marca = Marca::findOrFail($id);
            $marca->update($request->all());
            return ApiResponse::success('Marca actualizada exitosamente', 200, $marca);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('¡Marca no encontrada!', 404);
        } catch (Exception $e) {
            return ApiResponse::error('Ocurrió un error al actualizar la marca: ' . $e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $marca = Marca::findOrFail($id);
            $marca->delete();
            return ApiResponse::success('Marca eliminada exitosamente', 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('¡Marca no encontrada!', 404);
        } catch (Exception $e) {
            return ApiResponse::error('Ocurrió un error al eliminar la marca: ' . $e->getMessage(), 500);
        }
    }

    public function productosPorMarca($id)
    {
        # code
    }
}
