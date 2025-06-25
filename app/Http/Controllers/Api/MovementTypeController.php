<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\CreateMovementTypeRequest;
use App\Http\Requests\UpdateMovementTypeRequest;
use App\Http\Resources\MovementTypeResource;
use App\Models\MovementType;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Throwable;
use App\Traits\ApiResponseTrait;
use App\DTOs\FilterDTO;
use App\Services\PaginateRegistersService;

class MovementTypeController extends Controller
{
    use ApiResponseTrait;

    protected array $searchables = ['name'];
    protected array $filterables = ['status_id'];

    public function __construct(
        protected PaginateRegistersService $pagination
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $dto = FilterDTO::fromRequest($request->all());
            $results = $this->pagination->execute(MovementType::query(), $dto, $this->searchables, $this->filterables);
            $data = [
                'items' => MovementTypeResource::collection($results),
                'pagination' => ['current_page' => $results->currentPage(), 'per_page' => $results->perPage(), 'total' => $results->total(), 'last_page' => $results->lastPage()],
            ];
            return $this->successResponse($data, 'Registros obtenidos exitosamente.');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al obtener los registros.', 500, $e->getMessage());
        }
    }

    public function store(CreateMovementTypeRequest $request): JsonResponse
    {
        try {
            $movement_type = MovementType::create($request->validated());
            return $this->successResponse(new MovementTypeResource($movement_type), 'Registro creado éxitosamente.', 201);
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al crear el registro.', 500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al crear el registro.', 500, $e->getMessage());
        }
    }

    public function show(MovementType $movement_type): JsonResponse
    {
        try {
            return $this->successResponse(new MovementTypeResource($movement_type), 'Registro obtenido éxitosamente.');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al obtener el registro.', 500, $e->getMessage());
        }
    }

    public function update(UpdateMovementTypeRequest $request, MovementType $movement_type): JsonResponse
    {
        try {
            $movement_type->update($request->validated());
            return $this->successResponse(new MovementTypeResource($movement_type), 'Registro actualizado correctamente.');
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al actualizar el registro.', 500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al actualizar el registro.', 500, $e->getMessage());
        }
    }

    public function destroy(MovementType $movement_type): JsonResponse
    {
        try {
            $movement_type->delete();
            return $this->successResponse(null, 'Registro eliminado éxitosamente.', 204);
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al eliminar el registro.', 500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al eliminar el registro.', 500, $e->getMessage());
        }
    }

    public function toggleStatus(MovementType $movement_type): JsonResponse
    {
        try {
            $movement_type->status_id = $movement_type->status_id == 1 ? 2 : 1;
            $movement_type->save();
            return $this->successResponse($movement_type, 'Registro actualizado éxitosamente');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al actualizar el registro.', 500, $e->getMessage());
        }
    }
}
