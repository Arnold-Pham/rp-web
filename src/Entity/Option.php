<?php

namespace App\Entity;


use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\OptionRepository;


#[ORM\Entity(repositoryClass: OptionRepository::class)]
class Option
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $extra = null;

    #[ORM\OneToOne(mappedBy: 'option', cascade: ['persist', 'remove'])]
    private ?Reservation $reservation = null;

    #[ORM\ManyToOne(inversedBy: 'optionsA')]
    private ?User $valetA = null;

    #[ORM\ManyToOne(inversedBy: 'optionsB')]
    private ?User $valetB = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getExtra(): ?int
    {
        return $this->extra;
    }

    public function setExtra(int $extra): static
    {
        $this->extra = $extra;

        return $this;
    }

    public function getReservation(): ?Reservation
    {
        return $this->reservation;
    }

    public function setReservation(?Reservation $reservation): static
    {
        // unset the owning side of the relation if necessary
        if ($reservation === null && $this->reservation !== null) {
            $this->reservation->setOption(null);
        }

        // set the owning side of the relation if necessary
        if ($reservation !== null && $reservation->getOption() !== $this) {
            $reservation->setOption($this);
        }

        $this->reservation = $reservation;

        return $this;
    }

    public function getValetA(): ?User
    {
        return $this->valetA;
    }

    public function setValetA(?User $valetA): static
    {
        $this->valetA = $valetA;

        return $this;
    }

    public function getValetB(): ?User
    {
        return $this->valetB;
    }

    public function setValetB(?User $valetB): static
    {
        $this->valetB = $valetB;

        return $this;
    }
}
