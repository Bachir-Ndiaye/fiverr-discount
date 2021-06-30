<?php

namespace App\Entity;

use App\Repository\DiscountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DiscountRepository::class)
 */
class Discount
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\OneToMany(targetEntity=Product::class, mappedBy="discountId")
     */
    private $products;

    /**
     * @ORM\Column(type="datetime")
     */
    private $discountFrom;

    /**
     * @ORM\Column(type="datetime")
     */
    private $discountTo;

    /**
     * @ORM\Column(type="integer")
     */
    private $discountPrice;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setDiscountId($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getDiscountId() === $this) {
                $product->setDiscountId(null);
            }
        }

        return $this;
    }

    public function getDiscountFrom(): ?\DateTime
    {
        return $this->discountFrom;
    }

    public function setDiscountFrom(\DateTime $discountFrom): self
    {
        $this->discountFrom = $discountFrom;

        return $this;
    }

    public function getDiscountTo(): ?\DateTime
    {
        return $this->discountTo;
    }

    public function setDiscountTo(\DateTime $discountTo): self
    {
        $this->discountTo = $discountTo;

        return $this;
    }

    public function getDiscountPrice(): ?int
    {
        return $this->discountPrice;
    }

    public function setDiscountPrice(int $discountPrice): self
    {
        $this->discountPrice = $discountPrice;

        return $this;
    }
}
