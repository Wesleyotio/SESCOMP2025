<?php

declare(strict_types=1);

namespace Tests\Units\Infrastructure\Web\Controllers;

use App\Application\Dtos\ProductDto;
use App\Application\Exceptions\ProductException;
use App\Application\UseCases\CreateProductUseCase;
use App\Application\UseCases\DeleteProductUseCase;
use App\Application\UseCases\FindAllProductUseCase;
use App\Application\UseCases\FindProductUseCase;
use App\Application\UseCases\UpdateProductUseCase;
use App\Domain\Entities\Product;
use App\Infrastructure\Exceptions\DataBaseException;
use App\Infrastructure\Web\Controllers\ProductController;
use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Symfony\Component\HttpFoundation\Response as ResponseCode;
use PHPUnit\Framework\TestCase;

class ProductControllerTest extends TestCase
{

    /** @var Request&\PHPUnit\Framework\MockObject\MockObject */
    private $request; 

    /** @var Response&\PHPUnit\Framework\MockObject\MockObject */
    private $response; 

    /** @var ProductController&\PHPUnit\Framework\MockObject\MockObject */
    private $productController;

    /** @var CreateProductUseCase&\PHPUnit\Framework\MockObject\MockObject */
    private  $createProductUseCase;

    /** @var FindProductUseCase&\PHPUnit\Framework\MockObject\MockObject */
    private  $findProductUseCase;

    /** @var FindAllProductUseCase&\PHPUnit\Framework\MockObject\MockObject */
    private  $findAllProductUseCase;

    /** @var UpdateProductUseCase&\PHPUnit\Framework\MockObject\MockObject */
    private  $updateProductUseCase;

    /** @var DeleteProductUseCase&\PHPUnit\Framework\MockObject\MockObject */
    private  $deleteProductUseCase;

    public function setUp(): void
    {
        $this->request = $this->createMock(Request::class);
       
        $this->response = $this->getMockBuilder(Response::class)
            ->getMock();

        $this->createProductUseCase = $this->createMock(CreateProductUseCase::class);
        $this->findProductUseCase = $this->createMock(FindProductUseCase::class);
        $this->findAllProductUseCase = $this->createMock(FindAllProductUseCase::class);
        $this->updateProductUseCase = $this->createMock(UpdateProductUseCase::class);
        $this->deleteProductUseCase = $this->createMock(DeleteProductUseCase::class);

        $this->productController = new ProductController(
            $this->createProductUseCase,
            $this->findProductUseCase,
            $this->findAllProductUseCase,
            $this->updateProductUseCase,
            $this->deleteProductUseCase
        );
    }

    public function test_create_product_way_api()
    {
        $productData = [
            'code'  => 125,
            'type_product_id' => 2,
            'name'  => 'productTest',
            'value' => '16.25'
        ];

        $productDTO = new ProductDto(
            $productData['code'],
            $productData['type_product_id'],
            $productData['name'],
            $productData['value'],
        );

        $encodedEntityCreated = json_encode(
            [ 'message' => 'Product created successfully!'],
            JSON_THROW_ON_ERROR
        );

        $this->request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn($productData);
    
        $streamMock = $this->createMock(\Psr\Http\Message\StreamInterface::class);
        $streamMock->expects($this->once())
            ->method('write')
            ->with($encodedEntityCreated);

        $this->response->expects($this->any())
            ->method('getHeaders')
            ->willReturn( ['Content-Type' => ['application/json']]);

        $this->response->expects($this->any())
            ->method('getBody')
            ->willReturn($streamMock);

        $this->response->expects($this->any())
            ->method('getStatusCode')
            ->willReturn(ResponseCode::HTTP_CREATED);
            
        $this->response->expects($this->any())
            ->method('withHeader')
            ->willReturnSelf();

        $this->response->expects($this->any())
            ->method('withStatus')
            ->willReturnSelf();
        
        $this->createProductUseCase->expects($this->once())
            ->method('action')
            ->with($productDTO);
        
        $responseTest = $this->productController->create($this->request, $this->response);
        

        $this->assertEquals(ResponseCode::HTTP_CREATED, $responseTest->getStatusCode());

    }

