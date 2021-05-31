<?php


namespace AppBundle\Lib\Customer;


use AppBundle\Entity\Customer;
use Symfony\Component\EventDispatcher\Event;

class CustomerCreatedEvent extends Event
{
    public const NAME = 'customer.created';

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
     * @return CustomerCreatedEvent
     */
    public function setCustomer(Customer $customer): CustomerCreatedEvent
    {
        $this->customer = $customer;
        return $this;
    }

    public function addMessage(string $message) :void
    {
        self::$messages[] = $message;
    }
}