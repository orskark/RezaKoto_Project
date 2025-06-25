<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\CreateOrderShippingRequest;
use App\Http\Requests\UpdateOrderShippingRequest;
use App\Http\Resources\OrderShippingResource;
use App\Models\OrderShipping;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Throwable;
use App\Traits\ApiResponseTrait;
use App\DTOs\FilterDTO;
use App\Services\PaginateRegistersService;

class OrderShippingController extends Controller
{
    use ApiResponseTrait;

    protected array $searchables = ['delivery_date'];
    protected array $filterables = ['status_id', 'order_id', 'enterprise_id', 'shipping_status_id'];

    public function __construct(
        protected PaginateRegistersService $pagination
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $dto = FilterDTO::fromRequest($request->all());
            $results = $this->pagination->execute(OrderShipping::query(), $dto, $this->searchables, $this->filterables);
            $data = [
                'items' => OrderShippingResource::collection($results),
                'pagination' => ['current_page' => $results->currentPage(), 'per_page' => $results->perPage(), 'total' => $results->total(), 'last_page' => $results->lastPage()],
            ];
            return $this->successResponse($data, 'Registros obtenidos exitosamente.');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al obtener los registros.', 500, $e->getMessage());
        }
    }

    public function store(CreateOrderShippingRequest $request): JsonResponse
    {
        try {
            $order_shipping = OrderShipping::create($request->validated());
            return $this->successResponse(new OrderShippingResource($order_shipping), 'Registro creado éxitosamente.', 201);
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al crear el registro.', 500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al crear el registro.', 500, $e->getMessage());
        }
    }

    public function show(OrderShipping $order_shipping): JsonResponse
    {
        try {
            return $this->successResponse(new OrderShippingResource($order_shipping), 'Registro obtenido éxitosamente.');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al obtener el registro.', 500, $e->getMessage());
        }
    }

    public function update(UpdateOrderShippingRequest $request, OrderShipping $order_shipping): JsonResponse
    {
        try {
            $order_shipping->update($request->validated());
            return $this->successResponse(new OrderShippingResource($order_shipping), 'Registro actualizado correctamente.');
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al actualizar el registro.', 500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al actualizar el registro.', 500, $e->getMessage());
        }
    }

    public function destroy(OrderShipping $order_shipping): JsonResponse
    {
        try {
            $order_shipping->delete();
            return $this->successResponse(null, 'Registro eliminado éxitosamente.', 204);
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al eliminar el registro.', 500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al eliminar el registro.', 500, $e->getMessage());
        }
    }

    public function toggleStatus(OrderShipping $order_shipping): JsonResponse
    {
        try {
            $order_shipping->status_id = $order_shipping->status_id == 1 ? 2 : 1;
            $order_shipping->save();
            return $this->successResponse($order_shipping, 'Registro actualizado éxitosamente');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al actualizar el registro.', 500, $e->getMessage());
        }
    }
}
