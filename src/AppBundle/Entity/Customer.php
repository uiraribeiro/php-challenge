<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Form
 *
 * @ORM\Table(name="customers")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CustomerRepository")
 * @Serializer\ExclusionPolicy("all")
 */
class Customer
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Serializer\Expose
     * @Serializer\Groups(groups={"customer"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Serializer\Expose
     * @Serializer\Groups(groups={"customer"})
     * @Assert\NotBlank(message="You must provide a name")
     */
    private $name;

    /**
     * @var Store[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="Store", mappedBy="customer")
     * @Serializer\Expose
     * @Serializer\Groups(groups={"customer_stores"})
     */
    private $stores;
    
    use TimestampableTrait;

    public function __construct()
    {
        $this->stores = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId() :?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Customer
     */
    public function setName(?string $name): Customer
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return Store[]
     */
    public function getStores()
    {
        return $this->stores;
    }

    /**
     * @param Store[]|ArrayCollection $stores
     * @return Customer
     */
    public function setStores($stores) :Customer
    {
        $this->stores = $stores;
        return $this;
    }

    public function addStore(Store $store) :Customer
    {
        if (!$this->stores->contains($store)) {
            $store->setCustomer($this);
            $this->stores->add($store);
        }

        return $this;
    }

    public function removeStore(Store $store) :Customer
    {
        if ($this->stores->contains($store)) {
            $this->stores->removeElement($store);
        }

        return $this;
    }



}

