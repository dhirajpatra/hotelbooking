<?php
declare (strict_types = 1);

namespace App;

use PDO;

// Singleton to connect db.
class ConnectDb
{
    // Hold the class instance.
    private static $instance = null;
    private static $dbConn;
    private static $dsn;

    private static $host = DB_HOST;
    private static $user = DB_USER;
    private static $pass = DB_PASSWORD;
    private static $name = DB_NAME;
    private static $port = DB_PORT;

    /**
     * is not allowed to call from outside to prevent from creating multiple instances,
     * to use the singleton, you have to obtain the instance from Singleton::getInstance() instead
     */
    private function __construct()
    {
    }

    /**
     * prevent the instance from being cloned (which would create a second instance of it)
     */
    private function __clone()
    {
    }

    /**
     * prevent from being unserialized (which would create a second instance of it)
     */
    private function __wakeup()
    {
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new ConnectDb();
        }

        return self::$instance;
    }

    /**
     * get connection
     *
     * @return void
     */
    public static function getConnection()
    {
        // Create connection
        try {
            if (!self::$dbConn) {
                self::$dsn = 'mysql:host=' . self::$host . ';port=' . self::$port . ';dbname=' . self::$name . ';charset=utf8';
                self::$dbConn = new \PDO(self::$dsn, self::$user, self::$pass);
                self::$dbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // set some attributes in mysql
                // $sql_set = self::$dbConn->prepare("SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
                // $sql_set->execute();
            }

            return self::$dbConn;
        } catch (\PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }
    }
}
