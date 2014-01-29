<?php

use MajorApi\Library\Registry;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

use \Redis,
    \Resque;

require_once __DIR__ . '/bootstrap.php';

// Connect to the Postgres database.
$postgres = DriverManager::getConnection($configPostgres, new Configuration);

// Connect to Redis.
$redis = new Redis;
$redis->connect($configRedis['host'], $configRedis['port']);

// Set up logging.
$logPath = __DIR__ . '/../log/majorapi-worker.log';
$logger = new Logger('majorapi-worker');
$logger->pushHandler(new StreamHandler($logPath, Logger::ERROR));

// Set up Twig for template rendering.
$loader = new Twig_Loader_Filesystem(__DIR__ . '/templates');
$twig = new Twig_Environment($loader, [
    'debug' => true,
    'cache' => __DIR__ . '/templates_cache'
]);

// The Registry is a global memory database that holds common objects.
Registry::setPostgres($postgres);
Registry::setRedis($redis);
Registry::setLogger($logger);
Registry::setTwig($twig);

Resque::setBackend($configRedis['dsn']);
