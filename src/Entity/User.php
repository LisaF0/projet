<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity=ShipAddress::class, mappedBy="user", cascade={"persist"})
     */
    private $shipAddresses;

    /**
     * @ORM\OneToMany(targetEntity=Ordering::class, mappedBy="user", cascade={"persist"})
     */
    private $orderings;

    public function __construct()
    {
        $this->shipAddresses = new ArrayCollection();
        $this->orderings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection|ShipAddress[]
     */
    public function getShipAddresses(): Collection
    {
        return $this->shipAddresses;
    }

    public function addShipAddress(ShipAddress $shipAddress): self
    {
        if (!$this->shipAddresses->contains($shipAddress)) {
            $this->shipAddresses[] = $shipAddress;
            $shipAddress->setUser($this);
        }

        return $this;
    }

    public function removeShipAddress(ShipAddress $shipAddress): self
    {
        if ($this->shipAddresses->removeElement($shipAddress)) {
            // set the owning side to null (unless already changed)
            if ($shipAddress->getUser() === $this) {
                $shipAddress->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Ordering[]
     */
    public function getOrderings(): Collection
    {
        return $this->orderings;
    }

    public function addOrdering(Ordering $ordering): self
    {
        if (!$this->orderings->contains($ordering)) {
            $this->orderings[] = $ordering;
            $ordering->setUser($this);
        }

        return $this;
    }

    public function removeOrdering(Ordering $ordering): self
    {
        if ($this->orderings->removeElement($ordering)) {
            // set the owning side to null (unless already changed)
            if ($ordering->getUser() === $this) {
                $ordering->setUser(null);
            }
        }

        return $this;
    }
}
