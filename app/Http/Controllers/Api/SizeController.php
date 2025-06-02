<?php

namespace App\Http\Controllers;

use App\DTOs\FilterDTO;
use App\Http\Requests\CreateSizeRequest;
use App\Http\Requests\UpdateSizeRequest;
use App\Http\Resources\SizeResource;
use App\Models\Size;
use App\Services\PaginateRegistersService;
use App\Traits\ApiResponseTrait;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class SizeController extends Controller
{
    use ApiResponseTrait;
    protected array $searchables = ['code'];

    public function __construct(
        protected PaginateRegistersService $pagination
    ) {}
    /**
     * Muestra todas las tallas.
     */
    public function index(Request $request):JsonResponse
    {
        try {
            $dto = FilterDTO::fromRequest($request->all());
            $results = $this->pagination->execute(Size::query(), $dto, $this->searchables);
            $data = [
                'items' => SizeResource::collection($results),
                'pagination' => ['current_page' => $results->currentPage(), 'per_page' => $results->perPage(), 'total' => $results->total(), 'last_page' => $results->lastPage()],
            ];
            return $this->successResponse($data,'Registros Obtenidos Exitosamente');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al obtener los registros',500, $e->getMessage());
        }
        
    }

    /**
     * Guarda una nueva talla en la base de datos.
     */
    public function store(CreateSizeRequest $request):JsonResponse
    { 
        try {
            $model = Size::create($request->validated());
            return $this->successResponse(new SizeResource($model),'Registro Creado Exitosamente',201);
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al crear el registro',500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al crear el registro',500, $e->getMessage());
        }
            
    }

    /**
     * Muestra una talla específica.
     */
    public function show(Size $size):JsonResponse
    {
        try {
            return $this->successResponse(new SizeResource($size),'Registro Obtenido Exitosamente');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al mostrar el registro',500,$e->getMessage());
        }
    }

    /**
     * Actualiza una talla existente.
     */
    public function update(UpdateSizeRequest $request, Size $size):JsonResponse
    {
        try {
            $size->update($request->validated());
            return $this->successResponse(new SizeResource($size),'Registro actualizado exitosamente');
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al actualizar el registro',500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al actualizar el registro',500, $e->getMessage());
        }
    }

    /**
     * Elimina una talla.
     */
    public function destroy(Size $size):JsonResponse
    {
        try {
            $size->delete();
            return $this->successResponse(null,'Registro eliminado exitosamente');
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al eliminar el registro',500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al eliminar el registro',500, $e->getMessage());
        }
    }
}
