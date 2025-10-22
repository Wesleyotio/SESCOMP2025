<?php

declare(strict_types=1);

namespace App\Application\Dtos;

use App\Application\Exceptions\TaxException;
use App\Domain\Entities\Tax;
use TypeError;

class TaxUpdateDto
{
    public function __construct(
        private ?int $typeProductId = null,
        private ?string $value = null
    ) {
    }

    /**
     * @param array{
     *   type_product_id?: int,
     *   value?: string
     * } $taxData
     */
    public static function fromRequest(array $taxData): self
    {
        $arrayKeys = ['type_product_id', 'value'];

        if (! validateKeysContainedInArray(array_keys($taxData), $arrayKeys)) {
            throw new TaxException("unknown fields are being passed for tax update");
        }

        foreach ($taxData as $key => $value) {
            switch ($key) {
                case 'type_product_id':
                    if ((is_int($value) == false) || ($value <= 0)) {
                        throw new TypeError(
                            "O parâmetro type_product_id: {$value} precisa ser do tipo Inteiro e maior que zero"
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
            typeProductId: $taxData['type_product_id'] ?? null,
            value: $taxData['value'] ?? null
        );
    }

    public static function fromEntity(Tax $tax): self
    {
        return new self(
            typeProductId: $tax->getTypeProductId(),
            value: $tax->getValue(),
        );
    }

    /**
    * @return array<mixed> $taxData
    */
    public function toArray(): array
    {
        return array_filter([
            'type_product_id' => $this->typeProductId,
            'value' => $this->value

        ], function ($value, $key) {
            return !in_array($key, ['type_product_id', 'value']) || !empty($value);
        }, ARRAY_FILTER_USE_BOTH);
    }

    public function getTypeProductId(): ?int
    {
        return $this->typeProductId;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }
}
