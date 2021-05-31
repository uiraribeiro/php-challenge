<?php


namespace AppBundle\Lib\Item;


use AppBundle\Entity\Item;
use AppBundle\Entity\Product;
use AppBundle\Lib\AbstractService;
use AppBundle\Lib\ValidationException;

class ItemService extends AbstractService
{

    /**
     * @param Item $item
     * @return Item
     * @throws ValidationException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function create(Item $item) :Item
    {
        $errors = $this->getValidator()->validate($item, ['Default', 'create']);
        if (0 !== count($errors)) {
            throw new ValidationException('Item is not valid', $errors);
        }

        $em = $this->getEntityManager();
        $em->persist($item);
        $em->flush();

        $this->getLogger()->info('Created item with name: '.$item->getName());
        $event = new ItemCreatedEvent($item);
        $this->getDispatcher()->dispatch(ItemCreatedEvent::NAME, $event);

        $em->refresh($item);

        return $item;
    }

    /**
     * @param Item $item
     * @return Item
     * @throws ValidationException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function update(Item $item) :Item
    {
        $errors = $this->getValidator()->validate($item, ['Default', 'update']);
        if (0 !== count($errors)) {
            throw new ValidationException('Item is not valid', $errors);
        }

        $em = $this->getEntityManager();
        $em->persist($item);
        $em->flush();

        $this->getLogger()->info('Updated item with name: '.$item->getName());
        $event = new ItemUpdatedEvent($item);
        $this->getDispatcher()->dispatch(ItemUpdatedEvent::NAME, $event);

        $em->refresh($item);

        return $item;
    }

    protected function getRepository(): string
    {
        return Item::class;
    }
}