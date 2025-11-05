<?php

namespace FormFlow\Database;

use MongoDB\Client;
use MongoDB\Database;

class MongoDB
{
    private static ?MongoDB $instance = null;
    private Client $client;
    private Database $database;

    private function __construct()
    {
        $config = require __DIR__ . '/../../config/database.php';

        $this->client = new Client(
            $config['mongodb']['uri'],
            [],
            $config['mongodb']['options']
        );

        $this->database = $this->client->selectDatabase($config['mongodb']['database']);
    }

    public static function getInstance(): MongoDB
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getDatabase(): Database
    {
        return $this->database;
    }

    public function getCollection(string $collectionName)
    {
        return $this->database->selectCollection($collectionName);
    }
}
