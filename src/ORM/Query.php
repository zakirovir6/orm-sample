<?php
/**
 * Created by PhpStorm.
 * User: igorek
 * Date: 10.01.18
 * Time: 0:55
 */

namespace TestWork\ORM;


use TestWork\ConnectionFactory;
use TestWork\ORM\Filter\Filters\EqualsFilter;
use TestWork\ORM\Filter\Filters\InFilter;
use TestWork\ORM\Iterator\BufferedIterator;
use TestWork\ORM\Iterator\UnbufferedIterator;
use TestWork\ORM\PostFilter\FilterInterface;
use \TestWork\ORM\Filter\FilterInterface as OrmFilterInterface;

class Query
{
    const SORT_ASC = 'ASC';
    const SORT_DESC = 'DESC';

    /** @var \PDO */
    private $connection;

    /** @var AbstractModelObject */
    private $modelObject;

    /** @var OrmFilterInterface */
    private $filter;

    /** @var FilterInterface */
    private $postFilter;

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
     * @return OrmFilterInterface
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * @param string $column
     * @param string $op
     * @param mixed $value
     * @return EqualsFilter
     */
    public function makeEqualsFilter( $column, $op, $value)
    {
        return new EqualsFilter($this->modelObject->getTable(), $column, $op, $value);
    }

    /**
     * @param string $column
     * @param array $values
     * @return InFilter
     */
    public function makeInFilter($column, array $values)
    {
        return new InFilter($this->modelObject->getTable(), $column, $values);
    }

    /**
     * @param OrmFilterInterface $filter
     *
     * @return $this
     */
    public function setFilter( OrmFilterInterface $filter)
    {
        $this->filter = $filter;

        return $this;
    }

    /**
     * @param FilterInterface $postFilter
     * @return $this
     */
    public function setPostFilter(FilterInterface $postFilter)
    {
        $this->postFilter = $postFilter;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasPostFilter()
    {
        return $this->postFilter !== null;
    }

    /**
     * @return FilterInterface
     */
    public function getPostFilter()
    {
        return $this->postFilter;
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
     * @return BufferedIterator
     */
    public function iteratorBuffered()
    {
        return new BufferedIterator($this, $this->connection, $this->modelObject);
    }

    /**
     * @return UnbufferedIterator
     */
    public function iteratorUnbuffered()
    {
        return new UnbufferedIterator($this, $this->connection, $this->modelObject);
    }
}