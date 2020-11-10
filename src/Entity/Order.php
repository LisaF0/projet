<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 */
class Order
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
    private $reference;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    const STATUS_ORDER = [
        0 => "en attente",
        1 => "validée"
    ];

    /**
     * @ORM\Column(type="integer")
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity=ShipAddress::class, inversedBy="order")
     * @ORM\JoinColumn(nullable=false)
     */
    private $shipAddress;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="order")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=ProductOrder::class, mappedBy="order", cascade={"persist"})
     */
    private $ProductsOrder;

    /**
     * @ORM\OneToOne(targetEntity=Facture::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $facture;

    public function __construct()
    {
        $this->ProductsOrder = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->reference = 1;
        $this->status = 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getStatus(): ?string
    {
        return self::STATUS_ORDER[$this->status];
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }


    public function getShipAddress(): ?ShipAddress
    {
        return $this->shipAddress;
    }

    public function setShipAddress(?ShipAddress $shipAddress): self
    {
        $this->shipAddress = $shipAddress;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getFacture(): ?Facture
    {
        return $this->facture;
    }

    public function setFacture(Facture $facture): self
    {
        $this->facture = $facture;

        return $this;
    }

    /**
     * @return Collection|ProductsOrder[]
     */
    public function getProductsOrder(): Collection
    {
        return $this->ProductsOrder;
    }

    public function addProductOrder(ProductOrder $productOrder): self
    {
        if (!$this->ProductsOrder->contains($productOrder)) {
            $this->ProductsOrder[] = $productOrder;
            $productOrder->setOrder($this);
        }

        return $this;
    }

    public function removeProductOrder(ProductOrder $productOrder): self
    {
        if ($this->ProductsOrder->removeElement($productOrder)) {
            // set the owning side to null (unless already changed)
            if ($productOrder->getOrder() === $this) {
                $productOrder->setOrder(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->reference;
    }



}
