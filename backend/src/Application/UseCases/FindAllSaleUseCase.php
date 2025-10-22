<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Application\Dtos\SaleDto;
use App\Domain\Repositories\SaleRepositoryInterface;

class FindAllSaleUseCase
{
    private SaleRepositoryInterface $saleRepository;

    public function __construct(SaleRepositoryInterface $saleRepository)
    {
        $this->saleRepository = $saleRepository;
    }

    /**
     * @return array<mixed>
     *
    */
    public function action(): array
    {
        $sales = $this->saleRepository->findAll();

        return $this->transformSales($sales);
    }

    /**
     * @param array<int, array{
     *     value_sale: string,
     *     value_tax: string,
     *     id: int,
     * }> $sales
     *
     * @return array<mixed>
     */
    private function transformSales(array $sales): array
    {

        return array_map(function (array $saleData) {
            $sale = new SaleDto(
                $saleData['value_sale'],
                $saleData['value_tax'],
                $saleData['id']
            );
            return $sale->toArray();
        }, $sales);
    }
}
