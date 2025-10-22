<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Application\Dtos\ProductDto;
use App\Domain\Entities\Product;
use App\Domain\Repositories\ProductRepositoryInterface;
use DateTimeImmutable;

class FindAllProductUseCase
{
    private ProductRepositoryInterface $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @return array<mixed>
     *
    */
    public function action(): array
    {

        $products = $this->productRepository->findAll();

        return $this->transformProducts($products);
    }

    /**
     * @param array<int, array{
     *     code: int,
     *     type_product_id: int,
     *     name: string,
     *     value: string,
     *     id: int,
     *     created_at: string,
     *     updated_at: string
     * }> $products
     *
     * @return array<mixed>
     */
    private function transformProducts(array $products): array
    {

        return array_map(function (array $productData) {
            $product = new ProductDto(
                $productData['code'],
                $productData['type_product_id'],
                $productData['name'],
                $productData['value'],
                $productData['id'],
                formatDate($productData['created_at']),
                formatDate($productData['updated_at'])
            );
            return $product->toArray();
        }, $products);
    }
}
