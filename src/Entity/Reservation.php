<?php

namespace App\Entity;


use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ReservationRepository;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Email(message: 'L\'email {{ value }} n\'est pas une adresse valide')]
    private ?string $email = null;

    #[ORM\Column(length: 50)]
    private ?string $code = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\GreaterThan(value: '+1 day', message: 'La réservation doit se faire 24h à l\'avance.')]
    private ?\DateTimeInterface $dateA = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\GreaterThan(message: 'La fin de réservation doit être après la date de début.', propertyPath: "dateA")]
    private ?\DateTimeInterface $dateB = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(
        min: 5,
        minMessage: 'Le numéro de vol est trop court ({{ limit }} caractères minimum).',
        max: 10,
        maxMessage: 'Le numéro de vol est trop long ({{ limit }} caractères maximum).'
    )]
    private ?string $flightA = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(
        min: 5,
        minMessage: 'Le numéro de vol est trop court ({{ limit }} caractères minimum).',
        max: 10,
        maxMessage: 'Le numéro de vol est trop long ({{ limit }} caractères maximum).'
    )]
    private ?string $flightB = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column]
    private ?bool $valet = null;

    #[ORM\OneToOne(inversedBy: 'reservation', cascade: ['persist', 'remove'])]
    private ?Option $option = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    private ?PersonalData $personalData = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    private ?Airport $airport = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    private ?Parking $parking = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateC = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    private ?Place $place = null;


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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getDateA(): ?\DateTimeInterface
    {
        return $this->dateA;
    }

    public function setDateA(\DateTimeInterface $dateA): static
    {
        $this->dateA = $dateA;

        return $this;
    }

    public function getDateB(): ?\DateTimeInterface
    {
        return $this->dateB;
    }

    public function setDateB(\DateTimeInterface $dateB): static
    {
        $this->dateB = $dateB;

        return $this;
    }

    public function getFlightA(): ?string
    {
        return $this->flightA;
    }

    public function setFlightA(string $flightA): static
    {
        $this->flightA = $flightA;

        return $this;
    }

    public function getFlightB(): ?string
    {
        return $this->flightB;
    }

    public function setFlightB(string $flightB): static
    {
        $this->flightB = $flightB;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getPersonalData(): ?PersonalData
    {
        return $this->personalData;
    }

    public function setPersonalData(?PersonalData $personalData): static
    {
        $this->personalData = $personalData;

        return $this;
    }

    public function isValet(): ?bool
    {
        return $this->valet;
    }

    public function setValet(bool $valet): static
    {
        $this->valet = $valet;

        return $this;
    }

    public function getOption(): ?Option
    {
        return $this->option;
    }

    public function setOption(?Option $option): static
    {
        $this->option = $option;

        return $this;
    }

    public function getAirport(): ?Airport
    {
        return $this->airport;
    }

    public function setAirport(?Airport $airport): static
    {
        $this->airport = $airport;

        return $this;
    }

    public function getParking(): ?Parking
    {
        return $this->parking;
    }

    public function setParking(?Parking $parking): static
    {
        $this->parking = $parking;

        return $this;
    }

    public function getDateC(): ?\DateTimeInterface
    {
        return $this->dateC;
    }

    public function setDateC(\DateTimeInterface $dateC): static
    {
        $this->dateC = $dateC;

        return $this;
    }

    public function getPlace(): ?Place
    {
        return $this->place;
    }

    public function setPlace(?Place $place): static
    {
        $this->place = $place;

        return $this;
    }
}
