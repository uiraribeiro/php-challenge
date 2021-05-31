<?php


namespace AppBundle\Lib\Store;


use AppBundle\Entity\Customer;
use AppBundle\Entity\Store;
use AppBundle\Lib\AbstractService;
use AppBundle\Lib\ValidationException;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class StoreService extends AbstractService
{

    /**
     * Creates a new prototype of store with the given properties.
     *
     * @param Customer $customer
     * @param \DateTime|null $start
     * @return Store
     */
    public static function createPrototype(?Customer $customer = null, ?\DateTime $start = null) :Store
    {
        $start = $start ?? new \DateTime();

        $store = new Store();
        $store->setCustomer($customer);
        $store->setStartDate($start);

        return $store;
    }

    /**
     * Saves the given store to the database and propagates a StoreCreatedEvent
     *
     * @param Store $store
     * @return Store
     * @throws ValidationException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function create(Store $store) :Store
    {
        $errors = $this->getValidator()->validate($store, ["Default", "create"]);

        if (0 !== count($errors)) {
            throw new ValidationException('Cannot create store', $errors);
        }

        $em = $this->getEntityManager();
        $em->persist($store);
        $em->flush();
        $em->refresh($store);

        // Propagate StoreCreatedEvent, the store specific subscribers are located in the Subscribers folder in this directory
        // or in Lib/EventSubscriber and configured in /app/config/services.yml
        $event = new StoreCreatedEvent($store);
        $this->getDispatcher()->dispatch(StoreCreatedEvent::NAME, $event);

        $this->getLogger()->info("Created store: ".$store->getName());

        return $store;
    }

    /**
     * Saves the given store to the database and propagates a StoreCreatedEvent
     *
     * @param Store $store
     * @return Store
     * @throws ValidationException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function update(Store $store) :Store
    {
        $errors = $this->getValidator()->validate($store, ["Default", "update"]);

        if (0 !== count($errors)) {
            throw new ValidationException('Cannot update store', $errors);
        }

        $em = $this->getEntityManager();
        $em->persist($store);
        $em->flush();
        $em->refresh($store);

        // Propagate StoreEventEvent, the store specific subscribers are located in the Subscribers folder in this directory
        // or in Lib/EventSubscriber and configured in /app/config/services.yml
        $event = new StoreUpdatedEvent($store);
        $this->getDispatcher()->dispatch(StoreUpdatedEvent::NAME, $event);

        return $store;
    }

    protected function getRepository(): string
    {
        return Store::class;
    }
}