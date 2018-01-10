<?php
/**
 * Created by PhpStorm.
 * User: igorek
 * Date: 09.01.18
 * Time: 23:31
 */

namespace TestWork\ORM;

class AbstractModelObject
{
    /** @var string */
    protected $database = '';

    /** @var string */
    protected $table = '';

    /** @var array */
    protected $properties = [];

    /** @var array */
    private $data = [];

    /**
     * @return static
     */
    final public static function getNew()
    {
        return new static();
    }

    /**
     * @return string
     */
    final public function getTable()
    {
        return $this->table;
    }

    /**
     * @return string
     */
    final public function getDatabase()
    {
        return $this->database;
    }

    /**
     * @return array
     */
    final public function getPropNames()
    {
        return $this->properties;
    }

    /**
     * AbstractModelObject constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        if (! count($this->properties))
            throw new \Exception('Empty properties');

        if (! $this->database)
            throw new \Exception('Database not set');

        if (! $this->table)
            throw new \Exception('Table not set');

        $this->data = array_fill_keys($this->properties, null);
    }


    /**
     * @param $name
     * @return mixed
     * @throws \Exception
     */
    public function __get( $name )
    {
        if (in_array($name, $this->getPropNames()))
        {
            return $this->data[$name];
        }

        throw new \Exception('Model ' . get_class($this) . ' doesnot have property ' . $name);
    }

    /**
     * @param $name
     * @param $value
     * @throws \Exception
     */
    public function __set( $name, $value )
    {
        if (!in_array($name, $this->getPropNames()))
        {
            throw new \Exception('Model ' . get_class($this) . ' doesnot have property ' . $name);
        }

        $this->data[$name] = $value;
    }


}