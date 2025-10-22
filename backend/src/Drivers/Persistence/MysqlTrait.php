<?php

namespace App\Drivers\Persistence;

use App\Infrastructure\Exceptions\DataBaseException;
use PDO;
use Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Response;

trait PostgresTrait
{
    private PDO $pdo;

    public function connect(): PDO
    {
        try {
            $dotenv = Dotenv::createImmutable(__DIR__ . "/config/", '.env');
            $dotenv->load();


            $dbHost = $_ENV['DB_HOST'];
            $dbPort = $_ENV['DB_PORT'];
            $dbDatabase = $_ENV['DB_DATABASE'];
            $dbUsername = $_ENV['DB_USERNAME'];
            $dbPassword = $_ENV['DB_PASSWORD'];



            $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbDatabase}";
            $this->pdo = new PDO($dsn, $dbUsername, $dbPassword);
            $this->pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);


            return $this->pdo;
        } catch (\PDOException $e) {
            throw new DataBaseException(
                "Connection failed: " . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR,
                $e
            );
        }
    }

    public function disconnect()
    {
        $this->pdo = null;
    }
}
