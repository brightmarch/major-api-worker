<?php

use MajorApi\Library\Registry;

require_once __DIR__ . '/../vendor/autoload.php';

$configPostgres = require_once __DIR__ . '/config/config-postgres.php';
$configRedis = require_once __DIR__ . '/config/config-redis.php';
$configMajorApi = require_once __DIR__ . '/config/config-majorapi-worker.php';

Registry::setMajorApiConfig($configMajorApi);
