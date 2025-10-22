<?php

declare(strict_types=1);

namespace App\Application\Dtos;

use App\Application\Exceptions\SaleException;
use App\Domain\Entities\Sale;
use TypeError;

readonly class SaleDto
{
    public function __construct(
        private string $valueSale,
        private string $valueTax,
        private ?int $id = null
    ) {
    }

    /**
    * @param array{'value_sale': string,'value_tax': string} $saleData
    */
    public static function fromRequest(array $saleData): self
    {

        $arrayKeys = ['value_sale', 'value_tax'];
        if (! validateArrayKeys($arrayKeys, $saleData)) {
            throw new SaleException("Sale has missing fields");
        }

        if (convertValueInStringForFloat($saleData['value_sale']) <= 0) {
            throw new TypeError(
                "O parâmetro value_sale: {$saleData['value_sale']} não pode ser menor ou igual a zero"
            );
        }

        if (convertValueInStringForFloat($saleData['value_tax']) <= 0) {
            throw new TypeError("O parâmetro value_tax: {$saleData['value_tax']} não pode ser menor ou igual a zero");
        }
        return new self(
            $saleData['value_sale'],
            $saleData['value_tax']
        );
    }

    public static function fromEntity(Sale $sale): self
    {
        return new self(
            valueSale: $sale->getValueSale(),
            valueTax: $sale->getValueTax(),
            id: $sale->getId()
        );
    }

    /**
    * @return array<mixed> $saleData
    */
    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'value_sale' => $this->valueSale,
            'value_tax' => $this->valueTax

        ], function ($value, $key) {
            return !in_array($key, ['id']) || !empty($value);
        }, ARRAY_FILTER_USE_BOTH);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValueSale(): string
    {
        return $this->valueSale;
    }

    public function getValueTax(): string
    {
        return $this->valueTax;
    }
}
