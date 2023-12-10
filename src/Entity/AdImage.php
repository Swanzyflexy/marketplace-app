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
    private ?string $ad_image_name = null;

    #[ORM\Column(length: 255)]
    private ?string $ad_image_path = null;

    #[ORM\ManyToOne(inversedBy: 'adImages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ad $ad = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAdImageName(): ?string
    {
        return $this->ad_image_name;
    }

    public function setAdImageName(string $ad_image_name): static
    {
        $this->ad_image_name = $ad_image_name;

        return $this;
    }

    public function getAdImagePath(): ?string
    {
        return $this->ad_image_path;
    }

    public function setAdImagePath(string $ad_image_path): static
    {
        $this->ad_image_path = $ad_image_path;

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

    public function setUpdatedAt(\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }
}
