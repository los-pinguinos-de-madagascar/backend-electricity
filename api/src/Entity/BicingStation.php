<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\BicingStationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\OneToMany(mappedBy: 'bicingStation', targetEntity: Comment::class)]
    private Collection $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
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
            $comment->setBicingStation($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getBicingStation() === $this) {
                $comment->setBicingStation(null);
            }
        }

        return $this;
    }
}
