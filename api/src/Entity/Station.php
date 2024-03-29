<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\StationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\MappedSuperclass(repositoryClass: StationRepository::class)]
class Station
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $latitude = null;

    #[ORM\Column]
    private ?float $longitude = null;

    #[ORM\Column]
    private ?bool $status = null;

    #[ORM\Column(length: 255)]
    private ?string $adress = " ";

    #[ORM\Column(nullable: true)]
    private ?int $polution = null;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private array $dangerousGases = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): self
    {
        $this->latitude = $latitude;
        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->adress;
    }

    public function setAddress(string $adress): self
    {
        $this->adress = $adress;

        return $this;
    }

    public function getPolution(): ?int
    {
        return $this->polution;
    }

    public function setPolution(?int $polution): self
    {
        $this->polution = $polution;

        return $this;
    }

    public function getDangerousGases(): array
    {
        return $this->dangerousGases;
    }

    public function setDangerousGases(?array $dangerousGases): self
    {
        $this->dangerousGases = $dangerousGases;

        return $this;
    }

}
