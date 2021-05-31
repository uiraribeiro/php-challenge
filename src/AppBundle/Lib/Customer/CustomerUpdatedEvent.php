<?php


namespace AppBundle\Lib\Customer;


use AppBundle\Entity\Customer;
use Symfony\Component\EventDispatcher\Event;

class CustomerUpdatedEvent extends Event
{
    public const NAME = 'customer.updated';

    public static $messages = [];

    /**
     * @var Customer
     */
    protected $customer;

    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
        self::$messages = [];
    }

    /**
     * @return Customer
     */
    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    /**
     * @param Customer $customer
     * @return CustomerUpdatedEvent
     */
    public function setCustomer(Customer $customer): CustomerUpdatedEvent
    {
        $this->customer = $customer;
        return $this;
    }


    public function addMessage(string $message) :void
    {
        self::$messages[] = $message;
    }
}