<?php

namespace App\Entity;

use App\Repository\FactureRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FactureRepository::class)
 */
class Facture
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
    private $factureReference;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=5)
     */
    private $zipcode;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $address;

   
     /**
     * @ORM\OneToOne(targetEntity=Ordering::class)
     * @ORM\JoinColumn(name="ordering_id", onDelete="SET NULL")
     */
    private $ordering;

    /**
     * @ORM\Column(type="integer")
     */
    private $userId;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->factureReference = $this->createdAt->format('dmY').'-'.uniqid();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFactureReference(): ?string
    {
        return $this->factureReference;
    }

    public function setFactureReference(string $factureReference): self
    {
        $this->factureReference = $factureReference;

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

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getZipcode(): ?string
    {
        return $this->zipcode;
    }

    public function setZipcode(?string $zipcode): self
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

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

    public function __toString()
    {
        return $this->firstname.' '.$this->lastname.' - '.$this->city.', '.$this->address.', '.$this->zipcode;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }
}
