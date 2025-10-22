<?php

namespace App\Drivers\Routes;

use App\Infrastructure\Web\Controllers\ProductController;
use App\Infrastructure\Web\Controllers\SaleController;
use App\Infrastructure\Web\Controllers\TaxController;
use App\Infrastructure\Web\Controllers\TypeProductController;
use Slim\App;

return static function (App $app) {

    $app->post('/products', [ProductController::class, 'create']);
    $app->get('/product/{id}', [ProductController::class, 'findById']);
    $app->get('/product', [ProductController::class, 'findAll']);
    $app->put('/product/{id}', [ProductController::class, 'updateAll']);
    $app->patch('/product/{id}', [ProductController::class, 'update']);
    $app->delete('/product/{id}', [ProductController::class, 'delete']);

    $app->post('/types', [TypeProductController::class, 'create']);
    $app->get('/type/{id}', [TypeProductController::class, 'findById']);
    $app->get('/type', [TypeProductController::class, 'findAll']);
    $app->patch('/type/{id}', [TypeProductController::class, 'update']);
    $app->delete('/type/{id}', [TypeProductController::class, 'delete']);

    $app->post('/taxes', [TaxController::class, 'create']);
    $app->get('/tax/{id}', [TaxController::class, 'findById']);
    $app->get('/tax', [TaxController::class, 'findAll']);
    $app->put('/tax/{id}', [TaxController::class, 'updateAll']);
    $app->patch('/tax/{id}', [TaxController::class, 'update']);
    $app->delete('/tax/{id}', [TaxController::class, 'delete']);


    $app->post('/sale/order', [SaleController::class, 'order']);
    $app->post('/sale/pay', [SaleController::class, 'checkout']);
    $app->get('/sale', [SaleController::class, 'findAll']);
    $app->get('/sale/{id}', [SaleController::class, 'findById']);
};
