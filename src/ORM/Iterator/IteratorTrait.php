<?php
/**
 * Created by PhpStorm.
 * User: igorek
 * Date: 10.01.18
 * Time: 23:29
 */

namespace TestWork\ORM\Iterator;

use TestWork\ORM\AbstractModelObject;
use TestWork\ORM\PostFilter\FilterInterface;

trait IteratorTrait
{
    /**
     * @param \PDO $connection
     * @return int
     * @throws \Exception
     */
    public function getTotal(\PDO $connection)
    {
        $total = 0;
        $sql = "SELECT FOUND_ROWS()";
        if (!$totalStatement = $connection->query($sql, \PDO::FETCH_COLUMN, 0))
        {
            throw new \Exception("Cannot create PDO prepared statement with sql " . $sql . PHP_EOL .
                "Error code: {$connection->errorCode()}, error: " . var_export($connection->errorInfo(), true));
        }
        foreach ($totalStatement as $totalRow)
        {
            $total = (int)$totalRow;
            break;
        }

        return $total;
    }

    /**
     * @param AbstractModelObject $modelObject
     * @param array $data
     * @return AbstractModelObject
     */
    public function castToModel(AbstractModelObject $modelObject, array $data)
    {
        $model = clone $modelObject;
        foreach ($data as $prop => $value)
        {
            $model->{$prop} = $value;
        }

        return $model;
    }
}