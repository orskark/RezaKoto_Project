<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\CreateOrderStatusRequest;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Http\Resources\OrderStatusResource;
use App\Models\OrderStatus;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Throwable;
use App\Traits\ApiResponseTrait;
use App\DTOs\FilterDTO;
use App\Services\PaginateRegistersService;

class OrderStatusController extends Controller
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
            $results = $this->pagination->execute(OrderStatus::query(), $dto, $this->searchables, $this->filterables);
            $data = [
                'items' => OrderStatusResource::collection($results),
                'pagination' => ['current_page' => $results->currentPage(), 'per_page' => $results->perPage(), 'total' => $results->total(), 'last_page' => $results->lastPage()],
            ];
            return $this->successResponse($data, 'Registros obtenidos exitosamente.');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al obtener los registros.', 500, $e->getMessage());
        }
    }

    public function store(CreateOrderStatusRequest $request): JsonResponse
    {
        try {
            $order_status = OrderStatus::create($request->validated());
            return $this->successResponse(new OrderStatusResource($order_status), 'Registro creado éxitosamente.', 201);
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al crear el registro.', 500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al crear el registro.', 500, $e->getMessage());
        }
    }

    public function show(OrderStatus $order_status): JsonResponse
    {
        try {
            return $this->successResponse(new OrderStatusResource($order_status), 'Registro obtenido éxitosamente.');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al obtener el registro.', 500, $e->getMessage());
        }
    }

    public function update(UpdateOrderStatusRequest $request, OrderStatus $order_status): JsonResponse
    {
        try {
            $order_status->update($request->validated());
            return $this->successResponse(new OrderStatusResource($order_status), 'Registro actualizado correctamente.');
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al actualizar el registro.', 500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al actualizar el registro.', 500, $e->getMessage());
        }
    }

    public function destroy(OrderStatus $order_status): JsonResponse
    {
        try {
            $order_status->delete();
            return $this->successResponse(null, 'Registro eliminado éxitosamente.', 204);
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al eliminar el registro.', 500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al eliminar el registro.', 500, $e->getMessage());
        }
    }

    public function toggleStatus(OrderStatus $order_status): JsonResponse
    {
        try {
            $order_status->status_id = $order_status->status_id == 1 ? 2 : 1;
            $order_status->save();
            return $this->successResponse($order_status, 'Registro actualizado éxitosamente');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al actualizar el registro.', 500, $e->getMessage());
        }
    }
}
