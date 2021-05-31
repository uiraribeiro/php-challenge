<?php


namespace AppBundle\Lib\Stock\Validator;


use Symfony\Component\Validator\Constraint;

/**
 * Class StockUpRequestValid
 * @package AppBundle\Lib\Item\Validator
 * @Annotation
 */
class StockUpRequestValid extends Constraint
{
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

}