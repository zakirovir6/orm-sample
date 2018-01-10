<?php
/**
 * Created by PhpStorm.
 * User: igorek
 * Date: 10.01.18
 * Time: 1:06
 */

namespace TestWork\ORM\Filter\Filters;

use TestWork\ORM\Filter\Binding;
use TestWork\ORM\Filter\BindingTrait;
use TestWork\ORM\Filter\FilterInterface;

class EqualsFilter implements FilterInterface
{
    use BindingTrait;

    /** @var string */
    public $op;
    /** @var string */
    public $column;
    /** @var mixed */
    public $value;
    /** @var string */
    private $table;
    /** @var Binding */
    private $binding;

    /**
     * EqualsFilter constructor.
     * @param string $table
     * @param string $column
     * @param string $op
     * @param mixed $value
     * @throws \Exception
     */
    public function __construct($table, $column, $op, $value)
    {
        if (! in_array($op, ['=', '!=', '>', '<', '>=', '<=']))
        {
            throw new \Exception('Unsupported operator');
        }

        $this->table = $table;
        $this->column = $column;
        $this->op = $op;

        $this->binding = $this->getBinding($value);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return "(`{$this->table}`.`{$this->column}` {$this->op} {$this->binding->parameter})";
    }

    /**
     * @return array|Binding[]
     */
    public function getBindings()
    {
        return [ $this->binding ];
    }
}