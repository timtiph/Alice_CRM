<?php

namespace App\Entity;

use App\Repository\SerpInfoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\ManyToOne(inversedBy: 'serpInfos', cascade: ['persist'])]
    private ?Contract $contract = null;

    #[ORM\OneToMany(mappedBy: 'serpInfo', targetEntity: SerpResult::class, orphanRemoval: true)]
    private Collection $serpResults;

    public function __construct()
    {
        $this->serpResults = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, SerpResult>
     */
    public function getSerpResults(): Collection
    {
        return $this->serpResults;
    }

    public function addSerpResult(SerpResult $serpResult): self
    {
        if (!$this->serpResults->contains($serpResult)) {
            $this->serpResults->add($serpResult);
            $serpResult->setSerpInfo($this);
        }

        return $this;
    }

    public function removeSerpResult(SerpResult $serpResult): self
    {
        if ($this->serpResults->removeElement($serpResult)) {
            // set the owning side to null (unless already changed)
            if ($serpResult->getSerpInfo() === $this) {
                $serpResult->setSerpInfo(null);
            }
        }

        return $this;
    }
}
