<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * Subscription
 *
 * @ORM\Table(name="subscriptions")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SubscriptionRepository")
 * @Serializer\ExclusionPolicy("all")
 */
class Subscription
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Serializer\Expose
     * @Serializer\Groups(groups={"subscription"})
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer")
     * @Serializer\Expose
     * @Serializer\Groups(groups={"subscription"})
     */
    private $quantity;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="startDate", type="datetime")
     * @Serializer\Expose
     * @Serializer\Groups(groups={"subscription"})
     */
    private $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="endDate", type="datetime")
     * @Serializer\Expose
     * @Serializer\Groups(groups={"subscription"})
     */
    private $endDate;

    /**
     * @var bool
     * @ORM\Column(name="recurring", type="boolean", nullable=false)
     * @Serializer\Expose
     * @Serializer\Groups(groups={"subscription"})
     */
    private $recurring = true;

    /**
     * @var Store
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Store", inversedBy="subscriptions")
     */
    private $store;

    /**
     * @var Product
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Product", inversedBy="subscriptions")
     */
    private $product;

    /**
     * @var Shipment[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Shipment", mappedBy="subscription")
     * @Serializer\Expose
     * @Serializer\Groups(groups={"include_shipments"})
     */
    private $shipments;

    use TimestampableTrait;

    public function __construct()
    {
        $this->shipments = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId() :int
    {
        return $this->id;
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
     * @return Subscription
     */
    public function setQuantity(int $quantity): Subscription
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getStartDate(): \DateTime
    {
        return $this->startDate;
    }

    /**
     * @param \DateTime $startDate
     * @return Subscription
     */
    public function setStartDate(\DateTime $startDate): Subscription
    {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEndDate(): \DateTime
    {
        return $this->endDate;
    }

    /**
     * @param \DateTime $endDate
     * @return Subscription
     */
    public function setEndDate(\DateTime $endDate): Subscription
    {
        $this->endDate = $endDate;
        return $this;
    }

    /**
     * @return bool
     */
    public function isRecurring(): bool
    {
        return $this->recurring;
    }

    /**
     * @param bool $recurring
     * @return Subscription
     */
    public function setRecurring(bool $recurring): Subscription
    {
        $this->recurring = $recurring;
        return $this;
    }

    /**
     * @return Store
     */
    public function getStore(): Store
    {
        return $this->store;
    }

    /**
     * @param Store $store
     * @return Subscription
     */
    public function setStore(Store $store): Subscription
    {
        $this->store = $store;

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
     * @return Subscription
     */
    public function setProduct(Product $product): Subscription
    {
        $this->product = $product;

        return $this;
    }

    /**
     * @return int
     * @Serializer\VirtualProperty()
     * @Serializer\Groups(groups={"subscription", "subsription_minimal"})
     * @Serializer\SerializedName("product_id")
     */
    public function getProductId() :int
    {
        return $this->getProduct()->getId();
    }

    /**
     * @return int
     * @Serializer\VirtualProperty()
     * @Serializer\Groups(groups={"subscription"})
     * @Serializer\SerializedName("item_id")
     */
    public function getItemId() :int
    {
        return $this->getProduct()->getItemId();
    }

    /**
     * @return string
     * @Serializer\VirtualProperty()
     * @Serializer\Groups(groups={"subscription"})
     * @Serializer\SerializedName("name")
     */
    public function getName() :string
    {
        return $this->getProduct()->getName();
    }

    /**
     * @return string
     * @Serializer\VirtualProperty()
     * @Serializer\Groups(groups={"subscription"})
     * @Serializer\SerializedName("description")
     */
    public function getDescription() :string
    {
        return $this->getProduct()->getDescription();
    }

    /**
     * @return string
     * @Serializer\VirtualProperty()
     * @Serializer\Groups(groups={"subscription"})
     * @Serializer\SerializedName("country")
     */
    public function getCountryCode() :string
    {
        return $this->getProduct()->getCountryCode();
    }

    /**
     * @return string
     * @Serializer\VirtualProperty()
     * @Serializer\Groups(groups={"subscription"})
     * @Serializer\SerializedName("runtime")
     */
    public function getRuntime() :string
    {
        return $this->getProduct()->getMinSubscriptionTime();
    }

    /**
     * @return string
     * @Serializer\VirtualProperty()
     * @Serializer\Groups(groups={"store_info"})
     * @Serializer\SerializedName("store_id")
     */
    public function getStoreId() :int
    {
        return $this->getStore()->getId();
    }

    /**
     * @return string
     * @Serializer\VirtualProperty()
     * @Serializer\Groups(groups={"store_info"})
     * @Serializer\SerializedName("store_name")
     */
    public function getStoreName() :string
    {
        return $this->getStore()->getName();
    }

    /**
     * @return Shipment[]|ArrayCollection
     */
    public function getShipments()
    {
        return $this->shipments;
    }

    /**
     * @param Shipment[]|ArrayCollection $shipments
     * @return Subscription
     */
    public function setShipments($shipments)
    {
        $this->shipments = $shipments;
        return $this;
    }

}

