<?php

namespace App\Http\Controllers\Api;

use App\DTOs\FilterDTO;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserRoleResource;
use App\Models\UserRole;
use App\Services\PaginateRegistersService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Throwable;

class UserRoleController extends Controller
{
    protected array $filterables = ['user_id'];

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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
