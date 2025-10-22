<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Client;

class TypeTest extends TestCase
{
    private Client $client;

    protected function setUp(): void
    {
        $this->client = new Client(['base_uri' => 'http://host.docker.internal']);
    }

    public function test_get_types()
    {

        $response = $this->client->request('GET', '/type', ['http_errors' => false]);
        

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $body = json_decode($response->getBody()->getContents(), true);

        $this->assertNotEmpty($body, 'Body  está vazio.');
        $this->assertIsArray($body, 'Body não é um array.');
    }

    public function test_get_types_by_id()
    {
    
        $response = $this->client->request('GET', '/type', ['http_errors' => false]);

        $listTypesBody = json_decode($response->getBody()->getContents(), true);

        $lastElement = end($listTypesBody);

        $id = $lastElement['id'];

        $response = $this->client->request(
            'GET',
            "/type/{$id}",
            [
                'http_errors' => false
            ]
        );
        


        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $body = json_decode($response->getBody()->getContents(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($id, $body['id']);
    }

    public function test_get_type_by_invalid_id()
    {

        $response = $this->client->request('GET', '/type/0', ['http_errors' => false]);

        $body = (string) $response->getBody()->getContents();
        $dataResponse = json_decode($body, true);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals("Id provider is invalid", $dataResponse['error']);
    }

    public function test_create_types()
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
    }

    public function test_failure_to_create_types()
    {
        $data = [
            'value' =>  22.75
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

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    $this->assertEquals("Type of product has missing fields", $dataResponse['error']);
    }

    public function test_failure_to_create_types_with_same_name()
    {
        $response = $this->client->request('GET', '/type', ['http_errors' => false]);

        $listTypesBody = json_decode($response->getBody()->getContents(), true);

        $lastElement = end($listTypesBody);

        $name = $lastElement['name'];

        $data = [
            'name'   => $name
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

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals("Name has already been registered, use another name of type of product", $dataResponse['error']);
    }

    public function test_failure_convert_json_to_create_types()
    {
        $data = [
            'names'   => 'produto1'
        ];
        $response = $this->client->request(
            'POST',
            '/types',
            [
                'http_errors' => false,
                'json' => $data
            ]
        );

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    
    public function test_failure_update_type_by_id_incorrect()
    {
        $id = -99;
        $data = [
            'name'  => 'ProductFail'
        ];
        $response = $this->client->request(
            'PATCH',
            "/type/{$id}",
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

    public function test_failure_update_type_with_incorrect_fields()
    {
        $response = $this->client->request('GET', '/type', ['http_errors' => false]);

        $listTypesBody = json_decode($response->getBody()->getContents(), true);

        $lastElement = end($listTypesBody);

        $id = $lastElement['id'];
        $data = [
            'names' => 'ProductNameFail'
        ];
        $response = $this->client->request(
            'PATCH',
            "/type/{$id}",
            [
                'http_errors'   => false,
                'json'          => $data
            ]
        );

        $body = (string) $response->getBody()->getContents();

        $dataResponse = json_decode($body, true);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals("Type of product has missing fields", $dataResponse['error']);
    }

    public function test_update_type_by_id()
    {
        $response = $this->client->request('GET', '/type', ['http_errors' => false]);

        $listTypesBody = json_decode($response->getBody()->getContents(), true);

        $lastElement = end($listTypesBody);

        $id = $lastElement['id'];
        $data = [
            'name'  => $lastElement['name'] . 'UpdatePatch'
        ];
        $response = $this->client->request(
            'PATCH',
            "/type/{$id}",
            [
                'http_errors'   => false,
                'json'          => $data
            ]
        );

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function test_failure_update_type_by_id()
    {
        $response = $this->client->request('GET', '/type', ['http_errors' => false]);

        $listTypesBody = json_decode($response->getBody()->getContents(), true);

        $lastElement = end($listTypesBody);

        $id = $lastElement['id'];

        $data = [
            'name'  => 9995.95,
        ];
        $response = $this->client->request(
            'PATCH',
            "/type/{$id}",
            [
                'http_errors'   => false,
                'json'          => $data
            ]
        );

        $body = (string) $response->getBody()->getContents();

        $dataResponse = json_decode($body, true);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals("Expected string, got double", $dataResponse['error']);
    }

    public function test_delete_type_by_id()
    {
        $responseListTypes = $this->client->request('GET', '/type', ['http_errors' => false]);
        
        $this->assertEquals(Response::HTTP_OK, $responseListTypes->getStatusCode());

        $bodyList = json_decode($responseListTypes->getBody()->getContents(), true);

        $lastElement = end($bodyList);

        $id = $lastElement['id'];

        $response = $this->client->request(
            'DELETE',
            "/type/{$id}",
            [
                'http_errors'   => false
            ]
        );

        $body = (string) $response->getBody()->getContents();

        $dataResponse = json_decode($body, true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals("Type deleted successfully!", $dataResponse['message']);
    }

    public function test_failure_delete_type_by_id()
    {
        $id = -99999;

        $response = $this->client->request(
            'DELETE',
            "/type/{$id}",
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
