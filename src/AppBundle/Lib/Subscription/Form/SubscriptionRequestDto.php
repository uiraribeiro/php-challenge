<?php


namespace AppBundle\Lib\Subscription\Form;


use AppBundle\Entity\Customer;
use AppBundle\Entity\Product;
use AppBundle\Entity\Store;
use AppBundle\Entity\Subscription;
use AppBundle\Lib\Subscription\Validator\SubscriptionRequestValid;
use AppBundle\Lib\Tools;

/**
 * Class SubscriptionRequestDto
 * @package AppBundle\Lib\Subscription\Form
 * @SubscriptionRequestValid()
 */
class SubscriptionRequestDto
{
    /**
     * @var Customer|null
     */
    private $customer;

    /**
     * @var Product
     */
    private $product;

    /**
     * @var Store
     */
    private $store;

    /**
     * @var \DateTime
     */
    private $start;

    /**
     * @var int
     */
    private $quantity = 0;

    /**
     * @var bool
     */
    private $recurring = true;

    /**
     * @return Product
     */
    public function getProduct(): ?Product
    {
        return $this->product;
    }

    /**
     * @param Product|null $product
     * @return SubscriptionRequestDto
     */
    public function setProduct(?Product $product): SubscriptionRequestDto
    {
        $this->product = $product;
        return $this;
    }

    /**
     * @return Store
     */
    public function getStore(): ?Store
    {
        return $this->store;
    }

    /**
     * @param Store|null $store
     * @return SubscriptionRequestDto
     */
    public function setStore(?Store $store): SubscriptionRequestDto
    {
        $this->store = $store;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getStart(): ?\DateTime
    {
        return $this->start;
    }

    /**
     * @param \DateTime|null $start
     * @return SubscriptionRequestDto
     */
    public function setStart(?\DateTime $start): SubscriptionRequestDto
    {
        $this->start = $start;
        return $this;
    }

    /**
     * @return int
     */
    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     * @return SubscriptionRequestDto
     */
    public function setQuantity(?int $quantity): SubscriptionRequestDto
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @return bool
     */
    public function isRecurring(): ?bool
    {
        return $this->recurring;
    }

    /**
     * @param bool $recurring
     * @return SubscriptionRequestDto
     */
    public function setRecurring(?bool $recurring): SubscriptionRequestDto
    {
        $this->recurring = $recurring;
        return $this;
    }

    /**
     * Returns the start date of the subscription, if $this->start is not set, it will return either the first day of
     * the next month or if the product is not available until then the date from which the product is available
     *
     * @return \DateTime
     */
    public function getStartDate() :\DateTime
    {
        if ($this->getStart() instanceof \DateTime) {
            return Tools::getFirstDayDateTimeForMonth($this->getStart());
        }

        if (!$this->getProduct() instanceof Product) {
            throw new \RuntimeException('Cannot determine start date no product set');
        }

        $start = Tools::getFirstDayDateForNextMonth(new \DateTime());

        if ($start < $this->getProduct()->getAvailableFrom()) {
            return $this->getProduct()->getAvailableFrom();
        }

        return $start;
    }

    /**
     * @return \DateTime
     * @throws \Exception
     */
    public function getEndDate(): \DateTime
    {
        if (!$this->getProduct() instanceof Product) {
            throw new \RuntimeException('Cannot determine start date no product set');
        }
        $min = $this->getProduct()->getMinSubscriptionTime();

        $start = $this->getStartDate();
        $end = clone $start;
        $end->add(new \DateInterval('P'.$min.'M'));

        return Tools::getLastDayDateTimeForPreviousMonth($end);
    }

    /**
     * @return Customer|null
     */
    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    /**
     * @param Customer|null $customer
     * @return SubscriptionRequestDto
     */
    public function setCustomer(?Customer $customer): SubscriptionRequestDto
    {
        $this->customer = $customer;
        return $this;
    }

    /**
     * Converts the request to a subscription object
     * @return Subscription
     * @throws \Exception
     */
    public function toSubscription():Subscription
    {
        $subscription = new Subscription();
        $subscription->setStore($this->getStore());
        $subscription->setProduct($this->getProduct());
        $subscription->setQuantity($this->getQuantity());
        $subscription->setRecurring($this->isRecurring());
        $subscription->setStartDate($this->getStartDate());
        $subscription->setEndDate($this->getEndDate());

        return $subscription;
    }


}