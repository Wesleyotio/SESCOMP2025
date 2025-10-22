<?php

declare(strict_types=1);

namespace App\Application\Dtos;

use App\Application\Exceptions\TypeProductException;
use App\Domain\Entities\TypeProduct;

readonly class TypeProductDto
{
    public function __construct(
        private string $name,
        private ?int $id = null
    ) {
    }

    /**
    * @param string $typeProductData
    */
    public static function fromRequest(string $typeProductData): self
    {
        if (!is_string($typeProductData)) {
            throw new TypeProductException("name of type product has missing");
        }
        return new self(
            name: $typeProductData
        );
    }

    public static function fromEntity(TypeProduct $typeProduct): self
    {
        return new self(
            name: $typeProduct->getName(),
            id: $typeProduct->getId()
        );
    }

    /**
    * @return array<mixed> $taxData
    */
    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'name' => $this->name

        ], function ($value, $key) {
            return !in_array($key, ['id']) || !empty($value);
        }, ARRAY_FILTER_USE_BOTH);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
