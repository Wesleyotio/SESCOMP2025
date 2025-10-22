<?php

namespace App\Application\UseCases;

use App\Domain\Repositories\ProductRepositoryInterface;

class DeleteProductUseCase
{
    private ProductRepositoryInterface $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function action(int $id): ?int
    {
        return $this->productRepository->delete($id);
    }
}