    #[DataProvider('failCreateProvider')]
    public function test_fail_create_product_way_api($productData, $code , $message)
    {
        $encodedResponse = json_encode(
            [ 'error' => $message],
            JSON_THROW_ON_ERROR
        );


        $this->request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn($productData);
    
        $streamMock = $this->createMock(\Psr\Http\Message\StreamInterface::class);
        $streamMock->expects($this->once())
            ->method('write')
            ->with($encodedResponse);

        $this->response->expects($this->any())
            ->method('getHeaders')
            ->willReturn( ['Content-Type' => ['application/json']]);

        $this->response->expects($this->any())
            ->method('getBody')
            ->willReturn($streamMock);

        $this->response->expects($this->any())
            ->method('getStatusCode')
            ->willReturn($code);
            
        $this->response->expects($this->any())
            ->method('withHeader')
            ->willReturnSelf();

        $this->response->expects($this->any())
            ->method('withStatus')
            ->willReturnSelf();
        
        $this->createProductUseCase->expects($this->never())
            ->method('action');
        
        $responseTest = $this->productController->create($this->request, $this->response);
        
       
        $this->assertEquals($code, $responseTest->getStatusCode());
    }

    #[DataProvider('failCreateProviderForUseCase')]
    public function test_fail_create_product_for_use_case_way_api($productData, $code , $exception ,$message)
    {
        $encodedResponse = json_encode(
            [ 'error' => $message],
            JSON_THROW_ON_ERROR
        );


        $this->request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn($productData);
    
        $streamMock = $this->createMock(\Psr\Http\Message\StreamInterface::class);
        $streamMock->expects($this->once())
            ->method('write')
            ->with($encodedResponse);

        $this->response->expects($this->any())
            ->method('getHeaders')
            ->willReturn( ['Content-Type' => ['application/json']]);

        $this->response->expects($this->any())
            ->method('getBody')
            ->willReturn($streamMock);

        $this->response->expects($this->any())
            ->method('getStatusCode')
            ->willReturn($code);
            
        $this->response->expects($this->any())
            ->method('withHeader')
            ->willReturnSelf();

        $this->response->expects($this->any())
            ->method('withStatus')
            ->willReturnSelf();
        
        $this->createProductUseCase->expects($this->once())
            ->method('action')
            ->willThrowException( new $exception($message));
        
        $responseTest = $this->productController->create($this->request, $this->response);
        
       
        $this->assertEquals($code, $responseTest->getStatusCode());
    }

    public function test_search_product_by_id_way_api()
    {
        $args = [
            "id" => 3
        ];

        $arrayResponse = [
            'id' => 3,
            'code' => 12,
            'type_product_id' => 22,
            'name' => "nameTest",
            'value' => '13825',
            'created_at' => \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', "1999-05-02 22:20:25" ),
            'updated_at' => \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', "2021-05-02 22:20:25" )
        ];

        $product = new Product(
            $arrayResponse['id'],
            $arrayResponse['code'],
            $arrayResponse['type_product_id'],
            $arrayResponse['name'],
            $arrayResponse['value'],
            $arrayResponse['created_at'],
            $arrayResponse['updated_at'],
            
        );

        $productDTO = ProductDto::fromEntity($product);
        
        $encodedEntity = json_encode(
            $productDTO->toArray(),
            JSON_THROW_ON_ERROR
        );

        $streamMock = $this->createMock(\Psr\Http\Message\StreamInterface::class);
        $streamMock->expects($this->once())
            ->method('write')
            ->with($encodedEntity);

        $this->response->expects($this->any())
            ->method('getHeaders')
            ->willReturn( ['Content-Type' => ['application/json']]);

        $this->response->expects($this->any())
            ->method('getBody')
            ->willReturn($streamMock);

        $this->response->expects($this->any())
            ->method('getStatusCode')
            ->willReturn(ResponseCode::HTTP_OK);
            
        $this->response->expects($this->any())
            ->method('withHeader')
            ->willReturnSelf();

        $this->response->expects($this->any())
            ->method('withStatus')
            ->willReturnSelf();

        $this->findProductUseCase->expects($this->once())
            ->method('action')
            ->with($args['id'])
            ->willReturn($productDTO);

        $responseTest = $this->productController->findById($this->request, $this->response, $args);

        $this->assertEquals(ResponseCode::HTTP_OK, $responseTest->getStatusCode());
    }

