<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\ApiTokenRepository;
use Doctrine\ORM\Mapping as ORM;

#[ApiResource]
#[ORM\Entity(repositoryClass: ApiTokenRepository::class)]

class ApiToken
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $token = null;

    #[ORM\ManyToOne(inversedBy: 'apiTokens', cascade: ["persist"])]
    #[ORM\JoinColumn(nullable: false, unique: true)]
    private ?User $tokenOwner = null;

    public function __construct(String $token, User $tokenOwner)
    {
        $this->setToken($token);
        $this->setTokenOwner($tokenOwner);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getTokenOwner(): ?User
    {
        return $this->tokenOwner;
    }

    public function setTokenOwner(?User $tokenOwner): self
    {
        $this->tokenOwner = $tokenOwner;

        return $this;
    }
}
