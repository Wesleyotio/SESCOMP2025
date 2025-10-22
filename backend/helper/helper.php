<?php

declare(strict_types=1);

use Symfony\Component\HttpFoundation\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
/**
 * Formata uma data no formato yyyy/mm/dd H:i:s
 *
 * @param string $date
 * @return DateTimeImmutable
 */
function formatDate(string $date): DateTimeImmutable 
{
    $formattedDate = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $date);
    if (!$formattedDate ) {
        throw new \ErrorException("Error when converting date", Response::HTTP_INTERNAL_SERVER_ERROR);
    }
    return $formattedDate;
}

/**
 * valida se é um array PDO valido
 *
 * @param array<mixed> $pdoStatementData
 * @return void 
 */
function validatePDO (array $pdoStatementData): void
{
    if (!is_array($pdoStatementData)) {
        throw new \ErrorException('Failure to obtain a array pdoStatementData', Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}

/**
 * valida se é um objeto JSON valido
 *
 * @param string $objectData
 * @return void 
 */
function validateJson(string $objectData): void 
{
    if (!json_validate($objectData)) {
        throw new JsonException('Json for object is invalid');
    }
}

/**
 * valida se um array possui todas as chaves esperadas
 *
 * @param array<mixed> $requireKeys
 * @param array<mixed> $arrayForCheck
 * @return bool 
 */
function validateArrayKeys(array $requireKeys, array $arrayForCheck): bool 
{
    $missingKeys = array_diff($requireKeys, array_keys($arrayForCheck));
    return empty($missingKeys);
}

/**
 * valida se as chaves um array está contido na chaves em outro array
 *
 * @param array<mixed> $subArrayKeys
 * @param array<mixed> $superArrayKeys
 * @return bool 
 */
function validateKeysContainedInArray(array $subArrayKeys, array $superArrayKeys): bool 
{
    foreach ($subArrayKeys as $key) {
        if (!in_array($key, $superArrayKeys)) {
            return false;
        }
    }
    return true;
}


/**
 * converte o valor monetário em string para float 
 *
 * @param string $value
 * @return float 
 */
function convertValueInStringForFloat(string $value): float
{
    return (float) str_replace(',', '.', str_replace('.', '', $value));
}

/**
 * converte o body da requisição em itens  em array{}
 *
 * @param Request $request 
 * @return array{'code': int, 'type_product_id': int, 'name': string, 'value': string} $validateArray
*/
function formatRequestInProductData(Request $request): array
{
    $requestData = $request->getParsedBody();

    $arrayKeys = ['code', 'type_product_id', 'name', 'value'];
    
    if (!is_array($requestData)) {
        throw new \InvalidArgumentException('Expected array, got ' . gettype($requestData));
    }

    if (! validateArrayKeys($arrayKeys, $requestData)) {
        throw new \TypeError("Product has missing fields");
    }

    $validateArray = [
        'code' => (int)$requestData['code'],
        'type_product_id' => (int)$requestData['type_product_id'],
        'name' => (string)$requestData['name'],
        'value' => (string)$requestData['value']
    ];
    return $validateArray;
}


/**
 * converte o body da requisição de Type Product
 *
 * @param Request $request 
 * @return string $name 

 */
function formatRequestInTypeProductData(Request $request): string
{
    $requestData = $request->getParsedBody();

    $arrayKeys = ['name'];
    
    if (!is_array($requestData)) {
        throw new \InvalidArgumentException('Expected array, got ' . gettype($requestData));
    }

    if (! validateArrayKeys($arrayKeys, $requestData)) {
        throw new \TypeError("Type of product has missing fields");
    }

    if (!is_string($requestData['name'])) {
        throw new \InvalidArgumentException('Expected string, got ' . gettype($requestData['name']));
    }

    return $requestData['name'];
}

/**
 * converte o body da requisição de Tax em array{}
 *
 * @param Request $request 
 * @return array{
 *      type_product_id: int,
 *      value: string
 * }
 */
function formatRequestInTaxData(Request $request): array
{
    $requestData = $request->getParsedBody();

    $arrayKeys = ['type_product_id', 'value'];
    
    if (!is_array($requestData)) {
        throw new \InvalidArgumentException('Expected array, got ' . gettype($requestData));
    }

    if (! validateArrayKeys($arrayKeys, $requestData)) {
        throw new \TypeError("Tax has missing fields");
    }

    $validateArray = [
        'type_product_id' => (int)$requestData['type_product_id'],
        'value' => (string)$requestData['value']
    ];
    return $validateArray;
}


/**
 * converte o body da requisição de sale itens em array{}
 *
 * @param Request $request 
 * @return array<int,array<string,int|string>> $validateArray
 */
function formatRequestInItensSaleData(Request $request): array
{
    $requestData = $request->getParsedBody();

    $arrayKeys = ['product_id', 'amount'];

    $validateArray = [];
    
    if (!is_array($requestData)) {
        throw new \InvalidArgumentException('Expected array, got ' . gettype($requestData));
    }

    foreach($requestData as $salesItem) {
        if (! validateArrayKeys($arrayKeys, $salesItem)) {
            throw new \TypeError("Sales Item has missing fields");
        }

        if((is_int($salesItem['product_id']) == false) || $salesItem['product_id'] <= 0) {
            throw new \TypeError("Id do produto: {$salesItem['product_id']} deve ser inteiro maior que zero");
        }

        if((is_int($salesItem['amount']) == false) || $salesItem['amount'] <= 0) {
            throw new \TypeError("quantidade : {$salesItem['amount']} deve ser inteiro maior que zero");
        }

        $item = [
            'product_id'    => (int)$salesItem['product_id'],
            'amount'        => (string)$salesItem['amount'],
        ];

        array_push($validateArray, $item);
    }

    return $validateArray;
}

/**
 * converte o body da requisição de update de Product em array{}
 *
 * @param Request $request 
 * @return array{
 *      code?: int,
 *      type_product_id?: int,
 *      name?: string,
 *      value?: string
 * }
 */
function formatRequestInProductDataUpdate(Request $request): array
{
    $requestData = $request->getParsedBody();

    $arrayKeys = ['code', 'type_product_id', 'name', 'value'];
    
    if (!is_array($requestData)) {
        throw new \InvalidArgumentException('Expected array, got ' . gettype($requestData));
    }

    if (! validateKeysContainedInArray(array_keys($requestData), $arrayKeys)) {
        throw new \App\Application\Exceptions\ProductException("unknown fields are being passed for product update");
    }

    foreach ($requestData as $key => $value) {
        switch ($key) {
            case 'code':
                if ( (is_int($value) == false) || ($value <= 0)) throw new  \InvalidArgumentException("Expected  for code: {$value} type int higher than zero");
                break;
                
            case 'type_product_id':
                if ( (is_int($value) == false) || ($value <= 0)) throw new  \InvalidArgumentException("Expected  for type_product_id: {$value} type int higher than zero");
                break;
                
            case 'name':
                if ( (is_string($value) == false) || empty($value) ) throw new  \InvalidArgumentException("Expected  for name: {$value} type string not empty");
                break;
                
            case 'value':
                if ( (is_string($value) == false) || (convertValueInStringForFloat($value) <= 0) ){
                    throw new  \InvalidArgumentException("Expected  for value: {$value} type float higher than zero");
                }
                break;
        
            default:
                break;
        }
    }
    return $requestData;
}

/**
 * converte o body da requisição de update de Tax em array{}
 *
 * @param Request $request 
 * @return array{
 *      type_product_id?: int,
 *      value?: string
 * }
 */
function formatRequestInTaxDataUpdate(Request $request): array
{
    $requestData = $request->getParsedBody();

    $arrayKeys = ['type_product_id', 'value'];
    
    if (!is_array($requestData)) {
        throw new \InvalidArgumentException('Expected array, got ' . gettype($requestData));
    }

    if (!validateKeysContainedInArray(array_keys($requestData), $arrayKeys)) {
        throw new \App\Application\Exceptions\TaxException("unknown fields are being passed for tax update");
    }

    foreach ($requestData as $key => $value) {
        switch ($key) {
                
            case 'type_product_id':
                if ( (is_int($value) == false) || ($value <= 0)) throw new  \InvalidArgumentException("Expected  for type_product_id: {$value} type int higher than zero");
                break;
                
            case 'value':
                if ( (is_string($value) == false) || (convertValueInStringForFloat($value) <= 0) ) {
                    throw new  \InvalidArgumentException("Expected  for value: {$value} type float higher than zero");
                } 
                break;
        
            default:
                break;
        }
    }
    return $requestData;
}

/**
 * converte o valor da chave id do array da requisição em inteiro
 *
 * @param array{id: int} $args 
 * @return int
 *   
 */
function formatArgsForId(array $args): int
{
    if (is_array($args) == false ) {
        throw new \InvalidArgumentException('Expected array, got ' . gettype($args));
    }
    
    if(array_key_exists("id", $args) == false) {
        throw new \InvalidArgumentException('Expected key of value id');
    }

    $id = intval($args["id"]);

    if(is_int($id ) == false || $id <= 0) {
        throw new \InvalidArgumentException("Id provider is invalid");
    }

    return $id;
}