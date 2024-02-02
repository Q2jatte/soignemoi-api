<?php

namespace App\Entity;

use App\Repository\ServiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ServiceRepository::class)]
class Service
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getServices"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getServices", "getStays", "getProfile", "getEntries", "getExits"])]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'service', targetEntity: Stay::class)]
    private Collection $stays;

    public function __construct()
    {
        $this->stays = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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
            $stay->setService($this);
        }

        return $this;
    }

    public function removeStay(Stay $stay): static
    {
        if ($this->stays->removeElement($stay)) {
            // set the owning side to null (unless already changed)
            if ($stay->getService() === $this) {
                $stay->setService(null);
            }
        }

        return $this;
    }
}
