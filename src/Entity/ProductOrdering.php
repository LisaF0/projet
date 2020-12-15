<?php

namespace App\Entity;

use App\Repository\ProductOrderingRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProductOrderingRepository::class)
 */
class ProductOrdering
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @ORM\ManyToOne(targetEntity=Ordering::class, inversedBy="productOrderings", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $ordering;

    /**
     * @ORM\ManyToOne(targetEntity=Product::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getOrdering(): ?Ordering
    {
        return $this->ordering;
    }

    public function setOrdering(?Ordering $ordering): self
    {
        $this->ordering = $ordering;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function __toString()
    {
        return $this->ordering." ".$this->quantity." x ".$this->product->getName();
    }
}
