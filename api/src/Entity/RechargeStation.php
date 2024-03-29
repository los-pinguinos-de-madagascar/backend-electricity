<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\RechargeStationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ApiResource]
#[ORM\Entity(repositoryClass: RechargeStationRepository::class)]
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

    #[ORM\OneToMany(mappedBy: 'rechargeStation', targetEntity: Comment::class)]
    private Collection $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
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

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setRechargeStation($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getRechargeStation() === $this) {
                $comment->setRechargeStation(null);
            }
        }

        return $this;
    }
}
