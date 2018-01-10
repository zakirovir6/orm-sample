<?php
/**
 * Created by PhpStorm.
 * User: igorek
 * Date: 11.01.18
 * Time: 0:02
 */

namespace TestWork\ORM\PostFilter;


class Aggregator implements FilterInterface
{
    const OP_AND = '&&';
    const OP_OR = '||';

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
            throw new \Exception('Unsupported operation [&&, ||]');
        }

        $this->operation = $operation;
    }

    /**
     * @param FilterInterface $filter
     *
     * @throws \Exception
     */
    public function add($filter)
    {
        if (! $filter instanceof FilterInterface)
        {
            throw new \Exception('Filter must be instance of FilterInterface');
        }

        $this->filterStack[] = $filter;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function test()
    {
        switch ($this->operation)
        {
            case self::OP_AND:
                $result = true;
                break;
            case self::OP_OR:
                $result = false;
                break;
            default:
                throw new \Exception("Invalid operation {$this->operation}");
        }

        foreach ($this->filterStack as $filter)
        {
            switch ($this->operation)
            {
                case self::OP_AND:
                    $result = $result && $filter->test();
                    break;
                case self::OP_OR:
                    $result = $result || $filter->test();
                    break;
            }
        }

        return $result;
    }
}