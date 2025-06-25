<?php

namespace App\Http\Controllers\Api;

use Throwable;
use App\Models\User;
use App\DTOs\FilterDTO;
use App\Enums\StatusEnum;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Services\PaginateRegistersService;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    use ApiResponseTrait;

    protected array $filterables = ['document_type_id', 'status_id'];
    protected array $searchables = ['complete_name', 'email', 'identification', 'phone_number', 'address'];
    protected array $relationSearchables = [
        'document_type' => ['name'],
        'roles' => ['name']
    ];
    protected array $relationsToInclude = ['document_type', 'roles'];

    public function __construct(
        protected PaginateRegistersService $pagination
    ) {}

    public function register(CreateUserRequest $request) : JsonResponse
    {
        try {
            $user = User::create([
                'complete_name' => $request->input('complete_name'),
                'email' => $request->input('email'),
                'password' => bcrypt($request->input('password')),
                'identification' => $request->input('identification'),
                'phone_number' => $request->input('phone_number'),
                'address' => $request->input('address'),
                'document_type_id' => $request->input('document_type_id'),
                'status_id' => StatusEnum::Active->value
            ]);

            $roles = [2];
            $user->roles()->attach($roles);

            return $this->successResponse(new UserResource($user), 'Registro creado exitosamente.', 201);
        } catch (QueryException $e) {
            return $this->errorResponse('Error al crear el usuario.', 500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Ha ocurrido un error inesperado.', 500, $e->getMessage());
        }
    }

    public function login(LoginUserRequest $request): JsonResponse
    {
        $credentials = $request->only(['email', 'password']);
        $roleId = $request->input('role_id');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return $this->errorResponse('Credenciales inválidas.', 401);
            }

            $user = auth('api')->user();
            $first_name = $user->first_name;

            if (!$user->roles->contains('id', $roleId)) {
                return $this->errorResponse('Rol no autorizado.', 403);
            }

            $customClaims = ['role_id' => $roleId];
            $token = JWTAuth::claims($customClaims)->fromUser($user);

            return $this->successResponse(['token' => $token, 'first_name' => $first_name, 'role_id' => $roleId], 'Acceso exitoso.', 200);

        } catch (JWTException $e) {
            return $this->errorResponse('No se pudo generar el token.', 500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Ha ocurrido un error inesperado.', 500, $e->getMessage());
        }
    }

    public function getUser(): JsonResponse
    {
        try {
            $user = Auth::user();
            return $this->successResponse(new UserResource($user), 'Registro obtenido exitosamente.');
        } catch (Throwable $e) {
            return $this->errorResponse('Ha ocurrido un error al obtener el usuario autenticado.', 500, $e->getMessage());
        }
    }

    public function logout(): JsonResponse
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());

            return $this->successResponse(null, 'Cierre de sesión exitoso.', 204);
        } catch (JWTException $e) {
            return $this->errorResponse('No se pudo invalidar el token.', 500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Ha ocurrido un error inesperado durante el cierre de sesión.', 500,$e->getMessage());
        }
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $dto = FilterDTO::fromRequest($request->all());
            $query = User::query();

            $results = $this->pagination->execute($query, $dto, $this->searchables, $this->filterables, $this->relationSearchables, $this->relationsToInclude);

            $data = [
                'items' => UserResource::collection($results),
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

    public function show(User $user): JsonResponse
    {
        try {
            $resource = new UserResource($user);
            return $this->successResponse($resource, 'Registro obtenido exitosamente.');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al obtener el registro.', 500, $e->getMessage());
        }
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        try {
            $user->update($request->validated());
            $resource = new UserResource($user);
            return $this->successResponse($resource, 'Registro actualizado exitosamente.');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al actualizar el registro.', 500, $e->getMessage());
        }
    }

    public function destroy(User $user): JsonResponse
    {
        try {
            $user->delete();
            return $this->successResponse(null, 'Registro eliminado exitosamente.', 204);
        } catch (Throwable $e) {
            return $this->errorResponse('Error al eliminar el registro.', 500, $e->getMessage());
        }
    }

    public function getIdByEmail(Request $request): JsonResponse
    {
        try {
            $userId = User::where('email', $request->input('email'))->value('id');
            if (!$userId) {
                return $this->successResponse(null, 'No se pudo encontrar el ID');
            }
            return $this->successResponse($userId, 'ID obtenido exitosamente');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al obtener los registros.', 500, $e->getMessage());
        }
    }
}
