<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;


//authentication
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware(['auth:sanctum'])->group(function () {   
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/payment/callback', [CategoryController::class, 'handleCallback'])->name('payment.callback');
});

//frontend
Route::get('category', [CategoryController::class, 'index']);
Route::get('collections/{slug}', [CategoryController::class, 'products']);
Route::get('collection/{category_slug}/{product_slug}', [CategoryController::class, 'viewProduct']);
Route::post('checkout', [CategoryController::class, 'checkout']);

 Route::get('verify-transaction/{reference}', [CategoryController::class, 'verify']);





Route::middleware(['auth:sanctum'])->group(function () {   
    Route::post('add-to-cart', [CategoryController::class, 'cart']);
    Route::get('cart', [CategoryController::class, 'viewCart']);
    Route::put('update-cart/{cart_id}/{scope}', [CategoryController::class, 'updateCart']);
    Route::delete('delete-cart/{id}', [CategoryController::class, 'deleteCart']);
    Route::get('clear-cart', [CategoryController::class, 'clear']);
   
    
    });


//admin
Route::middleware(['auth:sanctum','isAAdmin'])->group(function () {   
    Route::get('/auth', function(){
        return response()->json([
            'status' => 200,
            'message' => 'you are logged in',
            'role' => Auth::user()->role_as =='1'?'admin':'user'
            
        ],200);
    });
    Route::prefix('admin')->group(function(){
        Route::apiResource('add-category', CategoryController::class);
        Route::get('all-category', [CategoryController::class, 'view']);
        Route::apiResource('products', ProductController::class);
        Route::get('orders', [CategoryController::class, 'orders']);
        Route::get('order-details/{id}', [CategoryController::class, 'details']);

    });
   


    });
