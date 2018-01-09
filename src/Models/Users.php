<?php
/**
 * Created by PhpStorm.
 * User: igorek
 * Date: 09.01.18
 * Time: 23:50
 */

namespace TestWork\Models;

use TestWork\ORM\AbstractModelObject;

/**
 * Class Users
 * @package TestWork\Models\Users
 *
 * @property int $id
 * @property string $email
 * @property string $password
 * @property string $role
 * @property string $reg_date
 * @property string $last_visit
 */
class Users extends AbstractModelObject
{
    protected $properties = [
        'id',
        'email',
        'password',
        'role',
        'reg_date',
        'last_visit',
    ];

    /**
     * Users constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->database = getenv('DATABASE_USERS');
        $this->table = 'users';

        parent::__construct();
    }
}