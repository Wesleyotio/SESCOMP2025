<?php

namespace App\Application\UseCases;

use App\Domain\Repositories\TypeProductRepositoryInterface;

class DeleteTypeProductUseCase
{
    private TypeProductRepositoryInterface $typeProductRepository;

    public function __construct(TypeProductRepositoryInterface $typeProductRepository)
    {
        $this->typeProductRepository = $typeProductRepository;
    }

    public function action(int $id): ?int
    {
        return $this->typeProductRepository->delete($id);
    }
}
