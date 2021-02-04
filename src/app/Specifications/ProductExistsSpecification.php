<?php


namespace App\Specifications;


use App\Specifications\Contracts\ProductExistSpecificationInterface;

class ProductExistsSpecification implements ProductExistSpecificationInterface
{
    protected array $productNames;

    public function __construct(array $productNames)
    {
        $this->productNames = $productNames;
    }
    public function isSatisfiedBy(string $productName): bool
    {
        return in_array($productName, $this->productNames);
    }
}
