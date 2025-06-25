<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\CreateStockRequest;
use App\Http\Requests\UpdateStockRequest;
use App\Http\Resources\StockResource;
use App\Models\Stock;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Throwable;
use App\Traits\ApiResponseTrait;
use App\DTOs\FilterDTO;
use App\Services\PaginateRegistersService;

class StockController extends Controller
{
    use ApiResponseTrait;

    protected array $searchables = ['quantity'];
    protected array $filterables = ['status_id', 'product_variant_id', 'warehouse_id'];
    protected array $relationSearchables = [
        'product_variant'   => 'sku',
        'warehouse'   => 'name',
    ];

    public function __construct(
        protected PaginateRegistersService $pagination
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $dto = FilterDTO::fromRequest($request->all());
            $results = $this->pagination->execute(Stock::query(), $dto, $this->searchables, $this->filterables, $this->relationSearchables);
            $data = [
                'items' => StockResource::collection($results),
                'pagination' => ['current_page' => $results->currentPage(), 'per_page' => $results->perPage(), 'total' => $results->total(), 'last_page' => $results->lastPage()],
            ];
            return $this->successResponse($data, 'Registros obtenidos exitosamente.');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al obtener los registros.', 500, $e->getMessage());
        }
    }

    public function store(CreateStockRequest $request): JsonResponse
    {
        try {
            $stock = Stock::create($request->validated());
            return $this->successResponse(new StockResource($stock), 'Registro creado éxitosamente.', 201);
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al crear el registro.', 500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al crear el registro.', 500, $e->getMessage());
        }
    }

    public function show(Stock $stock): JsonResponse
    {
        try {
            return $this->successResponse(new StockResource($stock), 'Registro obtenido éxitosamente.');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al obtener el registro.', 500, $e->getMessage());
        }
    }

    public function update(UpdateStockRequest $request, Stock $stock): JsonResponse
    {
        try {
            $stock->update($request->validated());
            return $this->successResponse(new StockResource($stock), 'Registro actualizado correctamente.');
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al actualizar el registro.', 500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al actualizar el registro.', 500, $e->getMessage());
        }
    }

    public function destroy(Stock $stock): JsonResponse
    {
        try {
            $stock->delete();
            return $this->successResponse(null, 'Registro eliminado éxitosamente.', 204);
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al eliminar el registro.', 500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al eliminar el registro.', 500, $e->getMessage());
        }
    }

    public function toggleStatus(Stock $stock): JsonResponse
    {
        try {
            $stock->status_id = $stock->status_id == 1 ? 2 : 1;
            $stock->save();
            return $this->successResponse($stock, 'Registro actualizado éxitosamente');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al actualizar el registro.', 500, $e->getMessage());
        }
    }
}
