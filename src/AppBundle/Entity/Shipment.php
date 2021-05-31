<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Bridge\Twig\TokenParser\TransChoiceTokenParser;

/**
 * Shipment
 *
 * @ORM\Table(name="shipments", uniqueConstraints={@ORM\UniqueConstraint(name="UNIQIDX_SUBSCRIPTION_DATE", columns={"subscription_id", "shipping_date"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ShipmentRepository")
 * @Serializer\ExclusionPolicy("all")
 */
class Shipment
{
    public const SHIPMENT_PENDING = 0;

    public const SHIPMENT_SHIPPED = 1;

    public const SHIPMENT_CANCELED = -1;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Serializer\Expose
     * @Serializer\Groups(groups={"shipment"})
     */
    private $id;

    /**
     * @var Subscription
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Subscription", inversedBy="shipments")
     * @ORM\JoinColumn(name="subscription_id", referencedColumnName="id", nullable=false)
     */
    private $subscription;

    /**
     * @var Product
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Product")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", nullable=false)
     */
    private $product;

    /**
     * @var Item
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Item")
     * @ORM\JoinColumn(name="item_id", referencedColumnName="id", nullable=false)
     */
    private $item;

    /**
     * @var Country
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Country")
     * @ORM\JoinColumn(name="country_id", referencedColumnName="id", nullable=false)
     */
    private $country;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="bigint")
     * @Serializer\Expose
     * @Serializer\Groups(groups={"shipment"})
     */
    private $quantity = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="state", type="string", nullable=false)
     */
    private $state = self::SHIPMENT_PENDING;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="shipping_date", type="datetime", nullable=false)
     * @Serializer\Expose
     * @Serializer\Groups(groups={"shipment"})
     */
    private $shippingDate;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Shipment
     */
    public function setId(int $id): Shipment
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return Subscription
     */
    public function getSubscription(): Subscription
    {
        return $this->subscription;
    }

    /**
     * @param Subscription $subscription
     * @return Shipment
     */
    public function setSubscription(Subscription $subscription): Shipment
    {
        $this->subscription = $subscription;

        return $this;
    }

    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @param Product $product
     * @return Shipment
     */
    public function setProduct(Product $product): Shipment
    {
        $this->product = $product;

        return $this;
    }

    /**
     * @return Item
     */
    public function getItem(): Item
    {
        return $this->item;
    }

    /**
     * @param Item $item
     * @return Shipment
     */
    public function setItem(Item $item): Shipment
    {
        $this->item = $item;

        return $this;
    }

    /**
     * @return Country
     */
    public function getCountry(): Country
    {
        return $this->country;
    }

    /**
     * @param Country $country
     * @return Shipment
     */
    public function setCountry(Country $country): Shipment
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     * @return Shipment
     */
    public function setQuantity(int $quantity): Shipment
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @return int
     */
    public function getState(): int
    {
        return $this->state;
    }

    /**
     * @param int $state
     * @return Shipment
     */
    public function setState(int $state): Shipment
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getShippingDate(): \DateTime
    {
        return $this->shippingDate;
    }

    /**
     * @param \DateTime $shippingDate
     * @return Shipment
     */
    public function setShippingDate(\DateTime $shippingDate): Shipment
    {
        $this->shippingDate = $shippingDate;

        return $this;
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\Groups(groups={"shipment"})
     * @Serializer\SerializedName("status")
     */
    public function getLiteralState() :string
    {
        $map = [
            0 => 'pending',
            1 => 'shipped',
            -1 => 'canceled'
        ];

        return $map[$this->state];
    }

}

