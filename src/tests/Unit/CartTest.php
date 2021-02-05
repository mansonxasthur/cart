<?php



class CartTest extends \PHPUnit\Framework\TestCase
{
    protected \App\Models\Tax $tax;
    protected \App\Models\Currency $currency;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->tax = new \App\Models\Tax(14);
        $this->currency = new \App\Models\Currency('USD', ['exchange_rate' => 1, 'sign' => '$']);
    }

    /**
     * @test
     */
    public function it_calculate_cart_items()
    {
        $cart = new \App\Models\Cart($this->tax, $this->currency);
        $product = new \App\Models\Product([
            'name' => 'Laptop',
            'price' => 10000,
            'discount' => null,
            'discount_type' => null
        ]);
        $cartItem = new \App\Models\CartItem($product, 2);
        $cart->addCartItem($cartItem);
        $cart->calculate();

        $this->assertIsNumeric($cart->getSubTotal());
        $this->assertEquals(20000, $cart->getSubTotal());
        $this->assertEquals($this->tax->collect(20000), $cart->getCollectedTaxes());
        $this->assertEquals(0, $cart->getTotalDiscounts());
        $this->assertEquals(22800, $cart->getTotal());
    }

    /**
     * @test
     */
    public function it_calculate_cart_items_with_discounts()
    {
        $cart = new \App\Models\Cart($this->tax, $this->currency);
        $product = new \App\Models\Product([
            'name' => 'Laptop',
            'price' => 10000,
            'discount' => 10,
            'discount_type' => 'percentage'
        ]);
        $cartItem = new \App\Models\CartItem($product, 1);
        $cart->addCartItem($cartItem);
        $cart->setPriceHandlers([
            new \App\Services\Price\DiscountHandler(
                new \App\Specifications\ProductHasDiscountSpecification(),
                new \App\Services\Discounts\Factories\DiscountCalculatorFactory()
            ),
        ]);
        $cart->calculate();

        $this->assertIsNumeric($cart->getSubTotal());
        $this->assertEquals(10000, $cart->getSubTotal());
        $this->assertEquals($this->tax->collect(10000), $cart->getCollectedTaxes());
        $this->assertEquals(1000, $cart->getTotalDiscounts());
        $this->assertEquals(10400, $cart->getTotal());
    }
}
