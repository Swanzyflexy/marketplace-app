<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['username', 'email'], message: 'This User Exists in the system')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Full name cannot be blank')]
    #[Assert\Length(max: 255, maxMessage: 'Full name cannot be longer than {{ limit }} characters')]
    private ?string $full_name = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank(message: 'Username cannot be blank')]
    #[Assert\Length(min: 3, max: 255, minMessage: 'Username must be at least {{ limit }} characters long', maxMessage: 'Username cannot be longer than {{ limit }} characters')]
    private ?string $username = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message: 'Email cannot be blank')]
    #[Assert\Email(message: 'Invalid email address')]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Phone cannot be blank')]
    // #[Assert\Regex(
    //     pattern: "/^\d{17}$/",
    //     message: 'Phone must be a 17-digit number'
    // )]
    private ?string $phone = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Status cannot be blank')]
    private ?string $status = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Ad::class, orphanRemoval: true)]
    private Collection $Ads;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255, maxMessage: 'Address cannot be longer than {{ limit }} characters')]
    private ?string $address = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'City cannot be blank')]
    private ?string $city = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'State cannot be blank')]
    private ?string $state = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Zipcode cannot be blank')]
    // #[Assert\Regex(
    //     pattern: "/^\d{5}$/",
    //     message: 'Zipcode must be a 8-digit number'
    // )]
    private ?string $zipcode = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Country cannot be blank')]
    private ?string $country = null;

    #[ORM\Column(length: 255, nullable: true)]
    // #[Assert\Url(message: 'Profile image must be a valid URL')]
    private ?string $profile_image = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Roles cannot be blank')]
    #[Assert\Choice(choices: ['ROLE_USER', 'ROLE_ADMIN'], multiple: true, message: 'Invalid roles')]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Assert\NotBlank(message: 'Password cannot be blank')]
    #[Assert\Length(min: 8, minMessage: 'Password must be at least {{ limit }} characters long')]
    private ?string $password = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: ActivityLog::class, cascade: ['persist'])]
    private Collection $activityLogs;

    public function __construct()
    {
        $this->Ads = new ArrayCollection();
        $this->activityLogs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullName(): ?string
    {
        return $this->full_name;
    }

    public function setFullName(string $full_name): static
    {
        $this->full_name = $full_name;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, Ad>
     */
    public function getAds(): Collection
    {
        return $this->Ads;
    }

    public function addAd(Ad $ad): static
    {
        if (!$this->Ads->contains($ad)) {
            $this->Ads->add($ad);
            $ad->setUser($this);
        }

        return $this;
    }

    public function removeAd(Ad $ad): static
    {
        if ($this->Ads->removeElement($ad)) {
            // set the owning side to null (unless already changed)
            if ($ad->getUser() === $this) {
                $ad->setUser(null);
            }
        }

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function getZipcode(): ?string
    {
        return $this->zipcode;
    }

    public function setZipcode(string $zipcode): static
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getProfileImage(): ?string
    {
        return $this->profile_image;
    }

    public function setProfileImage(?string $profile_image): static
    {
        $this->profile_image = $profile_image;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
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

    public function setRoles(array $roles): static
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

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAt(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @ORM\PreUpdate
     */
    public function setUpdatedAt(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return Collection<int, ActivityLog>
     */
    public function getActivityLogs(): Collection
    {
        return $this->activityLogs;
    }

    public function addActivityLog(ActivityLog $activityLog): static
    {
        if (!$this->activityLogs->contains($activityLog)) {
            $this->activityLogs->add($activityLog);
            $activityLog->setUser($this);
        }

        return $this;
    }

    public function removeActivityLog(ActivityLog $activityLog): static
    {
        if ($this->activityLogs->removeElement($activityLog)) {
            // set the owning side to null (unless already changed)
            if ($activityLog->getUser() === $this) {
                $activityLog->setUser(null);
            }
        }

        return $this;
    }

    public function logActivity(string $activity)
    {
        $activityLog = new ActivityLog();
        $activityLog->setUser($this);
        $activityLog->setActivity($activity);
        $activityLog->setCreatedAt(new \DateTimeImmutable());

        $this->addActivityLog($activityLog);
    }
}