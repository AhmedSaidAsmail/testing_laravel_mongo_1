<?php

namespace Matrix\MongoDb;

use MongoDB\Client;

class Connection
{
    /**
     * The Active MongoDb Client used
     *
     * @var Client $client
     */
    public $client;
    /**
     * The name of connected database
     *
     * @var string $database
     */
    public $database;

    /**
     * Connection constructor.
     * @param Client $client
     * @param string $database
     */
    public function __construct(Client $client, $database)
    {
        $this->client = $client;
        $this->database = $database;
    }

    public function create($collection, array $values)
    {
        $db = $this->client->selectDatabase($this->database);
        try {
            return $db->selectCollection($collection)->insertOne($values);
        } catch (\Exception $e) {
            throw $e;
        }
    }

}