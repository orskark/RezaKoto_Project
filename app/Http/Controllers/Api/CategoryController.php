<?php

namespace App\Http\Controllers\Api;

use Throwable;
use App\DTOs\FilterDTO;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use App\Http\Resources\CategoryResource;
use App\Services\PaginateRegistersService;
use App\Http\Requests\CreateCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;

class CategoryController extends Controller
{
    use ApiResponseTrait;

    protected array $filterables = ['status_id'];
    protected array $searchables = ['name'];

    public function __construct(
        protected PaginateRegistersService $pagination
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $dto = FilterDTO::fromRequest($request->all());
            $query = Category::query();
            $results = $this->pagination->execute($query, $dto, $this->searchables, $this->filterables);
            $data = [
                'items' => CategoryResource::collection($results),
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

    public function store(CreateCategoryRequest $request): JsonResponse
    {
        try {
            $category = Category::create($request->validated());
            return $this->successResponse(new CategoryResource($category), 'Registro creado exitosamente.', 201);
        } catch (QueryException $e) {
            return $this->errorResponse('Error al crear el registro.', 500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Ha ocurrido un error inesperado.', 500, $e->getMessage());
        }
    }

    public function show(Category $category): JsonResponse
    {
        try {
            $resource = new CategoryResource($category);
            return $this->successResponse($resource, 'Registro obtenido exitosamente.');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al obtener el registro.', 500, $e->getMessage());
        }
    }

    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        try {
            $category->update($request->validated());
            $resource = new CategoryResource($category);
            return $this->successResponse($resource, 'Registro actualizado exitosamente.');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al actualizar el registro.', 500, $e->getMessage());
        }
    }

    public function destroy(Category $category): JsonResponse
    {
        try {
            $category->delete();
            return $this->successResponse(null, 'Registro eliminado exitosamente.', 204);
        } catch (Throwable $e) {
            return $this->errorResponse('Error al eliminar el registro.', 500, $e->getMessage());
        }
    }

    public function toggleStatus(Category $category): JsonResponse
    {
        try {
            $category->status_id = $category->status_id == 1 ? 2 : 1;
            $category->save();
            return $this->successResponse($category, 'Registro actualizado éxitosamente');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al actualizar el registro.', 500, $e->getMessage());
        }
    }
}
