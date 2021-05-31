<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Form
 *
 * @ORM\Table(name="stores")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\StoreRepository")
 * @Serializer\ExclusionPolicy("all")
 */
class Store
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Serializer\Expose
     * @Serializer\Groups(groups={"store"})
     */
    private $id;

    /**
     * @var Customer
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Customer", inversedBy="stores")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id", nullable=false)
     * @Assert\NotNull(message="bogus")
     */
    private $customer;

    /**
     * @var Country
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Country", inversedBy="stores")
     * @ORM\JoinColumn(name="country_id", referencedColumnName="id", nullable=false)
     * @Assert\NotNull(message="You must provide a country")
     */
    private $country;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Serializer\Expose
     * @Serializer\Groups(groups={"store"})
     * @Assert\NotBlank (message="You must provide a name")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     * @Serializer\Expose
     * @Serializer\Groups(groups={"store"})
     * @Assert\NotBlank(message="You must provide a description")
     */
    private $description = '';

    /**
     * @var \DateTime
     * @ORM\Column(name="start_date", type="datetime", nullable=false)
     * @Serializer\Expose
     * @Serializer\Groups(groups={"store"})
     */
    private $startDate;

    /**
     * @var Subscription[]
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Subscription", mappedBy="store", cascade={"all"})
     * @Serializer\Expose
     * @Serializer\Groups(groups={"store_subscriptions"})
     */
    private $subscriptions;

    use TimestampableTrait;

    public function __construct()
    {
        $this->subscriptions = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }


    /**
     * @return Country
     */
    public function getCountry(): ?Country
    {
        return $this->country;
    }

    /**
     * @param Country|null $country
     * @return Store
     */
    public function setCountry(?Country $country): Store
    {
        $this->country = $country;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return Store
     */
    public function setName(?string $name): Store
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return Store
     */
    public function setDescription(?string $description): Store
    {
        $this->description = $description;
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
     * @return Store
     */
    public function setStartDate(\DateTime $startDate): Store
    {
        $this->startDate = $startDate;
        return $this;
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
     * @return Store
     */
    public function setCustomer(Customer $customer): Store
    {
        $this->customer = $customer;
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
     * @return Store
     */
    public function setSubscriptions($subscriptions) :Store
    {
        $this->subscriptions = $subscriptions;
        return $this;
    }

    public function addSubscription(Subscription $subscription) :Store
    {
        if (!$this->subscriptions->contains($subscription)) {
            $subscription->setStore($this);
            $this->subscriptions->add($subscription);
        }

        return $this;
    }

    public function removeSubscription(Subscription $subscription) :Store
    {
        if ($this->subscriptions->contains($subscription)) {
            $this->subscriptions->removeElement($subscription);
        }

        return $this;
    }

    /**
     * @return string
     * @Serializer\VirtualProperty()
     * @Serializer\Groups(groups={"store"})
     * @Serializer\SerializedName("country")
     */
    public function getCountryCode() :string
    {
        return $this->getCountry()->getId();
    }

    /**
     * @return int
     * @Serializer\VirtualProperty()
     * @Serializer\Groups(groups={"store"})
     * @Serializer\SerializedName("customer_id")
     */
    public function getCustomerId() :int
    {
        return $this->getCustomer()->getId();
    }

    /**
     * @param ExecutionContextInterface $context
     * @Assert\Callback
     */
    public function canChangeCountry(ExecutionContextInterface $context) :void
    {
        if ($this->getId() === null) {
            return;
        }

        if (count($this->getSubscriptions()) === 0) {
            return;
        }

        $m = static function (Subscription $sub) {
            return $sub->getProduct()->getCountryCode();
        };

        $countries = array_unique(array_map($m, $this->getSubscriptions()->toArray()));

        if (in_array($this->getCountry()->getId(), $countries, true)) {
            return;
        }

        $context->buildViolation('Country cannot be changed')
            ->atPath('country')
            ->addViolation();

    }
}

