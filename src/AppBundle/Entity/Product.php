<?php

namespace AppBundle\Entity;

use AppBundle\Lib\Entity\ChangeAwareEntityInterface;
use AppBundle\Lib\Entity\ChangeAwareTrait;
use AppBundle\Lib\Tools;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * AvailableProducts
 *
 * @ORM\Table(name="products")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProductRepository")
 * @Serializer\ExclusionPolicy("all")
 *
 */
class Product implements ChangeAwareEntityInterface
{
    /**
     * @var int|null
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Serializer\Expose
     * @Serializer\Groups(groups={"product", "product_minimal"})
     */
    private $id;

    /**
     * @var int|null
     * @ORM\Column(name="price", type="bigint", nullable=false)
     * @Serializer\Expose
     * @Serializer\Groups(groups={"product", "product_minimal"})
     * @Assert\NotBlank
     * @Assert\GreaterThan(value="0", message="price must be greater than 0")
     */
    private $price = 0;

    /**
     * @var float|null
     *
     * @ORM\Column(name="vat", type="decimal", precision=4, scale=2, nullable=false, options={"default" = "7.00"})
     * @Serializer\Expose
     * @Serializer\Groups(groups={"product", "product_minimal"})
     * @Serializer\Type("double")
     * @Assert\NotBlank
     * @Assert\GreaterThan(value="0", message="VAT must be greater than 0")
     */
    private $vat = 7.00;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="availableFrom", type="datetime")
     * @Serializer\Expose
     * @Serializer\Groups(groups={"product", "product_minimal"})
     * @Assert\NotBlank(message="start date must be set")
     * @Assert\DateTime
     */
    private $availableFrom;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="availableUntil", type="datetime")
     * @Serializer\Expose
     * @Serializer\Groups(groups={"product", "product_minimal"})
     * @Assert\NotBlank(message="end date must be set")
     * @Assert\DateTime
     */
    private $availableUntil;

    /**
     * @var int|null product_minimal subscription time in month
     * @ORM\Column(name="min_subscription_time", type="integer")
     * @Serializer\Expose()
     * @Serializer\Groups(groups={"product", "product_minimal"})
     * @Assert\NotBlank(message="You must provide a minimum subscription time")
     */
    private $minSubscriptionTime = 1;

    /**
     * @var int|null minimal quantity in kg
     * @ORM\Column(name="min_quantity", type="integer")
     * @Serializer\Expose
     * @Serializer\Groups(groups={"product", "product_minimal"})
     * @Assert\NotBlank(message="You must provide a minimum quantity")
     * @Assert\GreaterThan(value="0")
     */
    private $minQuantity = 10;

    /**
     * @var array|null Shipping costs [<amount> => <price_in_cent>]
     *
     * @ORM\Column(name="shipping_costs", type="json_array", nullable=false)
     * @Serializer\Expose
     * @Serializer\Groups(groups={"product"})
     * @Assert\NotBlank
     * @Assert\Count(min="1")
     */
    private $shippingCosts = [
        ['from' => 0, 'costs' => 100],
        ['from' => 50, 'costs' => 50],
    ];

    /**
     * @var string|null
     * @ORM\Column(name="notes", type="string", length=255)
     * @Serializer\Expose
     * @Serializer\Groups(groups={"product"})
     * @Assert\NotBlank(message="You must provide some notes for this product")
     * @Assert\Length(min="0")
     */
    private $notes = '';

    /**
     * @var Country|null
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Country", inversedBy="products")
     * @Assert\NotNull(message="You must set a country")
     */
    private $country;

    /**
     * @var Item|null
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Item", inversedBy="products")
     * @Assert\NotNull(message="You must set an item")
     */
    private $item;

    /**
     * @var Subscription[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Subscription", mappedBy="product")
     * @Serializer\Expose
     * @Serializer\Groups(groups={"product_subscriptions"})
     */
    private $subscriptions;


    use TimestampableTrait;

    use ChangeAwareTrait;


    public function __construct()
    {
        $this->subscriptions = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return Product
     */
    public function setId(?int $id): Product
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getPrice(): ?int
    {
        return $this->price;
    }

    /**
     * @param int|null $price
     * @return Product
     */
    public function setPrice(?int $price): Product
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getVat(): ?float
    {
        return $this->vat;
    }

    /**
     * @param float|null $vat
     * @return Product
     */
    public function setVat(?float $vat): Product
    {
        $this->vat = $vat;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getAvailableFrom(): ?\DateTime
    {
        return $this->availableFrom;
    }

    /**
     * @param \DateTime|null $availableFrom
     * @return Product
     */
    public function setAvailableFrom(?\DateTime $availableFrom): Product
    {
        if ($availableFrom instanceof \DateTime) {
            $availableFrom = Tools::getFirstDayDateTimeForMonth($availableFrom);
        }

        $this->availableFrom = $availableFrom;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getAvailableUntil(): ?\DateTime
    {
        return $this->availableUntil;
    }

    /**
     * @param \DateTime|null $availableUntil
     * @return Product
     */
    public function setAvailableUntil(?\DateTime $availableUntil): Product
    {
        // Force date to be last day of month
        if ($availableUntil instanceof \DateTime) {
            $availableUntil = Tools::getLastDayDateTimeForMonth($availableUntil);
        }

        $this->availableUntil = $availableUntil;
        return $this;
    }

    /**
     * @Assert\Callback
     */
    public function validateStartEndAndMinSubscriptionTime(ExecutionContextInterface $context) :void
    {
        $from = $this->getAvailableFrom();
        $until = $this->getAvailableUntil();
        $minSubscriptionTime = $this->getMinSubscriptionTime();

        if ($until === null || $from === null ) {
            return;;
        }

        if($until->getTimestamp() < $from->getTimestamp()) {
            $context->buildViolation('end date must be greater than start date')
                ->atPath('availableUntil')
                ->addViolation();
            return;
        }

        if ($minSubscriptionTime === null || $minSubscriptionTime <= 0) {
            return;
        }

        $minEnd = clone $from;
        $minEnd->add(new \DateInterval('P'.$this->minSubscriptionTime.'M'));
        if ($minEnd->getTimestamp() > $until->getTimestamp()) {
            $context->buildViolation('end date must be greater than min subscription period')
                ->atPath('availableUntil')
                ->addViolation();
        }

        return;
    }

    /**
     * @param ExecutionContextInterface $context
     * @throws \AppBundle\Lib\Entity\NotTrackedException
     * @Assert\Callback
     */
    public function canChangeCountry(ExecutionContextInterface $context) :void
    {
        if ($this->getId() === null) {
            return;
        }

        if (!$this->isChanged('country')) {
            return;
        }

        if (0 === count($this->getSubscriptions())) {
            return;
        }

        $context->buildViolation('Yo can not change the country since there are active subscriptions')
            ->atPath('country')
            ->addViolation();
    }

    /**
     * @param ExecutionContextInterface $context
     * @throws \AppBundle\Lib\Entity\NotTrackedException
     * @Assert\Callback
     */
    public function canChangePrice(ExecutionContextInterface $context) :void
    {
        if ($this->getId() === null) {
            return;
        }

        if (!$this->isChanged('price')) {
            return;
        }

        $change = $this->getChange('price');

        if ($change->getNewValue() < $change->getOldValue()) {
            return;
        }

        // Todo: this should only take active subscriptions into account
        if (0 === count($this->getSubscriptions())) {
            return;
        }

        $context
            ->buildViolation('You cannot change the price, there are subscription booked.')
            ->atPath('price')
            ->addViolation();

    }

    /**
     * @param ExecutionContextInterface $context
     * @throws \AppBundle\Lib\Entity\NotTrackedException
     * @Assert\Callback
     */
    public function canChangeProduct(ExecutionContextInterface $context) :void
    {
        if ($this->getId() === null) {
            return;
        }

        if (!$this->isExpired(new \DateTime())) {
            return;
        }

        if (!$this->isChanged('availableUntil')) {
            $context->buildViolation('You can not change the product since it is expired')
                ->atPath('availableUntil')
                ->addViolation();

            return;
        }

        $change = $this->getChange('availableUntil');

        if ($change->getNewValue() > $change->getOldValue()) {
            return;
        }

        $context->buildViolation('You can not change the product since it is expired')
            ->atPath('availableUntil')
            ->addViolation();

        return;
    }

    /**
     * @return int|null
     */
    public function getMinSubscriptionTime(): ?int
    {
        return $this->minSubscriptionTime;
    }

    /**
     * @param int|null $minSubscriptionTime
     * @return Product
     */
    public function setMinSubscriptionTime(?int $minSubscriptionTime): Product
    {
        if (null !== $minSubscriptionTime) {
            $minSubscriptionTime = (int) abs($minSubscriptionTime);
        }
        $this->minSubscriptionTime = $minSubscriptionTime;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getMinQuantity(): ?int
    {
        return $this->minQuantity;
    }

    /**
     * @param int|null $minQuantity
     * @return Product
     */
    public function setMinQuantity(?int $minQuantity): Product
    {
        if (null !== $minQuantity) {
            $minQuantity = (int) abs($minQuantity);
        }

        $this->minQuantity = $minQuantity;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getShippingCosts(): ?array
    {
        return $this->shippingCosts;
    }

    /**
     * @param array|null $shippingCosts
     * @return Product
     */
    public function setShippingCosts(?array $shippingCosts): Product
    {
        if (!is_array($shippingCosts)) {
            $shippingCosts = [];
        }

        $this->shippingCosts = $shippingCosts;

        return $this;
    }

    /**
     * @param array $costs
     * @return $this
     */
    public function addShippingCost(array $costs) :Product
    {
        if (!is_array($this->shippingCosts)) {
            $this->shippingCosts = [];
        }

        $this->shippingCosts[] = $costs;

        return $this;
    }

    /**
     * @Assert\Callback
     */
    public function validateShippingCosts(ExecutionContextInterface $context) :void
    {
        if (!is_array($this->getShippingCosts())) {
            return;
        }

        $orig = $this->getShippingCosts();
        $from = [];
        for ($i = 0, $iMax = count($orig); $i < $iMax; $i++) {
            $skipFromCheck = false;
            if (!array_key_exists('from', $orig[$i]) || !is_int($orig[$i]['from'])) {
                $context->buildViolation('you must provide a from value')
                    ->atPath('shippingCosts['.$i.']')
                    ->addViolation();
                $skipFromCheck = true;
            }

            if (!array_key_exists('costs', $orig[$i]) || !is_int($orig[$i]['costs'])) {
                $context->buildViolation('you must provide a costs value')
                    ->atPath('shippingCosts['.$i.']')
                    ->addViolation();
                $skipFromCheck = true;
            }

            if ($skipFromCheck) {
                continue;
            }

            $_from = (int) $orig[$i]['from'];

            if (in_array($_from, $from, true)) {
                $context->buildViolation('duplicate scale')
                    ->atPath('shippingCosts['.$i.']')
                    ->addViolation();
            }

            $from[] = $_from;
        }
    }

    /**
     * @return string|null
     */
    public function getNotes(): ?string
    {
        return $this->notes;
    }

    /**
     * @param string|null $notes
     * @return Product
     */
    public function setNotes(?string $notes): Product
    {
        $this->notes = $notes;
        return $this;
    }

    /**
     * @return Country|null
     */
    public function getCountry(): ?Country
    {
        return $this->country;
    }

    /**
     * @param Country|null $country
     * @return Product
     */
    public function setCountry(?Country $country): Product
    {
        $this->country = $country;
        return $this;
    }

    /**
     * @return Item|null
     */
    public function getItem(): ?Item
    {
        return $this->item;
    }

    /**
     * @param Item|null $item
     * @return Product
     */
    public function setItem(?Item $item): Product
    {
        $this->item = $item;
        return $this;
    }

    /**
     * @return Subscription[]|ArrayCollection
     */
    public function getSubscriptions()
    {
        return $this->subscriptions;
    }

    /**
     * @param Subscription[]|ArrayCollection $subscriptions
     * @return Product
     */
    public function setSubscriptions($subscriptions) :Product
    {
        $this->subscriptions = $subscriptions;

        return $this;
    }


    /**
     * @return string
     * @Serializer\VirtualProperty()
     * @Serializer\Groups(groups={"product", "product_minimal"})
     * @Serializer\SerializedName("item")
     */
    public function getItemId() :int
    {
        if (null === $this->getItem()) {
            return 0;
        }

        return $this->getItem()->getId();
    }

    /**
     * @return string
     * @Serializer\VirtualProperty()
     * @Serializer\Groups(groups={"product"})
     * @Serializer\SerializedName("name")
     */
    public function getName() :string
    {
        if (null === $this->getItem()) {
            return '';
        }

        return $this->getItem()->getName();
    }

    /**
     * @return string
     * @Serializer\VirtualProperty()
     * @Serializer\Groups(groups={"product"})
     * @Serializer\SerializedName("description")
     */
    public function getDescription() :string
    {
        if (null === $this->getItem()) {
            return '';
        }

        return $this->getItem()->getDescription();
    }

    /**
     * @return string
     * @Serializer\VirtualProperty()
     * @Serializer\Groups(groups={"product_minimal", "product"})
     * @Serializer\SerializedName("country")
     */
    public function getCountryCode() :string
    {
        if (null === $this->getCountry()) {
            return '';
        }

        return $this->country->getId();
    }

    /**
     * @param \DateTime|null $atDate
     * @return bool
     */
    public function isExpired(?\DateTime $atDate = null):bool
    {
        if (null === $atDate) {
            $atDate = Tools::getFirstDayDateForNextMonth(new \DateTime());
        }

        return $atDate > $this->getAvailableUntil();
    }

    public function getTrackList(): array
    {
        return ['price', 'vat', 'availableFrom', 'availableUntil', 'country', 'item'];
    }


}

