<?php

namespace App\Entity;

use App\Repository\DoctorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: DoctorRepository::class)]
class Doctor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getDoctor"])]
    private ?int $id = null;    

    #[ORM\Column(length: 255, unique: true)]    
    private ?string $registrationNumber = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["getDoctor", "getComments"])]    
    private ?User $user = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["getProfile"])]   
    private ?Service $service = null;

    #[ORM\OneToMany(mappedBy: 'doctor', targetEntity: Stay::class)]
    private Collection $stays;

    public function __construct()
    {
        $this->stays = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }    

    public function getRegistrationNumber(): ?string
    {
        return $this->registrationNumber;
    }

    public function setRegistrationNumber(string $registrationNumber): static
    {
        $this->registrationNumber = $registrationNumber;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;
        $user->setDoctor($this); // Valeur inverse dans user
        return $this;
    }

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service): static
    {
        $this->service = $service;

        return $this;
    }

    /**
     * @return Collection<int, Stay>
     */
    public function getStays(): Collection
    {
        return $this->stays;
    }

    public function addStay(Stay $stay): static
    {
        if (!$this->stays->contains($stay)) {
            $this->stays->add($stay);
            $stay->setDoctor($this);
        }

        return $this;
    }

    public function removeStay(Stay $stay): static
    {
        if ($this->stays->removeElement($stay)) {
            // set the owning side to null (unless already changed)
            if ($stay->getDoctor() === $this) {
                $stay->setDoctor(null);
            }
        }

        return $this;
    }
}
