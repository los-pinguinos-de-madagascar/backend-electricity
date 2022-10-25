<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\RechargeStationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RechargeStationRepository::class)]
#[ApiResource]
class RechargeStation extends Station
{

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $speedType = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $connectionType = null;

    #[ORM\Column(nullable: true)]
    private ?float $power = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $currentType = null;

    #[ORM\Column(nullable: true)]
    private ?int $slots = null;

    public function __construct(float $latitude, float $longitude, bool $status, string $address, string $speedType,
                                string $connectionType, float $power, string $currentType, int $slots)
    {
        $this->setLatitude($latitude);
        $this->setLongitude($longitude);
        $this->setStatus($status);
        $this->setAddress($address);
        $this->setSpeedType($speedType);
        $this->setConnectionType($connectionType);
        $this->setPower($power);
        $this->setCurrentType($currentType);
        $this->setSlots($slots);
    }

    public function getSpeedType(): ?string
    {
        return $this->speedType;
    }

    public function setSpeedType(?string $speedType): self
    {
        $this->speedType = $speedType;

        return $this;
    }

    public function getConnectionType(): ?string
    {
        return $this->connectionType;
    }

    public function setConnectionType(?string $connectionType): self
    {
        $this->connectionType = $connectionType;

        return $this;
    }

    public function getPower(): ?float
    {
        return $this->power;
    }

    public function setPower(?float $power): self
    {
        $this->power = $power;

        return $this;
    }

    public function getCurrentType(): ?string
    {
        return $this->currentType;
    }

    public function setCurrentType(?string $currentType): self
    {
        $this->currentType = $currentType;

        return $this;
    }

    public function getSlots(): ?int
    {
        return $this->slots;
    }

    public function setSlots(?int $slots): self
    {
        $this->slots = $slots;

        return $this;
    }
}