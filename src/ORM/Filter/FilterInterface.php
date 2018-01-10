<?php
/**
 * Created by PhpStorm.
 * User: igorek
 * Date: 11.01.18
 * Time: 1:46
 */

namespace TestWork\ORM\Filter;


interface FilterInterface
{
    public function __toString();

    /**
     * @return Binding[]
     */
    public function getBindings();
}