<?php

namespace App\Http\Controllers\Api;

use App\DTOs\FilterDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use App\Services\PaginateRegistersService;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Throwable;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
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
            $query = Role::query();
            $results = $this->pagination->execute($query, $dto, $this->searchables, $this->filterables);
            $data = [
                'items' => RoleResource::collection($results),
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

    public function store(CreateRoleRequest $request): JsonResponse
    {
        try {
            $role = Role::create($request->validated());
            return $this->successResponse(new RoleResource($role), 'Registro creado exitosamente.', 201);
        } catch (QueryException $e) {
            return $this->errorResponse('Error al crear el registro.', 500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Ha ocurrido un error inesperado.', 500, $e->getMessage());
        }
    }

    public function show(Role $role): JsonResponse
    {
        try {
            $resource = new RoleResource($role);
            return $this->successResponse($resource, 'Registro obtenido exitosamente.');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al obtener el registro.', 500, $e->getMessage());
        }
    }

    public function update(UpdateRoleRequest $request, Role $role): JsonResponse
    {
        try {
            $role->update($request->validated());
            $resource = new RoleResource($role);
            return $this->successResponse($resource, 'Registro actualizado exitosamente.');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al actualizar el registro.', 500, $e->getMessage());
        }
    }

    public function destroy(Role $role): JsonResponse
    {
        try {
            $role->delete();
            return $this->successResponse(null, 'Registro eliminado exitosamente.', 204);
        } catch (Throwable $e) {
            return $this->errorResponse('Error al eliminar el registro.', 500, $e->getMessage());
        }
    }

    public function toggleStatus(Role $role): JsonResponse
    {
        try {
            $role->status_id = $role->status_id == 1 ? 2 : 1;
            $role->save();
            return $this->successResponse($role, 'Registro actualizado éxitosamente');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al actualizar el registro.', 500, $e->getMessage());
        }
    }
}
