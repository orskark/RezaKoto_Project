<?php

namespace App\Http\Controllers;

use App\DTOs\FilterDTO;
use App\Http\Requests\CreatePaymentStatusRequest;
use App\Http\Requests\UpdatePaymentStatusRequest;
use App\Http\Resources\PaymentStatusResource;
use App\Models\PaymentStatus;
use App\Services\PaginateRegistersService;
use App\Traits\ApiResponseTrait;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class PaymentStatusController extends Controller
{
    use ApiResponseTrait;
    protected array $searchables = ['name'];

    public function __construct(
        protected PaginateRegistersService $pagination
    ) {}
    /**
     * Muestra todos los estados de pago.
     */
    public function index(Request $request):JsonResponse
    {
        try {
            $dto = FilterDTO::fromRequest($request->all());
            $results = $this->pagination->execute(PaymentStatus::query(), $dto, $this->searchables);
            $data = [
                'items' => PaymentStatusResource::collection($results),
                'pagination' => ['current_page' => $results->currentPage(), 'per_page' => $results->perPage(), 'total' => $results->total(), 'last_page' => $results->lastPage()],
            ];
            return $this->successResponse($data,'Registros Obtenidos Exitosamente');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al obtener los registros',500, $e->getMessage());
        }
        
    }

    /**
     * Guarda un nuevo estado de pago en la base de datos.
     */
    public function store(CreatePaymentStatusRequest $request):JsonResponse
    { 
        try {
            $model = PaymentStatus::create($request->validated());
            return $this->successResponse(new PaymentStatusResource($model),'Registro Creado Exitosamente',201);
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al crear el registro',500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al crear el registro',500, $e->getMessage());
        }
            
    }

    /**
     * Muestra un estado de pago específico.
     */
    public function show(PaymentStatus $payment_status):JsonResponse
    {
        try {
            return $this->successResponse(new PaymentStatusResource($payment_status),'Registro Obtenido Exitosamente');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al mostrar el registro',500,$e->getMessage());
        }
    }

    /**
     * Actualiza un estado de pago existente.
     */
    public function update(UpdatePaymentStatusRequest $request, PaymentStatus $payment_status):JsonResponse
    {
        try {
            $payment_status->update($request->validated());
            return $this->successResponse(new PaymentStatusResource($payment_status),'Registro actualizado exitosamente');
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al actualizar el registro',500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al actualizar el registro',500, $e->getMessage());
        }
    }

    /**
     * Elimina un estado de pago.
     */
    public function destroy(PaymentStatus $payment_status):JsonResponse
    {
        try {
            $payment_status->delete();
            return $this->successResponse(null,'Registro eliminado exitosamente');
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al eliminar el registro',500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al eliminar el registro',500, $e->getMessage());
        }
    }
}