    #[DataProvider('failFindProviderForId')]
    public function test_fail_search_product_by_id_way_api($args, $code, $message)
    {
        
        $encodedEntity = json_encode(
            ["error" => $message],
            JSON_THROW_ON_ERROR
        );

        $streamMock = $this->createMock(\Psr\Http\Message\StreamInterface::class);
        $streamMock->expects($this->once())
            ->method('write')
            ->with($encodedEntity);

        $this->response->expects($this->any())
            ->method('getHeaders')
            ->willReturn( ['Content-Type' => ['application/json']]);

        $this->response->expects($this->any())
            ->method('getBody')
            ->willReturn($streamMock);

        $this->response->expects($this->any())
            ->method('getStatusCode')
            ->willReturn($code);
            
        $this->response->expects($this->any())
            ->method('withHeader')
            ->willReturnSelf();

        $this->response->expects($this->any())
            ->method('withStatus')
            ->willReturnSelf();

        $this->findProductUseCase->expects($this->never())
            ->method('action');
            

        $responseTest = $this->productController->findById($this->request, $this->response, $args);

        $this->assertEquals($code, $responseTest->getStatusCode());
    }

    #[DataProvider('failFindProviderForId')]
    public function test_fail_search_product_by_id_in_use_case_way_api($args, $code, $message)
    {
        
        $encodedEntity = json_encode(
            ["error" => $message],
            JSON_THROW_ON_ERROR
        );

        $streamMock = $this->createMock(\Psr\Http\Message\StreamInterface::class);
        $streamMock->expects($this->once())
            ->method('write')
            ->with($encodedEntity);

        $this->response->expects($this->any())
            ->method('getHeaders')
            ->willReturn( ['Content-Type' => ['application/json']]);

        $this->response->expects($this->any())
            ->method('getBody')
            ->willReturn($streamMock);

        
            
        $this->response->expects($this->any())
            ->method('withHeader')
            ->willReturnSelf();

        $this->response->expects($this->any())
            ->method('withStatus')
            ->willReturnSelf();

        $this->findProductUseCase->expects($this->never())
            ->method('action');
            

        $responseTest = $this->productController->findById($this->request, $this->response, $args);

        $this->assertEquals($code, $responseTest->getStatusCode());
    }


