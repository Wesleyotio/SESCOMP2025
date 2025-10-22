<?php

namespace App\Application\UseCases;

use App\Application\Dtos\TypeProductDto;
use App\Domain\Repositories\TypeProductRepositoryInterface;
use App\Infrastructure\Exceptions\ClientException;

class FindTypeProductUseCase
{
    private TypeProductRepositoryInterface $typeProductRepository;

    public function __construct(TypeProductRepositoryInterface $typeProductRepository)
    {
        $this->typeProductRepository = $typeProductRepository;
    }

    public function action(int $id): TypeProductDto
    {
        if ($id <= 0) {
            throw new ClientException("ID is invalid for values less or equals zero");
        }

        $typeProduct = $this->typeProductRepository->findById($id);

        if (is_null($typeProduct)) {
            throw new ClientException("there is no matching product for the ID");
        }
        return TypeProductDto::fromEntity($typeProduct);
    }
}
