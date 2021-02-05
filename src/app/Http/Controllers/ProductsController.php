<?php


namespace App\Http\Controllers;


use App\Http\Resources\OffersResource;
use App\Http\Resources\ProductsResource;
use App\Models\Discount;
use App\Models\Offer;
use App\Models\Product;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class ProductsController extends Controller
{
    protected Collection $products;
    private Collection $offers;

    public function __construct()
    {
        $this->products = collect(config('products'))->map(function ($product) {
            return new Product($product);
        });
        $this->offers = collect(config('offers'))->map(function ($offer) {
            return new Offer($offer['offeree'], $offer['offerable'], new Discount($offer['discount_value'], $offer['discount_type']), $offer['quantity']);
        });
    }

    public function index(): JsonResource
    {
        return ProductsResource::collection($this->products)
            ->additional(['offers' => OffersResource::collection($this->offers)]);
    }
}
