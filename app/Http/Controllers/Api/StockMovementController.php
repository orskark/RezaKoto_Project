<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\CreateStockMovementRequest;
use App\Http\Requests\UpdateStockMovementRequest;
use App\Http\Resources\StockMovementResource;
use App\Models\StockMovement;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Throwable;
use App\Traits\ApiResponseTrait;
use App\DTOs\FilterDTO;
use App\Services\PaginateRegistersService;
use App\Services\StockMovementService;
use Illuminate\Support\Facades\DB;

class StockMovementController extends Controller
{
    use ApiResponseTrait;

    protected array $searchables = ['quantity'];
    protected array $filterables = ['status_id', 'movement_type_id'];
    protected array $relationSearchables = [
        'stock.product_variant'   => 'sku',
    ];

    public function __construct(
        protected PaginateRegistersService $pagination,
        protected StockMovementService $stockMovementService
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $dto = FilterDTO::fromRequest($request->all());
            $results = $this->pagination->execute(StockMovement::query(), $dto, $this->searchables, $this->filterables, $this->relationSearchables);
            $data = [
                'items' => StockMovementResource::collection($results),
                'pagination' => ['current_page' => $results->currentPage(), 'per_page' => $results->perPage(), 'total' => $results->total(), 'last_page' => $results->lastPage()],
            ];
            return $this->successResponse($data, 'Registros obtenidos exitosamente.');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al obtener los registros.', 500, $e->getMessage());
        }
    }

    public function store(CreateStockMovementRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $stock_movement = StockMovement::create($request->validated());
            $this->stockMovementService->applyMovement($stock_movement);
            DB::commit();
            return $this->successResponse(new StockMovementResource($stock_movement), 'Registro creado éxitosamente.', 201);
        } catch (QueryException $e) {
            DB::rollBack();
            return $this->errorResponse('Error de base de datos al crear el registro.', 500, $e->getMessage());
        } catch (Throwable $e) {
            DB::rollBack();
            return $this->errorResponse('Error al crear el registro.', 500, $e->getMessage());
        }
    }

    public function show(StockMovement $stock_movement): JsonResponse
    {
        try {
            return $this->successResponse(new StockMovementResource($stock_movement), 'Registro obtenido éxitosamente.');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al obtener el registro.', 500, $e->getMessage());
        }
    }

    public function update(UpdateStockMovementRequest $request, StockMovement $stock_movement): JsonResponse
    {
        try {
            DB::beginTransaction();
            $oldMovement = clone $stock_movement;
            $stock_movement->update($request->validated());
            $this->stockMovementService->handleUpdatedMovement($oldMovement, $stock_movement);
            DB::commit();
            return $this->successResponse(new StockMovementResource($stock_movement), 'Registro actualizado correctamente.');
        } catch (QueryException $e) {
            DB::rollBack();
            return $this->errorResponse('Error de base de datos al actualizar el registro.', 500, $e->getMessage());
        } catch (Throwable $e) {
            DB::rollBack();
            return $this->errorResponse('Error al actualizar el registro.', 500, $e->getMessage());
        }
    }

    public function destroy(StockMovement $stock_movement): JsonResponse
    {
        try {
            $stock_movement->delete();
            return $this->successResponse(null, 'Registro eliminado éxitosamente.', 204);
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al eliminar el registro.', 500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al eliminar el registro.', 500, $e->getMessage());
        }
    }

    public function toggleStatus(StockMovement $stock_movement): JsonResponse
    {
        try {
            $stock_movement->status_id = $stock_movement->status_id == 1 ? 2 : 1;
            $stock_movement->save();
            return $this->successResponse($stock_movement, 'Registro actualizado éxitosamente');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al actualizar el registro.', 500, $e->getMessage());
        }
    }
}
