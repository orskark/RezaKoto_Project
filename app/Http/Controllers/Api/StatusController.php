<?php

namespace App\Http\Controllers\Api;

use App\DTOs\FilterDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateStatusRequest;
use App\Http\Requests\UpdateStatusRequest;
use App\Http\Resources\StatusResource;
use App\Models\Status;
use App\Services\PaginateRegistersService;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Throwable;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;

class StatusController extends Controller
{
    use ApiResponseTrait;

    protected array $searchables = ['name'];

    public function __construct(
        protected PaginateRegistersService $pagination
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $dto = FilterDTO::fromRequest($request->all());
            $query = Status::query();
            $results = $this->pagination->execute($query, $dto, $this->searchables);
            $data = [
                'items' => StatusResource::collection($results),
                'pagination' => [
                    'current_page' => $results->currentPage(),
                    'per_page' => $results->perPage(),
                    'total' => $results->total(),
                    'last_page' => $results->lastPage(),
                ],
            ];
            return $this->successResponse($data, 'Registros obtenidos exitosamente.');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al obtener los registros.', 500, $e->getMessage());
        }
    }

    public function store(CreateStatusRequest $request): JsonResponse
    {
        try {
            $status = Status::create($request->validated());
            return $this->successResponse(new StatusResource($status), 'Registro creado exitosamente.', 201);
        } catch (QueryException $e) {
            return $this->errorResponse('Error al crear el registro.', 500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Ha ocurrido un error inesperado.', 500, $e->getMessage());
        }
    }

    public function show(Status $status): JsonResponse
    {
        try {
            $resource = new StatusResource($status);
            return $this->successResponse($resource, 'Registro obtenido exitosamente.');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al obtener el registro.', 500, $e->getMessage());
        }
    }

    public function update(UpdateStatusRequest $request, Status $status): JsonResponse
    {
        try {
            $status->update($request->validated());
            $resource = new StatusResource($status);
            return $this->successResponse($resource, 'Registro actualizado exitosamente.');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al actualizar el registro.', 500, $e->getMessage());
        }
    }

    public function destroy(Status $status): JsonResponse
    {
        try {
            $status->delete();
            return $this->successResponse(null, 'Registro eliminado exitosamente.', 204);
        } catch (Throwable $e) {
            return $this->errorResponse('Error al eliminar el registro.', 500, $e->getMessage());
        }
    }
}
