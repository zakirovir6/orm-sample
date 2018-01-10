<?php
/**
 * Created by PhpStorm.
 * User: igorek
 * Date: 11.01.18
 * Time: 0:02
 */

namespace TestWork\ORM\PostFilter\Filters;


use TestWork\ORM\AbstractModelObject;
use TestWork\ORM\PostFilter\FilterInterface;

class EqualsFilter implements FilterInterface
{
    /** @var AbstractModelObject */
    private $modelObject;
    /** @var string */
    private $property;
    /** @var string */
    private $operation;
    /** @var mixed */
    private $value;

    /**
     * EqualsFilter constructor.
     * @param string $property
     * @param string $operation
     * @param mixed $value
     *
     * @throws \Exception
     */
    public function __construct($property, $operation, $value)
    {
        if (! in_array($operation, ['=', '!=']))
        {
            throw new \Exception("Unsupported operation {$operation}");
        }

        $this->property = $property;
        $this->operation = $operation;
        $this->value = $value;
    }

    /**
     * @param AbstractModelObject $modelObject
     * @return bool
     * @throws \Exception
     */
    public function test(AbstractModelObject $modelObject)
    {
        switch ($this->operation)
        {
            case "=":
                return ($modelObject->{$this->property} == $this->value);
            case '!=':
                return ($modelObject->{$this->property} != $this->value);
        }

        throw new \Exception("Unsupported operation {$this->operation}");
    }

}