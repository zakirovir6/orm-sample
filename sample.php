<?php

require_once (__DIR__ . '/vendor/autoload.php');

$connectionFactory = new \TestWork\ConnectionFactory();

/**
 * @param \TestWork\Models\Users[]|Iterator $users
 *
 * @return array
 */
function makeResponse($users)
{
    $result = [];
    foreach ($users as $user)
    {
        $result[] = [
            'users.id' => $user->id,
            'users.email' => $user->email,
            'users.role' => $user->role,
            'users.reg_date' => $user->reg_date
        ];
    }

    return $result;
}

$usersModel = \TestWork\Models\Users::getNew();
$usersAboutModel = \TestWork\Models\UsersAbout::getNew();

//-------------------------------------------------------------------------
//ID(users.id)
//-------------------------------------------------------------------------
$query1 = new \TestWork\ORM\Query($connectionFactory, $usersModel);
$filter1 = $query1->makeEqualsFilter('id', '=', 100);
$query1->setFilter($filter1);
$response1 = makeResponse($query1->iteratorUnbuffered());


//-------------------------------------------------------------------------
//E-Mail (users.email)
//-------------------------------------------------------------------------
$query2 = new \TestWork\ORM\Query($connectionFactory, $usersModel);
$filter2 = $query2->makeEqualsFilter('email', '=', 'a@a.ru');
$query2->setFilter($filter2);
$response2 = makeResponse($query2->iteratorUnbuffered());

//-------------------------------------------------------------------------
//Страна (users_about.item = "country")
//-------------------------------------------------------------------------
$query3_1 = new \TestWork\ORM\Query($connectionFactory, $usersAboutModel);
$filter3_1 = new \TestWork\ORM\Filter\Aggregator();
$filter3_1->add($query3_1->makeEqualsFilter('item', '=', \TestWork\Models\UsersAbout::ITEM_country));
$filter3_1->add($query3_1->makeEqualsFilter('value', '=', 'Russia'));
$query3_1
    ->setFilter($filter3_1)
    ->limit(500);
$userIDs3 = [];
/** @var \TestWork\Models\UsersAbout $usersAbout */
foreach ($query3_1->iteratorBuffered() as $usersAbout)
{
    $userIDs3[$usersAbout->user] = null;
}
$query3_2 = new \TestWork\ORM\Query($connectionFactory, $usersModel);
$query3_2
    ->setFilter($query3_2->makeInFilter('id', array_keys($userIDs3)))
    ->limit(count($userIDs3));
$response3 = makeResponse($query3_2->iteratorUnbuffered());

//-------------------------------------------------------------------------
//Имя (users_about.item = "firstname")
//-------------------------------------------------------------------------
$query4_1 = new \TestWork\ORM\Query($connectionFactory, $usersAboutModel);
$filter4_1 = new \TestWork\ORM\Filter\Aggregator();
$filter4_1->add($query4_1->makeEqualsFilter('item', '=', \TestWork\Models\UsersAbout::ITEM_firstname));
$filter4_1->add($query4_1->makeEqualsFilter('value', '=', 'Igor'));
$query4_1
    ->setFilter($filter4_1)
    ->limit(500);
$userIDs4 = [];
/** @var \TestWork\Models\UsersAbout $usersAbout */
foreach ($query4_1->iteratorBuffered() as $usersAbout)
{
    $userIDs4[$usersAbout->user] = null;
}
$query4_2 = new \TestWork\ORM\Query($connectionFactory, $usersModel);
$query4_2
    ->setFilter($query4_2->makeInFilter('id', array_keys($userIDs4)))
    ->limit(count($userIDs4));
$response4 = makeResponse($query4_2->iteratorUnbuffered());

//-------------------------------------------------------------------------
//Состояние пользователя (users_about.item = "state")
//-------------------------------------------------------------------------
$query5_1 = new \TestWork\ORM\Query($connectionFactory, $usersAboutModel);
$filter5_1 = new \TestWork\ORM\Filter\Aggregator();
$filter5_1->add($query5_1->makeEqualsFilter('item', '=', \TestWork\Models\UsersAbout::ITEM_state));
$filter5_1->add($query5_1->makeEqualsFilter('value', '=', 'CA'));
$query5_1
    ->setFilter($filter5_1)
    ->limit(500);
$userIDs5 = [];
/** @var \TestWork\Models\UsersAbout $usersAbout */
foreach ($query5_1->iteratorBuffered() as $usersAbout)
{
    $userIDs5[$usersAbout->user] = null;
}
$query5_2 = new \TestWork\ORM\Query($connectionFactory, $usersModel);
$query5_2
    ->setFilter($query5_2->makeInFilter('id', array_keys($userIDs5)))
    ->limit(count($userIDs5));
$response5 = makeResponse($query5_2->iteratorUnbuffered());

//-------------------------------------------------------------------------
//[NEW] Наличие Gravatar (https://www.gravatar.com)
//-------------------------------------------------------------------------
$query6 = new \TestWork\ORM\Query($connectionFactory, $usersModel);
$query6
    ->setPostFilter(new \TestWork\ORM\PostFilter\Filters\GravatarFilter())
    ->limit(5)
    ->offset(30);
$response6 = makeResponse($query6->iteratorUnbuffered());

//-------------------------------------------------------------------------
//((ID = 1000) ИЛИ (Страна != Россия))
//-------------------------------------------------------------------------
$query7_1 = new \TestWork\ORM\Query($connectionFactory, $usersAboutModel);
$aggregator7 = new \TestWork\ORM\Filter\Aggregator(\TestWork\ORM\Filter\Aggregator::OP_OR);
$aggregator7->add($query7_1->makeEqualsFilter('user', '=', 1000));
$aggregator7_1 = new \TestWork\ORM\Filter\Aggregator();
$aggregator7->add($aggregator7_1);
$aggregator7_1->add($query7_1->makeEqualsFilter('item', '=', \TestWork\Models\UsersAbout::ITEM_country));
$aggregator7_1->add($query7_1->makeEqualsFilter('value', '!=', 'Russia'));
$query7_1
    ->setFilter($aggregator7)
    ->limit(100);
$userIDs7 = [];
/** @var \TestWork\Models\UsersAbout $usersAbout */
foreach ( $query7_1->iteratorBuffered() as $usersAbout)
{
    $userIDs7[$usersAbout->user] = null;
}
$query7_2 = new \TestWork\ORM\Query($connectionFactory, $usersModel);
$query7_2
    ->setFilter($query7_2->makeInFilter('id', array_keys($userIDs7)))
    ->limit(count($userIDs7));
$response7 = makeResponse($query7_2->iteratorBuffered());

//-------------------------------------------------------------------------
//((Страна = Россия) И (Состояние пользователя != active) И (Граватар = Нет))
//-------------------------------------------------------------------------
//Здесь по факту идет join таблицы users_about на саму себя.
//В плане производительности на текущей структуре данных в mysql - это очень плохой запрос. Реализация с моей ORM невозможна


//-------------------------------------------------------------------------
//((((Страна != Россия) ИЛИ (Состояние пользователя = active)) И (E-Mail = user@domain.com)) ИЛИ (Граватар = Есть))
//-------------------------------------------------------------------------
//Аналогично предыдущему запросу - плохой по производительности.
//При этом еще добавляется ИЛИ условию по граватару - чтобы его выполнить - надо обойти всю таблицу users и отправить запрос на граватар для каждого пользователя