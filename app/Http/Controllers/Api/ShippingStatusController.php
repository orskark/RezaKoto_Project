<?php

namespace App\Http\Controllers\Api;

use App\DTOs\FilterDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateShippingStatusRequest;
use App\Http\Requests\UpdateShippingStatusRequest;
use App\Http\Resources\ShippingStatusResource;
use App\Models\ShippingStatus;
use App\Services\PaginateRegistersService;
use App\Traits\ApiResponseTrait;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class ShippingStatusController extends Controller
{
    use ApiResponseTrait;
    protected array $searchables = ['name'];

    public function __construct(
        protected PaginateRegistersService $pagination
    ) {}
    /**
     * Muestra todos los estados de envíos.
     */
    public function index(Request $request):JsonResponse
    {
        try {
            $dto = FilterDTO::fromRequest($request->all());
            $results = $this->pagination->execute(ShippingStatus::query(), $dto, $this->searchables);
            $data = [
                'items' => ShippingStatusResource::collection($results),
                'pagination' => ['current_page' => $results->currentPage(), 'per_page' => $results->perPage(), 'total' => $results->total(), 'last_page' => $results->lastPage()],
            ];
            return $this->successResponse($data,'Registros Obtenidos Exitosamente');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al obtener los registros',500, $e->getMessage());
        }

    }

    /**
     * Guarda un nuevo estado de envío en la base de datos.
     */
    public function store(CreateShippingStatusRequest $request):JsonResponse
    {
        try {
            $model = ShippingStatus::create($request->validated());
            return $this->successResponse(new ShippingStatusResource($model),'Registro Creado Exitosamente',201);
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al crear el registro',500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al crear el registro',500, $e->getMessage());
        }

    }

    /**
     * Muestra un estado de envío específico.
     */
    public function show(ShippingStatus $shipping_status):JsonResponse
    {
        try {
            return $this->successResponse(new ShippingStatusResource($shipping_status),'Registro Obtenido Exitosamente');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al mostrar el registro',500,$e->getMessage());
        }
    }

    /**
     * Actualiza un estado de envío existente.
     */
    public function update(UpdateShippingStatusRequest $request, ShippingStatus $shipping_status):JsonResponse
    {
        try {
            $shipping_status->update($request->validated());
            return $this->successResponse(new ShippingStatusResource($shipping_status),'Registro actualizado exitosamente');
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al actualizar el registro',500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al actualizar el registro',500, $e->getMessage());
        }
    }

    /**
     * Elimina un estado de envío.
     */
    public function destroy(ShippingStatus $shipping_status):JsonResponse
    {
        try {
            $shipping_status->delete();
            return $this->successResponse(null,'Registro eliminado exitosamente');
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al eliminar el registro',500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al eliminar el registro',500, $e->getMessage());
        }
    }
}
