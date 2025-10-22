<?php

namespace App\Drivers\Persistence;

use App\Infrastructure\Exceptions\DataBaseException;
use App\Infrastructure\Persistence\DatabaseSaleInterface;
use PDO;
use Symfony\Component\HttpFoundation\Response;

class DatabaseSale implements DatabaseSaleInterface
{
    use PostgresTrait;

    private PDO $pdo;

    /**
    * @param string $saleData JSON string:
    * {
    *       value_sale: string,
    *       value_tax: string
    * }
    * @return int
    * @throws DataBaseException
    * @throws \JsonException
    */
    public function create(string $saleData): int
    {
        $this->pdo = $this->connect();

        try {
            validateJson($saleData);

            /** @var array{
             *     value_sale: string,
             *     value_tax: string
             * } $arrayDecoded
             */
            $arrayDecoded = json_decode($saleData, true, 512, JSON_THROW_ON_ERROR);

            $createdAt = date('Y-m-d H:i:s');
            $updatedAt = date('Y-m-d H:i:s');

            $sql = "INSERT INTO sales (
                value_sale,
                value_tax,
                created_at,
                updated_at
            ) 
            VALUES (:value_sale, :value_tax, :created_at, :updated_at)";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':value_sale', $arrayDecoded['value_sale']);
            $stmt->bindParam(':value_tax', $arrayDecoded['value_tax']);
            $stmt->bindParam(':created_at', $createdAt);
            $stmt->bindParam(':updated_at', $updatedAt);
            
            if (!$stmt->execute()) {
                throw new \PDOException('Fail to insert register in table sales');
            }
            return (int) $this->pdo->lastInsertId();
        } catch (\PDOException $e) {
            throw new DataBaseException($e->getMessage(), previous: $e);
        }
    }

    /**
     * @param int $saleId
     * @return array<mixed>|null
     */
    public function selectById(int $saleId): ?array
    {
        $this->pdo = $this->connect();

        try {
            $sql = "SELECT id, value_sale, value_tax, created_at, updated_at
                        FROM sales 
                        WHERE id = :id AND deleted_at IS NULL";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $saleId, PDO::PARAM_INT);
            $stmt->execute();

            $sale = $stmt->fetch(PDO::FETCH_ASSOC);

            return is_array($sale) ? $sale : null;
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
            $sql = "SELECT id, value_sale, value_tax
                        FROM sales 
                        WHERE deleted_at IS NULL ORDER BY id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $sales = $stmt->fetchAll(PDO::FETCH_DEFAULT);

            validatePDO($sales);

            return $sales;
        } catch (\PDOException $e) {
            throw new DataBaseException($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, $e);
        }
    }

    /**
     * @param int $id
     * @param string $saleItensData
     * @return int
     */
    public function createSaleItens(int $id, string $saleItensData): int
    {
        $this->pdo = $this->connect();

        $createdAt = date('Y-m-d H:i:s');
        $updatedAt = date('Y-m-d H:i:s');

        $sql = "INSERT INTO sale_items (
            sale_id,
            product_id,
            amount,
            created_at,
            updated_at
        ) 
        VALUES (:sale_id, :product_id, :amount, :created_at, :updated_at)";
        try {
            validateJson($saleItensData);

            /** @var array<int,array<mixed>> $saleItens
             */
            $saleItens = json_decode($saleItensData, true, 512, JSON_THROW_ON_ERROR);
            $stmt = $this->pdo->prepare($sql);

            foreach ($saleItens as $item) {
                $stmt->bindValue(':sale_id', $id, PDO::PARAM_INT);
                $stmt->bindParam(':product_id', $item['product_id']);
                $stmt->bindParam(':amount', $item['amount']);
                $stmt->bindParam(':created_at', $createdAt);
                $stmt->bindParam(':updated_at', $updatedAt);
                if (!$stmt->execute()) {
                    throw new \PDOException('Fail to insert register in table sale_itens');
                }
            }
            return 1;
        } catch (\PDOException $e) {
            throw new DataBaseException($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, $e);
        }
    }
}
