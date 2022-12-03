<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dataIni = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dataFi = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userReservation = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?RechargeStation $rechargeStation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDataIni(): ?\DateTimeInterface
    {
        return $this->dataIni;
    }

    public function setDataIni(\DateTimeInterface $dataIni): self
    {
        $this->dataIni = $dataIni;

        return $this;
    }

    public function getDataFi(): ?\DateTimeInterface
    {
        return $this->dataFi;
    }

    public function setDataFi(\DateTimeInterface $dataFi): self
    {
        $this->dataFi = $dataFi;

        return $this;
    }

    public function getUserReservation(): ?User
    {
        return $this->userReservation;
    }

    public function setUserReservation(?User $userReservation): self
    {
        $this->userReservation = $userReservation;

        return $this;
    }

    public function getRechargeStation(): ?RechargeStation
    {
        return $this->rechargeStation;
    }

    public function setRechargeStation(?RechargeStation $rechargeStation): self
    {
        $this->rechargeStation = $rechargeStation;

        return $this;
    }
}
