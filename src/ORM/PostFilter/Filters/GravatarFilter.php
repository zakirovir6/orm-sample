<?php
/**
 * Created by PhpStorm.
 * User: igorek
 * Date: 11.01.18
 * Time: 0:25
 */

namespace TestWork\ORM\PostFilter\Filters;


use TestWork\Models\Users;
use TestWork\ORM\AbstractModelObject;
use TestWork\ORM\PostFilter\FilterInterface;

class GravatarFilter implements FilterInterface
{
    const GRAVATAR_URL_PATTERN = "https://www.gravatar.com/avatar/%s?d=404";

    /** @var bool */
    private $needExists = true;

    public function __construct( $needExists = true)
    {
        $this->needExists = $needExists;
    }


    /**
     * @param AbstractModelObject $modelObject
     * @return bool
     * @throws \Exception
     */
    public function test(AbstractModelObject $modelObject)
    {
        if (! $modelObject instanceof Users)
            throw new \Exception("This filter is applied only to Users model");

        if ($this->needExists)
        {
            return $this->avatarIsExists($modelObject);
        }
        else
        {
            return !$this->avatarIsExists($modelObject);
        }
    }

    /**
     * @param Users $modelObject
     * @return bool
     * @throws \Exception
     */
    private function avatarIsExists(Users $modelObject)
    {
        $url = sprintf(self::GRAVATAR_URL_PATTERN, md5($modelObject->email));
        $curl = \curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 5);
        curl_setopt($curl, CURLOPT_FILETIME, true);
        curl_setopt($curl, CURLOPT_NOBODY, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_exec($curl);
        $info = curl_getinfo($curl);

        if (! in_array($info['http_code'], [200, 404, 304]))
        {
            throw new \Exception("Unexpected http code " . $info['http_code'] . " from " . $url);
        }

        return $info['http_code'] !== 404;
    }

}