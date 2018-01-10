<?php
/**
 * Created by PhpStorm.
 * User: igorek
 * Date: 10.01.18
 * Time: 21:57
 */

namespace TestWork\ORM\Iterator;

use TestWork\ORM\AbstractModelObject;
use TestWork\ORM\Query;

class UnbufferedIterator implements \Iterator
{
    use IteratorTrait;

    /** @var Query */
    private $query;
    /** @var \PDO */
    private $connection;
    /** @var AbstractModelObject */
    private $modelObject;
    /** @var AbstractModelObject */
    private $data;
    /** @var \PDOStatement */
    private $statement;
    /** @var int */
    private $key = 0;
    /** @var int */
    public $total = 0;

    public function __construct( Query $query, \PDO $connection, AbstractModelObject $modelObject)
    {
        $this->query = $query;
        $this->connection = $connection;
        $this->modelObject = $modelObject;
    }

    public function current()
    {
        return $this->data;
    }

    public function next()
    {
        $this->data = $this->castToModel($this->modelObject, $this->statement->fetch(\PDO::FETCH_ASSOC));
        $this->key++;
    }

    public function key()
    {
        return $this->key;
    }

    public function valid()
    {
        return $this->data !== false;
    }

    /**
     * @throws \Exception
     */
    public function rewind()
    {
        $this->statement = $this->query->getPreparedStatement();
        if (!$this->statement->execute())
        {
            throw new \Exception("Cannot execute prepared statement "  . PHP_EOL .
                "Error code: {$this->statement->errorCode()}, error: " . var_export($this->statement->errorInfo(), true));
        }

        if ($this->query->isSqlCalcFoundRows())
        {
            $this->total = $this->getTotal($this->connection);
        }

        $this->data = $this->castToModel($this->modelObject, $this->statement->fetch(\PDO::FETCH_ASSOC));
    }

}