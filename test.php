<?php

require_once (__DIR__ . '/vendor/autoload.php');

$connectionFactory = new \TestWork\ConnectionFactory();

try
{
    $usersModel = \TestWork\Models\Users::getNew();
    $usersAboutModel = \TestWork\Models\UsersAbout::getNew();
    
    $queryUsersAboutModel = new \TestWork\ORM\Query( $connectionFactory, $usersAboutModel );
    $filter1 = new \TestWork\ORM\Filter\Aggregator(\TestWork\ORM\Filter\Aggregator::OP_OR);
    $filter1->add($queryUsersAboutModel->makeEqualsFilter('user', '=', 100 ));
    $filter1_1 = new \TestWork\ORM\Filter\Aggregator();
    $filter1_1->add($queryUsersAboutModel->makeEqualsFilter('item', '=', 'country'));
    $filter1_1->add($queryUsersAboutModel->makeEqualsFilter('value', '!=', 'Russia'));
    $filter1->add($filter1_1);

    $postFilter = new \TestWork\ORM\PostFilter\Filters\GravatarFilter();

    $it = $queryUsersAboutModel
        ->setFilter($filter1)
        ->limit(100)
        ->sqlCalcFoundRows()
        ->iteratorUnbuffered();

    $userIDs = [];
    /** @var \TestWork\Models\UsersAbout $usersAbout */
    foreach ($it as $usersAbout)
    {
        $userIDs[$usersAbout->user] = null;
    }

    $usersQuery = new \TestWork\ORM\Query($connectionFactory, $usersModel);
    $itUsers = $usersQuery
        ->setFilter($usersQuery->makeInFilter('id', array_keys($userIDs)))
        ->setPostFilter(new \TestWork\ORM\PostFilter\Filters\GravatarFilter(false))
        ->limit(count($userIDs))
        ->iteratorBuffered();

    /** @var \TestWork\Models\Users $user */
    foreach ($itUsers as $user)
    {
        $s = $user;
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