    public static function failCreateProvider(): array
    {

        $dataNotArray = "this is string";
    
        $arrayWithoutCode = [
            'type_product_id' => 1,
            'name' => "product",
            'value' => 16.77
        ];
       
        $arrayWithoutType = [
            'code' => 23,
            'name' => "product",
            'value' => 16.77
        ];

        $arrayWithoutName = [
            'code' => 23,
            'type_product_id' => 1,
            'value' => 16.77
        ];
       
        $arrayWithoutValue = [
            'code' => 23,
            'type_product_id' => 1,
            'name' => "product"
        ];

        $arrayDataPdoException = [
            'code'  => 23,
            'type_product_id' => 1,
            'name'  => "product",
            'value' => 16.77
            
        ];

     
        $arrayCodeNegative = [
            'code' => -6,           
            'type_product_id' => 1,
            'name' => "product",
            'value' => 16.77
        ];

       
        $arrayTypeZero = [
            'code' => 23,
            'type_product_id' => 0, 
            'name' => "product",
            'value' => 16.77
        ];

       
        $arrayNameEmpty = [
            'code' => 23,
            'type_product_id' => 1,
            'name' => "",    
            'value' => 16.77
        ];

       
        $arrayValueLess = [
            'code' => 23,
            'type_product_id' => 1,
            'name' => "product",
            'value' => -1      
        ];

        return [
            'when_invalid_data' => ['productData' => $dataNotArray , 'code' => ResponseCode::HTTP_BAD_REQUEST ,'message' => "Expected array, got string"],
            'when_missing_code' => ['productData' => $arrayWithoutCode , 'code' => ResponseCode::HTTP_BAD_REQUEST, 'message' => "Product has missing fields"], 
            'when_missing_type' => ['productData' => $arrayWithoutType , 'code' => ResponseCode::HTTP_BAD_REQUEST ,'message' => "Product has missing fields"], 
            'when_missing_name' => ['productData' => $arrayWithoutName , 'code' => ResponseCode::HTTP_BAD_REQUEST ,'message' => "Product has missing fields"], 
            'when_missing_value' => ['productData' => $arrayWithoutValue , 'code' => ResponseCode::HTTP_BAD_REQUEST ,'message' => "Product has missing fields"], 
            'when_invalid_code' => ['productData' => $arrayCodeNegative , 'code' => ResponseCode::HTTP_BAD_REQUEST,  'message' => "O parâmetro code: {$arrayCodeNegative['code']} precisa ser maior que zero"], 
            'when_invalid_type' => ['productData' => $arrayTypeZero , 'code' => ResponseCode::HTTP_BAD_REQUEST, 'message' => "O parâmetro type_product_id: {$arrayTypeZero['type_product_id']} precisa ser maior que zero"], 
            'when_invalid_name' => ['productData' => $arrayNameEmpty , 'code' => ResponseCode::HTTP_BAD_REQUEST, 'message' => "O parâmetro name: {$arrayNameEmpty['name']} não pode ser vazio"], 
            'when_invalid_value' => ['productData' => $arrayValueLess , 'code' => ResponseCode::HTTP_BAD_REQUEST, 'message' => "O parâmetro value: {$arrayValueLess['value']} não pode ser negativo"] 
            // 'when_throw_pdoException' => ['productData' => $arrayDataPdoException , 'code' => ResponseCode::HTTP_INTERNAL_SERVER_ERROR,  'exception' => TypeError::class ,'message' => "SQLSTATE[22003]: Numeric value out of range: 7 ERROR:"]
        ];
    }

    public static function failCreateProviderForUseCase(): array
    {

        $arrayDataCodeDuplicate = [
            'code'  => 23,
            'type_product_id' => 1,
            'name'  => "product",
            'value' => 16.77
            
        ];
        $arrayDataPdoException = [
            'code'  => 299999999999999999999,
            'type_product_id' => 1,
            'name'  => "product",
            'value' => 16.77
            
        ];

        return [
            'when_code_duplicate' => ['productData' => $arrayDataCodeDuplicate , 'code' => ResponseCode::HTTP_BAD_REQUEST , 'exception' => ProductException::class, 'message' => "Code has already been registered, use another code"],
            'when_throw_pdoException' => ['productData' => $arrayDataPdoException , 'code' => ResponseCode::HTTP_INTERNAL_SERVER_ERROR,  'exception' => DataBaseException::class ,'message' => "SQLSTATE[22003]: Numeric value out of range: 7 ERROR:"]
        ];
    }

    public static function failFindProviderForId(): array
    {
       
        $argsWithoutId = [
            'code' => 22
        ];

        $argsLessZeroId = [
            'id' => -6
        ];

        return [ 
            'when_args_is_without_id' => [ 'args' => $argsWithoutId, 'code' => ResponseCode::HTTP_BAD_REQUEST, 'message' => "Expected key of value id"],
            'when_args_is_less_zero_id' => [ 'args' => $argsLessZeroId, 'code' => ResponseCode::HTTP_BAD_REQUEST, 'message' => "Id provider is invalid"]
        ];
    }
   
}