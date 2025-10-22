<?php

namespace App\Application\UseCases;

use App\Application\Dtos\TaxDto;
use App\Domain\Repositories\TaxRepositoryInterface;
use App\Infrastructure\Exceptions\ClientException;

class FindTaxUseCase
{
    private TaxRepositoryInterface $taxRepository;

    public function __construct(TaxRepositoryInterface $taxRepository)
    {
        $this->taxRepository = $taxRepository;
    }

    public function action(int $id): TaxDto
    {

        if ($id <= 0) {
            throw new ClientException("ID is invalid for values less or equals zero");
        }

        $tax = $this->taxRepository->findById($id);

        if (is_null($tax)) {
            throw new ClientException("there is no matching tax for the ID");
        }
        return TaxDto::fromEntity($tax);
    }
}
