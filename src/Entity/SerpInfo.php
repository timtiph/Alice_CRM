<?php

namespace App\Entity;

use App\Repository\SerpInfoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SerpInfoRepository::class)]
class SerpInfo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 400)]
    private ?string $keyword = null;

    #[ORM\Column(length: 2, nullable: true)]
    private ?string $googleRank = null;

    #[ORM\ManyToOne(inversedBy: 'serpInfos')]
    private ?Contract $contract = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getKeyword(): ?string
    {
        return $this->keyword;
    }

    public function setKeyword(string $keyword): self
    {
        $this->keyword = $keyword;

        return $this;
    }

    public function getGoogleRank(): ?string
    {
        return $this->googleRank;
    }

    public function setGoogleRank(?string $googleRank): self
    {
        $this->googleRank = $googleRank;

        return $this;
    }

    public function getContract(): ?Contract
    {
        return $this->contract;
    }

    public function setContract(?Contract $contract): self
    {
        $this->contract = $contract;

        return $this;
    }
}
