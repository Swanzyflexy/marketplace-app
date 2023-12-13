<?php

namespace App\Entity;

use App\Repository\AdImageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdImageRepository::class)]
class AdImage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $adImageName = null;

    #[ORM\Column(length: 255)]
    private ?string $adImagePath = null;

    #[ORM\ManyToOne(inversedBy: 'adImages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ad $ad = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAdImageName(): ?string
    {
        return $this->adImageName;
    }

    public function setAdImageName(string $adImageName): static
    {
        $this->adImageName = $adImageName;

        return $this;
    }

    public function getAdImagePath(): ?string
    {
        return $this->adImagePath;
    }

    public function setAdImagePath(string $adImagePath): static
    {
        $this->adImagePath = $adImagePath;

        return $this;
    }

    public function getAd(): ?Ad
    {
        return $this->ad;
    }

    public function setAd(?Ad $ad): static
    {
        $this->ad = $ad;

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
}