<?php

namespace App\Entity;

use App\Repository\OrderingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderingRepository::class)
 * @ORM\Table(name="ordering")
 */
class Ordering
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
    private $orderingReference;

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
    private $orderingStatus;

    /**
     * @ORM\ManyToOne(targetEntity=ShipAddress::class, inversedBy="orderings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $shipAddress;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="orderings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=ProductOrdering::class, mappedBy="ordering")
     */
    private $productOrderings;

    /**
     * @ORM\OneToOne(targetEntity=Facture::class, cascade={"all"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $facture;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->orderingReference = '1';
        $this->orderingStatus = self::STATUS_ORDER[0];
        $this->productOrderings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderingReference(): ?string
    {
        return $this->orderingReference;
    }

    public function setOrderingReference(string $orderingReference): self
    {
        $this->orderingReference = $orderingReference;

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

    public function getShipAddress(): ?ShipAddress
    {
        return $this->shipAddress;
    }

    public function setShipAddress(?ShipAddress $shipAddress): self
    {
        $this->shipAddress = $shipAddress;
        //$shipAddress->addOrdering($this);

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

    public function setFacture(?Facture $facture): self
    {
        $this->facture = $facture;

        return $this;
    }

    public function __toString()
    {
        return $this->orderingReference;
    }

    /**
     * @return Collection|ProductOrdering[]
     */
    public function getProductOrderings(): Collection
    {
        return $this->productOrderings;
    }

    public function getOrderingStatus(): ?int
    {
        return $this->orderingStatus;
    }

    public function setOrderingStatus(int $orderingStatus): self
    {
        $this->orderingStatus = $orderingStatus;

        return $this;
    }

    public function addProductOrdering(ProductOrdering $productOrdering): self
    {
        if (!$this->productOrderings->contains($productOrdering)) {
            $this->productOrderings[] = $productOrdering;
            $productOrdering->setOrdering($this);
        }

        return $this;
    }

    public function removeProductOrdering(ProductOrdering $productOrdering): self
    {
        if ($this->productOrderings->removeElement($productOrdering)) {
            // set the owning side to null (unless already changed)
            if ($productOrdering->getOrdering() === $this) {
                $productOrdering->setOrdering(null);
            }
        }

        return $this;
    }



}
