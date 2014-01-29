<?php

namespace MajorApi\Library;

use Doctrine\DBAL\Connection;
use Monolog\Logger;

use \Twig_Environment;

use \DateTime,
    \Redis;

class Registry
{

    /** @var array */
    private static $majorApiConfig;

    /** @var Doctrine\DBAL\Connection */
    private static $postgres;

    /** @var Redis */
    private static $redis;

    /** @var Monolog\Logger */
    private static $logger;

    /** @var Twig_Environment */
    private static $twig;

    /** @var string */
    private static $timeString = '';

    /** @const integer */
    const STATUS_ENABLED = 1;

    public static function setMajorApiConfig(array $majorApiConfig)
    {
        self::$majorApiConfig = $majorApiConfig;
    }

    public static function getMajorApiConfig()
    {
        return self::$majorApiConfig;
    }

    public static function setPostgres(Connection $postgres)
    {
        self::$postgres = $postgres;
    }

    public static function getPostgres()
    {
        return self::$postgres;
    }

    public static function setRedis(Redis $redis)
    {
        self::$redis = $redis;
    }

    public static function getRedis()
    {
        return self::$redis;
    }

    public static function setLogger(Logger $logger)
    {
        self::$logger = $logger;
    }

    public static function getLogger()
    {
        return self::$logger;
    }

    public static function setTwig(Twig_Environment $twig)
    {
        self::$twig = $twig;
    }

    public static function getTwig()
    {
        return self::$twig;
    }

    public static function getTimeString()
    {
        if (empty(self::$timeString)) {
            self::$timeString = date('Y-m-d H:i:s', time());
        }

        return self::$timeString;
    }

}
