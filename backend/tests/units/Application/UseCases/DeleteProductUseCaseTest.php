<?php

declare(strict_types=1);

namespace Tests\Units\Application\UseCases;

use App\Application\UseCases\DeleteProductUseCase;
use App\Domain\Repositories\ProductRepositoryInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class DeleteProductUseCaseTest extends TestCase
{
    
    #[DataProvider('valueProvider')]
    public function test_delete_product_for_id($id, $expect) 
    {
        /** @var ProductRepositoryInterface&\PHPUnit\Framework\MockObject\MockObject */
        $productRepository = $this->createMock(ProductRepositoryInterface::class);
        
        $createProductUseCase = new DeleteProductUseCase($productRepository);

        $productRepository
                ->expects($this->once())
                ->method('delete')
                ->willReturnCallback(function($idValue)  use ($id, $expect){
                    if($idValue == $id) {
                        return $expect;
                    }
                });
        
        $result = $createProductUseCase->action($id);

        $this->assertEquals($expect, $result);

    }

    public static function valueProvider()
    {
        return [
            'when_id_does_not_exist' => [ 'id' => -5, 'expect' => null],
            'when_id_is_valid' => [ 'id' => 10, 'expect' => 1 ]
            
        ];
    }
}
