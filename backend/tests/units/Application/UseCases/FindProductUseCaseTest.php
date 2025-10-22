<?php

declare(strict_types=1);

namespace Tests\Units\Application\UseCases;

use App\Application\UseCases\FindProductUseCase;
use App\Application\Dtos\ProductDto;
use App\Domain\Entities\Product;
use App\Domain\Repositories\ProductRepositoryInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class FindProductUseCaseTest extends TestCase
{
    
  

    #[DataProvider('valueProvider')]
    public function test_invalid_id_for_product($id, $expect) 
    {
         /** @var ProductRepositoryInterface&\PHPUnit\Framework\MockObject\MockObject */
        $productRepository = $this->createMock(ProductRepositoryInterface::class);
        
        $findProductUseCase = new FindProductUseCase($productRepository);

        $this->expectException($expect);

        $productRepository
            ->expects($this->never())
            ->method('findById');
        
        $findProductUseCase->action($id);
        

    }

    public function test_fail_find_product_for_id_non_existent() 
    {
         /** @var ProductRepositoryInterface&\PHPUnit\Framework\MockObject\MockObject */
        $productRepository = $this->createMock(ProductRepositoryInterface::class);
        
        $findProductUseCase = new FindProductUseCase($productRepository);


        $productRepository
            ->expects($this->once())
            ->method('findById')
            ->willReturn(null);
        
        $this->expectException(\App\Infrastructure\Exceptions\ClientException::class);

        $findProductUseCase->action(999999999999999);
        

    }

    public function test_find_product_for_id() 
    {
        /** @var ProductRepositoryInterface&\PHPUnit\Framework\MockObject\MockObject */
        $productRepository = $this->createMock(ProductRepositoryInterface::class);
        
        $findProductUseCase = new FindProductUseCase($productRepository);

        $productRepository
                ->expects($this->once())
                ->method('findById')
                ->willReturn(new Product(
                    1,
                    99,
                    1,
                    'MyproductName',
                    '99.95',
                    \DateTimeImmutable::createFromFormat('Y-m-d H:i:s','2024-12-10 00:11:58'),
                    \DateTimeImmutable::createFromFormat('Y-m-d H:i:s','2024-12-10 00:11:58')

                ));
        
        $productDto = $findProductUseCase->action(1);


        $this->assertInstanceOf(ProductDto::class, $productDto);

        
    }

    public static function valueProvider()
    {
        return [
            'when_id_is_zero' => [ 'id' => 0, 'expect' => \App\Infrastructure\Exceptions\ClientException::class],
            'when_id_is_less_zero' => [ 'id' => -25, 'expect' => \App\Infrastructure\Exceptions\ClientException::class]
            
        ];
    }
}
