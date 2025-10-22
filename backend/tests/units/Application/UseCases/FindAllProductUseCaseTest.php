<?php

declare(strict_types=1);

namespace Tests\Units\Application\UseCases;


use App\Application\UseCases\FindAllProductUseCase;
use App\Domain\Repositories\ProductRepositoryInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class FindAllProductUseCaseTest extends TestCase
{
    
    

    #[DataProvider('valueProvider')]
    public function test_when_an_array_of_products_is_returned($expect) 
    {
        /** @var ProductRepositoryInterface&\PHPUnit\Framework\MockObject\MockObject */
        $productRepository = $this->createMock(ProductRepositoryInterface::class);
        $createProductUseCase = new FindAllProductUseCase( $productRepository);
        
        $productRepository
                ->expects($this->once())
                ->method('findAll')
                ->willReturnCallback(function() use ($expect){
                    return $expect;
                });
        

        $products = $createProductUseCase->action();

        $this->assertIsArray($products);

        foreach($products as $product) {

            $this->assertArrayHasKey('id', $product);
            $this->assertArrayHasKey('code', $product);
            $this->assertArrayHasKey('type_product_id', $product);
            $this->assertArrayHasKey('name', $product);
            $this->assertArrayHasKey('value', $product);
            $this->assertArrayHasKey('created_at', $product);
            $this->assertArrayHasKey('updated_at', $product);
        }

    }

    public static function valueProvider()
    {
        return [
            'when_array_products_is_valid' => [ 
                'expect' => [
                    [
                        'id'                => 1,
                        'code'              => 99,
                        'type_product_id'   => 1,
                        'name'              => 'Product1',
                        'value'             => '99.98',
                        'created_at'        => "2024-12-10 00:11:58",
                        'updated_at'        => "2024-12-10 00:11:58"
                    ],
                    [
                        'id'                => 2,
                        'code'              => 78,
                        'type_product_id'   => 1,
                        'name'              => 'Product2',
                        'value'             => '97.78',
                        'created_at'        => "2024-07-22 19:23:55",
                        'updated_at'        => "2025-01-07 20:24:34",
                    ],
                    
                ]
            ]
            
        ];
    }
}
