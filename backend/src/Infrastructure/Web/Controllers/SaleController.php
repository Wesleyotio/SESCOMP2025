<?php

namespace App\Infrastructure\Web\Controllers;

use App\Application\UseCases\CalculateSaleUseCase;
use App\Application\UseCases\CreateSaleUseCase;
use App\Application\UseCases\FindAllSaleUseCase;
use App\Application\UseCases\FindSaleUseCase;
use App\Application\Dtos\SaleDto;
use App\Application\Exceptions\SaleException;
use App\Infrastructure\Exceptions\ClientException;
use App\Infrastructure\Exceptions\DataBaseException;
use Symfony\Component\HttpFoundation\Response as ResponseCode;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class SaleController
{
    public function __construct(
        private CreateSaleUseCase $createSaleUseCase,
        private CalculateSaleUseCase $calculateSaleUseCase,
        private FindAllSaleUseCase $findAllSaleUseCase,
        private FindSaleUseCase $findSaleUseCase
    ) {
    }

    public function order(Request $request, Response $response): Response
    {
        $response = $response->withHeader('Content-Type', 'application/json');

        try {
            $salesItens = formatRequestInItensSaleData($request);

            $saleEntity = $this->calculateSaleUseCase->action($salesItens);
        } catch (SaleException | \TypeError $th) {
            $response->getBody()->write(
                json_encode(
                    [
                    'error' => $th->getMessage()
                    ],
                    JSON_THROW_ON_ERROR
                )
            );

            return $response->withStatus(ResponseCode::HTTP_BAD_REQUEST);
        } catch (DataBaseException | \Throwable $th) {
            $response->getBody()->write(
                json_encode(
                    [
                    'error' => $th->getMessage()
                    ],
                    JSON_THROW_ON_ERROR
                )
            );

            return $response->withStatus(ResponseCode::HTTP_INTERNAL_SERVER_ERROR);
        }

        $encodedEntity = json_encode(
            $saleEntity->toArray(),
            JSON_THROW_ON_ERROR
        );

        $response->getBody()->write($encodedEntity);
        return $response->withStatus(ResponseCode::HTTP_OK);
    }

    public function checkout(Request $request, Response $response): Response
    {

        $response = $response->withHeader('Content-Type', 'application/json');

        try {
            $salesItens = formatRequestInItensSaleData($request);

            $saleDto = $this->calculateSaleUseCase->action($salesItens);

            $this->createSaleUseCase->action($saleDto, $salesItens);
        } catch (SaleException | \TypeError $th) {
            $response->getBody()->write(
                json_encode(
                    [
                    'error' => $th->getMessage()
                    ],
                    JSON_THROW_ON_ERROR
                )
            );

            return $response->withStatus(ResponseCode::HTTP_BAD_REQUEST);
        } catch (DataBaseException | \Throwable $th) {
            $response->getBody()->write(
                json_encode(
                    [
                    'error' => $th->getMessage()
                    ],
                    JSON_THROW_ON_ERROR
                )
            );

            return $response->withStatus(ResponseCode::HTTP_INTERNAL_SERVER_ERROR);
        }

        $encodedEntityCreated = json_encode(
            [ 'message' => 'Sale created successfully!'],
            JSON_THROW_ON_ERROR
        );

        $response->getBody()->write($encodedEntityCreated);
        return $response->withStatus(ResponseCode::HTTP_CREATED);
    }

    /**
    * @param Response $response
    * @param array{id: int} $args
    * @return Response
    */
    public function findById(Request $request, Response $response, array $args): Response
    {
        $response = $response->withHeader('Content-Type', 'application/json');

        try {
            $saleId = formatArgsForId($args);

            $saleEntity = $this->findSaleUseCase->action($saleId);
        } catch (\InvalidArgumentException | ClientException $ex) {
            $messageException = json_encode(
                ["error" => $ex->getMessage()],
                JSON_THROW_ON_ERROR
            );

            $response->getBody()->write($messageException);
            return $response->withStatus(ResponseCode::HTTP_BAD_REQUEST);
        } catch (\App\Infrastructure\Exceptions\DataBaseException $th) {
            $messageException = json_encode(
                ["error" => $th->getMessage()],
                JSON_THROW_ON_ERROR
            );

            $response->getBody()->write($messageException);
            return $response->withStatus($th->getCode());
        } catch (\Throwable $th) {
            $messageException = json_encode(
                ["error" => $th->getMessage()],
                JSON_THROW_ON_ERROR
            );

            $response->getBody()->write($messageException);
            return $response->withStatus(ResponseCode::HTTP_INTERNAL_SERVER_ERROR);
        }

        $encodedEntity = json_encode(
            $saleEntity->toArray(),
            JSON_THROW_ON_ERROR
        );

        $response->getBody()->write($encodedEntity);
        return $response->withStatus(ResponseCode::HTTP_OK);
    }

    /**
    * @param Response $response
    * @return Response
    */
    public function findAll(Request $request, Response $response): Response
    {

        $response = $response->withHeader('Content-Type', 'application/json');

        try {
            $salesEntities = $this->findAllSaleUseCase->action();
        } catch (\Throwable $th) {
            $messageException = json_encode(
                ["error" => $th->getMessage()],
                JSON_THROW_ON_ERROR
            );
            $response->getBody()->write($messageException);
            return $response->withStatus(ResponseCode::HTTP_INTERNAL_SERVER_ERROR);
        }

        $encodedSales = json_encode(
            $salesEntities,
            JSON_THROW_ON_ERROR
        );

        $response->getBody()->write($encodedSales);
        return $response->withStatus(ResponseCode::HTTP_OK);
    }
}
