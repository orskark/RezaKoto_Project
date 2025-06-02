<?php

namespace App\Http\Controllers;

use App\DTOs\FilterDTO;
use App\Http\Requests\CreateEnterpriseTypeRequest;
use App\Http\Requests\UpdateEnterpriseTypeRequest;
use App\Http\Resources\EnterpriseTypeResource;
use App\Models\EnterpriseType;
use App\Services\PaginateRegistersService;
use App\Traits\ApiResponseTrait;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class EnterpriseTypeController extends Controller
{
    use ApiResponseTrait;
    protected array $searchables = ['name'];

    public function __construct(
        protected PaginateRegistersService $pagination
    ) {}
    /**
     * Muestra todos los tipos de empresa.
     */
    public function index(Request $request):JsonResponse
    {
        try {
            $dto = FilterDTO::fromRequest($request->all());
            $results = $this->pagination->execute(EnterpriseType::query(), $dto, $this->searchables);
            $data = [
                'items' => EnterpriseTypeResource::collection($results),
                'pagination' => ['current_page' => $results->currentPage(), 'per_page' => $results->perPage(), 'total' => $results->total(), 'last_page' => $results->lastPage()],
            ];
            return $this->successResponse($data,'Registros Obtenidos Exitosamente');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al obtener los registros',500, $e->getMessage());
        }
        
    }

    /**
     * Guarda un nuevo tipo de empresa en la base de datos.
     */
    public function store(CreateEnterpriseTypeRequest $request):JsonResponse
    { 
        try {
            $model = EnterpriseType::create($request->validated());
            return $this->successResponse(new EnterpriseTypeResource($model),'Registro Creado Exitosamente',201);
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al crear el registro',500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al crear el registro',500, $e->getMessage());
        }
            
    }

    /**
     * Muestra un tipo de empresa específica.
     */
    public function show(EnterpriseType $enterpriseType):JsonResponse
    {
        try {
            return $this->successResponse(new EnterpriseTypeResource($enterpriseType),'Registro Obtenido Exitosamente');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al mostrar el registro',500,$e->getMessage());
        }
    }

    /**
     * Actualiza un tipo de empresa existente.
     */
    public function update(UpdateEnterpriseTypeRequest $request, EnterpriseType $enterpriseType):JsonResponse
    {
        try {
            $enterpriseType->update($request->validated());
            return $this->successResponse(new EnterpriseTypeResource($enterpriseType),'Registro actualizado exitosamente');
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al actualizar el registro',500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al actualizar el registro',500, $e->getMessage());
        }
    }

    /**
     * Elimina un tipo de empresa.
     */
    public function destroy(EnterpriseType $enterpriseType):JsonResponse
    {
        try {
            $enterpriseType->delete();
            return $this->successResponse(null,'Registro eliminado exitosamente');
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al eliminar el registro',500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al eliminar el registro',500, $e->getMessage());
        }
    }
}

