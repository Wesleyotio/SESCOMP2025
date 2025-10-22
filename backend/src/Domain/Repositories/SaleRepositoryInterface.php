<?php

namespace App\Domain\Repositories;

use App\Application\Dtos\SaleDto;
use App\Domain\Entities\Sale;

interface SaleRepositoryInterface
{
	public function create(SaleDTO $sale): int;

     /**
      * @param int $id
      * @param array<mixed> $saleItensData
      */
	public function createSaleItens(int $id, array $saleItensData): void;
	public function findById(int $id): ?Sale;

	/**
     * @return array<int, array{
     *     value_sale: string,
     *     value_tax: string,
     *     id: int
     * }> $taxes
     *
     *
     */
	public function findAll(): array;
}
