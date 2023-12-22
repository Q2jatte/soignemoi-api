<?php

namespace App\Entity;

use App\Repository\PrescriptionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PrescriptionRepository::class)]
class Prescription
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getPrescriptions"])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(["getPrescriptions"])]
    private ?\DateTimeInterface $startAt = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(["getPrescriptions"])]
    private ?\DateTimeInterface $endAt = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Doctor $doctor = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Patient $patient = null;

    #[ORM\OneToMany(mappedBy: 'prescription', targetEntity: Medication::class)]
    #[Groups(["getPrescriptions"])]
    private Collection $medications;

    public function __construct()
    {
        $this->medications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartAt(): ?\DateTimeInterface
    {
        return $this->startAt;
    }

    public function setStartAt(\DateTimeInterface $startAt): static
    {
        $this->startAt = $startAt;

        return $this;
    }

    public function getEndAt(): ?\DateTimeInterface
    {
        return $this->endAt;
    }

    public function setEndAt(\DateTimeInterface $endAt): static
    {
        $this->endAt = $endAt;

        return $this;
    }

    public function getDoctor(): ?Doctor
    {
        return $this->doctor;
    }

    public function setDoctor(?Doctor $doctor): static
    {
        $this->doctor = $doctor;

        return $this;
    }

    public function getPatient(): ?Patient
    {
        return $this->patient;
    }

    public function setPatient(?Patient $patient): static
    {
        $this->patient = $patient;

        return $this;
    }

    /**
     * @return Collection<int, Stay>
     */
    public function getMedications(): Collection
    {
        return $this->medications;
    }

    public function addMedication(Medication $medication): static
    {
        if (!$this->medications->contains($medication)) {
            $this->medications->add($medication);
            $medication->setPrescription($this);
        }

        return $this;
    }

    public function removeMedication(Medication $medication): static
    {
        if ($this->medications->removeElement($medication)) {
            // set the owning side to null (unless already changed)
            if ($medication->getPrescription() === $this) {
                $medication->setPrescription(null);
            }
        }

        return $this;
    }
}
