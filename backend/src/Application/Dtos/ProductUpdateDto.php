<?php

declare(strict_types=1);

namespace App\Application\Dtos;

use App\Application\Exceptions\ProductException;
use App\Domain\Entities\Product;
use DateTimeImmutable;
use TypeError;

class ProductUpdateDto
{
    public function __construct(
        private ?int $code = null,
        private ?int $typeProductId = null,
        private ?string $name = null,
        private ?string $value = null,
        private ?DateTimeImmutable $updatedAt = null,
    ) {
    }


    /**
     * @param array{
     *   code?: int,
     *   type_product_id?: int,
     *   name?: string,
     *   value?: string
     * } $productData
     */
    public static function fromRequest(array $productData): self
    {

        $arrayKeys = ['code', 'type_product_id', 'name', 'value'];

        if (! validateKeysContainedInArray(array_keys($productData), $arrayKeys)) {
            throw new ProductException("unknown fields are being passed for product update");
        }

        foreach ($productData as $key => $value) {
            switch ($key) {
                case 'code':
                    if ((is_int($value) == false) || ($value <= 0)) {
                        throw new TypeError(
                            "O parâmetro code: {$value} precisa ser do tipo Inteiro e ser maior que zero"
                        );
                    }
                    break;

                case 'type_product_id':
                    if ((is_int($value) == false) || ($value <= 0)) {
                        throw new TypeError(
                            "O parâmetro type_product_id: {$value} precisa ser do tipo Inteiro e maior que zero"
                        );
                    }
                    break;

                case 'name':
                    if ((is_string($value) == false) || empty($value)) {
                        throw new TypeError(
                            "O parâmetro name: {$value} precisa ser do tipo string e não vazia"
                        );
                    }
                    break;

                case 'value':
                    if ((is_string($value) == false) || (convertValueInStringForFloat($value) <= 0)) {
                        throw new TypeError(
                            "O parâmetro value: {$value} precisa ser do tipo string e ser maior que zero"
                        );
                    }
                    break;

                default:
                    break;
            }
        }

        return new self(
            code:  $productData['code'] ?? null,
            typeProductId: $productData['type_product_id'] ?? null,
            name: $productData['name'] ?? null,
            value: $productData['value'] ?? null
        );
    }

    public static function fromEntity(Product $product): self
    {
        return new self(
            code: $product->getCode(),
            typeProductId: $product->getTypeProductId(),
            name: $product->getName(),
            value: $product->getValue(),
            updatedAt: $product->getUpdatedAt()
        );
    }


    /**
    * @return array<mixed> $productData
    */
    public function toArray(): array
    {
        return array_filter([
            'code' => $this->code,
            'type_product_id' => $this->typeProductId,
            'name' => $this->name,
            'value' => $this->value,

        ], function ($value, $key) {
            return !in_array($key, ['code','type_product_id', 'name', 'value']) || !empty($value);
        }, ARRAY_FILTER_USE_BOTH);
    }

    public function getCode(): ?int
    {
        return $this->code;
    }

    public function getTypeProductId(): ?int
    {
        return $this->typeProductId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
