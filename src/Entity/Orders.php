<?php

namespace App\Entity;

use App\Repository\OrdersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrdersRepository::class)
 */
class Orders
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
     * @ORM\ManyToOne(targetEntity=ShipAddresses::class, inversedBy="orders")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ShipAddress;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="orders")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=ProductsOrder::class, mappedBy="orders")
     */
    private $ProductsOrder;

    /**
     * @ORM\OneToOne(targetEntity=Factures::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $facture;

    public function __construct()
    {
        $this->ProductsOrder = new ArrayCollection();
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


    public function getShipAddress(): ?ShipAddresses
    {
        return $this->ShipAddress;
    }

    public function setShipAddress(?ShipAddresses $ShipAddress): self
    {
        $this->ShipAddress = $ShipAddress;

        return $this;
    }

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(?Users $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getFacture(): ?Factures
    {
        return $this->facture;
    }

    public function setFacture(Factures $facture): self
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

    public function addProductsOrder(ProductsOrder $productsOrder): self
    {
        if (!$this->ProductsOrder->contains($productsOrder)) {
            $this->ProductsOrder[] = $productsOrder;
            $productsOrder->setOrders($this);
        }

        return $this;
    }

    public function removeProductsOrder(ProductsOrder $productsOrder): self
    {
        if ($this->ProductsOrder->removeElement($productsOrder)) {
            // set the owning side to null (unless already changed)
            if ($productsOrder->getOrders() === $this) {
                $productsOrder->setOrders(null);
            }
        }

        return $this;
    }



}
