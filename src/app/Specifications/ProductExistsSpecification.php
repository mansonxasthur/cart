<?php


namespace App\Specifications;


use App\Models\Product;
use Illuminate\Support\Collection;
use App\Specifications\Contracts\ProductExistSpecificationInterface;

class ProductExistsSpecification implements ProductExistSpecificationInterface
{
    protected Collection $products;

    public function __construct(Collection $products)
    {
        $this->products = $products;
    }

    public function isSatisfiedBy(string $productName): bool
    {
        return $this->products->filter(fn(Product $product) => $product->getName() === $productName)
                              ->isNotEmpty();
    }
}
