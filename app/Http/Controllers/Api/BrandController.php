<?php

namespace App\Http\Controllers\Api;

use App\DTOs\FilterDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use App\Http\Resources\BrandResource;
use App\Models\Brand;
use App\Services\PaginateRegistersService;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Throwable;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;

class BrandController extends Controller
{
    use ApiResponseTrait;

    protected $searchables = ['name'];
    protected array $filterables = ['status_id'];

    public function __construct(
        protected PaginateRegistersService $pagination
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $dto = FilterDTO::fromRequest($request->all());
            $query = Brand::query();
            $results = $this->pagination->execute($query, $dto, $this->searchables, $this->filterables);
            $data = [
                'items' => BrandResource::collection($results),
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

    public function store(CreateBrandRequest $request): JsonResponse
    {
        try {
            $brand = Brand::create($request->validated());
            return $this->successResponse(new BrandResource($brand), 'Registro creado exitosamente.', 201);
        } catch (QueryException $e) {
            return $this->errorResponse('Error al crear el registro.', 500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Ha ocurrido un error inesperado.', 500, $e->getMessage());
        }
    }

    public function show(Brand $brand): JsonResponse
    {
        try {
            $resource = new BrandResource($brand);
            return $this->successResponse($resource, 'Registro obtenido exitosamente.');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al obtener el registro.', 500, $e->getMessage());
        }
    }

    public function update(UpdateBrandRequest $request, Brand $brand): JsonResponse
    {
        try {
            $brand->update($request->validated());
            $resource = new BrandResource($brand);
            return $this->successResponse($resource, 'Registro actualizado exitosamente.');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al actualizar el registro.', 500, $e->getMessage());
        }
    }

    public function destroy(Brand $brand): JsonResponse
    {
        try {
            $brand->delete();
            return $this->successResponse(null, 'Registro eliminado exitosamente.', 204);
        } catch (Throwable $e) {
            return $this->errorResponse('Error al eliminar el registro.', 500, $e->getMessage());
        }
    }

    public function toggleStatus(Brand $brand): JsonResponse
    {
        try {
            $brand->status_id = $brand->status_id == 1 ? 2 : 1;
            $brand->save();
            return $this->successResponse($brand, 'Registro actualizado éxitosamente');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al actualizar el registro.', 500, $e->getMessage());
        }
    }
}
