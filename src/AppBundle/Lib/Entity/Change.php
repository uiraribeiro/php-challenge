<?php
/**
 * smapi2
 * @author   M. Seifert <m.seifert@syseleven.de>
  * @package SysEleven\Library\BaseBundle\Lib
 */
namespace AppBundle\Lib\Entity;

 
/**
 * Class Change
 *
 * @author M. Seifert <m.seifert@syseleven.de>
 */
class Change 
{
    /**
     * @var null
     */
    public $oldValue = null;

    /**
     * @var null
     */
    public $newValue = null;

    /**
     * @var string
     */
    public $key;


    /**
     * Change constructor.
     * @param string $key
     * @param null $oldValue
     * @param null $newValue
     */
    public function __construct(string $key, $oldValue = null, $newValue = null)
    {
        $this->key = $key;
        $this->oldValue = $oldValue;
        $this->newValue = $newValue;
    }

    /**
     * @return null
     */
    public function getNewValue()
    {
        return $this->newValue;
    }

    /**
     * @param mixed $newValue
     *
     * @return Change
     */
    public function setNewValue($newValue)
    {
        $this->newValue = $newValue;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOldValue()
    {
        return $this->oldValue;
    }

    /**
     * @param null $oldValue
     *
     * @return Change
     */
    public function setOldValue($oldValue)
    {
        $this->oldValue = $oldValue;

        return $this;
    }


    /**
     * @return bool
     */
    public function isChanged()
    {
        return $this->oldValue != $this->newValue;
    }




}
 