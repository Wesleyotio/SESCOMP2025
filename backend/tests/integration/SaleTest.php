<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Client;

class SaleTest extends TestCase
{
    private Client $client;

    protected function setUp(): void
    {
        $this->client = new Client(['base_uri' => 'http://host.docker.internal']);
    }

    public function test_get_sales()
    {
        $response = $this->client->request('GET', '/sale', ['http_errors' => false]);
        
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $body = json_decode($response->getBody()->getContents(), true);

        $this->assertIsArray($body, 'Body não é um array.');
    }

    public function test_get_sale_by_id()
    {
    
        $response = $this->client->request('GET', '/sale', ['http_errors' => false]);

        $listSalesBody = json_decode($response->getBody()->getContents(), true);

        $lastElement = end($listSalesBody);

        $id = $lastElement['id'];

        $response = $this->client->request(
            'GET',
            "/sale/{$id}",
            [
                'http_errors' => false
            ]
        );
        
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $body = json_decode($response->getBody()->getContents(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($id, $body['id']);
    }

    public function test_get_sale_by_invalid_id()
    {

        $response = $this->client->request('GET', '/sale/0', ['http_errors' => false]);

        $body = (string) $response->getBody()->getContents();
        $dataResponse = json_decode($body, true);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals("Id provider is invalid", $dataResponse['error']);
    }

    public function test_sale_order()
    {
        $response = $this->client->request('GET', '/type', ['http_errors' => false]);

        $listTypesOfProductsBody = json_decode($response->getBody()->getContents(), true);

        $lastElement = end($listTypesOfProductsBody);

        $id =  $lastElement['id'];
        $data = [
            'name'  => 'newProductTestSale' . $id + 1
        ];
        $response = $this->client->request(
            'POST',
            '/types',
            [
                'http_errors' => false,
                'json' => $data
            ]
        );
        
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode(), "Tipo de produto não inserido");
    

        $response = $this->client->request('GET', '/type', ['http_errors' => false]);

        $listTypesOfProductsBody = json_decode($response->getBody()->getContents(), true);

        $lastElement = end($listTypesOfProductsBody);

        $type_product_id =  $lastElement['id'];

        $data = [
            'type_product_id'   => $type_product_id,
            'value'             => '2.5'
        ];
        $response = $this->client->request(
            'POST',
            '/taxes',
            [
                'http_errors' => false,
                'json' => $data
            ]
        );

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode(), "Imposto de Produto não inserido");
        

        $response = $this->client->request('GET', '/product', ['http_errors' => false]);

        $listProductsBody = json_decode($response->getBody()->getContents(), true);

        $lastElement = end($listProductsBody);

        $code =  $lastElement['code'];
        $data = [
            'code'              => 5 + $code,
            'type_product_id'   => $type_product_id,
            'name'              => 'produtoCreateTestSale',
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
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode(), "Produto não inserido");

        $response = $this->client->request('GET', '/product', ['http_errors' => false]);

        $listProductsBody = json_decode($response->getBody()->getContents(), true);

        $lastElement = end($listProductsBody);

        $product_id = $lastElement['id'];


        $data = [
            [
                'product_id'    => $product_id,
                'amount'        => 5
            ]
        ];
        $response = $this->client->request(
            'POST',
            '/sale/order',
            [
                'http_errors' => false,
                'json' => $data
            ]
        );

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode(), "Pedido não solicitado");
       
    
    }

    public function test_sale_pay()
    {
        $response = $this->client->request('GET', '/type', ['http_errors' => false]);

        $listTypesOfProductsBody = json_decode($response->getBody()->getContents(), true);

        $lastElement = end($listTypesOfProductsBody);

        $id =  $lastElement['id'];
        $data = [
            'name'  => 'newProductTestSale' . $id + 1
        ];
        $response = $this->client->request(
            'POST',
            '/types',
            [
                'http_errors' => false,
                'json' => $data
            ]
        );
        
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode(), "Tipo de produto não inserido");
    
        $response = $this->client->request('GET', '/type', ['http_errors' => false]);

        $listTypesOfProductsBody = json_decode($response->getBody()->getContents(), true);

        $lastElement = end($listTypesOfProductsBody);

        $type_product_id =  $lastElement['id'];

        $data = [
            'type_product_id'   => $type_product_id,
            'value'             => '2.5'
        ];
        $response = $this->client->request(
            'POST',
            '/taxes',
            [
                'http_errors' => false,
                'json' => $data
            ]
        );

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode(), "Imposto de Produto não inserido");
        

        $response = $this->client->request('GET', '/product', ['http_errors' => false]);

        $listProductsBody = json_decode($response->getBody()->getContents(), true);

        $lastElement = end($listProductsBody);

        $code =  $lastElement['code'];
        $data = [
            'code'              => 5 + $code,
            'type_product_id'   => $type_product_id,
            'name'              => 'produtoCreateTestSale',
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
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode(), "Produto não inserido");

        $response = $this->client->request('GET', '/product', ['http_errors' => false]);

        $listProductsBody = json_decode($response->getBody()->getContents(), true);

        $lastElement = end($listProductsBody);

        $product_id = $lastElement['id'];


        $data = [
            [
                'product_id'    => $product_id,
                'amount'        => 5
            ]
        ];
        $response = $this->client->request(
            'POST',
            '/sale/pay',
            [
                'http_errors' => false,
                'json' => $data
            ]
        );

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode(), "Pedido não criado");
    }

    public function test_failure_to_sale_order()
    {
        $data = [
            [
                'product_id'    => 2,
            ],
            [
                'amount'        => 22    
            ],
            
        ];
        $response = $this->client->request(
            'POST',
            '/sale/order',
            [
                'http_errors' => false,
                'json' => $data
            ]
        );

        $body = (string) $response->getBody()->getContents();

        $dataResponse = json_decode($body, true);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertStringContainsString("Sales Item has missing fields", $dataResponse['error']);
    }

    public function test_failure_to_sale_pay()
    {
        $data = [
            [
                'product_id'    => -22,
                'amount'        => 5
            ],
            [
                'product_id'    => 25,
                'amount'        => -13    
            ],
            
        ];
        $response = $this->client->request(
            'POST',
            '/sale/pay',
            [
                'http_errors' => false,
                'json' => $data
            ]
        );

        $body = (string) $response->getBody()->getContents();

        $dataResponse = json_decode($body, true);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertStringContainsString("deve ser inteiro maior que zero", $dataResponse['error']);
    }
}
