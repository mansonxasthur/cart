<?php

namespace Tests\Feature;

use Tests\TestCase;

class CartTest extends TestCase
{
    /**
     * @test
     */
    public function it_excepts_post_requests_only()
    {
        $methods = ['get',
                    'post',
                    'put',
                    'patch',
                    'head',
                    'options'];
        foreach ($methods as $method) {
            $response = $this->json($method, '/cart');
            if ($method === 'post') {
                $response->assertResponseStatus(422);
                continue;
            }
            $response->assertResponseStatus(405);
        }
    }

    /**
     * @test
     */
    public function it_validates_products_field_exists()
    {
        $response = $this->json('post', '/cart');

        $response->assertResponseStatus(422);
        $response->seeJsonContains(['The products field is required.']);
    }

    /**
     * @test
     */
    public function it_validates_currency_field_exists()
    {
        $response = $this->json('post', '/cart');

        $response->assertResponseStatus(422);
        $response->seeJsonContains(['The currency field is required.']);
    }

    /**
     * @test
     */
    public function it_validates_product_name_exists()
    {
        $response = $this->json('post', '/cart', ['products' => [[]]]);

        $response->assertResponseStatus(422);
        $response->seeJsonContains(['The products.0.quantity field is required.']);
    }

    /**
     * @test
     */
    public function it_validates_product_quantity_exists()
    {
        $response = $this->json('post', '/cart', ['products' => [[]]]);

        $response->assertResponseStatus(422);
        $response->seeJsonContains(['The products.0.quantity field is required.']);
    }

    /**
     * @test
     */
    public function it_validates_product_quantity_is_a_number()
    {
        $response = $this->json('post', '/cart', ['products' => [['quantity' => 'one']]]);

        $response->assertResponseStatus(422);
        $response->seeJsonContains(['The products.0.quantity must be a number.']);
    }

    /**
     * @test
     */
    public function it_validates_product_quantity_is_greater_than_1()
    {
        $response = $this->json('post', '/cart', ['products' => [['quantity' => 0]]]);

        $response->assertResponseStatus(422);
        $response->seeJsonContains(['The products.0.quantity must be at least 1.']);
    }

    /**
     * @test
     */
    public function it_gets_bad_request_if_invalid_currency()
    {
        $product = [
            'name'     => 'T-shirt',
            'quantity' => 1,
        ];

        $currency = 'eur';
        $response = $this->json('post', '/cart', ['products' => [$product],
                                                  'currency' => $currency]);

        $response->assertResponseStatus(400);
        $response->seeJsonContains(['The selected currency is invalid. Valid currencies: USD, EGP.']);
    }

    /**
     * @test
     */
    public function it_gets_not_found_request_for_unknown_products()
    {
        $product = [
            'name'     => 'Non existing product',
            'quantity' => 1,
        ];

        $currency = 'usd';
        $response = $this->json('post', '/cart', ['products' => [$product],
                                                  'currency' => $currency]);

        $response->assertResponseStatus(404);
        $response->seeJsonContains(['Product not found.']);
    }

    /**
     * @test
     */
    public function it_gets_response_ok_for_valid_entries()
    {
        $product = [
            'name'     => 'T-shirt',
            'quantity' => 1,
        ];

        $currency = 'usd';
        $response = $this->json('post', '/cart', ['products' => [$product],
                                                  'currency' => $currency]);

        $response->assertResponseOk();
        $response->seeJsonStructure(['Subtotal',
                                     'Taxes',
                                     'Total']);
    }

    /**
     * @test
     */
    public function it_gets_discounts_if_cart_has_discounted_items()
    {
        $product = [
            'name'     => 'Shoes',
            'quantity' => 1,
        ];

        $currency = 'usd';
        $response = $this->json('post', '/cart', ['products' => [$product],
                                                  'currency' => $currency]);

        $response->assertResponseOk();
        $response->seeJsonStructure(['Subtotal',
                                     'Taxes',
                                     'Discounts',
                                     'Total']);
    }

    /**
     * @test
     */
    public function it_do_not_get_discounts_if_cart_does_not_have_discounted_items()
    {
        $product = [
            'name'     => 'T-shirt',
            'quantity' => 1,
        ];

        $currency = 'egp';
        $response = $this->json('post', '/cart', ['products' => [$product],
                                                  'currency' => $currency]);

        $response->assertResponseOk();
        $response->seeJsonStructure(['Subtotal',
                                     'Taxes',
                                     'Total']);
    }
}
