<?php

namespace App\Application\UseCases;

use App\Domain\Repositories\TaxRepositoryInterface;
use TypeError;

class UpdateTaxUseCase
{
    private TaxRepositoryInterface $taxRepository;

    public function __construct(TaxRepositoryInterface $taxRepository)
    {
        $this->taxRepository = $taxRepository;
    }

    /**
     * @param int $id
     * @param array<string|int,mixed> $taxData
     *
    */
    public function action(int $id, array $taxData): ?int
    {
        return $this->taxRepository->update($id, $taxData);
    }
}
