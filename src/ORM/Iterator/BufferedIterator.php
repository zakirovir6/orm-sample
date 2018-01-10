<?php
/**
 * Created by PhpStorm.
 * User: igorek
 * Date: 10.01.18
 * Time: 20:12
 */

namespace TestWork\ORM\Iterator;

use TestWork\ORM\AbstractModelObject;
use TestWork\ORM\Query;

class BufferedIterator implements \Iterator
{
    use IteratorTrait;

    /** @var Query */
    private $query;
    /** @var \PDO */
    private $connection;
    /** @var AbstractModelObject */
    private $modelObject;
    /** @var array */
    private $data = [];
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
        return current($this->data);
    }

    public function next()
    {
        next($this->data);
    }

    public function key()
    {
        return key($this->data);
    }

    public function valid()
    {
        return key($this->data) !== null;
    }

    /**
     * @throws \Exception
     */
    public function rewind()
    {
        $statement = $this->query->getPreparedStatement();
        if (!$statement->execute())
        {
            throw new \Exception("Cannot execute prepared statement "  . PHP_EOL .
                "Error code: {$statement->errorCode()}, error: " . var_export($statement->errorInfo(), true));
        }

        foreach ($statement->fetchAll(\PDO::FETCH_ASSOC) as $item)
        {
            $this->data[] = $this->castToModel($this->modelObject, $item);
        }

        if ($this->query->hasPostFilter())
        {
            $this->data = array_filter($this->data, function(AbstractModelObject $modelObject) {
                return $this->query->getPostFilter()->test($modelObject);
            });
        }

        if ($this->query->isSqlCalcFoundRows())
        {
            $this->total = $this->getTotal($this->connection);

        }

        reset($this->data);
    }

}