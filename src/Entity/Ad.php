<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdsRepository::class)]
class Ad
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $price = null;

    #[ORM\ManyToOne(inversedBy: 'Ads')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'ad', targetEntity: AdImage::class, orphanRemoval: true)]
    private Collection $adImages;

    #[ORM\Column(length: 255)]
    private ?string $ad_city = null;

    #[ORM\Column(length: 255)]
    private ?string $ad_country = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updated_at = null;

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
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }
}