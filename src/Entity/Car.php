<?php

namespace App\Entity;


use App\Entity\PersonalData;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CarRepository;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: CarRepository::class)]
class Car
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(
        min: 2,
        minMessage: 'La marque de la voiture doit contenir au moins {{ limit }} caractères .',
        max: 100,
        maxMessage: 'La marque de la voiture doit contenir au maximum {{ limit }} caractères.'
    )]
    private ?string $brand = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(
        min: 1,
        minMessage: 'Veuillez saisir le nom du modèle de la voiture. ',
        max: 100,
        maxMessage: 'Le modèle de la voiture doit contenir au maximum {{ limit }} caractères.'
    )]
    private ?string $model = null;

    #[ORM\Column(length: 10)]
    private ?string $color = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(
        min: 7,
        minMessage: 'Veuillez saisir une plaque d\'immatriculation conforme Française ({{ limit }} caractères minimum).',
        max: 15,
        maxMessage: 'La valeur {{ value }} est trop longue pour être une plaque d\'immatriculation.'
    )]
    private ?string $plate = null;

    #[ORM\OneToOne(mappedBy: 'car', cascade: ['persist', 'remove'])]
    private ?PersonalData $personalData = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): static
    {
        $this->brand = $brand;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function getPlate(): ?string
    {
        return $this->plate;
    }

    public function setPlate(string $plate): static
    {
        $this->plate = $plate;

        return $this;
    }

    public function getPersonalData(): ?PersonalData
    {
        return $this->personalData;
    }

    public function setPersonalData(PersonalData $personalData): static
    {
        // set the owning side of the relation if necessary
        if ($personalData->getCar() !== $this) {
            $personalData->setCar($this);
        }

        $this->personalData = $personalData;

        return $this;
    }
}
