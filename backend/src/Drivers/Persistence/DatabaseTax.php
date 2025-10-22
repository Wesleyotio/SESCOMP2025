<?php

namespace App\Drivers\Persistence;

use App\Infrastructure\Exceptions\DataBaseException;
use App\Infrastructure\Persistence\DatabaseTaxInterface;
use PDO;
use Symfony\Component\HttpFoundation\Response;

class DatabaseTax implements DatabaseTaxInterface
{
    use PostgresTrait;

    private PDO $pdo;

    /**
    * @param string $taxData JSON string:
    * {
    *     "type_product_id": int,
    *     "value": string
    * }
    * @throws DataBaseException
    * @throws \JsonException
    */
    public function create(string $taxData): void
    {
        $this->pdo = $this->connect();

        try {
            validateJson($taxData);

            /** @var array{
             *     type_product_id: int,
             *     value: string
             * } $arrayDecoded
             */
            $arrayDecoded = json_decode($taxData, true, 512, JSON_THROW_ON_ERROR);

            $createdAt = date('Y-m-d H:i:s');
            $updatedAt = date('Y-m-d H:i:s');

            $sql = "INSERT INTO taxes (
                type_product_id,
                value,
                created_at,
                updated_at
            ) 
            VALUES (:type_product_id, :value, :created_at, :updated_at)";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':type_product_id', $arrayDecoded['type_product_id']);
            $stmt->bindParam(':value', $arrayDecoded['value']);
            $stmt->bindParam(':created_at', $createdAt);
            $stmt->bindParam(':updated_at', $updatedAt);
            $stmt->execute();
        } catch (\PDOException $e) {
            throw new DataBaseException($e->getMessage(), previous: $e);
        }
    }

    public function selectByTypeProductId(int $typeProductId): bool
    {
        $this->pdo = $this->connect();

        try {
            $sql = "SELECT id FROM taxes WHERE type_product_id = :type_product_id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':type_product_id', $typeProductId, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);


            return empty($result) ? false : true;
        } catch (\PDOException $e) {
            throw new DataBaseException($e->getMessage(), previous: $e);
        }
    }

    /**
    * @param int $typeProductId
    * @return array<mixed>|null
    */
    public function findByTypeProductId(int $typeProductId): ?array
    {
        $this->pdo = $this->connect();

        try {
            $sql = "SELECT id, type_product_id, value 
                        FROM taxes 
                        WHERE type_product_id = :type_product_id  AND deleted_at IS NULL";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':type_product_id', $typeProductId, PDO::PARAM_INT);
            $stmt->execute();

            $tax = $stmt->fetch(PDO::FETCH_ASSOC);

            return is_array($tax) ? $tax : null;
        } catch (\PDOException $e) {
            throw new DataBaseException($e->getMessage(), previous: $e);
        }
    }
    /**
     * @param int $taxId
     * @return array<mixed>|null
     */
    public function selectById(int $taxId): ?array
    {
        $this->pdo = $this->connect();

        try {
            $sql = "SELECT id, type_product_id, value, created_at, updated_at
                        FROM taxes 
                        WHERE id = :id AND deleted_at IS NULL";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $taxId, PDO::PARAM_INT);
            $stmt->execute();

            $tax = $stmt->fetch(PDO::FETCH_ASSOC);

            return is_array($tax) ? $tax : null;
        } catch (\PDOException $e) {
            throw new DataBaseException($e->getMessage(), previous: $e);
        }
    }

    /**
     *
     * @return array<mixed>
     */
    public function selectAll(): array
    {
        $this->pdo = $this->connect();

        try {
            $sql = "SELECT id, type_product_id, value
                        FROM taxes 
                        WHERE deleted_at IS NULL ORDER BY id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $taxes = $stmt->fetchAll(PDO::FETCH_DEFAULT);

            validatePDO($taxes);

            return $taxes;
        } catch (\PDOException $e) {
            throw new DataBaseException($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, $e);
        }
    }

    /**
     * @param int $id
     * @param array<mixed> $taxAttributes
     * @return int|null
     */
    public function update(int $id, array $taxAttributes): ?int
    {
        $this->pdo = $this->connect();


        $updatedAt = date('Y-m-d H:i:s');

        $setAttributes = [];
        foreach ($taxAttributes as $key => $value) {
            $setAttributes[] = $key . " = :" . $key;
        }
        $setAttributes[] = "updated_at = :updated_at";

        $setAttributes = implode(', ', $setAttributes);

        $sql = "UPDATE taxes SET " . $setAttributes . " WHERE id = :id";

        try {
            $stmt = $this->pdo->prepare($sql);

            foreach ($taxAttributes as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }

            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':updated_at', $updatedAt);
            $stmt->execute();
            if ($stmt->rowCount() === 0) {
                return null;
            }
            return 1;
        } catch (\PDOException $e) {
            throw new DataBaseException($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, $e);
        }
    }

    public function delete(int $id): ?int
    {
        $this->pdo = $this->connect();

        $deletedAt = date('Y-m-d H:i:s');

        $sql = "UPDATE taxes SET deleted_at = :deleted_at WHERE id = :id AND deleted_at IS NULL";

        try {
            $stmt = $this->pdo->prepare($sql);

            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':deleted_at', $deletedAt);
            $stmt->execute();
            if ($stmt->rowCount() === 0) {
                return null;
            }
             return 1;
        } catch (\PDOException $e) {
            throw new DataBaseException($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, $e);
        }
    }
}
