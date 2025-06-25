<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ColorController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DocumentTypeController;
use App\Http\Controllers\Api\EnterpriseController;
use App\Http\Controllers\Api\EnterpriseTypeController;
use App\Http\Controllers\Api\GenderController;
use App\Http\Controllers\Api\MovementTypeController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\OrderDetailController;
use App\Http\Controllers\Api\OrderPaymentController;
use App\Http\Controllers\Api\OrderShippingController;
use App\Http\Controllers\Api\OrderStatusController;
use App\Http\Controllers\Api\PaymentMethodController;
use App\Http\Controllers\Api\PaymentStatusController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductVariantController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\ShippingStatusController;
use App\Http\Controllers\Api\SizeController;
use App\Http\Controllers\Api\StatusController;
use App\Http\Controllers\Api\StockController;
use App\Http\Controllers\Api\StockMovementController;
use App\Http\Controllers\Api\UserRoleController;
use App\Http\Controllers\Api\WarehouseController;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::apiResource('roles', RoleController::class);
Route::apiResource('document_types', DocumentTypeController::class);
Route::get('getIdByEmail', [AuthController::class, 'getIdByEmail']);
Route::apiResource('user_roles', UserRoleController::class);

Route::middleware(['auth:api'])->group(function () {
    Route::get('getUser', [AuthController::class, 'getUser']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::apiResource('users', AuthController::class);

    Route::middleware(['is.admin'])->group(function () {
        Route::patch('brands/{brand}/toggle-status', [BrandController::class, 'toggleStatus']);
        Route::patch('categories/{category}/toggle-status', [CategoryController::class, 'toggleStatus']);
        Route::patch('colors/{color}/toggle-status', [ColorController::class, 'toggleStatus']);
        Route::patch('document_types/{document_type}/toggle-status', [DocumentTypeController::class, 'toggleStatus']);
        Route::patch('enterprise_types/{enterprise_type}/toggle-status', [EnterpriseTypeController::class, 'toggleStatus']);
        Route::patch('genders/{gender}/toggle-status', [GenderController::class, 'toggleStatus']);
        Route::patch('movement_types/{movement_type}/toggle-status', [MovementTypeController::class, 'toggleStatus']);
        Route::patch('order_statuses/{order_status}/toggle-status', [OrderStatusController::class, 'toggleStatus']);
        Route::patch('payment_methods/{payment_method}/toggle-status', [PaymentMethodController::class, 'toggleStatus']);
        Route::patch('payment_statuses/{payment_status}/toggle-status', [PaymentStatusController::class, 'toggleStatus']);
        Route::patch('roles/{role}/toggle-status', [RoleController::class, 'toggleStatus']);
        Route::patch('shipping_statuses/{shipping_status}/toggle-status', [ShippingStatusController::class, 'toggleStatus']);
        Route::patch('sizes/{size}/toggle-status', [SizeController::class, 'toggleStatus']);
        Route::patch('users/{user}/toggle-status', [AuthController::class, 'toggleStatus']);
        Route::patch('user_roles/{user_role}/toggle-status', [UserRoleController::class, 'toggleStatus']);
        Route::patch('enterprises/{enterprise}/toggle-status', [EnterpriseController::class, 'toggleStatus']);
        Route::patch('products/{product}/toggle-status', [ProductController::class, 'toggleStatus']);
        Route::patch('product_variants/{product_variant}/toggle-status', [ProductVariantController::class, 'toggleStatus']);
        Route::patch('warehouses/{warehouse}/toggle-status', [WarehouseController::class, 'toggleStatus']);
        Route::patch('stocks/{stock}/toggle-status', [StockController::class, 'toggleStatus']);
        Route::patch('stock_movements/{stock_movement}/toggle-status', [StockMovementController::class, 'toggleStatus']);
        Route::patch('orders/{order}/toggle-status', [OrderController::class, 'toggleStatus']);
        Route::patch('order_shippings/{order_shipping}/toggle-status', [OrderShippingController::class, 'toggleStatus']);
        Route::patch('order_payments/{order_payment}/toggle-status', [OrderPaymentController::class, 'toggleStatus']);

        Route::apiResource('statuses', StatusController::class);

        Route::apiResource('brands', BrandController::class);
        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('genders', GenderController::class);
        Route::apiResource('sizes', SizeController::class);
        Route::apiResource('enterprise_types', EnterpriseTypeController::class);
        Route::apiResource('colors', ColorController::class);
        Route::apiResource('movement_types', MovementTypeController::class);
        Route::apiResource('payment_methods', PaymentMethodController::class);
        Route::apiResource('shipping_statuses', ShippingStatusController::class);
        Route::apiResource('order_statuses', OrderStatusController::class);
        Route::apiResource('payment_statuses', PaymentStatusController::class);
        Route::apiResource('enterprises', EnterpriseController::class);
        Route::apiResource('products', ProductController::class);
        Route::apiResource('product_variants', ProductVariantController::class);
        Route::apiResource('warehouses', WarehouseController::class);
        Route::apiResource('stocks', StockController::class);
        Route::apiResource('stock_movements', StockMovementController::class);
        Route::apiResource('orders', OrderController::class);
        Route::apiResource('order_details', OrderDetailController::class);
        Route::apiResource('order_shippings', OrderShippingController::class);
        Route::apiResource('order_payments', OrderPaymentController::class);
    });
});
