<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\CreateOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Throwable;
use App\Traits\ApiResponseTrait;
use App\DTOs\FilterDTO;
use App\Enums\MovementTypeEnum;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use App\Services\PaginateRegistersService;
use App\Services\StockMovementService;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    use ApiResponseTrait;

    protected array $searchables = ['total_value', 'mailing_address'];
    protected array $filterables = ['status_id', 'user_id', 'payment_method_id', 'order_status_id', 'payment_status_id'];

    public function __construct(
        protected PaginateRegistersService $pagination,
        protected StockMovementService $stockMovementService
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $dto = FilterDTO::fromRequest($request->all());
            $results = $this->pagination->execute(Order::query(), $dto, $this->searchables, $this->filterables);
            $data = [
                'items' => OrderResource::collection($results),
                'pagination' => ['current_page' => $results->currentPage(), 'per_page' => $results->perPage(), 'total' => $results->total(), 'last_page' => $results->lastPage()],
            ];
            return $this->successResponse($data, 'Registros obtenidos exitosamente.');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al obtener los registros.', 500, $e->getMessage());
        }
    }

    public function store(CreateOrderRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            // Crear el pedido inicial con total_value = 0 y un tax ficticio
            $order = Order::create([
                'user_id' => $request->user_id,
                'payment_method_id' => $request->payment_method_id,
                'order_status_id' => $request->order_status_id,
                'payment_status_id' => $request->payment_status_id,
                'mailing_address' => $request->mailing_address,
                'total_value' => 0, // Se actualiza después
                'tax_value' => 1900, // Valor inventado o fijo
            ]);

            $total = 0;

            foreach ($request->order_details as $detail) {
                $variant = ProductVariant::with(['product', 'stock'])->findOrFail($detail['product_variant_id']);
                $unitPrice = $variant->product->value;
                $subTotal = $unitPrice * $detail['quantity'];
                $stockId = $variant->stock->id;

                // Acumular total
                $total += $subTotal;

                // Crear detalle
                $order->order_details()->create([
                    'product_variant_id' => $variant->id,
                    'quantity' => $detail['quantity'],
                    'unit_price' => $unitPrice,
                    'sub_total' => $subTotal,
                    'product_snapshot_json' => json_encode([
                        'product_name' => $variant->product->name,
                        'sku' => $variant->sku,
                        'unit_price' => $unitPrice
                    ]),
                ]);

                $stock_movement = StockMovement::create([
                    'stock_id' => $stockId,
                    'movement_type_id' => MovementTypeEnum::Outbound->value,
                    'quantity' => $detail['quantity'],
                    'reason' => 'Venta: #' . $order->id
                ]);

                $this->stockMovementService->applyMovement($stock_movement);
            }

            // Actualizar el total del pedido
            $order->update(['total_value' => $total]);

            DB::commit();
            return $this->successResponse(new OrderResource($order), 'Registro creado éxitosamente.', 201);
        } catch (QueryException $e) {
            DB::rollBack();
            return $this->errorResponse('Error de base de datos al crear el registro.', 500, $e->getMessage());
        } catch (Throwable $e) {
            DB::rollBack();
            return $this->errorResponse('Error al crear el registro.', 500, $e->getMessage());
        }
    }

    public function show(Order $order): JsonResponse
    {
        try {
            return $this->successResponse(new OrderResource($order), 'Registro obtenido éxitosamente.');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al obtener el registro.', 500, $e->getMessage());
        }
    }

    public function update(UpdateOrderRequest $request, Order $order): JsonResponse
    {
        try {
            $order->update($request->validated());
            return $this->successResponse(new OrderResource($order), 'Registro actualizado correctamente.');
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al actualizar el registro.', 500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al actualizar el registro.', 500, $e->getMessage());
        }
    }

    public function destroy(Order $order): JsonResponse
    {
        try {
            $order->delete();
            return $this->successResponse(null, 'Registro eliminado éxitosamente.', 204);
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al eliminar el registro.', 500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al eliminar el registro.', 500, $e->getMessage());
        }
    }

    public function toggleStatus(Order $order): JsonResponse
    {
        try {
            $order->status_id = $order->status_id == 1 ? 2 : 1;
            $order->save();
            return $this->successResponse($order, 'Registro actualizado éxitosamente');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al actualizar el registro.', 500, $e->getMessage());
        }
    }
}
