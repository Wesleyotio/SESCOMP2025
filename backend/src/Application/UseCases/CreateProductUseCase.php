<?php

namespace App\Application\UseCases;

use App\Application\Dtos\ProductDto;
use App\Application\Exceptions\ProductException;
use App\Domain\Repositories\ProductRepositoryInterface;

class CreateProductUseCase
{
    private ProductRepositoryInterface $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function action(ProductDto $productDto): void
    {

        if ($this->productRepository->findCode($productDto->getCode())) {
            throw new ProductException("Code has already been registered, use another code");
        }

        $this->productRepository->create($productDto);
    }
}
