<?php

namespace App\Application\UseCases;

use App\Domain\Repositories\TaxRepositoryInterface;

class DeleteTaxUseCase
{
    private TaxRepositoryInterface $taxRepository;

    public function __construct(TaxRepositoryInterface $taxRepository)
    {
        $this->taxRepository = $taxRepository;
    }

    public function action(int $id): ?int
    {
        return $this->taxRepository->delete($id);
    }
}
