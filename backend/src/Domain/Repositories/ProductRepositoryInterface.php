<?php

namespace App\Domain\Repositories;

use App\Application\Dtos\ProductDto;
use App\Domain\Entities\Product;

interface ProductRepositoryInterface
{
    public function create(ProductDto $product): void;
    public function findCode(int $code): bool;
    public function findById(int $id): ?Product;

    /**
     * @return array<int, array{
     *     code: int,
     *     type_product_id: int,
     *     name: string,
     *     value: string,
     *     id: int,
     *     created_at: string,
     *     updated_at: string
     * }> $products
     *
     *
     */
    public function findAll(): array;

    /**
     *
     * @param int $id
     * @param array<string|int,mixed> $productAttribute
     */
    public function update(int $id, array $productAttribute): ?int;
    public function delete(int $id): ?int;
}
