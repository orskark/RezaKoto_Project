<?php

namespace App\Http\Controllers\Api;

use App\DTOs\FilterDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateOrderPaymentRequest;
use App\Http\Requests\UpdateOrderPaymentRequest;
use App\Http\Resources\OrderPaymentResource;
use App\Models\OrderPayment;
use App\Services\PaginateRegistersService;
use App\Traits\ApiResponseTrait;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class OrderPaymentController extends Controller
{
    use ApiResponseTrait;
    protected array $searchables = ['transaction_reference'];

    public function __construct(
        protected PaginateRegistersService $pagination
    ) {}
    /**
     * Muestra todos los detalles de la ordenes.
     */
    public function index(Request $request):JsonResponse
    {
        try {
            $dto = FilterDTO::fromRequest($request->all());
            $results = $this->pagination->execute(OrderPayment::query(), $dto, $this->searchables);
            $data = [
                'items' => OrderPaymentResource::collection($results),
                'pagination' => ['current_page' => $results->currentPage(), 'per_page' => $results->perPage(), 'total' => $results->total(), 'last_page' => $results->lastPage()],
            ];
            return $this->successResponse($data,'Registros Obtenidos Exitosamente');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al obtener los registros',500, $e->getMessage());
        }

    }

    /**
     * Guarda un nuevo detalle de la orden en la base de datos.
     */
    public function store(CreateOrderPaymentRequest $request):JsonResponse
    {
        try {
            $model = OrderPayment::create($request->validated());
            return $this->successResponse(new OrderPaymentResource($model),'Registro Creado Exitosamente',201);
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al crear el registro',500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al crear el registro',500, $e->getMessage());
        }

    }

    /**
     * Muestra un detalle de la orden específico.
     */
    public function show(OrderPayment $order_payment):JsonResponse
    {
        try {
            return $this->successResponse(new OrderPaymentResource($order_payment),'Registro Obtenido Exitosamente');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al mostrar el registro',500,$e->getMessage());
        }
    }

    /**
     * Actualiza un detalle de la orden existente.
     */
    public function update(UpdateOrderPaymentRequest $request, OrderPayment $order_payment):JsonResponse
    {
        try {
            $order_payment->update($request->validated());
            return $this->successResponse(new OrderPaymentResource($order_payment),'Registro actualizado exitosamente');
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al actualizar el registro',500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al actualizar el registro',500, $e->getMessage());
        }
    }

    /**
     * Elimina un detalle de la orden.
     */
    public function destroy(OrderPayment $order_payment):JsonResponse
    {
        try {
            $order_payment->delete();
            return $this->successResponse(null,'Registro eliminado exitosamente');
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al eliminar el registro',500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al eliminar el registro',500, $e->getMessage());
        }
    }
}


