<?php

declare(strict_types=1);

namespace Tests\Units\Drivers\Persistense;

use App\Drivers\Persistence\DatabaseProduct;
use App\Infrastructure\Exceptions\DataBaseException;
use App\Infrastructure\Persistence\DatabaseProductInterface;
use PDO;
use PDOStatement;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class DatabaseProductTest extends TestCase
{
    /** @var DatabaseProductInterface&\PHPUnit\Framework\MockObject\MockObject */
    // private $databaseProductInterface;
    
    /** @var DatabaseProduct&\PHPUnit\Framework\MockObject\MockObject */
    private $databaseProduct;

    /** @var PDO&\PHPUnit\Framework\MockObject\MockObject */
    private $pdo;

     /** @var PDOStatement&\PHPUnit\Framework\MockObject\MockObject */
    private $pdoStmt;
    

    public function setUp():void 
    {
       
        $this->pdo = $this->createMock(PDO::class);

        $this->pdoStmt = $this->createMock(PDOStatement::class);

        $this->databaseProduct = $this->getMockBuilder(DatabaseProduct::class)
            ->onlyMethods(['connect'])
            ->getMock();
            
        $this->databaseProduct->method('connect')
            ->willReturn($this->pdo);
       
    }


    public function test_create_product_in_database()
    {
        
        $validProductData = json_encode([
            'code' => 123,
            'type_product_id' => 1,
            'name' => 'Test Product',
            'value' => 99.99
        ]);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->willReturn($this->pdoStmt);

        $this->pdoStmt->expects($this->exactly(6))
            ->method('bindParam');

        $this->pdoStmt->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $this->databaseProduct->create($validProductData);

        $this->assertTrue(true);
    }

    public function test_fail_with_create_product_in_database()
    {
        $validProductData = json_encode([
            'code' => 123,
            'type_product_id' => 1,
            'name' => 'Test Product',
            'value' => 99.99
        ]);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->willReturn($this->pdoStmt);

        
        

        $this->expectException(DataBaseException::class);

        $this->databaseProduct->create($validProductData);

    }

    public function test_find_product_for_code_in_database()
    {
        $code = 123;

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->willReturn($this->pdoStmt);


        $this->pdoStmt->expects($this->exactly(1))
        ->method('bindParam');

        $this->pdoStmt->expects($this->once())
        ->method('execute')
        ->willReturn(true);

        $result = $this->databaseProduct->selectByCode($code);

        $this->assertTrue(!$result);

    }

    public function test_not_found_product_for_code_in_database()
    {
        $code = 999999999;

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->willReturn($this->pdoStmt);

        $this->pdoStmt->expects($this->exactly(1))
        ->method('bindParam');

        $this->pdoStmt->expects($this->once())
        ->method('execute')
        ->willReturn(true);

        $result = $this->databaseProduct->selectByCode($code);

        $this->assertFalse($result);

    }

    public function test_find_product_for_id_in_database()
    {
        $product_id = 25;
        $array_product = [
            'id'    => 25,
            'code'  => 33,
            'type_product_id' => 1,
            'name'  => "productName",
            'value' => 16.75,
            'created_at' => '2025-01-30 18:38:35',
            'updated_at' => '2025-01-30 18:38:35'
        ];

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->willReturn($this->pdoStmt);

        $this->pdoStmt->expects($this->exactly(1))
        ->method('bindParam');

        $this->pdoStmt->expects($this->once())
        ->method('execute')
        ->willReturn(true);

        $this->pdoStmt->expects($this->once())
        ->method('fetch')
        ->with(PDO::FETCH_ASSOC)
        ->willReturn($array_product);

        $result = $this->databaseProduct->selectById($product_id);

        $this->assertIsArray($result);
    }

    public function test_not_found_product_for_id_in_database()
    {
        $product_id = 99;
        
        $this->pdo->expects($this->once())
            ->method('prepare')
            ->willReturn($this->pdoStmt);

        $this->pdoStmt->expects($this->exactly(1))
        ->method('bindParam');

        $this->pdoStmt->expects($this->once())
        ->method('execute')
        ->willReturn(true);

        $this->pdoStmt->expects($this->once())
        ->method('fetch')
        ->with(PDO::FETCH_ASSOC)
        ->willReturn(null);

        $result = $this->databaseProduct->selectById($product_id);

        $this->assertEquals(null,$result);
    }

    public function test_select_all_products_in_database()
    {
       $keys = ['id', 'code', 'type_product_id', 'name', 'value', 'created_at', 'updated_at'];
       $array_products = [
            [
                'id'    => 1,
                'code'  => 2,
                'type_product_id' => 1,
                'name'  => "productName",
                'value' => 17.87,
                'created_at' => '2025-01-30 18:38:35',
                'updated_at' => '2025-01-30 18:38:35'
            ],
            [
                'id'    => 2,
                'code'  => 3,
                'type_product_id' => 1,
                'name'  => "productName2",
                'value' => 15.87,
                'created_at' => '2025-03-30 18:39:35',
                'updated_at' => '2025-03-30 18:39:35'
            ]
        ];
        
        $this->pdo->expects($this->once())
            ->method('prepare')
            ->willReturn($this->pdoStmt);

      
        $this->pdoStmt->expects($this->once())
        ->method('execute')
        ->willReturn(true);

        $this->pdoStmt->expects($this->once())
        ->method('fetchAll')
        ->with(PDO::FETCH_DEFAULT)
        ->willReturn($array_products);

        $result = $this->databaseProduct->selectAll();
    
        $this->assertArrayIsEqualToArrayOnlyConsideringListOfKeys($array_products, $result, $keys);
    }

    #[DataProvider('updateDataProvider')]
    public function test_update_product_in_database($num, $id, $productAttributes, $value)
    {
        
        $this->pdo->expects($this->once())
            ->method('prepare')
            ->willReturn($this->pdoStmt);

        $this->pdoStmt->expects($this->exactly($num))
            ->method('bindValue');

        $this->pdoStmt->expects($this->exactly(1))
            ->method('bindParam');

        $this->pdoStmt->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $this->pdoStmt->expects($this->once())
            ->method('rowCount')
            ->willReturn($value);


        $result = $this->databaseProduct->update($id, $productAttributes);

        $this->assertEquals($value, $result);
    }

    #[DataProvider('updateFailDataProvider')]
    public function test_fail_update_product_in_database($num, $id, $productAttributes, $message)
    {
        
        $this->pdo->expects($this->once())
            ->method('prepare')
            ->willReturn($this->pdoStmt);

        $this->pdoStmt->expects($this->exactly($num))
            ->method('bindValue');

        $this->pdoStmt->expects($this->exactly(1))
            ->method('bindParam');

        $this->pdoStmt->expects($this->once())
            ->method('execute')
            ->willThrowException(new \PDOException($message));

        $this->expectException(DataBaseException::class);

        $this->databaseProduct->update($id, $productAttributes);

    }

    #[DataProvider('deleteDataProvider')]
    public function test_delete_product_in_database( $id, $value)
    {
        
        $this->pdo->expects($this->once())
            ->method('prepare')
            ->willReturn($this->pdoStmt);

        $this->pdoStmt->expects($this->exactly(1))
            ->method('bindValue');

        $this->pdoStmt->expects($this->exactly(1))
            ->method('bindParam');

        $this->pdoStmt->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $this->pdoStmt->expects($this->once())
            ->method('rowCount')
            ->willReturn($value);


        $result = $this->databaseProduct->delete($id);

        $this->assertEquals($value, $result);
    }

    public function test_fail_delete_product_in_database()
    {
        $id = 9999999999999999;

        $this->pdo->expects($this->once())
        ->method('prepare')
        ->willReturn($this->pdoStmt);

        $this->pdoStmt->expects($this->once())
            ->method('bindValue')
            ->with(':id', $id, PDO::PARAM_INT);;

        $this->pdoStmt->expects($this->exactly(1))
            ->method('bindParam');

        $this->pdoStmt->expects($this->once())
            ->method('execute')
            ->willThrowException(new \PDOException('SQLSTATE[22003]: Numeric value out of range: 7 ERROR:'));
        
            
        $this->expectException(DataBaseException::class);

        $this->databaseProduct->delete($id);
    }

    public static function updateDataProvider(): array
    {
        $arrayData2Param = [
            'code'  => 2 
        ];

        $arrayData3Param = [
            'code'  => 33,
            'type_product_id' => 1
        ];

        $arrayData4Param = [
            'code'  => 44,
            'type_product_id' => 1,
            'name'  => "productName4"
        ];

        $arrayData5Param = [
            'code'  => 55,
            'type_product_id' => 1,
            'name'  => "productName5",
            'value' => 16.75
        ];

        $arrayData6Param = [
            'code'  => 66,
            'type_product_id' => 1,
            
        ];

        return [
            'when_data_with_2_param' => ['num'=> 2, 'id' => 1, 'productAttributes' => $arrayData2Param, 'value' => 1],
            'when_data_with_3_param' => ['num'=> 3, 'id' => 2, 'productAttributes' => $arrayData3Param, 'value' => 1],
            'when_data_with_4_param' => ['num'=> 4, 'id' => 3, 'productAttributes' => $arrayData4Param, 'value' => 1],
            'when_data_with_5_param' => ['num'=> 5, 'id' => 4, 'productAttributes' => $arrayData5Param, 'value' => 1],
            'when_data_with_fail_param' => ['num'=> 3, 'id' => 5, 'productAttributes' => $arrayData6Param, 'value' => 0]
        ];
    }

    public static function updateFailDataProvider(): array
    {
        $arrayWithParamWrong = [
            'codes'  => 2 
        ];

        $arrayEmpty = [ ];

        $arrayWithInvalidParam = [
            'code'  => 'code-not-number',
            'type_product_id' => 1,
            'name'  => "productName4"
        ];

        return [
            'when_data_with_key_param_is_wrong' => ['num'=> 2, 'id' => 1, 'productAttributes' => $arrayWithParamWrong, 'message' => 'SQL Error [42703]: ERROR: column'],
            'when_data_is_empty' => ['num'=> 1, 'id' => 2, 'productAttributes' => $arrayEmpty, 'message' => 'SQL Error [42601]: ERROR: syntax error at or near "where"'],
            'when_data_is_invalid_param' => ['num'=> 4, 'id' => 3, 'productAttributes' => $arrayWithInvalidParam, 'message' => 'SQL Error [22P02]: ERROR: invalid input syntax for type ']
        ];
    }
    
    public static function deleteDataProvider(): array
    {
        return [
            'when_id_is_valid' => ['id'=> 1, 'value' => 1],
            'when_id_is_not_valid' => ['id'=> 12345, 'value' => 0]
        ];
    }
}