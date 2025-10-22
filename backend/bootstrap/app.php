<?php

use Slim\Factory\AppFactory;

$containerBuilder = new \DI\ContainerBuilder();
$containerBuilder->addDefinitions(__DIR__ . '/../src/Infrastructure/Dependencies/Container.php');
$container = $containerBuilder->build();


AppFactory::setContainer($container);

$app = AppFactory::create();

$app->addBodyParsingMiddleware();

$app->addErrorMiddleware(true,true,true);

$routes = require __DIR__ . "/../src/Drivers/Routes/Api.php";

$routes($app);

return $app;