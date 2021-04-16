<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Product;

class ProductsTest extends ApiTestCase
{

    public function testGetProducts(): void
    {

        $response = static::createClient()->request('GET', '/api/products');

        $this->assertResponseIsSuccessful();
        // Asserts that the returned content type is JSON-LD (the default)
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        // Asserts that the returned JSON is a superset of this one
        $this->assertJsonContains([
            '@context' => '/api/contexts/Product',
            '@id' => '/api/products',
            '@type' => 'hydra:Collection',
            //'hydra:totalItems' => 5 //total items

        ]);

        // Because test fixtures are automatically loaded between each test, you can assert on them
        //$this->assertCount(3, $response->toArray()['hydra:member']);  //total per page

        // Asserts that the returned JSON is validated by the JSON Schema generated for this resource by API Platform
        // This generated JSON Schema is also used in the OpenAPI spec!
        $this->assertMatchesResourceCollectionJsonSchema(Product::class);
    }

    public function testCreateDeleteProduct(): void
    {
        $response = self::createClient()->request(
            'POST',
            '/api/products',
            ['json' => [
                'name' => 'product test',
                'description' => 'product test description',
                'price' => 10.00,
                'stock' =>5
            ]]
        );

        $this->assertResponseIsSuccessful();
        $productId = json_decode($response->getContent())->id;


        $response = static::createClient()->request('GET', '/api/products/'.$productId );
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/api/contexts/Product',
            '@id' => '/api/products/'.$productId ,
            '@type' => 'Product',
            'name' => 'product test'

        ]);

        $response = static::createClient()->request(
            'DELETE',
            '/api/products/'.$productId
        );
        $this->assertResponseIsSuccessful();

        $response = static::createClient()->request('GET', '/api/products/'.$productId );


        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            '@type' => 'hydra:Error',
            'hydra:description' => 'Not Found'
        ]);


    }

//    public function testDeleteProduct(): void
//    {
//
//        echo('/api/products/'.$this->productId);
//        echo('/api/products/'.$this->productId);
//        echo('/api/products/'.$this->productId);
//        echo('/api/products/'.$this->productId);
//        echo('/api/products/'.$this->productId);
//        echo('/api/products/'.$this->productId);
//        echo('/api/products/'.$this->productId);
//        echo('/api/products/'.$this->productId);
//
//
//        $response = static::createClient()->request('GET', '/api/products/'.$this->productId );
//
//
//        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
//        $this->assertResponseStatusCodeSame(404);
//        $this->assertJsonContains([
//            '@type' => 'hydra:Error',
//            'hydra:description' => 'Not Found'
//        ]);
//
//    }





}
