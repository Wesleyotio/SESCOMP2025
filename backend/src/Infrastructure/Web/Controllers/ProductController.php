<?php

namespace App\Infrastructure\Web\Controllers;

use App\Application\UseCases\CreateProductUseCase;
use App\Application\UseCases\DeleteProductUseCase;
use App\Application\UseCases\FindAllProductUseCase;
use App\Application\UseCases\FindProductUseCase;
use App\Application\UseCases\UpdateProductUseCase;
use App\Application\Dtos\ProductDto;
use App\Application\Dtos\ProductUpdateDto;
use App\Application\Exceptions\ProductException;
use App\Infrastructure\Exceptions\ClientException;
use App\Infrastructure\Exceptions\DataBaseException;
use Symfony\Component\HttpFoundation\Response as ResponseCode;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ProductController
{
    public function __construct(
        private CreateProductUseCase $createProductUseCase,
        private FindProductUseCase $findProductUseCase,
        private FindAllProductUseCase $findAllProductUseCase,
        private UpdateProductUseCase $updateProductUseCase,
        private DeleteProductUseCase $deleteProductUseCase,
    ) {
    }

    public function create(Request $request, Response $response): Response
    {

        $response = $response->withHeader('Content-Type', 'application/json');

        try {
            $productData = formatRequestInProductData($request);

            $productDto = ProductDto::fromRequest($productData);

            $this->createProductUseCase->action($productDto);
        } catch (ProductException | \TypeError $th) {
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
            [ 'message' => 'Product created successfully!'],
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
            $productId = formatArgsForId($args);

            $productEntity = $this->findProductUseCase->action($productId);
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
            $productEntity->toArray(),
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
            $productsEntities = $this->findAllProductUseCase->action();
        } catch (\Throwable $th) {
            $messageException = json_encode(
                ["error" => $th->getMessage()],
                JSON_THROW_ON_ERROR
            );
            $response->getBody()->write($messageException);
            return $response->withStatus(ResponseCode::HTTP_INTERNAL_SERVER_ERROR);
        }

        $encodedProducts = json_encode(
            $productsEntities,
            JSON_THROW_ON_ERROR
        );

        $response->getBody()->write($encodedProducts);
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
            $productId = formatArgsForId($args);

            $productData = formatRequestInProductDataUpdate($request);

            $productUpdateDto = ProductUpdateDto::fromRequest($productData);

            if ($this->updateProductUseCase->action($productId, $productUpdateDto->toArray()) == null) {
                $messageException = json_encode(
                    ["error" => "o matching rows found for update"],
                    JSON_THROW_ON_ERROR
                );

                $response->getBody()->write($messageException);
                return $response->withStatus(ResponseCode::HTTP_NOT_FOUND);
            };
        } catch (ClientException | ProductException | \TypeError  $th) {
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
            $productId = formatArgsForId($args);

            $productData = formatRequestInProductDataUpdate($request);

            $productUpdateDto = ProductUpdateDto::fromRequest($productData);

            if ($this->updateProductUseCase->action($productId, $productUpdateDto->toArray()) == null) {
                $messageException = json_encode(
                    ["error" => "o matching rows found for update"],
                    JSON_THROW_ON_ERROR
                );

                $response->getBody()->write($messageException);
                return $response->withStatus(ResponseCode::HTTP_NOT_FOUND);
            };
        } catch (\InvalidArgumentException | ClientException | ProductException | \TypeError $ex) {
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
            $productId = formatArgsForId($args);

            if ($this->deleteProductUseCase->action($productId) == null) {
                $messageException = json_encode(
                    ["error" => "id does not match any item"],
                    JSON_THROW_ON_ERROR
                );

                $response->getBody()->write($messageException);
                return $response->withStatus(ResponseCode::HTTP_BAD_REQUEST);
            };
        } catch (\App\Infrastructure\Exceptions\ClientException $ex) {
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
            [ "message" => "Product deleted successfully!"],
            JSON_THROW_ON_ERROR
        );

        $response->getBody()->write($encodedEntity);
        return $response->withStatus(ResponseCode::HTTP_OK);
    }
}
