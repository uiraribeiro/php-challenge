<?php


namespace AppBundle\Lib\Subscription\Validator;


use Symfony\Component\Validator\Constraint;

/**
 * Class SubscriptionRequestValid
 * @package AppBundle\Lib\Subscription\Validator
 * @Annotation
 */
class SubscriptionRequestValid extends Constraint
{
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}