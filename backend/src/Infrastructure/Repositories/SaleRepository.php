<?php

namespace App\Infrastructure\Repositories;

use App\Application\Dtos\SaleDto;
use App\Domain\Entities\Sale;
use App\Domain\Repositories\SaleRepositoryInterface;
use App\Infrastructure\Persistence\DatabaseSaleInterface;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

class SaleRepository implements SaleRepositoryInterface
{
    private DatabaseSaleInterface $databaseSaleInterface;

    public function __construct(DatabaseSaleInterface $databaseSaleInterface)
    {
        $this->databaseSaleInterface = $databaseSaleInterface;
    }

    public function create(SaleDto $sale): int
    {
        $arraySale = [
            'value_sale' => $sale->getValueSale(),
            'value_tax' => $sale->getValueTax(),
        ];

        try {
            $jsonSale = json_encode($arraySale, JSON_THROW_ON_ERROR);
        } catch (\JsonException $th) {
            throw new RuntimeException('Failure to convert to json object', Response::HTTP_INTERNAL_SERVER_ERROR, $th);
        }

        return $this->databaseSaleInterface->create($jsonSale);
    }

    public function createSaleItens(int $id, array $saleItensData): void
    {
        try {
            $jsonSaleItens = json_encode($saleItensData, JSON_THROW_ON_ERROR);
        } catch (\JsonException $th) {
            throw new RuntimeException('Failure to convert to json object', Response::HTTP_INTERNAL_SERVER_ERROR, $th);
        }
        $this->databaseSaleInterface->createSaleItens($id, $jsonSaleItens);
    }

    public function findById(int $id): ?Sale
    {
        $saleData = $this->databaseSaleInterface->selectById($id);

        if (is_null($saleData)) {
            return null;
        }

        return new Sale(
            $saleData['id'],
            $saleData['value_sale'],
            $saleData['value_tax']
        );
    }

    public function findAll(): array
    {
        return $this->databaseSaleInterface->selectAll();
    }
}
