<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\CreatePaymentStatusRequest;
use App\Http\Requests\UpdatePaymentStatusRequest;
use App\Http\Resources\PaymentStatusResource;
use App\Models\PaymentStatus;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Throwable;
use App\Traits\ApiResponseTrait;
use App\DTOs\FilterDTO;
use App\Services\PaginateRegistersService;

class PaymentStatusController extends Controller
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
            $results = $this->pagination->execute(PaymentStatus::query(), $dto, $this->searchables, $this->filterables);
            $data = [
                'items' => PaymentStatusResource::collection($results),
                'pagination' => ['current_page' => $results->currentPage(), 'per_page' => $results->perPage(), 'total' => $results->total(), 'last_page' => $results->lastPage()],
            ];
            return $this->successResponse($data, 'Registros obtenidos exitosamente.');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al obtener los registros.', 500, $e->getMessage());
        }
    }

    public function store(CreatePaymentStatusRequest $request): JsonResponse
    {
        try {
            $payment_status = PaymentStatus::create($request->validated());
            return $this->successResponse(new PaymentStatusResource($payment_status), 'Registro creado éxitosamente.', 201);
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al crear el registro.', 500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al crear el registro.', 500, $e->getMessage());
        }
    }

    public function show(PaymentStatus $payment_status): JsonResponse
    {
        try {
            return $this->successResponse(new PaymentStatusResource($payment_status), 'Registro obtenido éxitosamente.');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al obtener el registro.', 500, $e->getMessage());
        }
    }

    public function update(UpdatePaymentStatusRequest $request, PaymentStatus $payment_status): JsonResponse
    {
        try {
            $payment_status->update($request->validated());
            return $this->successResponse(new PaymentStatusResource($payment_status), 'Registro actualizado correctamente.');
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al actualizar el registro.', 500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al actualizar el registro.', 500, $e->getMessage());
        }
    }

    public function destroy(PaymentStatus $payment_status): JsonResponse
    {
        try {
            $payment_status->delete();
            return $this->successResponse(null, 'Registro eliminado éxitosamente.', 204);
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al eliminar el registro.', 500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al eliminar el registro.', 500, $e->getMessage());
        }
    }

    public function toggleStatus(PaymentStatus $payment_status): JsonResponse
    {
        try {
            $payment_status->status_id = $payment_status->status_id == 1 ? 2 : 1;
            $payment_status->save();
            return $this->successResponse($payment_status, 'Registro actualizado éxitosamente');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al actualizar el registro.', 500, $e->getMessage());
        }
    }
}
