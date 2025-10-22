<?php

declare(strict_types=1);

namespace Tests\Units\Application\UseCases;


use App\Application\UseCases\CreateProductUseCase;
use App\Application\Dtos\ProductDto;
use App\Domain\Repositories\ProductRepositoryInterface;
use PHPUnit\Framework\TestCase;


class CreateProductUseCaseTest extends TestCase
{
    /** @var ProductRepositoryInterface&\PHPUnit\Framework\MockObject\MockObject */
    private $productRepository;

    

    public function test_throws_exception_when_any_parameter_code_is_invalid() 
    {
        $createProductUseCase = new CreateProductUseCase($this->productRepository);
        
        $productDto = new ProductDto(98,1,'ProductRepeat','100.25');    
        
        $this->productRepository
            ->expects($this->once())
            ->method('findCode')
            ->willReturn(true);
        
        $this->productRepository
            ->expects($this->never())
            ->method('create');

        
        $this->expectException(\App\Application\Exceptions\ProductException::class);

        

        $createProductUseCase->action($productDto);

    }

    public function test_when_parameters_is_valid()
    {
        
        $createProductUseCase = new CreateProductUseCase($this->productRepository);

        $productDto = new ProductDto(98,1,'ProductName','100.25');  
        $this->productRepository
                ->expects($this->once())
                ->method('findCode')
                ->willReturn(false);
      
        $this->productRepository
                ->expects($this->once())
                ->method('create')
                ->with($productDto);

        $createProductUseCase->action($productDto);
    }

 
}
