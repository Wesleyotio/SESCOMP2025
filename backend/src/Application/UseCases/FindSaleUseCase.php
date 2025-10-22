<?php

namespace App\Application\UseCases;

use App\Application\Dtos\SaleDto;
use App\Domain\Repositories\SaleRepositoryInterface;
use App\Infrastructure\Exceptions\ClientException;

class FindSaleUseCase
{
    private SaleRepositoryInterface $saleRepository;

    public function __construct(SaleRepositoryInterface $saleRepository)
    {
        $this->saleRepository = $saleRepository;
    }

    public function action(int $id): SaleDto
    {

        if ($id <= 0) {
            throw new ClientException("ID is invalid for values less or equals zero");
        }

        $sale = $this->saleRepository->findById($id);

        if (is_null($sale)) {
            throw new ClientException("there is no matching Sale for the ID");
        }
        return SaleDto::fromEntity($sale);
    }
}
