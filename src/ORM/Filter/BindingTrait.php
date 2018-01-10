<?php
/**
 * Created by PhpStorm.
 * User: igorek
 * Date: 11.01.18
 * Time: 2:12
 */

namespace TestWork\ORM\Filter;


trait BindingTrait
{
    /**
     * @param mixed $value
     * @return Binding
     */
    public function getBinding($value)
    {
        switch ( true )
        {
            case ( is_int( $value ) ):
            case ( is_double( $value ) ):
                $type = \PDO::PARAM_INT;
                break;
            case ( is_string( $value ) ):
                $type = \PDO::PARAM_STR;
                break;
            case ( is_bool( $value ) ):
                $type = \PDO::PARAM_BOOL;
                break;
            default:
                $type = \PDO::PARAM_STR;
        }

        //here should be snowflake algorithm
        return new Binding( \uniqid( ':pdo_' ), $value, $type );
    }
}