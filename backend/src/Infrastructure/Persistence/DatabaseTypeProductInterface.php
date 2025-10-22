<?php

namespace App\Infrastructure\Persistence;

interface DatabaseTypeProductInterface
{
    public function create(string $name): void;

    /**
     * @return array<int,array{
     *      id: int,
     *      name: string
     * }>
     */
    public function selectAll(): array;

    /**
     * @param string $typeProductName
     * @return bool
     */
    public function selectByTypeProductName(string $typeProductName): bool;

    /**
     * @param int $id
     * @return array{
     *      id: int,
     *      name: string
     * }|null
     */
    public function selectById(int $id): ?array;

    /**
     * @param int $id
     * @param string $typeProductName
     * @return int|null
     */
    public function update(int $id, string $typeProductName): ?int;

    /**
     * @param int $id
     * @return int|null
     */
    public function delete(int $id): ?int;
}
