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
#[ApiResource]
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
    #[ApiProperty(
        openapiContext: [
            'type' => 'string',
            'exmample' => 'electri@gmail.com'
        ]
    )]
    private ?string $email = null;

    #[ORM\Column(nullable: false)]
    #[Assert\NotBlank]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fullname = null;

    #[ORM\OneToMany(mappedBy: 'tokenOwner', targetEntity: ApiToken::class, orphanRemoval: true)]
    private Collection $apiTokens;

    #[ORM\Column]
    #[Assert\NotNull]
    private array $roles = ["ROLE_USER"];

    #[ORM\ManyToMany(targetEntity: Location::class, cascade:["persist"])]
    private Collection $favouriteLocations;

    #[ORM\ManyToMany(targetEntity: BicingStation::class)]
    private Collection $favouriteBicingStations;

    #[ORM\ManyToMany(targetEntity: RechargeStation::class)]
    private Collection $favouriteRechargeStations;

    #[ORM\Column]
    private ?int $electryCoins = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $skinCursor = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $skinPalette = null;

    #[ORM\ManyToMany(targetEntity: Award::class, inversedBy: 'users')]
    private Collection $awards;

    public function __construct()
    {
        $this->apiTokens = new ArrayCollection();
        $this->favouriteLocations = new ArrayCollection();
        $this->favouriteBicingStations = new ArrayCollection();
        $this->favouriteRechargeStations = new ArrayCollection();
        $this->awards = new ArrayCollection();
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

    /**
     * @return Collection<int, ApiToken>
     */
    public function getApiTokens(): Collection
    {
        return $this->apiTokens;
    }

    public function addApiToken(ApiToken $apiToken): self
    {
        if (!$this->apiTokens->contains($apiToken)) {
            $this->apiTokens->add($apiToken);
            $apiToken->setTokenOwner($this);
        }

        return $this;
    }

    public function removeApiToken(ApiToken $apiToken): self
    {
        if ($this->apiTokens->removeElement($apiToken)) {
            // set the owning side to null (unless already changed)
            if ($apiToken->getTokenOwner() === $this) {
                $apiToken->setTokenOwner(null);
            }
        }

        return $this;
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

    public function getElectryCoins(): ?int
    {
        return $this->electryCoins;
    }

    public function setElectryCoins(int $electryCoins): self
    {
        $this->electryCoins = $electryCoins;

        return $this;
    }

    public function getSkinCursor(): ?string
    {
        return $this->skinCursor;
    }

    public function setSkinCursor(?string $skinCursor): self
    {
        $this->skinCursor = $skinCursor;

        return $this;
    }

    public function getSkinPalette(): ?string
    {
        return $this->skinPalette;
    }

    public function setSkinPalette(?string $skinPalette): self
    {
        $this->skinPalette = $skinPalette;

        return $this;
    }

    /**
     * @return Collection<int, Award>
     */
    public function getAwards(): Collection
    {
        return $this->awards;
    }

    public function addAward(Award $award): self
    {
        if (!$this->awards->contains($award)) {
            $this->awards->add($award);
        }

        return $this;
    }

    public function removeAward(Award $award): self
    {
        $this->awards->removeElement($award);

        return $this;
    }

}
