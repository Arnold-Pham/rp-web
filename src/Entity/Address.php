<?php

namespace App\Entity;


use App\Entity\PersonalData;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\AddressRepository;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: AddressRepository::class)]
class Address
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(
        min: 10,
        minMessage: 'Votre adresse est trop courte ({{ limit }} caractères)',
        max: 255,
        maxMessage: 'Votre adresse est trop longue ({{ limit }} caractères)'
    )]
    private ?string $address = null;

    #[ORM\Column]
    #[Assert\Range(
        min: 1000,
        max: 98890,
        notInRangeMessage: 'Saisir un code postal entre 0{{ min }} et {{ max }} existant en France ',
    )]
    private ?int $zipCode = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(
        min: 1,
        minMessage: 'Veuillez saisir une ville',
        max: 45,
        maxMessage: 'Veuillez saisir une ville en France'
    )]
    private ?string $city = null;

    #[ORM\OneToOne(mappedBy: 'address', cascade: ['persist', 'remove'])]
    private ?PersonalData $personalData = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getZipCode(): ?int
    {
        return $this->zipCode;
    }

    public function setZipCode(int $zipCode): static
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getPersonalData(): ?PersonalData
    {
        return $this->personalData;
    }

    public function setPersonalData(PersonalData $personalData): static
    {
        // set the owning side of the relation if necessary
        if ($personalData->getAddress() !== $this) {
            $personalData->setAddress($this);
        }

        $this->personalData = $personalData;

        return $this;
    }
}
