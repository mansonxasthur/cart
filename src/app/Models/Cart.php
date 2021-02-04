<?php


namespace App\Models;


use App\Models\Contracts\TaxInterface;
use App\Services\Price\Contracts\PriceHandlerInterface;
use App\Traits\CanHashString;

class Cart
{
    use CanHashString;

    /**
     * @var CartItem[]|array
     */
    protected array $items = [];

    /**
     * @var PriceHandlerInterface[]|null
     */
    protected ?array $priceHandlers = null;

    protected array $discounts = [];
    protected TaxInterface $tax;
    protected Currency $currency;
    protected float $subTotal = 0;
    protected float $total = 0;

    public function __construct(TaxInterface $tax, Currency $currency)
    {
        $this->tax = $tax;
        $this->currency = $currency;
    }

    public function addCartItem(CartItem $item)
    {
        $this->items[$this->hash($item->getName())] = $item;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getItem(string $name): ?CartItem
    {
        $key = $this->hash($name);
        if (!$this->hasItemKey($key)) return null;
        return $this->items[$key];
    }

    public function getDiscounts(): array
    {
        if (empty($this->discounts)) {
            foreach ($this->getItems() as $item) {
                if ($discount = $item->getDiscount()) {
                    $type = $item->getDiscountType();
                    $sign = $type === Discount::PERCENTAGE ? '%' : '';

                    if ($type === Discount::FIXED_PRICE) {
                        $discount = $this->getFormattedAmount($discount);
                    }
                    $product = strtolower($item->getName());
                    $differ = '-' . $this->getFormattedAmount(
                        ($item->getOriginalPrice() * $item->getQuantity()) - $item->getTotalPrice()
                        );

                    $this->discounts[] = "{$discount}{$sign} off {$product}: {$differ}";
                }
            }
        }

        return $this->discounts;
    }

    public function getTax(): TaxInterface
    {
        return $this->tax;
    }

    public function getTaxes(): float
    {
        return $this->getTotal() - $this->getSubTotal();
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function getSubTotal(): float
    {
        if (!$this->subTotal) {
            $this->subTotal = $this->calculateSubTotal();
        }
        return $this->subTotal;
    }

    public function getTotal(): float
    {
        if (!$this->total) {
            $this->total = $this->tax->apply($this->getSubTotal());
        }
        return $this->total;
    }

    public function setPriceHandlers(array $priceHandlers): void
    {
        $this->priceHandlers = $priceHandlers;
    }

    public function getPriceHandlers(): ?array
    {
        return $this->priceHandlers;
    }

    protected function hasItemKey(string $key): bool
    {
        return isset($this->items[$key]);
    }

    protected function calculateSubTotal(): float
    {
        $subTotal = 0;

        foreach ($this->getItems() as $item) {
            $this->applyPriceHandlers($item);

            $subTotal += $item->getTotalPrice();
        }

        return $subTotal * $this->currency->getExchangeRate();
    }

    protected function getFormattedAmount(float $amount): string
    {
        switch ($this->currency->getCurrency()) {
            case Currency::EGP:
                $amount .= ' ' . $this->currency->getSign();
                break;
            default:
                $amount = $this->currency->getSign() . $amount;
        }

        return $amount;
    }

    protected function applyPriceHandlers(CartItem $item): CartItem
    {
        $priceHandlers = $this->getPriceHandlers();
        if (!$priceHandlers) return $item;
        foreach ($priceHandlers as $priceHandler) {
            $priceHandler->handle($item);
        }

        return $item;
    }

    public function __call(string $method, array $arguments): ?string
    {
        if (strpos($method,'getFormatted') === 0) {
            $method = str_replace('Formatted', '', $method);
            return $this->getFormattedAmount($this->{$method}());
        }
        return null;
    }
}
