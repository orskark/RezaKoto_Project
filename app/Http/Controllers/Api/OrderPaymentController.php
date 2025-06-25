<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\CreateOrderPaymentRequest;
use App\Http\Requests\UpdateOrderPaymentRequest;
use App\Http\Resources\OrderPaymentResource;
use App\Models\OrderPayment;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Throwable;
use App\Traits\ApiResponseTrait;
use App\DTOs\FilterDTO;
use App\Services\OrderPaymentService;
use App\Services\PaginateRegistersService;

class OrderPaymentController extends Controller
{
    use ApiResponseTrait;

    protected array $searchables = ['transaction_reference'];
    protected array $filterables = ['status_id', 'order_id'];

    public function __construct(
        protected PaginateRegistersService $pagination,
        protected OrderPaymentService $orderPaymentService
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $dto = FilterDTO::fromRequest($request->all());
            $results = $this->pagination->execute(OrderPayment::query(), $dto, $this->searchables, $this->filterables);
            $data = [
                'items' => OrderPaymentResource::collection($results),
                'pagination' => ['current_page' => $results->currentPage(), 'per_page' => $results->perPage(), 'total' => $results->total(), 'last_page' => $results->lastPage()],
            ];
            return $this->successResponse($data, 'Registros obtenidos exitosamente.');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al obtener los registros.', 500, $e->getMessage());
        }
    }

    public function store(CreateOrderPaymentRequest $request): JsonResponse
    {
        try {
            $order_payment = $this->orderPaymentService->create($request->validated());
            return $this->successResponse(new OrderPaymentResource($order_payment), 'Registro creado éxitosamente.', 201);
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al crear el registro.', 500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al crear el registro.', 500, $e->getMessage());
        }
    }

    public function show(OrderPayment $order_payment): JsonResponse
    {
        try {
            return $this->successResponse(new OrderPaymentResource($order_payment), 'Registro obtenido éxitosamente.');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al obtener el registro.', 500, $e->getMessage());
        }
    }

    public function update(UpdateOrderPaymentRequest $request, OrderPayment $order_payment): JsonResponse
    {
        try {
            $order_payment = $this->orderPaymentService->update($order_payment, $request->validated());
            return $this->successResponse(new OrderPaymentResource($order_payment), 'Registro actualizado correctamente.');
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al actualizar el registro.', 500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al actualizar el registro.', 500, $e->getMessage());
        }
    }

    public function destroy(OrderPayment $order_payment): JsonResponse
    {
        try {
            $order_payment->delete();
            return $this->successResponse(null, 'Registro eliminado éxitosamente.', 204);
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al eliminar el registro.', 500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al eliminar el registro.', 500, $e->getMessage());
        }
    }

    public function toggleStatus(OrderPayment $order_payment): JsonResponse
    {
        try {
            $order_payment->status_id = $order_payment->status_id == 1 ? 2 : 1;
            $order_payment->save();
            return $this->successResponse($order_payment, 'Registro actualizado éxitosamente');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al actualizar el registro.', 500, $e->getMessage());
        }
    }
}
