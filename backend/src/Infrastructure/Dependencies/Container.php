<?php

use App\Application\UseCases\CalculateSaleUseCase;
use App\Infrastructure\Persistence\DatabaseProductInterface;
use App\Drivers\Persistence\DatabaseProduct;
use App\Domain\Repositories\ProductRepositoryInterface;
use App\Infrastructure\Repositories\ProductRepository;
use App\Application\UseCases\CreateProductUseCase;
use App\Application\UseCases\CreateSaleUseCase;
use App\Application\UseCases\CreateTaxUseCase;
use App\Application\UseCases\CreateTypeProductUseCase;
use App\Application\UseCases\DeleteProductUseCase;
use App\Application\UseCases\DeleteTaxUseCase;
use App\Application\UseCases\DeleteTypeProductUseCase;
use App\Application\UseCases\FindAllProductUseCase;
use App\Application\UseCases\FindAllSaleUseCase;
use App\Application\UseCases\FindAllTaxUseCase;
use App\Application\UseCases\FindAllTypeProductUseCase;
use App\Application\UseCases\FindProductUseCase;
use App\Application\UseCases\FindSaleUseCase;
use App\Application\UseCases\FindTaxUseCase;
use App\Application\UseCases\FindTypeProductUseCase;
use App\Application\UseCases\UpdateProductUseCase;
use App\Application\UseCases\UpdateTaxUseCase;
use App\Application\UseCases\UpdateTypeProductUseCase;
use App\Domain\Entities\Product;
use App\Domain\Repositories\SaleRepositoryInterface;
use App\Domain\Repositories\TaxRepositoryInterface;
use App\Domain\Repositories\TypeProductRepositoryInterface;
use App\Drivers\Persistence\DatabaseSale;
use App\Drivers\Persistence\DatabaseTax;
use App\Drivers\Persistence\DatabaseTypeProduct;
use App\Infrastructure\Persistence\DatabaseSaleInterface;
use App\Infrastructure\Persistence\DatabaseTaxInterface;
use App\Infrastructure\Persistence\DatabaseTypeProductInterface;
use App\Infrastructure\Repositories\SaleRepository;
use App\Infrastructure\Repositories\TaxRepository;
use App\Infrastructure\Repositories\TypeProductRepository;
use App\Infrastructure\Web\Controllers\ProductController;
use App\Infrastructure\Web\Controllers\SaleController;
use App\Infrastructure\Web\Controllers\TaxController;
use App\Infrastructure\Web\Controllers\TypeProductController;
use Doctrine\Migrations\Configuration\EntityManager\EntityManagerLoader;
use Psr\Container\ContainerInterface;

return [

    EntityManagerLoader::class => function (ContainerInterface $c) {
        $doctrineConfig = require __DIR__ . '/../../../config/doctrine.php';
        return $doctrineConfig();
    },

    DatabaseProductInterface::class => \DI\create(DatabaseProduct::class),
    ProductRepositoryInterface::class => \DI\create(ProductRepository::class)
        ->constructor(\DI\get(DatabaseProductInterface::class)),
    CreateProductUseCase::class => \DI\create()
        ->constructor(\DI\get(ProductRepositoryInterface::class)),
    FindProductUseCase::class => \DI\create()
        ->constructor(\DI\get(ProductRepositoryInterface::class)),
    FindAllProductUseCase::class => \DI\create()
        ->constructor(\DI\get(ProductRepositoryInterface::class)),
    UpdateProductUseCase::class => \DI\create()
        ->constructor(\DI\get(ProductRepositoryInterface::class)),
    DeleteProductUseCase::class => \DI\create()
        ->constructor(\DI\get(ProductRepositoryInterface::class)),
    ProductController::class => \DI\create()
        ->constructor(
            \DI\get(CreateProductUseCase::class),
            \DI\get(FindProductUseCase::class),
            \DI\get(FindAllProductUseCase::class),
            \DI\get(UpdateProductUseCase::class),
            \DI\get(DeleteProductUseCase::class),
        ),
        
    DatabaseTypeProductInterface::class => \DI\create(DatabaseTypeProduct::class),
    TypeProductRepositoryInterface::class => \DI\create(TypeProductRepository::class)
        ->constructor(\DI\get(DatabaseTypeProductInterface::class)),
    CreateTypeProductUseCase::class => \DI\create()
        ->constructor(\DI\get(TypeProductRepositoryInterface::class)),
    FindTypeProductUseCase::class => \DI\create()
        ->constructor(\DI\get(TypeProductRepositoryInterface::class)),
    FindAllTypeProductUseCase::class => \DI\create()
        ->constructor(\DI\get(TypeProductRepositoryInterface::class)),
    UpdateTypeProductUseCase::class => \DI\create()
        ->constructor(\DI\get(TypeProductRepositoryInterface::class)),
    DeleteTypeProductUseCase::class => \DI\create()
        ->constructor(\DI\get(TypeProductRepositoryInterface::class)),
    TypeProductController::class => \DI\create()
        ->constructor(
            \DI\get(CreateTypeProductUseCase::class),
            \DI\get(FindTypeProductUseCase::class),
            \DI\get(FindAllTypeProductUseCase::class),
            \DI\get(UpdateTypeProductUseCase::class),
            \DI\get(DeleteTypeProductUseCase::class),
        ),

    DatabaseTaxInterface::class => \DI\create(DatabaseTax::class),
    TaxRepositoryInterface::class => \DI\create(TaxRepository::class)
        ->constructor(\DI\get(DatabaseTaxInterface::class)),
    CreateTaxUseCase::class => \DI\create()
        ->constructor(\DI\get(TaxRepositoryInterface::class)),
    FindTaxUseCase::class => \DI\create()
        ->constructor(\DI\get(TaxRepositoryInterface::class)),
    FindAllTaxUseCase::class => \DI\create()
        ->constructor(\DI\get(TaxRepositoryInterface::class)),
    UpdateTaxUseCase::class => \DI\create()
        ->constructor(\DI\get(TaxRepositoryInterface::class)),
    DeleteTaxUseCase::class => \DI\create()
        ->constructor(\DI\get(TaxRepositoryInterface::class)),
    TaxController::class => \DI\create()
        ->constructor(
            \DI\get(CreateTaxUseCase::class),
            \DI\get(FindTaxUseCase::class),
            \DI\get(FindAllTaxUseCase::class),
            \DI\get(UpdateTaxUseCase::class),
            \DI\get(DeleteTaxUseCase::class),
        ),

    DatabaseSaleInterface::class => \DI\create(DatabaseSale::class),
    SaleRepositoryInterface::class => \DI\create(SaleRepository::class)
        ->constructor(\DI\get(DatabaseSaleInterface::class)),
    CreateSaleUseCase::class => \DI\create()
        ->constructor(\DI\get(SaleRepositoryInterface::class)),
    CalculateSaleUseCase::class => \DI\create()
        ->constructor(
            \DI\get(ProductRepositoryInterface::class),
            \DI\get(TaxRepositoryInterface::class)
        ),
    FindAllSaleUseCase::class => \DI\create()
        ->constructor(\DI\get(SaleRepositoryInterface::class)),
    FindSaleUseCase::class => \DI\create()
        ->constructor(\DI\get(SaleRepositoryInterface::class)),
    SaleController::class => \DI\create()
        ->constructor(
            \DI\get(CreateSaleUseCase::class),
            \DI\get(CalculateSaleUseCase::class),
            \DI\get(FindAllSaleUseCase::class),
            \DI\get(FindSaleUseCase::class)
        ),
];
