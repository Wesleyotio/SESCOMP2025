<?php

namespace App\Infrastructure\Persistence;

interface DatabaseTaxInterface
{
    public function create(string $taxData): void;

    /**
     * @return array<int,array{
     *      type_product_id: int,
     *      value: string,
     *      id: int,
     * }>
     */
    public function selectAll(): array;

    /**
     * @param int $typeProductId
     * @return bool
     */
    public function selectByTypeProductId(int $typeProductId): bool;


    /**
     * @param int $typeProductId
     * @return array{
     *      id: int,
     *      type_product_id: int,
     *      value: string
     * }|null
     */
    public function findByTypeProductId(int $typeProductId): ?array;

    /**
     * @param int $id
     * @return array{
     *      id: int,
     *      type_product_id: int,
     *      value: string
     * }|null
     */
    public function selectById(int $id): ?array;

    /**
     * @param int $id
     * @param array<mixed> $taxAttribute
     * @return int|null
     */
    public function update(int $id, array $taxAttribute): ?int;

    /**
     * @param int $id
     * @return int|null
     */
    public function delete(int $id): ?int;
}
