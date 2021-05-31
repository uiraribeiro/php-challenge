<?php


namespace AppBundle\Lib\Entity;


use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

/**
 * Class ChangeListener
 *
 * @author M. Seifert <m.seifert@syseleven.de>
 * @package SysEleven\Library\BaseBundle\Lib
 */
class ChangeListener
{
    /**
     * @param LifecycleEventArgs $args
     */
    public function postLoad(LifecycleEventArgs $args) :void
    {
        $entity = $args->getObject();

        if ($entity instanceof ChangeAwareEntityInterface) {
            $entity->initCleanData();
        }
    }

}