<?php
/**
 * Created by PhpStorm.
 * User: igorek
 * Date: 10.01.18
 * Time: 0:26
 */

namespace TestWork\Models;

use TestWork\ORM\AbstractModelObject;

/**
 * Class UsersAbout
 * @package TestWork\Models\Users
 *
 * @property int $id
 * @property int $user
 * @property string $item
 * @property string $value
 * @property string $up_date
 */
class UsersAbout extends AbstractModelObject
{
    const ITEM_country = 'country';
    const ITEM_firstname = 'firstname';
    const ITEM_state = 'state';

    protected $properties = [
        'id',
        'user',
        'item',
        'value',
        'up_date',
    ];

    /**
     * UsersAbout constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->database = getenv('DATABASE_USERS_ABOUT');
        $this->table = 'users_about';

        parent::__construct();
    }
}