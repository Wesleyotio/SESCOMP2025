<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Client;

class TaxTest extends TestCase
{
    private Client $client;

    protected function setUp(): void
    {
        $this->client = new Client(['base_uri' => 'http://host.docker.internal']);
    }

    public function test_get_taxes()
    {

        $response = $this->client->request('GET', '/tax', ['http_errors' => false]);
        

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $body = json_decode($response->getBody()->getContents(), true);

        $this->assertNotEmpty($body, 'Body  está vazio.');
        $this->assertIsArray($body, 'Body não é um array.');
    }

    public function test_get_tax_by_id()
    {
    
        $response = $this->client->request('GET', '/tax', ['http_errors' => false]);

        $listTaxesBody = json_decode($response->getBody()->getContents(), true);

        $lastElement = end($listTaxesBody);

        $id = $lastElement['id'];

        $response = $this->client->request(
            'GET',
            "/tax/{$id}",
            [
                'http_errors' => false
            ]
        );
        


        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $body = json_decode($response->getBody()->getContents(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($id, $body['id']);
    }

    public function test_get_tax_by_invalid_id()
    {

        $response = $this->client->request('GET', '/tax/0', ['http_errors' => false]);

        $body = (string) $response->getBody()->getContents();
        $dataResponse = json_decode($body, true);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals("Id provider is invalid", $dataResponse['error']);
    }

    public function test_create_taxes()
    {
        $response = $this->client->request('GET', '/type', ['http_errors' => false]);

        $listTypesOfProductsBody = json_decode($response->getBody()->getContents(), true);

        $lastElement = end($listTypesOfProductsBody);

        $id =  $lastElement['id'];
        $data = [
            'name'  => 'newProduct' . $id + 1
        ];
        $response = $this->client->request(
            'POST',
            '/types',
            [
                'http_errors' => false,
                'json' => $data
            ]
        );
        
        $body = (string) $response->getBody()->getContents();

        $dataResponse = json_decode($body, true);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals("Type of product created successfully!", $dataResponse['message']);

        $response = $this->client->request('GET', '/type', ['http_errors' => false]);

        $listTypesOfProductsBody = json_decode($response->getBody()->getContents(), true);

     
        $lastElement = end($listTypesOfProductsBody);

        $id =  $lastElement['id'];

        $data = [
            'type_product_id'   => $id,
            'value'             => '3.99'
        ];
        $response = $this->client->request(
            'POST',
            '/taxes',
            [
                'http_errors' => false,
                'json' => $data
            ]
        );

        $body = (string) $response->getBody()->getContents();

        $dataResponse = json_decode($body, true);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals("Tax created successfully!", $dataResponse['message']);

    }

    public function test_failure_to_create_taxes()
    {
        $data = [
            'value' =>  '22.75'
        ];
        $response = $this->client->request(
            'POST',
            '/taxes',
            [
                'http_errors' => false,
                'json' => $data
            ]
        );

        $body = (string) $response->getBody()->getContents();

        $dataResponse = json_decode($body, true);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals("Tax has missing fields", $dataResponse['error']);
    }

    public function test_failure_to_create_taxes_with_same_product_id()
    {
        $response = $this->client->request('GET', '/tax', ['http_errors' => false]);

        $listTaxesBody = json_decode($response->getBody()->getContents(), true);

        $lastElement = end($listTaxesBody);

        $product_id = $lastElement['type_product_id'];

        $data = [
            'type_product_id'   => $product_id,
            'value'             => '22.75',
        ];
        $response = $this->client->request(
            'POST',
            '/taxes',
            [
                'http_errors' => false,
                'json' => $data
            ]
        );

        $body = (string) $response->getBody()->getContents();

        $dataResponse = json_decode($body, true);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals("Tax has already been registered, use another type of product", $dataResponse['error']);
    }

    public function test_failure_convert_json_to_create_taxes()
    {
        $data = [
            'type_product_id'   => 'produto1',
            'value'             => '22.75'
        ];
        $response = $this->client->request(
            'POST',
            '/taxes',
            [
                'http_errors' => false,
                'json' => $data
            ]
        );

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function test_update_all_tax_by_id()
    {
        $response = $this->client->request('GET', '/tax', ['http_errors' => false]);

        $listTaxesBody = json_decode($response->getBody()->getContents(), true);

        $lastElement = end($listTaxesBody);

        $id = $lastElement['id'];
        $data = [
            'type_product_id'   => $lastElement['type_product_id'], 
            'value'             => '1.25'
        ];
        $response = $this->client->request(
            'PUT',
            "/tax/{$id}",
            [
                'http_errors'   => false,
                'json'          => $data
            ]
        );

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function test_failure_update_tax_by_id_incorrect()
    {
        $id = -99;
        $data = [
            'type_product_id'   => 1,
            'value'             => '9.95'
        ];
        $response = $this->client->request(
            'PUT',
            "/tax/{$id}",
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

    public function test_failure_update_tax_with_incorrect_fields()
    {
        $response = $this->client->request('GET', '/tax', ['http_errors' => false]);

        $listTaxesBody = json_decode($response->getBody()->getContents(), true);

        $lastElement = end($listTaxesBody);

        $id = $lastElement['id'];
        $data = [
            'type_product_id'   => $lastElement['type_product_id'],
            'values'            => '19.95'
        ];
        $response = $this->client->request(
            'PUT',
            "/tax/{$id}",
            [
                'http_errors'   => false,
                'json'          => $data
            ]
        );

        $body = (string) $response->getBody()->getContents();

        $dataResponse = json_decode($body, true);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals("unknown fields are being passed for tax update", $dataResponse['error']);
    }

    public function test_update_tax_by_id()
    {
        $response = $this->client->request('GET', '/tax', ['http_errors' => false]);

        $listTaxesBody = json_decode($response->getBody()->getContents(), true);

        $lastElement = end($listTaxesBody);

        $id = $lastElement['id'];
        $data = [
            'value'             => '76.83'
        ];
        $response = $this->client->request(
            'PATCH',
            "/tax/{$id}",
            [
                'http_errors'   => false,
                'json'          => $data
            ]
        );

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function test_failure_update_tax_by_id()
    {
        $response = $this->client->request('GET', '/tax', ['http_errors' => false]);

        $listTaxesBody = json_decode($response->getBody()->getContents(), true);

        $lastElement = end($listTaxesBody);

        $id = $lastElement['id'];

        $data = [
            'values'             => 9995.95,
        ];
        $response = $this->client->request(
            'PATCH',
            "/tax/{$id}",
            [
                'http_errors'   => false,
                'json'          => $data
            ]
        );

        $body = (string) $response->getBody()->getContents();

        $dataResponse = json_decode($body, true);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals("unknown fields are being passed for tax update", $dataResponse['error']);
    }

    public function test_delete_tax_by_id()
    {
        $responseListTaxes = $this->client->request('GET', '/tax', ['http_errors' => false]);
        
        $this->assertEquals(Response::HTTP_OK, $responseListTaxes->getStatusCode());

        $bodyList = json_decode($responseListTaxes->getBody()->getContents(), true);

        $lastElement = end($bodyList);

        $id = $lastElement['id'];

        $response = $this->client->request(
            'DELETE',
            "/tax/{$id}",
            [
                'http_errors'   => false
            ]
        );

        $body = (string) $response->getBody()->getContents();

        $dataResponse = json_decode($body, true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals("Tax deleted successfully!", $dataResponse['message']);
    }

    public function test_failure_delete_tax_by_id()
    {
        $id = -99999;

        $response = $this->client->request(
            'DELETE',
            "/tax/{$id}",
            [
                'http_errors'   => false,
            ]
        );

        $body = (string) $response->getBody()->getContents();

        $dataResponse = json_decode($body, true);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals("Id provider is invalid", $dataResponse['error']);
    }
}
