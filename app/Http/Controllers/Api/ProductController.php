<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Throwable;
use App\Traits\ApiResponseTrait;
use App\DTOs\FilterDTO;
use App\Services\PaginateRegistersService;

class ProductController extends Controller
{
    use ApiResponseTrait;

    protected array $searchables = ['name'];
    protected array $filterables = ['brand_id', 'gender_id', 'category_id', 'enterprise_id', 'status_id'];
    protected array $relationSearchables = [
        'brand'      => 'name',
        'gender'     => 'name',
        'category'   => 'name',
        'enterprise' => 'name',
    ];

    public function __construct(
        protected PaginateRegistersService $pagination
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $dto = FilterDTO::fromRequest($request->all());
            $results = $this->pagination->execute(Product::query(), $dto, $this->searchables, $this->filterables, $this->relationSearchables);
            $data = [
                'items' => ProductResource::collection($results),
                'pagination' => ['current_page' => $results->currentPage(), 'per_page' => $results->perPage(), 'total' => $results->total(), 'last_page' => $results->lastPage()],
            ];
            return $this->successResponse($data, 'Registros obtenidos exitosamente.');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al obtener los registros.', 500, $e->getMessage());
        }
    }

    public function store(CreateProductRequest $request): JsonResponse
    {
        try {
            $product = Product::create($request->validated());
            return $this->successResponse(new ProductResource($product), 'Registro creado éxitosamente.', 201);
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al crear el registro.', 500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al crear el registro.', 500, $e->getMessage());
        }
    }

    public function show(Product $product): JsonResponse
    {
        try {
            return $this->successResponse(new ProductResource($product), 'Registro obtenido éxitosamente.');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al obtener el registro.', 500, $e->getMessage());
        }
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        try {
            $product->update($request->validated());
            return $this->successResponse(new ProductResource($product), 'Registro actualizado correctamente.');
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al actualizar el registro.', 500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al actualizar el registro.', 500, $e->getMessage());
        }
    }

    public function destroy(Product $product): JsonResponse
    {
        try {
            $product->delete();
            return $this->successResponse(null, 'Registro eliminado éxitosamente.', 204);
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al eliminar el registro.', 500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al eliminar el registro.', 500, $e->getMessage());
        }
    }

    public function toggleStatus(Product $product): JsonResponse
    {
        try {
            $product->status_id = $product->status_id == 1 ? 2 : 1;
            $product->save();
            return $this->successResponse($product, 'Registro actualizado éxitosamente');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al actualizar el registro.', 500, $e->getMessage());
        }
    }
}
