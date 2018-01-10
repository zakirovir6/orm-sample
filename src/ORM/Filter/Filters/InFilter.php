<?php
/**
 * Created by PhpStorm.
 * User: igorek
 * Date: 11.01.18
 * Time: 2:04
 */

namespace TestWork\ORM\Filter\Filters;


use TestWork\ORM\Filter\Binding;
use TestWork\ORM\Filter\BindingTrait;
use TestWork\ORM\Filter\FilterInterface;

class InFilter implements FilterInterface
{
    use BindingTrait;

    /** @var string */
    private $table;
    /** @var string */
    private $column;
    /** @var array */
    private $values = [];

    /** @var Binding[] */
    private $bindings = [];

    /**
     * InFilter constructor.
     * @param $table
     * @param $column
     * @param array $values
     */
    public function __construct( $table, $column, array $values)
    {
        $this->table = $table;
        $this->column = $column;
        $this->values = $values;

        foreach ($this->values as $value)
        {
            $this->bindings[] = $this->getBinding($value);
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $parameters = [];
        foreach ($this->bindings as $binding)
        {
            $parameters[] = $binding->parameter;
        }

        $parametersStr = implode(',', $parameters);
        return "(`{$this->table}`.`{$this->column}` IN ({$parametersStr}))";
    }

    /**
     * @return Binding[]
     */
    public function getBindings()
    {
        return $this->bindings;
    }

}