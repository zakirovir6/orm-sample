<?php
/**
 * Created by PhpStorm.
 * User: igorek
 * Date: 10.01.18
 * Time: 20:52
 */

namespace TestWork\ORM;


class FilterBinding
{
    /** @var string */
    public $parameter;
    /** @var mixed */
    public $value;
    /** @var bool */
    public $type;

    /**
     * FilterBinding constructor.
     * @param string $namedPlaceholder
     * @param mixed $value
     * @param bool $type
     */
    public function __construct( $namedPlaceholder, $value, $type )
    {
        $this->parameter = $namedPlaceholder;
        $this->value = $value;
        $this->type = $type;
    }
}