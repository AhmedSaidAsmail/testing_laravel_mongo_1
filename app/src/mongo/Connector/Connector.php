<?php

namespace Matrix\MongoDb\Connector;

use Illuminate\Support\Arr;
use MongoDB\Client;
use Exception;

class Connector
{
    use DetectsLostConnections;
    /**
     * @var array $uriOptions Mongodb configuration uri option
     */
    private $uriOptions = [
        'authSource' => 'admin',
        'connectTimeoutMS' => 5000
    ];
    /**
     * @var array $driverOptions Mongodb configuration driver option
     */
    private $driverOptions = [
        'typeMap' => [
            'root' => 'array',
            'document' => 'array',
            'array' => 'array',
        ],
    ];

    /**
     * @param array $config
     * @return \MongoDB\Client
     */
    public function connect(array $config)
    {
        $uriOptions = $this->getUriOptions($config);
        $driverOptions = $this->getDriverOptions($config);
        $dsn = $this->getDsn($config);
        return $this->makeConnection($dsn, $uriOptions, $driverOptions);

    }

    /**
     * Creating Mongo Client Uri Option array
     *
     * @param array $config
     * @return array
     */
    private function getUriOptions(array $config)
    {
        $uriOptionsStatement = $this->getUriOptionsStatement($config);
        if (!is_null(Arr::get($config, 'username'))) {
            return array_merge([
                'username' => Arr::get($config, 'username'),
                'password' => Arr::get($config, 'password')
            ], $uriOptionsStatement);
        }
        return $uriOptionsStatement;

    }

    /**
     * Merging Given Uri options from env and default parameters
     *
     * @param array $config
     * @return array
     */
    private function getUriOptionsStatement(array $config)
    {
        return array_merge($this->uriOptions, Arr::get($config, 'uriOptions'));
    }

    /**
     * Merging Given Driver options from env and default parameters
     *
     * @param array $config
     * @return array
     */
    private function getDriverOptions(array $config)
    {
        return array_merge($this->driverOptions, Arr::get($config, 'driverOptions'));

    }

    /**
     *  Create a DSN string from a configuration.
     *
     * @param array $config
     * @return string
     */
    private function getDsn(array $config)
    {
        return sprintf('mongodb://%s:%d', Arr::get($config, 'host'), Arr::get($config, 'port'));
    }

    /**
     * Creating Mongo db client instance
     *
     * @param $dsn
     * @param array $option
     * @param array $driver
     * @return Client
     * @throws Exception
     */
    private function makeConnection($dsn, array $option, array $driver)
    {
        try {
            $client = $this->createMongoClient($dsn, $option, $driver);
        } catch (Exception $e) {
            $client = $this->tryAgainIfCausedByLostConnection($e, $dsn, $option, $driver);
        }
        return $client;

    }

    /**
     * Creating Mongo Db Client instance
     *
     * @param $dsn
     * @param array $uri
     * @param array $driver
     * @return Client
     */
    private function createMongoClient($dsn, array $uri, array $driver)
    {

        $client = new Client($dsn, $uri, $driver);
        // Check Database connection cross listDatabase method
        $client->listDatabases();
        return $client;
    }

    private function tryAgainIfCausedByLostConnection(Exception $e, $dsn, array $option, array $driver)
    {
        if ($this->causedByLostConnection($e)) {
            return $this->createMongoClient($dsn, $option, $driver);
        }
        throw $e;

    }

}