<?php

namespace App\Http\Controllers\Api;

use App\DTOs\FilterDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateProductvariantImageRequest;
use App\Http\Requests\UpdateProductvariantImageRequest;
use App\Http\Resources\ProductVariantImageResource;
use App\Models\ProductVariantImage;
use App\Services\PaginateRegistersService;
use App\Traits\ApiResponseTrait;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class ProductVariantImageController extends Controller
{
    use ApiResponseTrait;
    protected array $searchables = ['name'];

    public function __construct(
        protected PaginateRegistersService $pagination
    ) {}
    /**
     * Muestra todos los colores.
     */
    public function index(Request $request):JsonResponse
    {
        try {
            $dto = FilterDTO::fromRequest($request->all());
            $results = $this->pagination->execute(ProductVariantImage::query(), $dto, $this->searchables);
            $data = [
                'items' => ProductVariantImageResource::collection($results),
                'pagination' => ['current_page' => $results->currentPage(), 'per_page' => $results->perPage(), 'total' => $results->total(), 'last_page' => $results->lastPage()],
            ];
            return $this->successResponse($data,'Registros Obtenidos Exitosamente');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al obtener los registros',500, $e->getMessage());
        }

    }

    /**
     * Guarda un nueva color en la base de datos.
     */
    public function store(CreateProductvariantImageRequest $request):JsonResponse
    {
        try {
            $model = ProductVariantImage::create($request->validated());
            return $this->successResponse(new ProductVariantImageResource($model),'Registro Creado Exitosamente',201);
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al crear el registro',500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al crear el registro',500, $e->getMessage());
        }

    }

    /**
     * Muestra un color específico.
     */
    public function show(ProductVariantImage $product_variant_image):JsonResponse
    {
        try {
            return $this->successResponse(new ProductVariantImageResource($product_variant_image),'Registro Obtenido Exitosamente');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al mostrar el registro',500,$e->getMessage());
        }
    }

    /**
     * Actualiza un color existente.
     */
    public function update(UpdateProductvariantImageRequest $request, ProductVariantImage $product_variant_image):JsonResponse
    {
        try {
            $product_variant_image->update($request->validated());
            return $this->successResponse(new ProductVariantImageResource($product_variant_image),'Registro actualizado exitosamente');
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al actualizar el registro',500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al actualizar el registro',500, $e->getMessage());
        }
    }

    /**
     * Elimina un color.
     */
    public function destroy(ProductVariantImage $product_variant_image):JsonResponse
    {
        try {
            $product_variant_image->delete();
            return $this->successResponse(null,'Registro eliminado exitosamente');
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al eliminar el registro',500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al eliminar el registro',500, $e->getMessage());
        }
    }
}


