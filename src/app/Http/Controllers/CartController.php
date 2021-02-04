<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidCurrencyException;
use App\Exceptions\ProductNotFoundException;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Currency;
use App\Models\Discount;
use App\Models\Offer;
use App\Models\Tax;
use App\Services\Discounts\Factories\DiscountCalculatorFactory;
use App\Services\Price\DiscountHandler;
use App\Services\Price\OfferHandler;
use App\Specifications\ApplicableForOfferSpecification;
use App\Specifications\ProductHasDiscountSpecification;
use App\Specifications\ProductExistsSpecification;
use App\Specifications\ValidCurrencySpecification;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    protected array $currencies;
    protected ValidCurrencySpecification $validCurrencySpecification;
    protected Collection $products;
    protected ProductExistsSpecification $productExistsSpecification;

    public function __construct()
    {
        $this->currencies = config('currencies');
        $this->validCurrencySpecification = new ValidCurrencySpecification();
        $this->products = collect(config('products'));
        $this->productExistsSpecification = new ProductExistsSpecification($this->products->pluck('name')
                                                                                          ->all());
    }

    /**
     * @param Request $request
     *
     * @return CartResource|\Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function checkout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'products'            => ['required',
                                      'array'],
            'products.*.name'     => ['required',
                                      'string'],
            'products.*.quantity' => ['required',
                                      'numeric', 'min:1'],
            'currency'            => ['required',
                                      'string'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $currency = strtoupper($request->get('currency'));

        if (!$this->validCurrencySpecification->isSatisfiedBy($currency)) throw new InvalidCurrencyException;
        $currency = new Currency($currency, $this->currencies[$currency]);
        $tax = new Tax(14);
        $cart = new Cart($tax, $currency);
        $products = $request->get('products');

        foreach ($products as $product) {
            if (!$this->productExistsSpecification->isSatisfiedBy($product['name'])) throw new ProductNotFoundException($product['name']);
            $quantity = $product['quantity'];
            $product = $this->products->where('name', $product['name'])
                                      ->first();
            $cart->addCartItem(new CartItem($product, $quantity));
        }

        $cart->setPriceHandlers([
            new DiscountHandler(
                new ProductHasDiscountSpecification(),
                new DiscountCalculatorFactory()
            ),
            new OfferHandler($cart, $this->loadOffers(), new ApplicableForOfferSpecification())
        ]);

        JsonResource::withoutWrapping();
        return new CartResource($cart);
    }

    protected function loadOffers(): array
    {
        return [
            new Offer('T-shirt', 'Jacket', new Discount(50, Discount::PERCENTAGE), 2),
            new Offer('Shoes', 'Pants', new Discount(8, Discount::FIXED_PRICE), 2),
        ];
    }
}
