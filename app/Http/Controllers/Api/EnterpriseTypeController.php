<?php

namespace App\Http\Controllers\Api;

use App\DTOs\FilterDTO;
use App\Http\Controllers\Controller;
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

    protected $searchables = ['name'];
    protected $filterables = ['status_id'];

    public function __construct(protected PaginateRegistersService $pagination) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $dto = FilterDTO::fromRequest($request->all());
            $results = $this->pagination->execute(EnterpriseType::query(), $dto, $this->searchables, $this->filterables);
            $data = [
                'items' => EnterpriseTypeResource::collection($results),
                'pagination' => ['current_page' => $results->currentPage(), 'per_page' => $results->perPage(), 'total' => $results->total(), 'last_page' => $results->lastPage()],
            ];
            return $this->successResponse($data, 'Registros obtenidos éxitosamente.');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al obtener los registros.', 500, $e->getMessage());
        }


    }

    public function store(CreateEnterpriseTypeRequest $request): JsonResponse
    {
        try {
            $result = EnterpriseType::create($request->validated());
            return $this->successResponse(new EnterpriseTypeResource($result), 'Registro creado éxitosamente.');
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al crear el registro.', 500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al crear el registro.', 500, $e->getMessage());
        }
    }

    public function show(EnterpriseType $enterprise_type): JsonResponse
    {
        try {
            return $this->successResponse(new EnterpriseTypeResource($enterprise_type), 'Registro obtenido ésitosamente.');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al obtener el registro.', 500, $e->getMessage());
        }
    }

    public function update(UpdateEnterpriseTypeRequest $request, EnterpriseType $enterprise_type): JsonResponse
    {
        try {
            $enterprise_type->update($request->validated());
            return $this->successResponse(new EnterpriseTypeResource($enterprise_type), 'Registro actualizado éxitosamente.');
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al actualizar el registro.', 500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al actualizar el registro.', 500, $e->getMessage());
        }
    }

    public function destroy(EnterpriseType $enterprise_type): JsonResponse
    {
        try {
            $enterprise_type->delete();
            return $this->successResponse(null, 'Registro eliminado éxitosamente.', 204);
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al eliminar el registro.', 500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al eliminar el registro.', 500, $e->getMessage());
        }
    }

    public function toggleStatus(EnterpriseType $enterprise_type): JsonResponse
    {
        try {
            $enterprise_type->status_id = $enterprise_type->status_id == 1 ? 2 : 1;
            $enterprise_type->save();
            return $this->successResponse($enterprise_type, 'Registro actualizado éxitosamente');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al actualizar el registro.', 500, $e->getMessage());
        }
    }
}
