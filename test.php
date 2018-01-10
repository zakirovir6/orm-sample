<?php

require_once (__DIR__ . '/vendor/autoload.php');

$connectionFactory = new \TestWork\ConnectionFactory();

try
{
    $usersModel = \TestWork\Models\Users::getNew();
    $usersAboutModel = \TestWork\Models\UsersAbout::getNew();
    
    $queryUsersAboutModel = new \TestWork\ORM\Query( $connectionFactory, $usersAboutModel );
    $filter1 = new \TestWork\ORM\LogicalFilter(\TestWork\ORM\LogicalFilter::OP_OR);
    $filter1->add($queryUsersAboutModel->makeFilter('user', '=', 100 ));
    $filter1_1 = new \TestWork\ORM\LogicalFilter();
    $filter1_1->add($queryUsersAboutModel->makeFilter('item', '=', 'country'));
    $filter1_1->add($queryUsersAboutModel->makeFilter('value', '!=', 'Russia'));
    $filter1->add($filter1_1);

    $it = $queryUsersAboutModel
        //->setFilter($filter1)
        ->sqlCalcFoundRows()
        ->iterator();
    foreach ( $it as $key => $item)
    {
        $s = $item;
    }

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