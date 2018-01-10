<?php
/**
 * Created by PhpStorm.
 * User: igorek
 * Date: 10.01.18
 * Time: 0:55
 */

namespace TestWork\ORM;


use TestWork\ConnectionFactory;

class Query
{
    const SORT_ASC = 'ASC';
    const SORT_DESC = 'DESC';

    /** @var \PDO */
    private $connection;

    /** @var AbstractModelObject */
    private $modelObject;

    /** @var LogicalFilter|Filter */
    private $filter;

    /** @var int */
    private $limit = 10;

    /** @var int */
    private $offset = 0;

    /** @var array */
    private $sort = [];

    /** @var string */
    public $sql = '';

    private $sqlCalcFoundRows = false;

    public function __construct(ConnectionFactory $connectionFactory, AbstractModelObject $modelObject)
    {
        $this->connection = $connectionFactory->getConnection($modelObject->getDatabase(), getenv('DB_HOST'), getenv('DB_USER'), getenv('DB_PASSWORD'));

        $this->modelObject = $modelObject;
    }

    /**
     * @return Filter|LogicalFilter
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * @param string $column
     * @param string $op
     * @param string $value
     * @return Filter
     */
    public function makeFilter($column, $op, $value)
    {
        return new Filter($this->modelObject->getTable(), $column, $op, $value);
    }

    /**
     * @param LogicalFilter $filter
     *
     * @return $this
     */
    public function setFilter( LogicalFilter $filter)
    {
        $this->filter = $filter;

        return $this;
    }

    /**
     * @param int $limit
     * @return $this
     */
    public function limit($limit)
    {
        $this->limit = (int)$limit;

        return $this;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param bool $sqlCalcFoundRows
     * @return $this
     */
    public function sqlCalcFoundRows($sqlCalcFoundRows = true)
    {
        $this->sqlCalcFoundRows = $sqlCalcFoundRows;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSqlCalcFoundRows()
    {
        return $this->sqlCalcFoundRows;
    }

    /**
     * @param int $offset
     * @return $this
     */
    public function offset($offset)
    {
        $this->offset = (int)$offset;

        return $this;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param string $column
     * @param string $direction
     * @return $this
     */
    public function sort($column, $direction = self::SORT_ASC)
    {
        $this->sort[] = [$column, $direction];

        return $this;
    }

    /**
     * @return array
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @return \PDOStatement
     * @throws \Exception
     */
    public function getPreparedStatement()
    {
        $fields = array_map(function($propName) {
            return "`{$this->modelObject->getTable()}`.`{$propName}`";
        }, $this->modelObject->getPropNames());

        $sortings = [];
        foreach ($this->getSort() as $sort)
        {
            list($column, $direction) = $sort;
            $sortings[] = "`{$this->modelObject->getTable()}`.`{$column}` {$direction}";
        }



        $this->sql = "SELECT " .
            ($this->isSqlCalcFoundRows() ? "SQL_CALC_FOUND_ROWS " : "") .
            implode(", ", $fields) . " " .
            "FROM `{$this->modelObject->getDatabase()}`.`{$this->modelObject->getTable()}` " .
            ($this->getFilter() ? "WHERE " . (string)$this->getFilter() : "" ) . " " .
            (count($sortings) ? "ORDER BY " . implode(', ', $sortings) : "" ) . " " .
            "LIMIT {$this->getOffset()}, {$this->getLimit()}";

        if (!$statement = $this->connection->prepare($this->sql))
        {
            throw new \Exception("Cannot create PDO prepared statement with sql " . $this->sql . PHP_EOL .
                "Error code: {$this->connection->errorCode()}, error: " . var_export($this->connection->errorInfo(), true));
        }

        if (!$this->getFilter())
        {
            return $statement;
        }

        foreach ($this->getFilter()->getBindings() as $binding)
        {
            if (!$statement->bindValue($binding->parameter, $binding->value, $binding->type))
            {
                throw new \Exception("Cannot bind value to statement " . var_export($binding, true) . PHP_EOL .
                    "Error code: {$statement->errorCode()}, error: " . var_export($statement->errorInfo(), true));
            }
        }

        return $statement;
    }

    /**
     * @return Iterator
     */
    public function iterator()
    {
        return new Iterator($this, $this->connection, $this->modelObject);
    }

    /**
     * @return IteratorUnbuffered
     */
    public function iteratorUnbuffered()
    {
        return new IteratorUnbuffered($this, $this->connection, $this->modelObject);
    }
}