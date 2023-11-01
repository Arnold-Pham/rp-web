<?php

namespace App\Entity;


use App\Entity\Parking;
use App\Entity\Reservation;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\AirportRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: AirportRepository::class)]
class Airport
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(
        min: 2,
        minMessage: 'Veuillez saisir le nom de votre aéroport ayant minimum {{ limit }} caractères.',
        max: 250,
        maxMessage: 'Le nom de l\'aéoroport contient trop de {{ limit }} caractères.'
    )]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $iataCode = null;

    #[ORM\Column(length: 255)]
    private ?string $zone = null;

    #[ORM\OneToMany(mappedBy: 'airport', targetEntity: Parking::class, orphanRemoval: true)]
    private Collection $parkings;

    #[ORM\OneToMany(mappedBy: 'airport', targetEntity: Reservation::class)]
    private Collection $reservations;


    public function __construct()
    {
        $this->parkings = new ArrayCollection();
        $this->reservations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getIataCode(): ?string
    {
        return $this->iataCode;
    }

    public function setIataCode(string $iataCode): static
    {
        $this->iataCode = $iataCode;

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

    /**
     * @return Collection<int, Parking>
     */
    public function getParkings(): Collection
    {
        return $this->parkings;
    }

    public function addParking(Parking $parking): static
    {
        if (!$this->parkings->contains($parking)) {
            $this->parkings->add($parking);
            $parking->setAirport($this);
        }

        return $this;
    }

    public function removeParking(Parking $parking): static
    {
        if ($this->parkings->removeElement($parking)) {
            // set the owning side to null (unless already changed)
            if ($parking->getAirport() === $this) {
                $parking->setAirport(null);
            }
        }

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
            $reservation->setAirport($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): static
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getAirport() === $this) {
                $reservation->setAirport(null);
            }
        }

        return $this;
    }
}
