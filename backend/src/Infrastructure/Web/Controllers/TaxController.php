<?php

namespace App\Infrastructure\Web\Controllers;

use App\Application\UseCases\CreateTaxUseCase;
use App\Application\UseCases\DeleteTaxUseCase;
use App\Application\UseCases\FindAllTaxUseCase;
use App\Application\UseCases\FindTaxUseCase;
use App\Application\UseCases\UpdateTaxUseCase;
use App\Application\Dtos\TaxDto;
use App\Application\Dtos\TaxUpdateDto;
use App\Application\Exceptions\TaxException;
use App\Infrastructure\Exceptions\ClientException;
use App\Infrastructure\Exceptions\DataBaseException;
use Symfony\Component\HttpFoundation\Response as ResponseCode;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class TaxController
{
    public function __construct(
        private CreateTaxUseCase $createTaxUseCase,
        private FindTaxUseCase $findTaxUseCase,
        private FindAllTaxUseCase $findAllTaxUseCase,
        private UpdateTaxUseCase $updateTaxUseCase,
        private DeleteTaxUseCase $deleteTaxUseCase
    ) {
    }

    public function create(Request $request, Response $response): Response
    {

        $response = $response->withHeader('Content-Type', 'application/json');

        try {
            $taxData = formatRequestInTaxData($request);

            $taxDto = TaxDto::fromRequest($taxData);

            $this->createTaxUseCase->action($taxDto);
        } catch (TaxException | \TypeError $th) {
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
            [ 'message' => 'Tax created successfully!'],
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
            $taxId = formatArgsForId($args);

            $taxEntity = $this->findTaxUseCase->action($taxId);
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
            $taxEntity->toArray(),
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
            $taxesEntities = $this->findAllTaxUseCase->action();
        } catch (\Throwable $th) {
            $messageException = json_encode(
                ["error" => $th->getMessage()],
                JSON_THROW_ON_ERROR
            );
            $response->getBody()->write($messageException);
            return $response->withStatus(ResponseCode::HTTP_INTERNAL_SERVER_ERROR);
        }

        $encodedTaxes = json_encode(
            $taxesEntities,
            JSON_THROW_ON_ERROR
        );

        $response->getBody()->write($encodedTaxes);
        return $response->withStatus(ResponseCode::HTTP_OK);
    }

    /**
    * @param Request $request
    * @param Response $response
    * @param array{id: int} $args
    * @return Response
    */
    public function update(Request $request, Response $response, array $args): Response
    {
        $response = $response->withHeader('Content-Type', 'application/json');

        try {
            $taxId = formatArgsForId($args);

            $taxData = formatRequestInTaxDataUpdate($request);

            $taxUpdateDto = TaxUpdateDto::fromRequest($taxData);

            if ($this->updateTaxUseCase->action($taxId, $taxUpdateDto->toArray()) == null) {
                $messageException = json_encode(
                    ["error" => "o matching rows found for update"],
                    JSON_THROW_ON_ERROR
                );

                $response->getBody()->write($messageException);
                return $response->withStatus(ResponseCode::HTTP_NOT_FOUND);
            };
        } catch (ClientException | TaxException | \TypeError  $th) {
            $messageException = json_encode(
                ["error" => $th->getMessage()],
                JSON_THROW_ON_ERROR
            );

            $response->getBody()->write($messageException);
            return $response->withStatus(ResponseCode::HTTP_BAD_REQUEST);
        } catch (DataBaseException | \Throwable $th) {
            $messageException = json_encode(
                ["error" => $th->getMessage()],
                JSON_THROW_ON_ERROR
            );

            $response->getBody()->write($messageException);
            return $response->withStatus(ResponseCode::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $response->withStatus(ResponseCode::HTTP_NO_CONTENT);
    }

    /**
    * @param Request $request
    * @param Response $response
    * @param array{id: int} $args
    * @return Response
    */
    public function updateALL(Request $request, Response $response, array $args): Response
    {
        $response = $response->withHeader('Content-Type', 'application/json');

        try {
            $taxId = formatArgsForId($args);

            $taxData = formatRequestInTaxDataUpdate($request);

            $taxUpdateDto = TaxUpdateDto::fromRequest($taxData);

            if ($this->updateTaxUseCase->action($taxId, $taxUpdateDto->toArray()) == null) {
                $messageException = json_encode(
                    ["error" => "o matching rows found for update"],
                    JSON_THROW_ON_ERROR
                );

                $response->getBody()->write($messageException);
                return $response->withStatus(ResponseCode::HTTP_NOT_FOUND);
            };
        } catch (\InvalidArgumentException | ClientException | TaxException | \TypeError $ex) {
            $messageException = json_encode(
                ["error" => $ex->getMessage()],
                JSON_THROW_ON_ERROR
            );

            $response->getBody()->write($messageException);
            return $response->withStatus(ResponseCode::HTTP_BAD_REQUEST);
        } catch (DataBaseException | \Throwable $th) {
            $response->getBody()->write(
                json_encode(
                    [ "error" => $th->getMessage()],
                    JSON_THROW_ON_ERROR
                )
            );
            return $response->withStatus(ResponseCode::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $response->withStatus(ResponseCode::HTTP_NO_CONTENT);
    }

    /**
    * @param Response $response
    * @param array{id: int} $args
    * @return Response
    */
    public function delete(Request $request, Response $response, array $args): Response
    {
        $response = $response->withHeader('Content-Type', 'application/json');

        try {
            $taxId = formatArgsForId($args);

            if ($this->deleteTaxUseCase->action($taxId) == null) {
                $messageException = json_encode(
                    ["error" => "id does not match any item"],
                    JSON_THROW_ON_ERROR
                );

                $response->getBody()->write($messageException);
                return $response->withStatus(ResponseCode::HTTP_BAD_REQUEST);
            };
        } catch (\App\Infrastructure\Exceptions\ClientException | \InvalidArgumentException $ex) {
            $messageException = json_encode(
                ["error" => $ex->getMessage()],
                JSON_THROW_ON_ERROR
            );

            $response->getBody()->write($messageException);
            return $response->withStatus(ResponseCode::HTTP_BAD_REQUEST);
        } catch (\App\Infrastructure\Exceptions\DataBaseException $th) {
            $messageException = json_encode(
                [
                    'error' => $th->getMessage()
                ],
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
            [ "message" => "Tax deleted successfully!"],
            JSON_THROW_ON_ERROR
        );

        $response->getBody()->write($encodedEntity);
        return $response->withStatus(ResponseCode::HTTP_OK);
    }
}
