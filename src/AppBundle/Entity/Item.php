<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Product
 *
 * @ORM\Table(name="items")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ItemRepository")
 * @Serializer\ExclusionPolicy("all")
 */
class Item
{
    /**
     * @var int|null
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Serializer\Expose
     * @Serializer\Groups(groups={"item", "item_minimal"})
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     * @Serializer\Expose
     * @Serializer\Groups(groups={"item", "item_minimal"})
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     * @Serializer\Expose
     * @Serializer\Groups(groups={"item"})
     * @Assert\NotBlank
     */
    private $description;

    /**
     * @var Product[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Product", mappedBy="item")
     * @Serializer\Expose
     * @Serializer\Groups(groups={"item_products"})
     */
    private $products;

    use TimestampableTrait;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return Item
     */
    public function setName(?string $name): Item
    {
        $this->name = strtolower($name);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return Item
     */
    public function setDescription(?string $description): Item
    {
        $this->description = $description;
        return $this;
    }



    /**
     * @return Product[]|ArrayCollection
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @param Product[]|ArrayCollection $products
     * @return Item
     */
    public function setProducts($products) :Item
    {
        $this->products = $products;
        return $this;
    }

    /**
     * Returns an array of products matching the given criteria
     * @param string|null $country
     * @param int|null $runtime
     * @param bool|null $activeOnly
     * @return array
     */
    public function getProductsBy(?string $country = null, ?int $runtime = null, ?bool $activeOnly = false) :array
    {
        $f = static function (Product $product) use ($country, $runtime, $activeOnly)
        {
            if ($activeOnly && $product->isExpired()) {
                return false;
            }

            if ($country && $product->getCountryCode() !== $country) {
                return false;
            }

            if ($runtime && $product->getMinSubscriptionTime() !== $runtime) {
                return false;
            }

            return true;
        };

        return array_values(array_filter($this->getProducts()->toArray(), $f));
    }

}

