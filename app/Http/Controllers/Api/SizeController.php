<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateSizeRequest;
use App\Http\Requests\UpdateSizeRequest;
use App\Http\Resources\SizeResource;
use App\Models\Size;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;
use App\Traits\ApiResponseTrait;
use App\DTOs\FilterDTO;
use App\Services\PaginateRegistersService;

class SizeController extends Controller
{
    use ApiResponseTrait;

    protected array $searchables = ['code', 'label'];
    protected array $filterables = ['gender_id', 'brand_id', 'category_id', 'status_id'];
    protected array $relationSearchables = [
        'gender'   => 'name',
        'brand'    => 'name',
        'category' => 'name',
    ];

    public function __construct(
        protected PaginateRegistersService $pagination
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $dto = FilterDTO::fromRequest($request->all());
            $results = $this->pagination->execute(Size::query(), $dto, $this->searchables, $this->filterables, $this->relationSearchables);
            $data = [
                'items' => SizeResource::collection($results),
                'pagination' => ['current_page' => $results->currentPage(), 'per_page' => $results->perPage(), 'total' => $results->total(), 'last_page' => $results->lastPage()],
            ];
            return $this->successResponse($data, 'Registros obtenidos exitosamente.');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al obtener los registros.', 500, $e->getMessage());
        }
    }

    public function store(CreateSizeRequest $request): JsonResponse
    {
        try {
            $size = Size::create($request->validated());
            return $this->successResponse(new SizeResource($size), 'Registro creado éxitosamente.', 201);
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al crear el registro.', 500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al crear el registro.', 500, $e->getMessage());
        }
    }

    public function show(Size $size): JsonResponse
    {
        try {
            return $this->successResponse(new SizeResource($size), 'Registro obtenido éxitosamente.');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al obtener el registro.', 500, $e->getMessage());
        }
    }

    public function update(UpdateSizeRequest $request, Size $size): JsonResponse
    {
        try {
            $size->update($request->validated());
            return $this->successResponse(new SizeResource($size), 'Registro actualizado correctamente.');
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al actualizar el registro.', 500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al actualizar el registro.', 500, $e->getMessage());
        }
    }

    public function destroy(Size $size): JsonResponse
    {
        try {
            $size->delete();
            return $this->successResponse(null, 'Registro eliminado éxitosamente.', 204);
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al eliminar el registro.', 500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al eliminar el registro.', 500, $e->getMessage());
        }
    }

    public function toggleStatus(Size $size): JsonResponse
    {
        try {
            $size->status_id = $size->status_id == 1 ? 2 : 1;
            $size->save();
            return $this->successResponse($size, 'Registro actualizado éxitosamente');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al actualizar el registro.', 500, $e->getMessage());
        }
    }
}
