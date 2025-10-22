<?php

namespace App\Drivers\Persistence;

use App\Infrastructure\Exceptions\DataBaseException;
use App\Infrastructure\Persistence\DatabaseTypeProductInterface;
use PDO;
use Symfony\Component\HttpFoundation\Response;

class DatabaseTypeProduct implements DatabaseTypeProductInterface
{
    use PostgresTrait;

    private PDO $pdo;

    /**
    * @param string $typeProductData JSON string:
    * {
    *     "name": string
    * }
    * @throws DataBaseException
    * @throws \JsonException
    */
    public function create(string $typeProductData): void
    {
        $this->pdo = $this->connect();

        try {
            validateJson($typeProductData);

            /** @var array{
             *     name: string
             * } $arrayDecoded
             */
            $arrayDecoded = json_decode($typeProductData, true, 512, JSON_THROW_ON_ERROR);

            $createdAt = date('Y-m-d H:i:s');
            $updatedAt = date('Y-m-d H:i:s');

            $sql = "INSERT INTO type_products (
                name,
                created_at,
                updated_at
            ) 
            VALUES (:name, :created_at, :updated_at)";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':name', $arrayDecoded['name']);
            $stmt->bindParam(':created_at', $createdAt);
            $stmt->bindParam(':updated_at', $updatedAt);
            $stmt->execute();
        } catch (\PDOException $e) {
            throw new DataBaseException($e->getMessage(), previous: $e);
        }
    }

    public function selectByTypeProductName(string $typeProductName): bool
    {
        $this->pdo = $this->connect();

        try {
            $sql = "SELECT id FROM type_products WHERE name = :name";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':name', $typeProductName, PDO::PARAM_INT);
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
    public function selectById(int $typeProductId): ?array
    {
        $this->pdo = $this->connect();

        try {
            $sql = "SELECT id, name, created_at, updated_at
                        FROM type_products 
                        WHERE id = :id AND deleted_at IS NULL";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $typeProductId, PDO::PARAM_INT);
            $stmt->execute();

            $typeProduct = $stmt->fetch(PDO::FETCH_ASSOC);

            return is_array($typeProduct) ? $typeProduct : null;
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
            $sql = "SELECT id, name
                        FROM type_products 
                        WHERE deleted_at IS NULL ORDER BY id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $typeProducts = $stmt->fetchAll(PDO::FETCH_DEFAULT);

            validatePDO($typeProducts);

            return $typeProducts;
        } catch (\PDOException $e) {
            throw new DataBaseException($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, $e);
        }
    }

    /**
     * @param int $id
     * @param string $typeProductName
     * @return int|null
     */
    public function update(int $id, string $typeProductName): ?int
    {
        $this->pdo = $this->connect();

        $updatedAt = date('Y-m-d H:i:s');

        $sql = "UPDATE type_products SET name = :name , updated_at = :updated_at WHERE id = :id";

        try {
            $stmt = $this->pdo->prepare($sql);

            $stmt->bindValue(":name", $typeProductName);
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

        $sql = "UPDATE type_products SET deleted_at = :deleted_at WHERE id = :id AND deleted_at IS NULL";

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
