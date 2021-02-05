<?php


namespace App\Models;


use App\Models\Contracts\TaxInterface;
use App\Services\Price\Contracts\PriceHandlerInterface;
use App\Traits\CanHashString;

/**
 * Class Cart
 * @package App\Models
 * @method string getFormattedSubTotal()
 * @method string getFormattedCollectedTaxes()
 * @method string getFormattedTotal()
 */
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
    protected float $collectedTaxes = 0;

    public function __construct(TaxInterface $tax, Currency $currency)
    {
        $this->tax = $tax;
        $this->currency = $currency;
    }

    public function calculate()
    {
        $this->collectTaxes();
        $this->setSubTotal(
            $this->getOriginalPriceTotal()
        );
        $this->applyDiscountsAndOffers();
        $this->setTotal(
            $this->getSubTotal() +
            $this->getCollectedTaxes() -
            $this->getTotalDiscounts()
        );
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

    public function getTax(): TaxInterface
    {
        return $this->tax;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function getSubTotal(): float
    {
        return $this->subTotal * $this->getCurrency()->getExchangeRate();
    }

    public function getTotal(): float
    {
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

    public function getCollectedTaxes(): float
    {
        return $this->collectedTaxes * $this->getCurrency()->getExchangeRate();
    }

    public function getTotalDiscounts(): float
    {
        return collect($this->discounts)->sum('total') * $this->getCurrency()->getExchangeRate();
    }

    public function setSubTotal(float $subTotal): void
    {
        $this->subTotal = $subTotal;
    }

    public function setTotal(float $total): void
    {
        $this->total = $total;
    }

    protected function hasItemKey(string $key): bool
    {
        return isset($this->items[$key]);
    }

    protected function applyDiscountsAndOffers(): float
    {
        $subTotal = 0;

        foreach ($this->getItems() as $item) {
            $this->applyPriceHandlers($item);

            $subTotal += $item->getTotalPrice();
        }

        return $subTotal * $this->getCurrency()->getExchangeRate();
    }

    protected function getFormattedAmount(float $amount): string
    {
        $amount = round($amount, 2);
        $currency = $this->getCurrency();
        $sign = $currency->getSign();
        switch ($this->getCurrency()->getName()) {
            case Currency::EGP:
                $amount .= ' ' . $sign;
                break;
            default:
                $amount = $sign . $amount;
        }

        return $amount;
    }

    protected function applyPriceHandlers(CartItem $item): CartItem
    {
        $priceHandlers = $this->getPriceHandlers();
        if (!$priceHandlers) return $item;
        foreach ($priceHandlers as $priceHandler) {
            $total = $priceHandler->apply($item);
            if (!$total) continue;
            $this->discounts[] = [
                'total' => $total,
                'type' => $item->getDiscountType(),
                'discount' => $item->getDiscount(),
                'name' => $item->getName()
            ];
        }

        return $item;
    }

    protected function isTaxesCollected(): bool
    {
        return $this->collectedTaxes !== (float) 0;
    }

    protected function collectTaxes(): void
    {
        $this->collectedTaxes = $this->getTax()->collect(
            $this->getOriginalPriceTotal()
        );
    }

    protected function getOriginalPriceTotal(): float
    {
        $total = 0;

        foreach ($this->getItems() as $item) {
            $total += $item->getOriginalPrice() * $item->getQuantity();
        }

        return $total;
    }

    public function getDiscounts(): array
    {
        $formattedDiscounts= [];
        foreach ($this->discounts as $discountItem) {
            $type = $discountItem['type'];
            $sign = $type === Discount::PERCENTAGE ? '%' : '';
            $discountAmount = $discountItem['discount'];
            if ($type === Discount::FIXED_PRICE) {
                $discountAmount = $this->getFormattedAmount($discountAmount);
            }
            $product = strtolower($discountItem['name']);
            $totalDiscount = $discountItem['total'];
            $differ = '-' . $this->getFormattedAmount(
                    $totalDiscount
                );

            $formattedDiscounts[] = "{$discountAmount}{$sign} off {$product}: {$differ}";
        }

        return $formattedDiscounts;
    }

    public function __call(string $method, array $arguments)
    {
        if (strpos($method,'getFormatted') === 0) {
            $method = str_replace('Formatted', '', $method);
            return $this->getFormattedAmount($this->{$method}());
        }
    }
}
