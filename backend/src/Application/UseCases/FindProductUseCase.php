<?php

namespace App\Application\UseCases;

use App\Application\Dtos\ProductDto;
use App\Domain\Repositories\ProductRepositoryInterface;
use App\Infrastructure\Exceptions\ClientException;

class FindProductUseCase
{
    private ProductRepositoryInterface $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function action(int $id): ProductDto
    {

        if ($id <= 0) {
            throw new ClientException("ID is invalid for values less or equals zero");
        }

        $product = $this->productRepository->findById($id);

        if (is_null($product)) {
            throw new ClientException("there is no matching product for the ID");
        }
        return ProductDto::fromEntity($product);
    }
}
