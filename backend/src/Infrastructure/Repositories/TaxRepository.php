<?php

namespace App\Infrastructure\Repositories;

use App\Application\Dtos\TaxDto;
use App\Domain\Entities\Tax;
use App\Domain\Repositories\TaxRepositoryInterface;
use App\Infrastructure\Persistence\DatabaseTaxInterface;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

class TaxRepository implements TaxRepositoryInterface
{
    private DatabaseTaxInterface $databaseTaxInterface;

    public function __construct(DatabaseTaxInterface $databaseTaxInterface)
    {
        $this->databaseTaxInterface = $databaseTaxInterface;
    }

    public function create(TaxDto $tax): void
    {
        $arrayTax = [
            'type_product_id' => $tax->getTypeProductId(),
            'value' => $tax->getValue(),
        ];

        try {
            $jsonTax = json_encode($arrayTax, JSON_THROW_ON_ERROR);
        } catch (\JsonException $th) {
            throw new RuntimeException('Failure to convert to json object', Response::HTTP_INTERNAL_SERVER_ERROR, $th);
        }

        $this->databaseTaxInterface->create($jsonTax);
    }

    public function findById(int $id): ?Tax
    {
        $taxData = $this->databaseTaxInterface->selectById($id);
        return $this->validateTax($taxData);
    }

    public function validateByTypeProductId(int $typeProductId): bool
    {
        return $this->databaseTaxInterface->selectByTypeProductId($typeProductId);
    }

    public function findByTypeProductId(int $typeProductId): ?Tax
    {
        $taxData = $this->databaseTaxInterface->findByTypeProductId($typeProductId);
        return $this->validateTax($taxData);
    }

    public function findAll(): array
    {
        return $this->databaseTaxInterface->selectAll();
    }

    public function update(int $id, array $array): ?int
    {
        return $this->databaseTaxInterface->update($id, $array);
    }

    public function delete(int $id): ?int
    {
        return $this->databaseTaxInterface->delete($id);
    }

    /**
     * @param array{
     *      id: int,
     *      type_product_id: int,
     *      value: string
     * }|null $taxData
     * @return null|Tax
     */
    private function validateTax(?array $taxData): ?Tax
    {
        if (is_null($taxData) == true) {
            return null;
        }
        return new Tax(
            $taxData['id'],
            $taxData['type_product_id'],
            $taxData['value']
        );
    }
}
