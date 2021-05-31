<?php


namespace AppBundle\Tests\Form\Types;

/**
 * Class SampleClass, class is used solely for testing.
 * @package AppBundle\Tests\Form\Types
 */
class SampleClass
{
    private $item;

    /**
     * @return mixed
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @param mixed $item
     * @return SampleClass
     */
    public function setItem($item)
    {
        $this->item = $item;
        return $this;
    }


}