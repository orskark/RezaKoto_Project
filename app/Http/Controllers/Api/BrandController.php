<?php

namespace App\Http\Controllers;

use App\DTOs\FilterDTO;
use App\Http\Requests\CreateBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use App\Http\Resources\BrandResource;
use App\Models\Brand;
use App\Services\PaginateRegistersService;
use App\Traits\ApiResponseTrait;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class BrandController extends Controller
{
    use ApiResponseTrait;
    protected array $searchables = ['name'];

    public function __construct(
        protected PaginateRegistersService $pagination
    ) {}
    /**
     * Muestra todas las marcas.
     */
    public function index(Request $request):JsonResponse
    {
        try {
            $dto = FilterDTO::fromRequest($request->all());
            $results = $this->pagination->execute(Brand::query(), $dto, $this->searchables);
            $data = [
                'items' => BrandResource::collection($results),
                'pagination' => ['current_page' => $results->currentPage(), 'per_page' => $results->perPage(), 'total' => $results->total(), 'last_page' => $results->lastPage()],
            ];
            return $this->successResponse($data,'Registros Obtenidos Exitosamente');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al obtener los registros',500, $e->getMessage());
        }
        
    }

    /**
     * Guarda una nueva marca en la base de datos.
     */
    public function store(CreateBrandRequest $request):JsonResponse
    { 
        try {
            $model = Brand::create($request->validated());
            return $this->successResponse(new BrandResource($model),'Registro Creado Exitosamente',201);
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al crear el registro',500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al crear el registro',500, $e->getMessage());
        }
            
    }

    /**
     * Muestra una marca específica.
     */
    public function show(Brand $brand):JsonResponse
    {
        try {
            return $this->successResponse(new BrandResource($brand),'Registro Obtenido Exitosamente');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al mostrar el registro',500,$e->getMessage());
        }
    }

    /**
     * Actualiza una marca existente.
     */
    public function update(UpdateBrandRequest $request, Brand $brand):JsonResponse
    {
        try {
            $brand->update($request->validated());
            return $this->successResponse(new BrandResource($brand),'Registro actualizado exitosamente');
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al actualizar el registro',500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al actualizar el registro',500, $e->getMessage());
        }
    }

    /**
     * Elimina una marca.
     */
    public function destroy(Brand $brand):JsonResponse
    {
        try {
            $brand->delete();
            return $this->successResponse(null,'Registro eliminado exitosamente');
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al eliminar el registro',500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al eliminar el registro',500, $e->getMessage());
        }
    }
}

