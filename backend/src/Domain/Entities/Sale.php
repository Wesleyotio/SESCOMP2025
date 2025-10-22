<?php

declare(strict_types=1);

namespace App\Domain\Entities;

readonly class Sale
{
    public function __construct(
        private int $id,
        private string $valueSale,
        private string $valueTax,
        
    ) {
    }

    public function getId(): int
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
