<?php


namespace AppBundle\Lib\Customer;


use AppBundle\Entity\Customer;
use AppBundle\Lib\AbstractService;
use AppBundle\Lib\ValidationException;

class CustomerService extends AbstractService
{
    /**
     * @param Customer $customer
     * @return Customer
     * @throws ValidationException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function create(Customer $customer) :Customer
    {
        $errors = $this->getValidator()->validate($customer, ['Default', 'create']);
        if (0 !== count($errors)) {
            throw new ValidationException('Customer is not valid', $errors);
        }

        $em = $this->getEntityManager();
        $em->persist($customer);
        $em->flush();

        $this->getLogger()->info('Created customer with name: '.$customer->getName());
        $event = new CustomerCreatedEvent($customer);
        $this->getDispatcher()->dispatch(CustomerCreatedEvent::NAME, $event);

        $em->refresh($customer);

        return $customer;
    }

    /**
     * @param Customer $customer
     * @return Customer
     * @throws ValidationException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function update(Customer $customer) :Customer
    {
        $errors = $this->getValidator()->validate($customer, ['Default', 'update']);
        if (0 !== count($errors)) {
            throw new ValidationException('Customer is not valid', $errors);
        }

        $em = $this->getEntityManager();
        $em->persist($customer);
        $em->flush();

        $this->getLogger()->info('Updated customer with name: '.$customer->getName());
        $event = new CustomerUpdatedEvent($customer);
        $this->getDispatcher()->dispatch(CustomerUpdatedEvent::NAME, $event);

        $em->refresh($customer);

        return $customer;
    }

    protected function getRepository(): string
    {
        return Customer::class;
    }
}