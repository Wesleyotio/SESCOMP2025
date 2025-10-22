<?php

declare(strict_types=1);

namespace Tests\Units\Application\UseCases;

use App\Application\Exceptions\ProductException;
use App\Application\UseCases\UpdateProductUseCase;
use App\Domain\Repositories\ProductRepositoryInterface;
use PHPUnit\Framework\TestCase;

class UpdateProductUseCaseTest extends TestCase
{
    /** @var ProductRepositoryInterface&\PHPUnit\Framework\MockObject\MockObject */
    private $productRepository;

    public function setUp(): void
    {
        $this->productRepository = $this->createMock(ProductRepositoryInterface::class);
        
    }



    public function test_when_parameters_for_update_is_valid()
    {
        
        $updateProductUseCase = new UpdateProductUseCase($this->productRepository);
        $productData = [    
            'code' => 98, 
            'type_product_id' => 1, 
            'name' => 'ProductName' , 
            'value' => 101.25 
        ];

        $this->productRepository
                ->expects($this->once())
                ->method('update')
                ->willReturn(1);
        
        $result = $updateProductUseCase->action(5,$productData);

        $this->assertEquals(1, $result);
    }
    public function test_when_parameters_for_update_is_invalid_id()
    {
        
        $updateProductUseCase = new UpdateProductUseCase($this->productRepository);
        $productData = [    
            'code' => 60, 
            'type_product_id' => 1, 
            'name' => 'ProductInvalid' , 
            'value' => 101.25 
        ];

        $this->productRepository
                ->expects($this->once())
                ->method('update')
                ->willReturn(null);
        
        $result = $updateProductUseCase->action(99999,$productData);

        $this->assertEquals(null, $result);
    }

  
}
