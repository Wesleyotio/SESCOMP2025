<?php

namespace App\Domain\Repositories;

use App\Application\Dtos\TaxDto;
use App\Domain\Entities\Tax;

interface TaxRepositoryInterface
{
	public function create(TaxDto $tax): void;
	public function findById(int $id): ?Tax;
	public function validateByTypeProductId(int $typeProductId): bool;
	public function findByTypeProductId(int $typeProductId): ?Tax;

	/**
     * @return array<int, array{
     *     type_product_id: int,
     *     value: string,
     *     id: int
     * }> $taxes
     *
     *
     */
	public function findAll(): array;

	/**
     *
     * @param int $id
     * @param array<string|int,mixed> $taxAttribute
     */
	public function update(int $id, array $taxAttribute): ?int;
	public function delete(int $id): ?int;
}
