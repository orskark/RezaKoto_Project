<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\CreateEnterpriseRequest;
use App\Http\Requests\UpdateEnterpriseRequest;
use App\Http\Resources\EnterpriseResource;
use App\Models\Enterprise;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Throwable;
use App\Traits\ApiResponseTrait;
use App\DTOs\FilterDTO;
use App\Services\PaginateRegistersService;

class EnterpriseController extends Controller
{
    use ApiResponseTrait;

    protected array $searchables = ['name', 'NIT'];
    protected array $filterables = ['enterprise_type_id', 'status_id'];
    protected array $relationSearchables = [
        'enterprise_type'   => 'name',
    ];

    public function __construct(
        protected PaginateRegistersService $pagination
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $dto = FilterDTO::fromRequest($request->all());
            $results = $this->pagination->execute(Enterprise::query(), $dto, $this->searchables, $this->filterables, $this->relationSearchables);
            $data = [
                'items' => EnterpriseResource::collection($results),
                'pagination' => ['current_page' => $results->currentPage(), 'per_page' => $results->perPage(), 'total' => $results->total(), 'last_page' => $results->lastPage()],
            ];
            return $this->successResponse($data, 'Registros obtenidos exitosamente.');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al obtener los registros.', 500, $e->getMessage());
        }
    }

    public function store(CreateEnterpriseRequest $request): JsonResponse
    {
        try {
            $enterprise = Enterprise::create($request->validated());
            return $this->successResponse(new EnterpriseResource($enterprise), 'Registro creado éxitosamente.', 201);
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al crear el registro.', 500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al crear el registro.', 500, $e->getMessage());
        }
    }

    public function show(Enterprise $enterprise): JsonResponse
    {
        try {
            return $this->successResponse(new EnterpriseResource($enterprise), 'Registro obtenido éxitosamente.');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al obtener el registro.', 500, $e->getMessage());
        }
    }

    public function update(UpdateEnterpriseRequest $request, Enterprise $enterprise): JsonResponse
    {
        try {
            $enterprise->update($request->validated());
            return $this->successResponse(new EnterpriseResource($enterprise), 'Registro actualizado correctamente.');
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al actualizar el registro.', 500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al actualizar el registro.', 500, $e->getMessage());
        }
    }

    public function destroy(Enterprise $enterprise): JsonResponse
    {
        try {
            $enterprise->delete();
            return $this->successResponse(null, 'Registro eliminado éxitosamente.', 204);
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al eliminar el registro.', 500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al eliminar el registro.', 500, $e->getMessage());
        }
    }

    public function toggleStatus(Enterprise $enterprise): JsonResponse
    {
        try {
            $enterprise->status_id = $enterprise->status_id == 1 ? 2 : 1;
            $enterprise->save();
            return $this->successResponse($enterprise, 'Registro actualizado éxitosamente');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al actualizar el registro.', 500, $e->getMessage());
        }
    }
}
