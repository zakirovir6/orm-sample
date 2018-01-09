<?php

require_once (__DIR__ . '/vendor/autoload.php');

$connectionFactory = new \TestWork\ConnectionFactory();

try
{
    $usersModel = \TestWork\Models\Users::getEmpty();

    $filter = new \TestWork\ORM\LogicalFilter();
    $filter->add( new TestWork\ORM\Filter( 'id', '=', 10 ) );
    $filter->add( new TestWork\ORM\Filter( 'id', '=', 20 ) );
    $subFilter = new \TestWork\ORM\LogicalFilter( \TestWork\ORM\LogicalFilter::OP_OR );
    $subFilter->add( new \TestWork\ORM\Filter( 'id', '=', 30 ) );
    $subFilter->add( new \TestWork\ORM\Filter( 'id', '=', 40 ) );
    $subFilter1 = new \TestWork\ORM\LogicalFilter(\TestWork\ORM\LogicalFilter::OP_OR);
    $subFilter11 = new \TestWork\ORM\LogicalFilter(\TestWork\ORM\LogicalFilter::OP_OR);
    $subFilter11->add(new \TestWork\ORM\Filter('id', '=', 111));
    $subFilter11->add(new \TestWork\ORM\Filter('id', '=', 112));
    $subFilter11->add(new \TestWork\ORM\Filter('id', '=', 113));
    $subFilter12 = new \TestWork\ORM\LogicalFilter(\TestWork\ORM\LogicalFilter::OP_OR);
    $subFilter12->add(new \TestWork\ORM\Filter('id', '=', 121));
    $subFilter12->add(new \TestWork\ORM\Filter('id', '=', 122));
    $subFilter12->add(new \TestWork\ORM\Filter('id', '=', 123));
    $subFilter1->add($subFilter11);
    $subFilter1->add($subFilter12);
    $filter->add($subFilter1);

    $filter->add($subFilter);

    $query = new \TestWork\ORM\Query( $connectionFactory, $usersModel );
    $query->filter($filter)->iterator();

    $strFilter = (string)$filter;
    $bindings = $filter->getBindings();

    $s = $strFilter;
    $b = $bindings;
}
catch (\Exception $e)
{
    throw $e;
}
/*
(
    (`id` = :pdo_5a55410820159 <10>) AND
    (`id` = :pdo_5a55410820219 <20>) AND
    (
        (
            (`id` = :pdo_5a55410820363 <111>) OR
            (`id` = :pdo_5a554108203c9 <112>) OR
            (`id` = :pdo_5a5541082042f <113>)
        ) OR
        (
            (`id` = :pdo_5a5541082049c <121>) OR
            (`id` = :pdo_5a5541082054d <122>) OR
            (`id` = :pdo_5a554108205a8 <123>)
        )
    ) AND
    (
        (`id` = :pdo_5a5541082027c <30>) OR
        (`id` = :pdo_5a554108202ed <40>)
    )
)
 *
 *
 */