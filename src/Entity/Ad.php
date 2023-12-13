<?php

namespace App\Entity;

use App\Repository\AdRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AdRepository::class)]
class Ad
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Title cannot be blank')]
    #[Assert\Length(max: 255, maxMessage: 'Title cannot be longer than {{ limit }} characters')]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Description cannot be blank')]
    #[Assert\Length(max: 255, maxMessage: 'Description cannot be longer than {{ limit }} characters')]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Status cannot be blank')]
    private ?string $status = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255, maxMessage: 'Price cannot be longer than {{ limit }} characters')]
    private ?string $price = null;

    #[ORM\ManyToOne(inversedBy: 'Ads')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'ad', targetEntity: AdImage::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    #[Assert\Valid]
    private Collection $adImages;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Ad city cannot be blank')]
    private ?string $ad_city = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Ad state cannot be blank')]
    private ?string $ad_state = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Ad country cannot be blank')]
    private ?string $ad_country = null;

    #[ORM\Column]
    #[Assert\NotNull(message: 'Created at cannot be null')]
    // #[Assert\DateTime(message: 'Created at must be a valid DateTime')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    // #[Assert\DateTime(message: 'Updated at must be a valid DateTime')]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'ads')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Category cannot be null')]
    private ?AdCategory $category = null;

    public function __construct()
    {
        $this->adImages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

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

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(?string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, AdImage>
     */
    public function getAdImages(): Collection
    {
        return $this->adImages;
    }

    public function addAdImage(AdImage $adImage): static
    {
        if (!$this->adImages->contains($adImage)) {
            $this->adImages->add($adImage);
            $adImage->setAd($this);
        }

        return $this;
    }

    public function removeAdImage(AdImage $adImage): static
    {
        if ($this->adImages->removeElement($adImage)) {
            // set the owning side to null (unless already changed)
            if ($adImage->getAd() === $this) {
                $adImage->setAd(null);
            }
        }

        return $this;
    }

    public function getAdCity(): ?string
    {
        return $this->ad_city;
    }

    public function setAdCity(string $ad_city): static
    {
        $this->ad_city = $ad_city;

        return $this;
    }

    public function getAdState(): ?string
    {
        return $this->ad_state;
    }

    public function setAdState(string $ad_state): static
    {
        $this->ad_state = $ad_state;

        return $this;
    }

    public function getAdCountry(): ?string
    {
        return $this->ad_country;
    }

    public function setAdCountry(string $ad_country): static
    {
        $this->ad_country = $ad_country;

        return $this;
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

    public function getCategory(): ?AdCategory
    {
        return $this->category;
    }

    public function setCategory(?AdCategory $category): static
    {
        $this->category = $category;

        return $this;
    }
}