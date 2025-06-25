<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\CreateProductVariantRequest;
use App\Http\Requests\UpdateProductVariantRequest;
use App\Http\Resources\ProductVariantResource;
use App\Models\ProductVariant;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Throwable;
use App\Traits\ApiResponseTrait;
use App\DTOs\FilterDTO;
use App\Models\ProductVariantImage;
use App\Services\PaginateRegistersService;
use Illuminate\Support\Facades\Storage;

class ProductVariantController extends Controller
{
    use ApiResponseTrait;

    protected array $searchables = ['sku'];
    protected array $filterables = ['product_id', 'size_id', 'color_id', 'status_id'];
    protected array $relationSearchables = [
        'product' => 'name',
        'size'    => 'name',
        'color'   => 'name',
    ];

    public function __construct(
        protected PaginateRegistersService $pagination
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $dto = FilterDTO::fromRequest($request->all());
            $results = $this->pagination->execute(ProductVariant::query(), $dto, $this->searchables, $this->filterables, $this->relationSearchables);
            $data = [
                'items' => ProductVariantResource::collection($results),
                'pagination' => ['current_page' => $results->currentPage(), 'per_page' => $results->perPage(), 'total' => $results->total(), 'last_page' => $results->lastPage()],
            ];
            return $this->successResponse($data, 'Registros obtenidos exitosamente.');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al obtener los registros.', 500, $e->getMessage());
        }
    }

    public function store(CreateProductVariantRequest $request): JsonResponse
    {
        try {
            $product_variant = ProductVariant::create($request->only(['product_id', 'size_id', 'color_id', 'sku']));
            // Si hay imágenes, procesarlas
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    // Almacenar la imagen en el disco 'public/product_variant_images'
                    $path = $image->store('product_variant_images', 'public');

                    // Crear el registro en la tabla product_variant_images
                    ProductVariantImage::create([
                        'product_variant_id' => $product_variant->id,
                        'image_route' => Storage::url($path), // URL pública
                    ]);
                }
            }
            return $this->successResponse(new ProductVariantResource($product_variant), 'Registro creado éxitosamente.', 201);
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al crear el registro.', 500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al crear el registro.', 500, $e->getMessage());
        }
    }

    public function show(ProductVariant $product_variant): JsonResponse
    {
        try {
            return $this->successResponse(new ProductVariantResource($product_variant), 'Registro obtenido éxitosamente.');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al obtener el registro.', 500, $e->getMessage());
        }
    }

    public function update(UpdateProductVariantRequest $request, ProductVariant $product_variant): JsonResponse
    {
        try {
            $product_variant->update($request->validated());
            return $this->successResponse(new ProductVariantResource($product_variant), 'Registro actualizado correctamente.');
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al actualizar el registro.', 500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al actualizar el registro.', 500, $e->getMessage());
        }
    }

    public function destroy(ProductVariant $product_variant): JsonResponse
    {
        try {
            $product_variant->delete();
            return $this->successResponse(null, 'Registro eliminado éxitosamente.', 204);
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al eliminar el registro.', 500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al eliminar el registro.', 500, $e->getMessage());
        }
    }

    public function toggleStatus(ProductVariant $product_variant): JsonResponse
    {
        try {
            $product_variant->status_id = $product_variant->status_id == 1 ? 2 : 1;
            $product_variant->save();
            return $this->successResponse($product_variant, 'Registro actualizado éxitosamente');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al actualizar el registro.', 500, $e->getMessage());
        }
    }
}
