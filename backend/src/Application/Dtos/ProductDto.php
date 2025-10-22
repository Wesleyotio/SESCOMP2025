<?php

declare(strict_types=1);

namespace App\Application\Dtos;

use App\Application\Exceptions\ProductException;
use App\Domain\Entities\Product;
use DateTimeImmutable;
use TypeError;

readonly class ProductDto
{
    public function __construct(
        private int $code,
        private int $typeProductId,
        private string $name,
        private string $value,
        private ?int $id = null,
        private ?DateTimeImmutable $createdAt = null,
        private ?DateTimeImmutable $updatedAt = null,
    ) {
    }

    /**
    * @param array{'code': int, 'type_product_id': int, 'name': string, 'value': string} $productData
    */
    public static function fromRequest(array $productData): self
    {

        $arrayKeys = ['code', 'type_product_id', 'name', 'value'];
        if (! validateArrayKeys($arrayKeys, $productData)) {
            throw new ProductException("Product has missing fields");
        }

        if ($productData['code'] <= 0) {
            throw new TypeError("O parâmetro code: {$productData['code']} precisa ser maior que zero");
        }

        if ($productData['type_product_id'] <= 0) {
            throw new TypeError(
                "O parâmetro type_product_id: {$productData['type_product_id']} precisa ser maior que zero"
            );
        }

        if (empty($productData['name'])) {
            throw new TypeError("O parâmetro name: {$productData['name']} não pode ser vazio");
        }

        if (convertValueInStringForFloat($productData['value']) < 0) {
            throw new TypeError("O parâmetro value: {$productData['value']} não pode ser negativo");
        }
        return new self(
            $productData['code'],
            $productData['type_product_id'],
            $productData['name'],
            $productData['value']
        );
    }

    public static function fromEntity(Product $product): self
    {
        return new self(
            code: $product->getCode(),
            typeProductId: $product->getTypeProductId(),
            name: $product->getName(),
            value: $product->getValue(),
            id: $product->getId(),
            createdAt: $product->getCreatedAt(),
            updatedAt: $product->getUpdatedAt()
        );
    }

    /**
    * @return array<mixed> $productData
    */
    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'code' => $this->code,
            'type_product_id' => $this->typeProductId,
            'name' => $this->name,
            'value' => $this->value,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt

        ], function ($value, $key) {
            return !in_array($key, ['id','created_at', 'updated_at']) || !empty($value);
        }, ARRAY_FILTER_USE_BOTH);
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getCode(): int
    {
        return $this->code;
    }

    public function getTypeProductId(): int
    {
        return $this->typeProductId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
