<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Application\Dtos\TaxDto;
use App\Domain\Repositories\TaxRepositoryInterface;

class FindAllTaxUseCase
{
    private TaxRepositoryInterface $taxRepository;

    public function __construct(TaxRepositoryInterface $taxRepository)
    {
        $this->taxRepository = $taxRepository;
    }

    /**
     * @return array<mixed>
     *
    */
    public function action(): array
    {
        $taxes = $this->taxRepository->findAll();

        return $this->transformTaxes($taxes);
    }

    /**
     * @param array<int, array{
     *     type_product_id: int,
     *     value: string,
     *     id: int,
     * }> $taxes
     *
     * @return array<mixed>
     */
    private function transformTaxes(array $taxes): array
    {

        return array_map(function (array $taxData) {
            $tax = new TaxDto(
                $taxData['type_product_id'],
                $taxData['value'],
                $taxData['id']
            );
            return $tax->toArray();
        }, $taxes);
    }
}
