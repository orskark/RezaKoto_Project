<?php

namespace App\Http\Controllers\Api;

use App\DTOs\FilterDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateMovementTypeRequest;
use App\Http\Requests\UpdateMovementTypeRequest;
use App\Http\Resources\MovementTypeResource;
use App\Models\MovementType;
use App\Services\PaginateRegistersService;
use App\Traits\ApiResponseTrait;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class MovementTypeController extends Controller
{
    use ApiResponseTrait;
    protected array $searchables = ['name'];

    public function __construct(
        protected PaginateRegistersService $pagination
    ) {}
    /**
     * Muestra todos los tipos de movimientos.
     */
    public function index(Request $request):JsonResponse
    {
        try {
            $dto = FilterDTO::fromRequest($request->all());
            $results = $this->pagination->execute(MovementType::query(), $dto, $this->searchables);
            $data = [
                'items' => MovementTypeResource::collection($results),
                'pagination' => ['current_page' => $results->currentPage(), 'per_page' => $results->perPage(), 'total' => $results->total(), 'last_page' => $results->lastPage()],
            ];
            return $this->successResponse($data,'Registros Obtenidos Exitosamente');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al obtener los registros',500, $e->getMessage());
        }

    }

    /**
     * Guarda un nuevo tipo de movimiento en la base de datos.
     */
    public function store(CreateMovementTypeRequest $request):JsonResponse
    {
        try {
            $model = MovementType::create($request->validated());
            return $this->successResponse(new MovementTypeResource($model),'Registro Creado Exitosamente',201);
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al crear el registro',500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al crear el registro',500, $e->getMessage());
        }

    }

    /**
     * Muestra un tipo de movimiento específico.
     */
    public function show(MovementType $movement_type):JsonResponse
    {
        try {
            return $this->successResponse(new MovementTypeResource($movement_type),'Registro Obtenido Exitosamente');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al mostrar el registro',500,$e->getMessage());
        }
    }

    /**
     * Actualiza un tipo de moviemiento.
     */
    public function update(UpdateMovementTypeRequest $request, MovementType $movement_type):JsonResponse
    {
        try {
            $movement_type->update($request->validated());
            return $this->successResponse(new MovementTypeResource($movement_type),'Registro actualizado exitosamente');
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al actualizar el registro',500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al actualizar el registro',500, $e->getMessage());
        }
    }

    /**
     * Elimina un tipo de movimiento.
     */
    public function destroy(MovementType $movement_type):JsonResponse
    {
        try {
            $movement_type->delete();
            return $this->successResponse(null,'Registro eliminado exitosamente');
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al eliminar el registro',500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al eliminar el registro',500, $e->getMessage());
        }
    }
}

