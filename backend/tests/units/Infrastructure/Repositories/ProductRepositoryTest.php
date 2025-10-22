<?php

declare(strict_types =1);

namespace Tests\Units\Infrastructure\Repositories;

use App\Application\Dtos\ProductDto;
use App\Domain\Entities\Product;
use App\Infrastructure\Persistence\DatabaseProductInterface;
use App\Infrastructure\Repositories\ProductRepository;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ProductRepositoryTest extends TestCase
{
    /** @var DatabaseProductInterface&\PHPUnit\Framework\MockObject\MockObject */
    private $databaseProduct;

    public function setUp(): void
    {
        $this->databaseProduct = $this->createMock(DatabaseProductInterface::class);
    }

    public function test_save_product_with_success_in_database()
    {
        $productRepository = new ProductRepository($this->databaseProduct);

        $productDto = new ProductDto(1212, 1, 'ProductInDB', '0.50' );
        
        $this->databaseProduct
            ->expects($this->once())
            ->method('create');

        $productRepository->create($productDto);
        
    }

    public function test_product_found_by_code()
    {
        $productRepository = new ProductRepository($this->databaseProduct);
        
        $this->databaseProduct
        ->expects($this->once())
        ->method('selectByCode')
        ->willReturn(true);

        $result = $productRepository->findCode(25);

        $this->assertEquals(true, $result);

    }

    #[DataProvider('findProductProviderById')]
    public function test_search_product_by_id($id, $productAttributes)
    {
        $productRepository = new ProductRepository($this->databaseProduct);
        
        $this->databaseProduct
        ->expects($this->once())
        ->method('selectById')
        ->willReturn($productAttributes);

        $result = $productRepository->findById($id);

        $this->assertTrue(
            is_null($result) || $result instanceof Product,
            "variável deve ser nula ou um objeto Product"
        );

    }

    public function test_get_all_products()
    {
        $productRepository = new ProductRepository($this->databaseProduct);
        
        $arrayObjects = [
            [
                'id' => 1,
                'code' => 3243,
                'type_product_id' => 2,
                'name' => 'foundProduct',
                'value' => '22.34',
                'created_at' => '2025-10-22 19:00:00', 
                'updated_at' =>'2025-10-22 19:00:00' 
            ],
            [
                'id' => 2,
                'code' => 3244,
                'type_product_id' => 3,
                'name' => 'foundProduct2',
                'value' => '21.34',
                'created_at' => '2022-10-22 19:00:00', 
                'updated_at' =>'2026-10-22 19:00:00' 
            ],
        ];

        $this->databaseProduct
        ->expects($this->once())
        ->method('selectAll')
        ->willReturn($arrayObjects);


        $this->assertIsArray($result);
    }

    #[DataProvider('deleteProductProviderById')]
    public function test_delete_product_by_id($id, $value)
    {
        $productRepository = new ProductRepository($this->databaseProduct);
        
        $this->databaseProduct
        ->expects($this->once())
        ->method('delete')
        ->willReturn($value);

        $result = $productRepository->delete($id);

        $this->assertTrue(
            is_null($result) || is_int($result),
            "variável deve ser nula ou um inteiro"
        );
    }

    public static function deleteProductProviderById(): array
    {
        return [
            'when_id_is_invalid' => ['id' => 25, 'value' => null ],
            'when_id_is_valid' => ['id' => 1, 'value' => 1],
        ];
    } 
   
    public static function findProductProviderById(): array
    {
        $arrayAttributes = [
            'id' => 1,
            'code' => 3243,
            'type_product_id' => 2,
            'name' => 'foundProduct',
            'value' => '22.34',
            'created_at' => '2025-10-22 19:00:00', 
            'updated_at' =>'2025-10-22 19:00:00' 

        ];
        return [
            'when_data_is_invalid' => ['id' => 25, 'productAttributes' => null ],
            'when_data_is_valid' => ['id' => 1, 'productAttributes' => $arrayAttributes],
        ];
    }
}