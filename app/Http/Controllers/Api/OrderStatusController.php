<?php

namespace App\Http\Controllers;

use App\DTOs\FilterDTO;
use App\Http\Requests\CreateOrderStatusRequest;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Http\Resources\OrderStatusResource;
use App\Models\OrderStatus;
use App\Services\PaginateRegistersService;
use App\Traits\ApiResponseTrait;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class OrderStatusController extends Controller
{
    use ApiResponseTrait;
    protected array $searchables = ['total_value'];

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
            $results = $this->pagination->execute(OrderStatus::query(), $dto, $this->searchables);
            $data = [
                'items' => OrderStatusResource::collection($results),
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
    public function store(CreateOrderStatusRequest $request):JsonResponse
    { 
        try {
            $model = OrderStatus::create($request->validated());
            return $this->successResponse(new OrderStatusResource($model),'Registro Creado Exitosamente',201);
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al crear el registro',500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al crear el registro',500, $e->getMessage());
        }
            
    }

    /**
     * Muestra un detalle de la orden específico.
     */
    public function show(OrderStatus $order_status):JsonResponse
    {
        try {
            return $this->successResponse(new OrderStatusResource($order_status),'Registro Obtenido Exitosamente');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al mostrar el registro',500,$e->getMessage());
        }
    }

    /**
     * Actualiza un detalle de la orden existente.
     */
    public function update(UpdateOrderStatusRequest $request, OrderStatus $order_status):JsonResponse
    {
        try {
            $order_status->update($request->validated());
            return $this->successResponse(new OrderStatusResource($order_status),'Registro actualizado exitosamente');
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al actualizar el registro',500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al actualizar el registro',500, $e->getMessage());
        }
    }

    /**
     * Elimina un detalle de la orden.
     */
    public function destroy(OrderStatus $order_status):JsonResponse
    {
        try {
            $order_status->delete();
            return $this->successResponse(null,'Registro eliminado exitosamente');
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al eliminar el registro',500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al eliminar el registro',500, $e->getMessage());
        }
    }
}



