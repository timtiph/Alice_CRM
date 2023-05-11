<?php

namespace App\Entity;

use App\Repository\SerpResultRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SerpResultRepository::class)]
class SerpResult
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $googleRank = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\ManyToOne(inversedBy: 'serpResults')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SerpInfo $serpInfo = null;

    #[ORM\PrePersist]
    public function prePersist()
    {
        $this->date = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGoogleRank(): ?int
    {
        return $this->googleRank;
    }

    public function setGoogleRank(int $googleRank): self
    {
        $this->googleRank = $googleRank;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getSerpInfo(): ?SerpInfo
    {
        return $this->serpInfo;
    }

    public function setSerpInfo(?SerpInfo $serpInfo): self
    {
        $this->serpInfo = $serpInfo;

        return $this;
    }
}
