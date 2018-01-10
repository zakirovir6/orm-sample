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
    /** @var string */
    private $table;
    /** @var FilterBinding */
    private $binding;

    /**
     * Filter constructor.
     * @param string $table
     * @param string $column
     * @param string $op
     * @param mixed $value
     * @throws \Exception
     */
    public function __construct($table, $column, $op, $value)
    {
        if (! in_array($op, ['=', '!=', '>', '<', '>=', '<=', 'IN']))
        {
            throw new \Exception('Unsupported operator');
        }

        $this->table = $table;
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

        //here should be snowflake algorithm
        $this->binding = new FilterBinding(\uniqid(':pdo_'), $value, $type);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return "(`{$this->table}`.`{$this->column}` {$this->op} {$this->binding->parameter})";
    }

    /**
     * @return FilterBinding
     */
    public function getBinding()
    {
        return $this->binding;
    }
}