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

    public function __construct(ConnectionFactory $connectionFactory, AbstractModelObject $modelObject)
    {
        $this->connection = $connectionFactory->getConnection($modelObject->getDatabase(), getenv('DB_HOST'), getenv('DB_USER'), getenv('DB_PASSWORD'));

        $this->modelObject = $modelObject;
    }

    /**
     * @param LogicalFilter $filter
     *
     * @return $this
     */
    public function filter(LogicalFilter $filter)
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
     * @param int $offset
     * @return $this
     */
    public function offset($offset)
    {
        $this->offset = (int)$offset;

        return $this;
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

    public function iterator()
    {

    }

    public function iteratorUnbuffered()
    {

    }
}