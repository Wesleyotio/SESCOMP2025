<?php

declare(strict_types=1);

namespace App\Application\Dtos;

use App\Application\Exceptions\TaxException;
use App\Domain\Entities\Tax;
use TypeError;

readonly class TaxDto
{
    public function __construct(
        private int $typeProductId,
        private string $value,
        private ?int $id = null
    ) {
    }

    /**
    * @param array{'type_product_id': int,'value': string} $taxData
    */
    public static function fromRequest(array $taxData): self
    {

        $arrayKeys = ['type_product_id', 'value'];
        if (! validateArrayKeys($arrayKeys, $taxData)) {
            throw new TaxException("Tax has missing fields");
        }

        if ($taxData['type_product_id'] <= 0) {
            throw new TypeError(
                "O parâmetro type_product_id: {$taxData['type_product_id']} precisa ser maior que zero"
            );
        }

        if (convertValueInStringForFloat($taxData['value']) <= 0) {
            throw new TypeError("O parâmetro value: {$taxData['value']} não pode ser menor ou igual a zero");
        }
        return new self(
            $taxData['type_product_id'],
            $taxData['value']
        );
    }

    public static function fromEntity(Tax $tax): self
    {
        return new self(
            typeProductId: $tax->getTypeProductId(),
            value: $tax->getValue(),
            id: $tax->getId()
        );
    }

    /**
    * @return array<mixed> $taxData
    */
    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'type_product_id' => $this->typeProductId,
            'value' => $this->value

        ], function ($value, $key) {
            return !in_array($key, ['id']) || !empty($value);
        }, ARRAY_FILTER_USE_BOTH);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeProductId(): int
    {
        return $this->typeProductId;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
