<?php


namespace AppBundle\Lib\Product;


use AppBundle\Entity\Item;
use AppBundle\Entity\Product;
use AppBundle\Lib\AbstractService;
use AppBundle\Lib\Tools;
use AppBundle\Lib\ValidationException;

class ProductService extends AbstractService
{

    public static function getPrototype(?Item $item = null) :Product
    {
        $start = Tools::getFirstDayDateForNextMonth(new \DateTime());
        $end = clone $start;
        $end->add(new \DateInterval('P12M'));
        $end = Tools::getLastDayDateTimeForMonth($end);

        $product = new Product();
        $product->setItem($item);
        $product->setAvailableFrom($start);
        $product->setAvailableUntil($end);

        return $product;

    }

    /**
     * @param Product $product
     * @return Product
     * @throws ValidationException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function create(Product $product) :Product
    {
        $errors = $this->getValidator()->validate($product, ['Default', 'create']);
        if (0 !== count($errors)) {
            throw new ValidationException('Product is not valid', $errors);
        }

        $em = $this->getEntityManager();
        $em->persist($product);
        $em->flush();

        $this->getLogger()->info('Created product with name: '.$product->getName());
        $event = new ProductCreatedEvent($product);
        $this->getDispatcher()->dispatch(ProductCreatedEvent::NAME, $event);

        $em->refresh($product);

        return $product;
    }

    /**
     * @param Product $product
     * @return Product
     * @throws ValidationException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \AppBundle\Lib\Entity\NotTrackedException
     */
    public function update(\AppBundle\Entity\Product $product) : Product
    {
        $errors = $this->getValidator()->validate($product, ['Default', 'update']);
        if (0 !== count($errors)) {
            throw new ValidationException('Product is not valid', $errors);
        }

        $changeList = $product->getChanges();

        $em = $this->getEntityManager();
        $em->persist($product);
        $em->flush();

        $this->getLogger()->info('Updated Product with name: '.$product->getName());
        $event = new ProductUpdatedEvent($product, $changeList);
        $this->getDispatcher()->dispatch(ProductUpdatedEvent::NAME, $event);

        $em->refresh($product);

        return $product;
    }

    protected function getRepository(): string
    {
        return Product::class;
    }
}