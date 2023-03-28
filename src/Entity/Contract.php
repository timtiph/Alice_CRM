<?php

namespace App\Entity;

use App\Repository\ContractRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContractRepository::class)]
class Contract
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $amountCharged = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $timeCharged = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $amountReal = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $timeReal = null;

    #[ORM\Column(length: 255)]
    private ?string $websiteLink = null;

    #[ORM\Column(length: 255)]
    private ?string $paymentFrequency = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $openArea = null;

    #[ORM\ManyToOne(inversedBy: 'contracts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Customer $customer = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function getAmountCharged(): ?string
    {
        return $this->amountCharged;
    }

    public function setAmountCharged(string $amountCharged): self
    {
        $this->amountCharged = $amountCharged;

        return $this;
    }

    public function getTimeCharged(): ?\DateTimeInterface
    {
        return $this->timeCharged;
    }

    public function setTimeCharged(\DateTimeInterface $timeCharged): self
    {
        $this->timeCharged = $timeCharged;

        return $this;
    }

    public function getAmountReal(): ?string
    {
        return $this->amountReal;
    }

    public function setAmountReal(string $amountReal): self
    {
        $this->amountReal = $amountReal;

        return $this;
    }

    public function getTimeReal(): ?\DateTimeInterface
    {
        return $this->timeReal;
    }

    public function setTimeReal(\DateTimeInterface $timeReal): self
    {
        $this->timeReal = $timeReal;

        return $this;
    }

    public function getWebsiteLink(): ?string
    {
        return $this->websiteLink;
    }

    public function setWebsiteLink(string $websiteLink): self
    {
        $this->websiteLink = $websiteLink;

        return $this;
    }

    public function getPaymentFrequency(): ?string
    {
        return $this->paymentFrequency;
    }

    public function setPaymentFrequency(string $paymentFrequency): self
    {
        $this->paymentFrequency = $paymentFrequency;

        return $this;
    }

    public function getOpenArea(): ?string
    {
        return $this->openArea;
    }

    public function setOpenArea(?string $openArea): self
    {
        $this->openArea = $openArea;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }
}
