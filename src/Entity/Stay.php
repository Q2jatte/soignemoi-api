<?php

namespace App\Entity;

use App\Repository\StayRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: StayRepository::class)]
class Stay
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getStays"])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(["getStays"])]
    private ?\DateTimeInterface $entranceDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(["getStays"])]
    private ?\DateTimeInterface $dischargeDate = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(["getStays"])]
    private ?string $reason = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?patient $patient = null;

    #[ORM\ManyToOne]
    #[Groups(["getStays"])]
    #[ORM\JoinColumn(nullable: false)]
    private ?service $service = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEntranceDate(): ?\DateTimeInterface
    {
        return $this->entranceDate;
    }

    public function setEntranceDate(\DateTimeInterface $entranceDate): static
    {
        $this->entranceDate = $entranceDate;

        return $this;
    }

    public function getDischargeDate(): ?\DateTimeInterface
    {
        return $this->dischargeDate;
    }

    public function setDischargeDate(\DateTimeInterface $dischargeDate): static
    {
        $this->dischargeDate = $dischargeDate;

        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(string $reason): static
    {
        $this->reason = $reason;

        return $this;
    }

    public function getPatient(): ?patient
    {
        return $this->patient;
    }

    public function setPatient(?patient $patient): static
    {
        $this->patient = $patient;

        return $this;
    }

    public function getService(): ?service
    {
        return $this->service;
    }

    public function setService(?service $service): static
    {
        $this->service = $service;

        return $this;
    }
}
