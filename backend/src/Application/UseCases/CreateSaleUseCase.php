<?php

namespace App\Application\UseCases;

use App\Application\Dtos\SaleDto;
use App\Application\Exceptions\SaleException;
use App\Domain\Repositories\SaleRepositoryInterface;

class CreateSaleUseCase
{
    private SaleRepositoryInterface $saleRepository;

    public function __construct(SaleRepositoryInterface $saleRepository)
    {
        $this->saleRepository = $saleRepository;
    }

    /**
     * @param SaleDto $saleDto
     * @param array<mixed> $saleItens
     */
    public function action(SaleDto $saleDto, array $saleItens): void
    {
        $saleId = $this->saleRepository->create($saleDto);

        $this->saleRepository->createSaleItens($saleId, $saleItens);
    }
}
