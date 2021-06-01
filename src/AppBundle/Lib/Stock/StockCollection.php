<?php


namespace AppBundle\Lib\Stock;


use Doctrine\Common\Collections\ArrayCollection;

class StockCollection extends ArrayCollection
{
    public function __construct(array $elements = array())
    {
        $elements = $this->convertTtems($elements);

        parent::__construct($elements);
    }

    public function convertItems(array $items) :array
    {
        $r = [];
        foreach ($items As $item) {
            if ($item instanceof StockItem) {
                $r[] = $item;
                continue;
            }
            $r[] = new StockItem($item);
        }
    }
}