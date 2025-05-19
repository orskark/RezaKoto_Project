<?php

use App\Http\Controllers\Api\StatusController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\EnterpriseController;
use App\Http\Controllers\EnterpriseTypeController;
use App\Http\Controllers\GenderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductVariantImageController;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsUserAuth;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\WarehouseController;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Private Routes
Route::middleware([IsUserAuth::class])->group(function () {
    Route::apiResource('genders', GenderController::class);
    Route::apiResource('statuses', StatusController::class);
    Route::apiResource('brands', BrandController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('sizes', SizeController::class);
    Route::apiResource('enterprises', EnterpriseController::class);
    Route::apiResource('enterprise_types', EnterpriseTypeController::class);
    Route::apiResource('colors', ColorController::class);
    Route::apiResource('warehouse', WarehouseController::class);
    Route::apiResource('product_variant_images', ProductVariantImageController::class);
    Route::controller(AuthController::class)->group(function () {
        Route::post('logout', 'logout');
        Route::get('me', 'getUser');
    });

    Route::get('products', [ProductController::class, 'getProducts']);

    // Usuarios autenticados pueden editar su propio perfil
    Route::put('users/{id}', [UserController::class, 'update']);

    // Solo administradores
    Route::middleware([IsAdmin::class])->group(function () {

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
