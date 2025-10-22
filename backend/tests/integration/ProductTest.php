<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Client;

class ProductTest extends TestCase
{
    private Client $client;

    protected function setUp(): void
    {
        $this->client = new Client(['base_uri' => 'http://host.docker.internal']);
    }

    public function test_get_products()
    {

        $response = $this->client->request('GET', '/product', ['http_errors' => false]);
        
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $body = json_decode($response->getBody()->getContents(), true);

        $this->assertNotEmpty($body, 'Body  está vazio.');
        $this->assertIsArray($body, 'Body não é um array.');
    }

    public function test_get_product_by_id()
    {
    
        $response = $this->client->request('GET', '/product', ['http_errors' => false]);

        $listProductsBody = json_decode($response->getBody()->getContents(), true);

        $lastElement = end($listProductsBody);

        $id = $lastElement['id'];

        $response = $this->client->request(
            'GET',
            "/product/{$id}",
            [
                'http_errors' => false
            ]
        );
        


        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $body = json_decode($response->getBody()->getContents(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($id, $body['id']);
    }

    public function test_get_product_by_invalid_id()
    {

        $response = $this->client->request('GET', '/product/0', ['http_errors' => false]);

        $body = (string) $response->getBody()->getContents();
        $dataResponse = json_decode($body, true);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals("Id provider is invalid", $dataResponse['error']);
    }

    public function test_create_products()
    {
        $response = $this->client->request('GET', '/product', ['http_errors' => false]);

        $listProductsBody = json_decode($response->getBody()->getContents(), true);

        $lastElement = end($listProductsBody);

        $code =  $lastElement['code'];
        $data = [
            'code'              => 5 + $code,
            'type_product_id'   => 1,
            'name'              => 'produtoCreate',
            'value'             => 22.75,
        ];
        $response = $this->client->request(
            'POST',
            '/products',
            [
                'http_errors' => false,
                'json' => $data
            ]
        );
        
        $body = (string) $response->getBody()->getContents();

        $dataResponse = json_decode($body, true);


        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals("Product created successfully!", $dataResponse['message']);
    }

    public function test_failure_to_create_products()
    {
        $data = [
            'code'              => 12,
            // 'type_product_id'   => 1,
            'name'              => 'produto1',
            'value'             => 22.75,
        ];
        $response = $this->client->request(
            'POST',
            '/products',
            [
                'http_errors' => false,
                'json' => $data
            ]
        );

        $body = (string) $response->getBody()->getContents();

        $dataResponse = json_decode($body, true);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals("Product has missing fields", $dataResponse['error']);
    }

    public function test_failure_to_create_products_with_same_code()
    {
        $response = $this->client->request('GET', '/product', ['http_errors' => false]);

        $listProductsBody = json_decode($response->getBody()->getContents(), true);

        $lastElement = end($listProductsBody);

        $code = $lastElement['code'];

        $data = [
            'code'              => $code,
            'type_product_id'   => 1,
            'name'              => 'produto1',
            'value'             => 22.75,
        ];
        $response = $this->client->request(
            'POST',
            '/products',
            [
                'http_errors' => false,
                'json' => $data
            ]
        );

        $body = (string) $response->getBody()->getContents();

        $dataResponse = json_decode($body, true);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals("Code has already been registered, use another code", $dataResponse['error']);
    }

    public function test_failure_convert_json_to_create_products()
    {
        $data = [
            'code'              => "tsest",
            'type_product_id'   => 1,
            'name'              => 'produto1',
            'value'             => 22.75,
        ];
        $response = $this->client->request(
            'POST',
            '/products',
            [
                'http_errors' => false,
                'json' => $data
            ]
        );



        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        // $this->assertEquals("Product has missing fields", $dataResponse['error']);
    }

    public function test_update_all_product_by_id()
    {
        $response = $this->client->request('GET', '/product', ['http_errors' => false]);

        $listProductsBody = json_decode($response->getBody()->getContents(), true);

        $lastElement = end($listProductsBody);

        $id = $lastElement['id'];
        $data = [
            'code'              => 7 + $lastElement['code'],
            'type_product_id'   => 1,
            'name'              => 'produtoPUT',
            'value'             => '99.95',
        ];
        $response = $this->client->request(
            'PUT',
            "/product/{$id}",
            [
                'http_errors'   => false,
                'json'          => $data
            ]
        );

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function test_failure_update_all_product_by_id_incorrect()
    {
        $id = -99;
        $data = [
            'code'              => 16,
            'type_product_id'   => 1,
            'name'              => 'produtoPUT',
            'value'             => 99.95,
        ];
        $response = $this->client->request(
            'PUT',
            "/product/{$id}",
            [
                'http_errors'   => false,
                'json'          => $data
            ]
        );

        $body = (string) $response->getBody()->getContents();

        $dataResponse = json_decode($body, true);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals("Id provider is invalid", $dataResponse['error']);
    }

    public function test_failure_update_all_product_with_incorrect_fields()
    {
        $response = $this->client->request('GET', '/product', ['http_errors' => false]);

        $listProductsBody = json_decode($response->getBody()->getContents(), true);

        $lastElement = end($listProductsBody);

        $id = $lastElement['id'];
        $data = [
            'codess'              => 18,
            'type_product_id'   => 1,
            'name'              => 'produtoPUT',
            'value'             => 99.95,
        ];
        $response = $this->client->request(
            'PUT',
            "/product/{$id}",
            [
                'http_errors'   => false,
                'json'          => $data
            ]
        );

        $body = (string) $response->getBody()->getContents();

        $dataResponse = json_decode($body, true);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals("unknown fields are being passed for product update", $dataResponse['error']);
    }

    public function test_update_product_by_id()
    {
        $response = $this->client->request('GET', '/product', ['http_errors' => false]);

        $listProductsBody = json_decode($response->getBody()->getContents(), true);

        $lastElement = end($listProductsBody);

        $id = $lastElement['id'];
        $data = [
            'name'              => 'produtoPATCH',
            'value'             => '199.95',
        ];
        $response = $this->client->request(
            'PATCH',
            "/product/{$id}",
            [
                'http_errors'   => false,
                'json'          => $data
            ]
        );

        

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function test_failure_update_product_by_id()
    {
        $response = $this->client->request('GET', '/product', ['http_errors' => false]);

        $listProductsBody = json_decode($response->getBody()->getContents(), true);

        $lastElement = end($listProductsBody);

        $id = $lastElement['id'];

        $data = [
            'codess'              => 9999,
            'name'              => 'produtoPATCH',
            'value'             => '199.95',
        ];
        $response = $this->client->request(
            'PATCH',
            "/product/{$id}",
            [
                'http_errors'   => false,
                'json'          => $data
            ]
        );

        $body = (string) $response->getBody()->getContents();

        $dataResponse = json_decode($body, true);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals("unknown fields are being passed for product update", $dataResponse['error']);
    }

    public function test_delete_product_by_id()
    {
        $response = $this->client->request('GET', '/product', ['http_errors' => false]);

        $listProductsBody = json_decode($response->getBody()->getContents(), true);

        $lastElement = end($listProductsBody);

       
        $data = [
            'code'              => 11 + $lastElement['code'],
            'type_product_id'   => 1,
            'name'              => 'produtoParaDelete',
            'value'             => 999.99,
        ];
        $response = $this->client->request(
            'POST',
            '/products',
            [
                'http_errors' => false,
                'json' => $data
            ]
        );

        $body = (string) $response->getBody()->getContents();

        $dataResponse = json_decode($body, true);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $responseListProducts = $this->client->request('GET', '/product', ['http_errors' => false]);
        

        $this->assertEquals(Response::HTTP_OK, $responseListProducts->getStatusCode());

        $bodyList = json_decode($responseListProducts->getBody()->getContents(), true);

        $lastElement = end($bodyList);

        $id = $lastElement['id'];

        $response = $this->client->request(
            'DELETE',
            "/product/{$id}",
            [
                'http_errors'   => false
            ]
        );

        $body = (string) $response->getBody()->getContents();

        $dataResponse = json_decode($body, true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals("Product deleted successfully!", $dataResponse['message']);
    }

    public function test_failure_delete_product_by_id()
    {
        $id = 99999;

        $response = $this->client->request(
            'DELETE',
            "/product/{$id}",
            [
                'http_errors'   => false,
            ]
        );

        $body = (string) $response->getBody()->getContents();

        $dataResponse = json_decode($body, true);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals("id does not match any item", $dataResponse['error']);
    }
}
