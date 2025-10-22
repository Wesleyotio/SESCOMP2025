<?php

namespace App\Drivers\Persistence;

use App\Infrastructure\Exceptions\ClientException;
use App\Infrastructure\Exceptions\DataBaseException;
use App\Infrastructure\Persistence\DatabaseProductInterface;
use PDO;
use Symfony\Component\HttpFoundation\Response;

class DatabaseProduct implements DatabaseProductInterface
{
    use PostgresTrait;

    private PDO $pdo;

    /**
    * @param string $productData JSON string:
    * {
    *     "code": int,
    *     "type_product_id": int,
    *     "name": string,
    *     "value": string
    * }
    * @throws DataBaseException
    * @throws \JsonException
    */
    public function create(string $productData): void
    {
        $this->pdo = $this->connect();

        try {
            validateJson($productData);

            /** @var array{
             *     code: int,
             *     type_product_id: int,
             *     name: string,
             *     value: string
             * } $arrayDecoded
             */
            $arrayDecoded = json_decode($productData, true, 512, JSON_THROW_ON_ERROR);

            $createdAt = date('Y-m-d H:i:s');
            $updatedAt = date('Y-m-d H:i:s');

            $sql = "INSERT INTO products (
                code, 
                type_product_id,
                name,
                value,
                created_at,
                updated_at
            ) 
            VALUES (:code, :type_product_id, :name, :value, :created_at, :updated_at)";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':code', $arrayDecoded['code']);
            $stmt->bindParam(':type_product_id', $arrayDecoded['type_product_id']);
            $stmt->bindParam(':name', $arrayDecoded['name']);
            $stmt->bindParam(':value', $arrayDecoded['value']);
            $stmt->bindParam(':created_at', $createdAt);
            $stmt->bindParam(':updated_at', $updatedAt);
            $stmt->execute();
        } catch (\PDOException $e) {
            throw new DataBaseException($e->getMessage(), previous: $e);
        }
    }

    public function selectByCode(int $code): bool
    {
        $this->pdo = $this->connect();

        try {
            $sql = "SELECT id FROM products WHERE code = :code";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':code', $code, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);


            return empty($result) ? false : true;
        } catch (\PDOException $e) {
            throw new DataBaseException($e->getMessage(), previous: $e);
        }
    }

    /**
     * @param int $productId
     * @return array<mixed>|null
     */
    public function selectById(int $productId): ?array
    {
        $this->pdo = $this->connect();

        try {
            $sql = "SELECT id, code, type_product_id, name, value, created_at, updated_at
                        FROM products 
                        WHERE id = :id AND deleted_at IS NULL";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $productId, PDO::PARAM_INT);
            $stmt->execute();

            $product = $stmt->fetch(PDO::FETCH_ASSOC);


            return is_array($product) ? $product : null;
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
            $sql = "SELECT id, code, type_product_id, name, value, created_at, updated_at
                        FROM products 
                        WHERE deleted_at IS NULL ORDER BY id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $products = $stmt->fetchAll(PDO::FETCH_DEFAULT);

            validatePDO($products);

            return $products;
        } catch (\PDOException $e) {
            throw new DataBaseException($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, $e);
        }
    }

    /**
     * @param int $id
     * @param array<mixed> $productAttributes
     * @return int|null
     */
    public function update(int $id, array $productAttributes): ?int
    {
        $this->pdo = $this->connect();


        $updatedAt = date('Y-m-d H:i:s');

        $setAttributes = [];
        foreach ($productAttributes as $key => $value) {
            $setAttributes[] = $key . " = :" . $key;
        }
        $setAttributes[] = "updated_at = :updated_at";

        $setAttributes = implode(', ', $setAttributes);

        $sql = "UPDATE products SET " . $setAttributes . " WHERE id = :id";

        try {
            $stmt = $this->pdo->prepare($sql);

            foreach ($productAttributes as $key => $value) {
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

        $sql = "UPDATE products SET deleted_at = :deleted_at WHERE id = :id AND deleted_at IS NULL";

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
