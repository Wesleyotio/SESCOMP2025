<?php

namespace App\Application\UseCases;

use App\Application\Dtos\TypeProductDto;
use App\Application\Exceptions\TypeProductException;
use App\Domain\Repositories\TypeProductRepositoryInterface;

class CreateTypeProductUseCase
{
    private TypeProductRepositoryInterface $typeProductRepository;

    public function __construct(TypeProductRepositoryInterface $typeProductRepository)
    {
        $this->typeProductRepository = $typeProductRepository;
    }

    public function action(TypeProductDto $typeProductDto): void
    {

        if ($this->typeProductRepository->findByTypeProductName($typeProductDto->getName())) {
            throw new TypeProductException("Name has already been registered, use another name of type of product");
        }

        $this->typeProductRepository->create($typeProductDto);
    }
}
