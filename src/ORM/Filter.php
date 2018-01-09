<?php
/**
 * Created by PhpStorm.
 * User: igorek
 * Date: 10.01.18
 * Time: 1:06
 */

namespace TestWork\ORM;

class Filter
{
    /** @var string */
    public $op;
    /** @var string */
    public $column;
    /** @var mixed */
    public $value;
    /** @var int */
    public $bindingType;
    /** @var string */
    public $namedPlaceholder;

    /**
     * Filter constructor.
     * @param string $column
     * @param string $op
     * @param mixed $value
     * @throws \Exception
     */
    public function __construct($column, $op, $value)
    {
        if (! in_array($op, ['=', '!=', '>', '<', '>=', '<=']))
        {
            throw new \Exception('Unsupported operator');
        }

        $this->column = $column;
        $this->op = $op;
        switch (true)
        {
            case (is_int($this->value)):
            case (is_double($this->value)):
                $type = \PDO::PARAM_INT;
                break;
            case (is_string($this->value)):
                $type = \PDO::PARAM_STR;
                break;
            case (is_bool($this->value)):
                $type = \PDO::PARAM_BOOL;
                break;
            default:
                $type = \PDO::PARAM_STR;
        }

        $this->value = $value;
        $this->bindingType = $type;
        //here should be snowflake algorithm
        $this->namedPlaceholder = \uniqid(':pdo_', true);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return "(`{$this->column}` {$this->op} {$this->namedPlaceholder} <{$this->value}>)";
    }

    /**
     * @return array
     */
    public function getBinding()
    {
        return [$this->namedPlaceholder, $this->value, $this->bindingType];
    }
}