<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StatusController;
use App\Http\Controllers\Api\UserRoleController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ColorController;
use App\Http\Controllers\Api\DocumentTypeController;
use App\Http\Controllers\Api\EnterpriseController;
use App\Http\Controllers\Api\EnterpriseTypeController;
use App\Http\Controllers\Api\GenderController;
use App\Http\Controllers\Api\MovementTypeController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\OrderDetailController;
use App\Http\Controllers\Api\OrderShippingController;
use App\Http\Controllers\Api\OrderStatusController;
use App\Http\Controllers\Api\PaymentMethodController;
use App\Http\Controllers\Api\PaymentStatusController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductVariantController;
use App\Http\Controllers\Api\ProductVariantImageController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\ShippingStatusController;
use App\Http\Controllers\Api\SizeController;
use App\Http\Controllers\Api\StockController;
use App\Http\Controllers\Api\StockMovementController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WarehouseController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::apiResource('roles', RoleController::class);
Route::apiResource('document_types', DocumentTypeController::class);
Route::get('getIdByEmail',[AuthController::class,'getIdByEmail']);
Route::apiResource('user_roles', UserRoleController::class);
Route::apiResource('statuses', StatusController::class);





// Private Routes
Route::middleware(['auth:api'])->group(function () {
    Route::apiResource('genders', GenderController::class);
    Route::apiResource('brands', BrandController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('sizes', SizeController::class);
    Route::apiResource('enterprises', EnterpriseController::class);
    Route::apiResource('enterprise_types', EnterpriseTypeController::class);
    Route::apiResource('colors', ColorController::class);
    Route::apiResource('warehouse', WarehouseController::class);
    Route::apiResource('product_variant_images', ProductVariantImageController::class);
    Route::apiResource('movement_type', MovementTypeController::class);
    Route::apiResource('stock', StockController::class);
    Route::apiResource('stock_movements', StockMovementController::class);
    Route::apiResource('product_variants', ProductVariantController::class);
    Route::apiResource('order_details', OrderDetailController::class);
    Route::apiResource('shipping_statuses', ShippingStatusController::class);
    Route::apiResource('order_shippings', OrderShippingController::class);
    Route::apiResource('payment_statuses', PaymentStatusController::class);
    Route::apiResource('orders', OrderController::class);
    Route::apiResource('order_statuses', OrderStatusController::class);
    Route::apiResource('payment_methods', PaymentMethodController::class);

    Route::controller(AuthController::class)->group(function () {
        Route::post('logout', 'logout');
        Route::get('me', 'getUser');
    });

    Route::get('products', [ProductController::class, 'getProducts']);

    // Usuarios autenticados pueden editar su propio perfil
    Route::put('users/{id}', [UserController::class, 'update']);

    // Solo administradores
    Route::middleware(['is.admin'])->group(function () {

        // Rutas de gestión de usuarios (solo admins)
        Route::get('users', [UserController::class, 'index']);
        Route::delete('users/{id}', [UserController::class, 'destroy']);

        Route::controller(ProductController::class)->group(function () {
            Route::post('products', 'addProduct');
            Route::get('/products/{id}', 'getProductById');
            Route::patch('/products/{id}', 'updateProductById');
            Route::delete('/products/{id}', 'deleteProductById');
        });
    });
});
