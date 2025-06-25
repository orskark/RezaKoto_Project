<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\CreateWarehouseRequest;
use App\Http\Requests\UpdateWarehouseRequest;
use App\Http\Resources\WarehouseResource;
use App\Models\Warehouse;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Throwable;
use App\Traits\ApiResponseTrait;
use App\DTOs\FilterDTO;
use App\Services\PaginateRegistersService;

class WarehouseController extends Controller
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
            $results = $this->pagination->execute(Warehouse::query(), $dto, $this->searchables, $this->filterables);
            $data = [
                'items' => WarehouseResource::collection($results),
                'pagination' => ['current_page' => $results->currentPage(), 'per_page' => $results->perPage(), 'total' => $results->total(), 'last_page' => $results->lastPage()],
            ];
            return $this->successResponse($data, 'Registros obtenidos exitosamente.');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al obtener los registros.', 500, $e->getMessage());
        }
    }

    public function store(CreateWarehouseRequest $request): JsonResponse
    {
        try {
            $warehouse = Warehouse::create($request->validated());
            return $this->successResponse(new WarehouseResource($warehouse), 'Registro creado éxitosamente.', 201);
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al crear el registro.', 500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al crear el registro.', 500, $e->getMessage());
        }
    }

    public function show(Warehouse $warehouse): JsonResponse
    {
        try {
            return $this->successResponse(new WarehouseResource($warehouse), 'Registro obtenido éxitosamente.');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al obtener el registro.', 500, $e->getMessage());
        }
    }

    public function update(UpdateWarehouseRequest $request, Warehouse $warehouse): JsonResponse
    {
        try {
            $warehouse->update($request->validated());
            return $this->successResponse(new WarehouseResource($warehouse), 'Registro actualizado correctamente.');
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al actualizar el registro.', 500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al actualizar el registro.', 500, $e->getMessage());
        }
    }

    public function destroy(Warehouse $warehouse): JsonResponse
    {
        try {
            $warehouse->delete();
            return $this->successResponse(null, 'Registro eliminado éxitosamente.', 204);
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al eliminar el registro.', 500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al eliminar el registro.', 500, $e->getMessage());
        }
    }

    public function toggleStatus(Warehouse $warehouse): JsonResponse
    {
        try {
            $warehouse->status_id = $warehouse->status_id == 1 ? 2 : 1;
            $warehouse->save();
            return $this->successResponse($warehouse, 'Registro actualizado éxitosamente');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al actualizar el registro.', 500, $e->getMessage());
        }
    }
}
