<?php

namespace App\Application\UseCases;

use App\Application\Dtos\SaleDto;
use App\Domain\Repositories\ProductRepositoryInterface;
use App\Domain\Repositories\TaxRepositoryInterface;
use App\Infrastructure\Exceptions\ClientException;

class CalculateSaleUseCase
{
    private ProductRepositoryInterface $productRepository;
    private TaxRepositoryInterface $taxRepository;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        TaxRepositoryInterface $taxRepository
    )
    {
        $this->productRepository = $productRepository;
        $this->taxRepository = $taxRepository;
    }

    /**
     * @param array<int,array<string,int|string>> $salesItens
     * @return SaleDto
     *
    */
    public function action(array $salesItens): SaleDto
    {
        $totalPrice = '0';
        $totalTax = '0';
               
        foreach ($salesItens as $salesItem) {
            $product = $this->productRepository->findById((int)$salesItem['product_id']);

            if (is_null($product)) {
                throw new ClientException("there is no matching product for the ID");
            }

            $tax = $this->taxRepository->findByTypeProductId($product->getTypeProductId());

            if (is_null($tax)) {
                throw new ClientException("there is no matching tax for the ID");
            }

            $price = bcmul($product->getValue(), (string)$salesItem['amount'], 2);
                   
            $valueTax = bcmul($price, bcdiv($tax->getValue(), '100', 3), 2);

            $priceWithTax = bcadd($price, $valueTax, 2);
            
            $totalPrice = bcadd($totalPrice, $priceWithTax, 2);

            $totalTax = bcadd($totalTax, $valueTax, 2);
        }

        $result = [
            'value_sale' => $totalPrice,
            'value_tax'  => $totalTax
        ];
        return SaleDto::fromRequest($result);
    }
}
