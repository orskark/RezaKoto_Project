<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\CreateShippingStatusRequest;
use App\Http\Requests\UpdateShippingStatusRequest;
use App\Http\Resources\ShippingStatusResource;
use App\Models\ShippingStatus;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Throwable;
use App\Traits\ApiResponseTrait;
use App\DTOs\FilterDTO;
use App\Services\PaginateRegistersService;

class ShippingStatusController extends Controller
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
            $results = $this->pagination->execute(ShippingStatus::query(), $dto, $this->searchables, $this->filterables);
            $data = [
                'items' => ShippingStatusResource::collection($results),
                'pagination' => ['current_page' => $results->currentPage(), 'per_page' => $results->perPage(), 'total' => $results->total(), 'last_page' => $results->lastPage()],
            ];
            return $this->successResponse($data, 'Registros obtenidos exitosamente.');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al obtener los registros.', 500, $e->getMessage());
        }
    }

    public function store(CreateShippingStatusRequest $request): JsonResponse
    {
        try {
            $shipping_status = ShippingStatus::create($request->validated());
            return $this->successResponse(new ShippingStatusResource($shipping_status), 'Registro creado éxitosamente.', 201);
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al crear el registro.', 500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al crear el registro.', 500, $e->getMessage());
        }
    }

    public function show(ShippingStatus $shipping_status): JsonResponse
    {
        try {
            return $this->successResponse(new ShippingStatusResource($shipping_status), 'Registro obtenido éxitosamente.');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al obtener el registro.', 500, $e->getMessage());
        }
    }

    public function update(UpdateShippingStatusRequest $request, ShippingStatus $shipping_status): JsonResponse
    {
        try {
            $shipping_status->update($request->validated());
            return $this->successResponse(new ShippingStatusResource($shipping_status), 'Registro actualizado correctamente.');
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al actualizar el registro.', 500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al actualizar el registro.', 500, $e->getMessage());
        }
    }

    public function destroy(ShippingStatus $shipping_status): JsonResponse
    {
        try {
            $shipping_status->delete();
            return $this->successResponse(null, 'Registro eliminado éxitosamente.', 204);
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al eliminar el registro.', 500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al eliminar el registro.', 500, $e->getMessage());
        }
    }

    public function toggleStatus(ShippingStatus $shipping_status): JsonResponse
    {
        try {
            $shipping_status->status_id = $shipping_status->status_id == 1 ? 2 : 1;
            $shipping_status->save();
            return $this->successResponse($shipping_status, 'Registro actualizado éxitosamente');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al actualizar el registro.', 500, $e->getMessage());
        }
    }
}
