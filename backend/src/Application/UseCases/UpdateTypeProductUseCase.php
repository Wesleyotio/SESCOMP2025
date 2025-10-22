<?php

namespace App\Application\UseCases;

use App\Domain\Repositories\TypeProductRepositoryInterface;

class UpdateTypeProductUseCase
{
    private TypeProductRepositoryInterface $typeProductRepository;

    public function __construct(TypeProductRepositoryInterface $typeProductRepository)
    {
        $this->typeProductRepository = $typeProductRepository;
    }

    /**
     * @param int $id
     * @param string $typeProductName
     *
    */
    public function action(int $id, string $typeProductName): ?int
    {
        return $this->typeProductRepository->update($id, $typeProductName);
    }
}
