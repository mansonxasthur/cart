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
use App\Models\Product;
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
    protected Collection $currencies;
    protected Collection $products;
    protected Collection $offers;
    protected ValidCurrencySpecification $validCurrencySpecification;
    protected ProductExistsSpecification $productExistsSpecification;

    public function __construct()
    {
        $this->currencies = collect(config('currencies'))->map(function ($currencyInfo, $currencyName) {
            return new Currency($currencyName, $currencyInfo);
        });
        $this->products = collect(config('products'))->map(function ($product) {
            return new Product($product);
        });
        $this->offers = collect(config('offers'))->map(function ($offer) {
            return new Offer($offer['offeree'], $offer['offerable'], new Discount($offer['discount_value'], $offer['discount_type']), $offer['quantity']);
        });
        $this->validCurrencySpecification = new ValidCurrencySpecification();
        $this->productExistsSpecification = new ProductExistsSpecification($this->products);
    }

    /**
     * @param Request $request
     *
     * @return CartResource|\Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function cart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'products'            => ['required',
                                      'array'],
            'products.*.name'     => ['required',
                                      'string'],
            'products.*.quantity' => ['required',
                                      'numeric',
                                      'min:1'],
            'currency'            => ['required',
                                      'string'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $currency = strtoupper($request->get('currency'));

        if (!$this->validCurrencySpecification->isSatisfiedBy($currency)) throw new InvalidCurrencyException;
        $currency = $this->currencies->get($currency);

        $tax = new Tax(14);
        $cart = new Cart($tax, $currency);
        $cartProducts = $request->get('products');

        foreach ($cartProducts as $cartProduct) {
            if (!$this->productExistsSpecification->isSatisfiedBy($cartProduct['name'])) throw new ProductNotFoundException();
            $quantity = $cartProduct['quantity'];
            $product = $this->products->filter(fn(Product $product) => $product->getName() === $cartProduct['name'])
                                      ->first();

            $cart->addCartItem(new CartItem($product, $quantity));
        }

        $cart->setPriceHandlers([
            new DiscountHandler(
                new ProductHasDiscountSpecification(),
                new DiscountCalculatorFactory()
            ),
            new OfferHandler($cart, $this->offers, new ApplicableForOfferSpecification())
        ]);
        $cart->calculate();
        JsonResource::withoutWrapping();
        return new CartResource($cart);
    }
}
