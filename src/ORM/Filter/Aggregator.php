<?php
/**
 * Created by PhpStorm.
 * User: igorek
 * Date: 10.01.18
 * Time: 1:15
 */

namespace TestWork\ORM\Filter;

class Aggregator implements FilterInterface
{
    const OP_AND = ' AND ';
    const OP_OR = ' OR ';

    /** @var FilterInterface[] */
    private $filterStack = [];

    /** @var string */
    private $operation;

    /**
     * Aggregator constructor.
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
     * @param FilterInterface $filter
     *
     * @throws \Exception
     */
    public function add(FilterInterface $filter)
    {
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
     * @return Binding[]
     */
    public function getBindings()
    {
        $bindings = [];

        foreach ($this->filterStack as $filter)
        {
            $bindings = array_merge($bindings, $filter->getBindings());
        }

        return $bindings;
    }
}