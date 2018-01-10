<?php
/**
 * Created by PhpStorm.
 * User: igorek
 * Date: 11.01.18
 * Time: 0:05
 */

namespace TestWork\ORM\PostFilter;


use TestWork\ORM\AbstractModelObject;

interface FilterInterface
{
    public function test(AbstractModelObject $modelObject);
}