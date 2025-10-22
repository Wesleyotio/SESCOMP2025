<?php

namespace App\Infrastructure\Persistence;

interface DatabaseSaleInterface
{
    public function create(string $saleData): int;

    public function createSaleItens(int $id, string $saleItensData): int;

    /**
     * @return array<int,array{
     *      value_sale: string,
     *      value_tax: string,
     *      id: int,
     * }>
     */
    public function selectAll(): array;

    /**
     * @param int $id
     * @return array{
     *      id: int,
     *      value_sale: string,
     *      value_tax: string
     * }|null
     */
    public function selectById(int $id): ?array;
}
