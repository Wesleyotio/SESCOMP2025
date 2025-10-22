<?php

namespace App\Infrastructure\Persistence;

interface DatabaseProductInterface
{
    public function create(string $productData): void;

    /**
     * @return array<int,array{
     *      id: int,
     *      code: int,
     *      type_product_id: int,
     *      name: string,
     *      value: string,
     *      created_at: string,
     *      updated_at: string
     * }>
     */
    public function selectAll(): array;

    /**
     * @param int $code
     * @return bool
     */
    public function selectByCode(int $code): bool;

    /**
     * @param int $id
     * @return array{
     *      id: int,
     *      code: int,
     *      type_product_id: int,
     *      name: string,
     *      value: string,
     *      created_at: string,
     *      updated_at: string
     * }|null
     */
    public function selectById(int $id): ?array;

    /**
     * @param int $id
     * @param array<mixed> $productAttribute
     * @return int|null
     */
    public function update(int $id, array $productAttribute): ?int;

    /**
     * @param int $id
     * @return int|null
     */
    public function delete(int $id): ?int;
}
