<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use PhpParser\Node\Expr\Array_;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    normalizationContext: ['groups' => ['read']],
    denormalizationContext: ['groups' => ['write']],
)]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
    /**
     * @var string The hashed password
     */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Email]
    #[Groups('read')]
    private ?string $email = null;

    #[ORM\Column(nullable: false)]
    #[Assert\NotBlank]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    #[Groups('read')]
    private ?string $username = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups('read')]
    private ?string $fullname = null;

    #[ORM\Column]
    #[Assert\NotNull]
    private array $roles = ["ROLE_USER"];

    #[ORM\ManyToMany(targetEntity: Location::class, cascade:["persist"])]
    #[Groups('read')]
    private Collection $favouriteLocations;

    #[ORM\ManyToMany(targetEntity: BicingStation::class)]
    #[Groups('read')]
    private Collection $favouriteBicingStations;

    #[ORM\ManyToMany(targetEntity: RechargeStation::class)]
    #[Groups('read')]
    private Collection $favouriteRechargeStations;


    #[ORM\OneToMany(mappedBy: 'sender', targetEntity: Message::class, orphanRemoval: true)]
    #[Groups('read')]
    private Collection $messagesSender;

    #[ORM\OneToMany(mappedBy: 'receiver', targetEntity: Message::class, orphanRemoval: true)]
    #[Groups('read')]
    private Collection $messagesReceiver;

    #[ORM\OneToMany(mappedBy: 'userOwner', targetEntity: Comment::class, orphanRemoval: true)]
    #[Groups('read')]
    private Collection $comments;


    public function __construct()
    {
        $this->favouriteLocations = new ArrayCollection();
        $this->favouriteBicingStations = new ArrayCollection();
        $this->favouriteRechargeStations = new ArrayCollection();
        $this->messagesSender = new ArrayCollection();
        $this->messagesReceiver = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getFullname(): ?string
    {
        return $this->fullname;
    }

    public function setFullname(string $fullname): self
    {
        $this->fullname = $fullname;

        return $this;
    }

    /**
     * @return Collection<int, Location>
     */
    public function getFavouriteLocations(): Collection
    {
        return $this->favouriteLocations;
    }

    public function addFavouriteLocation(Location $favouriteLocation): self
    {
        if (!$this->favouriteLocations->contains($favouriteLocation)) {
            $this->favouriteLocations->add($favouriteLocation);
        }

        return $this;
    }

    public function removeFavouriteLocation(Location $favouriteLocation): self
    {
        $this->favouriteLocations->removeElement($favouriteLocation);

        return $this;
    }

    /**
     * @return Collection<int, BicingStation>
     */
    public function getFavouriteBicingStations(): Collection
    {
        return $this->favouriteBicingStations;
    }

    public function addFavouriteBicingStation(BicingStation $favouriteBicingStation): self
    {
        if (!$this->favouriteBicingStations->contains($favouriteBicingStation)) {
            $this->favouriteBicingStations->add($favouriteBicingStation);
        }

        return $this;
    }

    public function removeFavouriteBicingStation(BicingStation $favouriteBicingStation): self
    {
        $this->favouriteBicingStations->removeElement($favouriteBicingStation);

        return $this;
    }

    /**
     * @return Collection<int, RechargeStation>
     */
    public function getFavouriteRechargeStations(): Collection
    {
        return $this->favouriteRechargeStations;
    }

    public function addFavouriteRechargeStation(RechargeStation $favouriteRechargeStation): self
    {
        if (!$this->favouriteRechargeStations->contains($favouriteRechargeStation)) {
            $this->favouriteRechargeStations->add($favouriteRechargeStation);
        }

        return $this;
    }

    public function removeFavouriteRechargeStation(RechargeStation $favouriteRechargeStation): self
    {
        $this->favouriteRechargeStations->removeElement($favouriteRechargeStation);

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessagesSender(): Collection
    {
        return $this->messagesSender;
    }

    public function addMessagesSender(Message $messagesSender): self
    {
        if (!$this->messagesSender->contains($messagesSender)) {
            $this->messagesSender->add($messagesSender);
            $messagesSender->setSender($this);
        }

        return $this;
    }
     /* @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setUserOwner($this);
        }

        return $this;
    }

    public function removeMessagesSender(Message $messagesSender): self
    {
        if ($this->messagesSender->removeElement($messagesSender)) {
            // set the owning side to null (unless already changed)
            if ($messagesSender->getSender() === $this) {
                $messagesSender->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessagesReceiver(): Collection
    {
        return $this->messagesReceiver;
    }

    public function addMessagesReceiver(Message $messagesReceiver): self
    {
        if (!$this->messagesReceiver->contains($messagesReceiver)) {
            $this->messagesReceiver->add($messagesReceiver);
            $messagesReceiver->setReceiver($this);
        }

        return $this;
    }

    public function removeMessagesReceiver(Message $messagesReceiver): self
    {
        if ($this->messagesReceiver->removeElement($messagesReceiver)) {
            // set the owning side to null (unless already changed)
            if ($messagesReceiver->getReceiver() === $this) {
                $messagesReceiver->setReceiver(null);
            }
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getUserOwner() === $this) {
                $comment->setUserOwner(null);
            }
        }

        return $this;
    }

}
