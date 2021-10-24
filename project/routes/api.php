<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('login', 'Api\UserController@login')->name('api.user.login');

Route::group(['middleware' => ['jwt.verify', 'cors']], function() {
    Route::get('user', 'Api\UserController@user');
    Route::post('user', 'Api\UserController@saveuser');
    Route::get('category', 'Api\CategoryController@index');
    Route::get('category/feature', 'Api\CategoryController@feature');
    Route::get('category/{cid}', 'Api\CategoryController@Subcategory');
    Route::get('subcategory/{sid}', 'Api\CategoryController@Childcategory');
    Route::get('product', 'Api\ProductController@index');
    Route::get('product/{id}', 'Api\ProductController@product');
    Route::get('products', 'Api\ProductController@products');
    Route::get('home', 'Api\ProductController@index');
    
    Route::get('order', 'Api\OrderController@index');
    Route::get('order/{id}', 'Api\OrderController@order');
});
/*
Route::group(['middleware' => ['jwt.verify']], function() {
    /*Route::get('logout', [ApiController::class, 'logout']);
    Route::get('get_user', [ApiController::class, 'get_user']);
    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/{id}', [ProductController::class, 'show']);
    Route::post('create', [ProductController::class, 'store']);
    Route::put('update/{product}',  [ProductController::class, 'update']);
    Route::delete('delete/{product}',  [ProductController::class, 'destroy']);
});
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/
