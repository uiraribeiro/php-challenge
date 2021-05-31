<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * Stock
 *
 * @ORM\Table(name="stock", uniqueConstraints={@ORM\UniqueConstraint(name="UNIQIDX_ITEM_COUNTRY_DATE", columns={"item_id", "country_id", "at_date"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\StockRepository")
 * @Serializer\ExclusionPolicy("all")
 */
class Stock
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

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
     * @var \DateTime
     *
     * @ORM\Column(name="at_date", type="datetime")
     */
    private $atDate;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="bigint")
     */
    private $quantity;


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Stock
     */
    public function setId(int $id): Stock
    {
        $this->id = $id;
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
     * @return Stock
     */
    public function setItem(Item $item): Stock
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
     * @return Stock
     */
    public function setCountry(Country $country): Stock
    {
        $this->country = $country;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getAtDate(): \DateTime
    {
        return $this->atDate;
    }

    /**
     * @param \DateTime $atDate
     * @return Stock
     */
    public function setAtDate(\DateTime $atDate): Stock
    {
        $this->atDate = $atDate;

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
     * @return Stock
     */
    public function setQuantity(int $quantity): Stock
    {
        $this->quantity = $quantity;

        return $this;
    }
}

