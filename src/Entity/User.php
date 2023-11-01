<?php

namespace App\Entity;


use App\Entity\Option;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;


#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\Email(message: 'L\'email {{ value }} n\'est pas une adresse valide')]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $lastname = null;

    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    #[ORM\Column(unique: true)]
    #[Assert\Positive]
    #[Assert\LessThan(value: 800000000, message: 'Le numéro doit commencer par 06 ou 07.')]
    #[Assert\GreaterThanOrEqual(value: 600000000, message: 'Le numéro doit commencer par 06 ou 07.')]
    private ?int $phoneNumber = null;

    #[ORM\Column(length: 255)]
    private ?string $picture = null;

    #[ORM\Column(length: 255)]
    private ?string $zone = null;

    #[ORM\Column(length: 255)]
    private ?string $gender = null;

    #[ORM\OneToMany(mappedBy: 'valetA', targetEntity: Option::class)]
    private Collection $optionsA;

    #[ORM\OneToMany(mappedBy: 'valetB', targetEntity: Option::class)]
    private Collection $optionsB;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateC = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateE = null;


    public function __construct()
    {
        $this->optionsA = new ArrayCollection();
        $this->optionsB = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getPhoneNumber(): ?int
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(int $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(string $picture): static
    {
        $this->picture = $picture;

        return $this;
    }

    public function getZone(): ?string
    {
        return $this->zone;
    }

    public function setZone(string $zone): static
    {
        $this->zone = $zone;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): static
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * @return Collection<int, Option>
     */
    public function getOptionsA(): Collection
    {
        return $this->optionsA;
    }

    public function addOptionsA(Option $optionsA): static
    {
        if (!$this->optionsA->contains($optionsA)) {
            $this->optionsA->add($optionsA);
            $optionsA->setValetA($this);
        }

        return $this;
    }

    public function removeOptionsA(Option $optionsA): static
    {
        if ($this->optionsA->removeElement($optionsA)) {
            // set the owning side to null (unless already changed)
            if ($optionsA->getValetA() === $this) {
                $optionsA->setValetA(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Option>
     */
    public function getOptionsB(): Collection
    {
        return $this->optionsB;
    }

    public function addOptionsB(Option $optionsB): static
    {
        if (!$this->optionsB->contains($optionsB)) {
            $this->optionsB->add($optionsB);
            $optionsB->setValetB($this);
        }

        return $this;
    }

    public function removeOptionsB(Option $optionsB): static
    {
        if ($this->optionsB->removeElement($optionsB)) {
            // set the owning side to null (unless already changed)
            if ($optionsB->getValetB() === $this) {
                $optionsB->setValetB(null);
            }
        }

        return $this;
    }

    public function getDateC(): ?\DateTimeInterface
    {
        return $this->dateC;
    }

    public function setDateC(?\DateTimeInterface $dateC): static
    {
        $this->dateC = $dateC;

        return $this;
    }

    public function getDateE(): ?\DateTimeInterface
    {
        return $this->dateE;
    }

    public function setDateE(?\DateTimeInterface $dateE): static
    {
        $this->dateE = $dateE;

        return $this;
    }
}
