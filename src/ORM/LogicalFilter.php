<?php
/**
 * Created by PhpStorm.
 * User: igorek
 * Date: 10.01.18
 * Time: 1:15
 */

namespace TestWork\ORM;

class LogicalFilter
{
    const OP_AND = ' AND ';
    const OP_OR = ' OR ';

    /** @var Filter[]|LogicalFilter[] */
    private $filterStack = [];

    /** @var string */
    private $operation;

    /**
     * LogicalFilter constructor.
     * @param string $operation
     * @throws \Exception
     */
    public function __construct($operation = self::OP_AND)
    {
        if (! in_array($operation, [self::OP_AND, self::OP_OR]))
        {
            throw new \Exception('Unsupported operation [AND, OR]');
        }

        $this->operation = $operation;
    }


    /**
     * @param Filter|LogicalFilter $filter
     *
     * @throws \Exception
     */
    public function add($filter)
    {
        if ((! $filter instanceof Filter) &&
            (! $filter instanceof LogicalFilter))
        {
            throw new \Exception('Filter must be instance of Filter or LogicalFilter');
        }

        $this->filterStack[] = $filter;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return '(' . implode($this->operation, $this->filterStack) . ')';
    }

    /**
     * @return FilterBinding[]
     */
    public function getBindings()
    {
        $bindings = [];

        foreach ($this->filterStack as $filter)
        {
            if ($filter instanceof Filter)
            {
                $bindings[] = $filter->getBinding();
                continue;
            }

            if ($filter instanceof LogicalFilter)
            {
                $bindings = array_merge($bindings, $filter->getBindings());
                continue;
            }
        }

        return $bindings;
    }
}