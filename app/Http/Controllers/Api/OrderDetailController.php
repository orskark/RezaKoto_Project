<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\OrderDetailResource;
use App\Models\OrderDetail;
use Illuminate\Http\JsonResponse;
use Throwable;
use App\Traits\ApiResponseTrait;
use App\DTOs\FilterDTO;
use App\Services\PaginateRegistersService;

class OrderDetailController extends Controller
{
    use ApiResponseTrait;

    protected array $filterables = ['status_id', 'order_id', 'product_variant_id'];

    public function __construct(
        protected PaginateRegistersService $pagination
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $dto = FilterDTO::fromRequest($request->all());
            $results = $this->pagination->execute(OrderDetail::query(), $dto, [], $this->filterables);
            $data = [
                'items' => OrderDetailResource::collection($results),
                'pagination' => ['current_page' => $results->currentPage(), 'per_page' => $results->perPage(), 'total' => $results->total(), 'last_page' => $results->lastPage()],
            ];
            return $this->successResponse($data, 'Registros obtenidos exitosamente.');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al obtener los registros.', 500, $e->getMessage());
        }
    }
}
