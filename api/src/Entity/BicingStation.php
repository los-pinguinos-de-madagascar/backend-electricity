<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\BicingStationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BicingStationRepository::class)]
#[ApiResource]
class BicingStation extends Station
{

    #[ORM\Column]
    private ?int $capacity = null;

    #[ORM\Column]
    private ?int $mechanical = null;

    #[ORM\Column]
    private ?int $electrical = null;

    #[ORM\Column]
    private ?int $availableSlots = null;

    public function __construct(float $latitude, float $longitude, bool $status,
                                string $adress, int $capacity, int $mechanical, int $electrical, int $availableSlots)
    {
        $this->setLatitude($latitude);
        $this->setLongitude($longitude);
        $this->setStatus($status);
        $this->setAddress($adress);
        $this->setCapacity($capacity);
        $this->setMechanical($mechanical);
        $this->setElectrical($electrical);
        $this->setAvailableSlots($availableSlots);
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): self
    {
        $this->capacity = $capacity;

        return $this;
    }

    public function getMechanical(): ?int
    {
        return $this->mechanical;
    }

    public function setMechanical(int $mechanical): self
    {
        $this->mechanical = $mechanical;

        return $this;
    }

    public function getElectrical(): ?int
    {
        return $this->electrical;
    }

    public function setElectrical(int $electrical): self
    {
        $this->electrical = $electrical;

        return $this;
    }

    public function getAvailableSlots(): ?int
    {
        return $this->availableSlots;
    }

    public function setAvailableSlots(int $availableSlots): self
    {
        $this->availableSlots = $availableSlots;

        return $this;
    }
}
