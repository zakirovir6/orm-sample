<?php
/**
 * Created by PhpStorm.
 * User: igorek
 * Date: 09.01.18
 * Time: 23:35
 */

namespace TestWork;

/**
 * Class ConnectionFactory
 * @package TestWork
 */
class ConnectionFactory
{
    /**
     * @var \PDO[]
     */
    private $connectionPool = [];

    /**
     * @param string $db
     * @param string $host
     * @param string $user
     * @param string $password
     * @return \PDO
     */
    public function getConnection($db, $host, $user, $password)
    {
        $dsn = "mysql:dbname={$db};host={$host}";

        $connId = sha1(implode('|', [$dsn, $user, $password]));

        if (isset($this->connectionPool[$connId]))
        {
            return $this->connectionPool[$connId];
        }

        try {
            $connection = new \PDO($dsn, $user, $password);
        }
        catch (\PDOException $e) {
            //some handling if need
            throw $e;
        }

        $this->connectionPool[$connId] = $connection;

        return $connection;
    }
}