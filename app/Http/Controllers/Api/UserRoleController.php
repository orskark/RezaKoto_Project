<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRoleRequest;
use App\Http\Requests\UpdateUserRoleRequest;
use App\Http\Resources\UserRoleResource;
use App\Models\UserRole;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;
use App\Traits\ApiResponseTrait;
use App\DTOs\FilterDTO;
use App\Services\PaginateRegistersService;

class UserRoleController extends Controller
{
    protected array $filterables = ['user_id', 'role_id', 'status_id'];

    use ApiResponseTrait;

    public function __construct(
        protected PaginateRegistersService $pagination
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $dto = FilterDTO::fromRequest($request->all());
            $query = UserRole::query()->with(['role']);
            $results = $this->pagination->execute($query, $dto, [], $this->filterables);
            $data = [
                'items' => UserRoleResource::collection($results),
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

    public function store(CreateUserRoleRequest $request): JsonResponse
    {
        try {
            $user_role = UserRole::create($request->validated());
            return $this->successResponse(new UserRoleResource($user_role), 'Registro creado éxitosamente.', 201);
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al crear el registro.', 500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al crear el registro.', 500, $e->getMessage());
        }
    }

    public function show(UserRole $user_role): JsonResponse
    {
        try {
            return $this->successResponse(new UserRoleResource($user_role), 'Registro obtenido éxitosamente.');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al obtener el registro.', 500, $e->getMessage());
        }
    }

    public function update(UpdateUserRoleRequest $request, UserRole $user_role): JsonResponse
    {
        try {
            $user_role->update($request->validated());
            return $this->successResponse(new UserRoleResource($user_role), 'Registro actualizado correctamente.');
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al actualizar el registro.', 500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al actualizar el registro.', 500, $e->getMessage());
        }
    }

    public function destroy(UserRole $user_role): JsonResponse
    {
        try {
            $user_role->delete();
            return $this->successResponse(null, 'Registro eliminado éxitosamente.', 204);
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al eliminar el registro.', 500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al eliminar el registro.', 500, $e->getMessage());
        }
    }

    public function toggleStatus(UserRole $user_role): JsonResponse
    {
        try {
            $user_role->status_id = $user_role->status_id == 1 ? 2 : 1;
            $user_role->save();
            return $this->successResponse($user_role, 'Registro actualizado éxitosamente');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al actualizar el registro.', 500, $e->getMessage());
        }
    }
}
