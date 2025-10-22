<?php

namespace App\Domain\Repositories;

use App\Application\Dtos\TypeProductDto;
use App\Domain\Entities\TypeProduct;

interface TypeProductRepositoryInterface
{
	public function create(TypeProductDto $typeProduct): void;
	public function findByTypeProductName(string $typeProductName): bool;

	/**
     * @return array<int, array{
     *     name: string,
     *     id: int
     * }> $typeProducts
	 *
     */
	public function findAll(): array;
	public function findById(int $id): ?TypeProduct;

	/**
     *
     * @param int $id
     * @param string $typeProductName
     */
	public function update(int $id, string $typeProductName): ?int;
	public function delete(int $id): ?int;
}
