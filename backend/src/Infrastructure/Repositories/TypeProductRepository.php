<?php

namespace App\Infrastructure\Repositories;

use App\Application\Dtos\TypeProductDto;
use App\Domain\Entities\TypeProduct;
use App\Domain\Repositories\TypeProductRepositoryInterface;
use App\Infrastructure\Persistence\DatabaseTypeProductInterface;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

class TypeProductRepository implements TypeProductRepositoryInterface
{
    private DatabaseTypeProductInterface $databaseTypeProductInterface;

    public function __construct(DatabaseTypeProductInterface $databaseTypeProductInterface)
    {
        $this->databaseTypeProductInterface = $databaseTypeProductInterface;
    }

    public function create(TypeProductDto $typeProduct): void
    {
        $arrayTypeProduct = [
            'name' => $typeProduct->getName()
        ];

        try {
            $jsonTypeProduct = json_encode($arrayTypeProduct, JSON_THROW_ON_ERROR);
        } catch (\JsonException $th) {
            throw new RuntimeException('Failure to convert to json object', Response::HTTP_INTERNAL_SERVER_ERROR, $th);
        }

        $this->databaseTypeProductInterface->create($jsonTypeProduct);
    }

    public function findById(int $id): ?TypeProduct
    {
        $typeProductData = $this->databaseTypeProductInterface->selectById($id);

        if (is_null($typeProductData)) {
            return null;
        }

        return new TypeProduct(
            $typeProductData['id'],
            $typeProductData['name']
        );
    }

    public function findByTypeProductName(string $typeProductName): bool
    {
        return $this->databaseTypeProductInterface->selectByTypeProductName($typeProductName);
    }

    public function findAll(): array
    {
        return $this->databaseTypeProductInterface->selectAll();
    }

    public function update(int $id, string $typeProductName): ?int
    {
        return $this->databaseTypeProductInterface->update($id, $typeProductName);
    }

    public function delete(int $id): ?int
    {
        return $this->databaseTypeProductInterface->delete($id);
    }
}
