<?php

namespace App\Application\UseCases;

use App\Application\Dtos\TaxDto;
use App\Application\Exceptions\TaxException;
use App\Domain\Repositories\TaxRepositoryInterface;

class CreateTaxUseCase
{
    private TaxRepositoryInterface $taxRepository;

    public function __construct(TaxRepositoryInterface $taxRepository)
    {
        $this->taxRepository = $taxRepository;
    }

    public function action(TaxDto $taxDto): void
    {

        if ($this->taxRepository->validateByTypeProductId($taxDto->getTypeProductId())) {
            throw new TaxException("Tax has already been registered, use another type of product");
        }

        $this->taxRepository->create($taxDto);
    }
}
