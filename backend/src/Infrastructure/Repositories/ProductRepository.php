<?php

namespace App\Infrastructure\Repositories;

use App\Application\Dtos\ProductDto;
use App\Domain\Entities\Product;
use App\Domain\Repositories\ProductRepositoryInterface;
use App\Infrastructure\Persistence\DatabaseProductInterface;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

class ProductRepository implements ProductRepositoryInterface
{
    private DatabaseProductInterface $databaseProductInterface;

    public function __construct(DatabaseProductInterface $databaseProductInterface)
    {
        $this->databaseProductInterface = $databaseProductInterface;
    }

    public function create(ProductDto $product): void
    {

        $arrayProduct = [
            'code' => $product->getCode(),
            'type_product_id' => $product->getTypeProductId(),
            'name' => $product->getName(),
            'value' => $product->getValue(),
        ];

        try {
            $jsonProduct = json_encode($arrayProduct, JSON_THROW_ON_ERROR);
        } catch (\JsonException $th) {
            throw new RuntimeException('Failure to convert to json object', Response::HTTP_INTERNAL_SERVER_ERROR, $th);
        }

        $this->databaseProductInterface->create($jsonProduct);
    }

    public function findCode(int $code): bool
    {

        return $this->databaseProductInterface->selectByCode($code);
    }

    public function findById(int $id): ?Product
    {


        $productData = $this->databaseProductInterface->selectById($id);

        if (is_null($productData)) {
            return null;
        }

        return new Product(
            $productData['id'],
            $productData['code'],
            $productData['type_product_id'],
            $productData['name'],
            $productData['value'],
            formatDate($productData['created_at']),
            formatDate($productData['updated_at'])
        );
    }

    public function findAll(): array
    {
        return $this->databaseProductInterface->selectAll();
    }

    public function update(int $id, array $array): ?int
    {
        return $this->databaseProductInterface->update($id, $array);
    }

    public function delete(int $id): ?int
    {
        return $this->databaseProductInterface->delete($id);
    }
}
