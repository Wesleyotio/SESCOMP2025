<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Application\Dtos\TypeProductDto;
use App\Domain\Repositories\TypeProductRepositoryInterface;

class FindAllTypeProductUseCase
{
    private TypeProductRepositoryInterface $typeProductRepository;

    public function __construct(TypeProductRepositoryInterface $typeProductRepository)
    {
        $this->typeProductRepository = $typeProductRepository;
    }

    /**
     * @return array<mixed>
     *
    */
    public function action(): array
    {
        $typeProducts = $this->typeProductRepository->findAll();

        return $this->transformTypeProducts($typeProducts);
    }

    /**
     * @param array<int, array{
     *     name: string,
     *     id: int,
     * }> $typeProducts
     *
     * @return array<mixed>
     */
    private function transformTypeProducts(array $typeProducts): array
    {
        return array_map(function (array $typeProductData) {
            $typeProduct = new TypeProductDto(
                $typeProductData['name'],
                $typeProductData['id']
            );
            return $typeProduct->toArray();
        }, $typeProducts);
    }
}
