<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\AwardRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AwardRepository::class)]
#[ApiResource]
class Award
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nameAward = null;

    #[ORM\Column]
    private ?int $price = null;


    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameAward(): ?string
    {
        return $this->nameAward;
    }

    public function setNameAward(string $nameAward): self
    {
        $this->nameAward = $nameAward;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }
}
