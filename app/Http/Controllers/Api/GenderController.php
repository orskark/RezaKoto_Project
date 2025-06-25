<?php

namespace App\Http\Controllers\Api;

use App\DTOs\FilterDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateGenderRequest;
use App\Http\Requests\UpdateGenderRequest;
use App\Http\Resources\GenderResource;
use App\Models\Gender;
use App\Services\PaginateRegistersService;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Throwable;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;

class GenderController extends Controller
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
            $query = Gender::query();
            $results = $this->pagination->execute($query, $dto, $this->searchables, $this->filterables);
            $data = [
                'items' => GenderResource::collection($results),
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

    public function store(CreateGenderRequest $request): JsonResponse
    {
        try {
            $gender = Gender::create($request->validated());
            return $this->successResponse(new GenderResource($gender), 'Registro creado exitosamente.', 201);
        } catch (QueryException $e) {
            return $this->errorResponse('Error al crear el registro.', 500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Ha ocurrido un error inesperado.', 500, $e->getMessage());
        }
    }

    public function show(Gender $gender): JsonResponse
    {
        try {
            $resource = new GenderResource($gender);
            return $this->successResponse($resource, 'Registro obtenido exitosamente.');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al obtener el registro.', 500, $e->getMessage());
        }
    }

    public function update(UpdateGenderRequest $request, Gender $gender): JsonResponse
    {
        try {
            $gender->update($request->validated());
            $resource = new GenderResource($gender);
            return $this->successResponse($resource, 'Registro actualizado exitosamente.');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al actualizar el registro.', 500, $e->getMessage());
        }
    }

    public function destroy(Gender $gender): JsonResponse
    {
        try {
            $gender->delete();
            return $this->successResponse(null, 'Registro eliminado exitosamente.', 204);
        } catch (Throwable $e) {
            return $this->errorResponse('Error al eliminar el registro.', 500, $e->getMessage());
        }
    }

    public function toggleStatus(Gender $gender): JsonResponse
    {
        try {
            $gender->status_id = $gender->status_id == 1 ? 2 : 1;
            $gender->save();
            return $this->successResponse($gender, 'Registro actualizado éxitosamente');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al actualizar el registro.', 500, $e->getMessage());
        }
    }
}
