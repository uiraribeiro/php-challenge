<?php


namespace AppBundle\Lib\Entity;


use Doctrine\Common\Collections\ArrayCollection;

class ChangeList extends ArrayCollection
{
    public function __construct(array $elements = array())
    {
        $f = static function ($v) {
            return $v instanceof Change;
        };

        $use = array_filter($elements, $f);

        parent::__construct($use);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function isChanged(string $key) :bool
    {
        return $this->contains($key);
    }

    /**
     * @param $key
     * @return mixed|null
     */
    public function getChange($key)
    {
        return $this->get($key);
    }
}