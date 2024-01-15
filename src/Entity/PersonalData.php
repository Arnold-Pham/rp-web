<?php

namespace App\Entity;


use App\Entity\Address;
use App\Entity\Reservation;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PersonalDataRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: PersonalDataRepository::class)]
class PersonalData
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(
        min: 2,
        minMessage: 'Le nom de famille est trop court ({{ limit }} caractères minimum).',
        max: 100,
        maxMessage: 'Le nom de famille est trop long ({{ limit }} caractères maximum).'
    )]
    private ?string $lastname = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(
        min: 2,
        minMessage: 'Le prénom est trop court ({{ limit }} caractères minimum).',
        max: 255,
        maxMessage: 'Le prénom est trop long ({{ limit }} caractères maximum).'
    )]
    private ?string $firstname = null;

    #[ORM\Column]
    #[Assert\Positive]
    #[Assert\LessThan(value: 800000000, message: 'Le numéro doit avoir dix chiffres et commencer par 06 ou 07.')]
    #[Assert\GreaterThanOrEqual(value: 600000000, message: 'Le numéro doit avoir dix chiffres et commencer par 06 ou 07.')]
    private ?int $phoneNumber = null;

    #[ORM\Column]
    private ?bool $type = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $companyName = null;

    #[ORM\Column(length: 20)]
    private ?string $gender = null;

    #[ORM\OneToOne(inversedBy: 'personalData', cascade: ['persist', 'remove'])]
    private ?Car $car = null;

    #[ORM\OneToOne(inversedBy: 'personalData', cascade: ['persist', 'remove'])]
    private ?Address $address = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Address $invoice = null;

    #[ORM\OneToMany(mappedBy: 'personalData', targetEntity: Reservation::class)]
    private Collection $reservations;


    public function __construct()
    {
        $this->reservations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function setFirstname(?string $firstname): static
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

    public function isType(): ?bool
    {
        return $this->type;
    }

    public function setType(bool $type): static
    {
        $this->type = $type;

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

    public function getCar(): ?car
    {
        return $this->car;
    }

    public function setCar(car $car): static
    {
        $this->car = $car;

        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(Address $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getInvoice(): ?Address
    {
        return $this->invoice;
    }

    public function setInvoice(?Address $invoice): static
    {
        $this->invoice = $invoice;

        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): static
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setPersonalData($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): static
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getPersonalData() === $this) {
                $reservation->setPersonalData(null);
            }
        }

        return $this;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(?string $companyName): static
    {
        $this->companyName = $companyName;

        return $this;
    }
}
