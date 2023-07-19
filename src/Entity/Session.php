<?php

namespace App\Entity;

use App\Repository\SessionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SessionRepository::class)]
class Session
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $totalPlaces = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateStart = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateEnd = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $program = null;

    #[ORM\ManyToOne(inversedBy: 'sessions')]
    private ?Formation $formation = null;

    #[ORM\ManyToMany(targetEntity: Stagiaire::class, mappedBy: 'sessions')]
    private Collection $stagiaires;

    #[ORM\OneToMany(mappedBy: 'sessions', targetEntity: Represent::class)]
    private Collection $represents;

    public function __construct()
    {
        $this->stagiaires = new ArrayCollection();
        $this->represents = new ArrayCollection();
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

    public function getTotalPlaces(): ?int
    {
        return $this->totalPlaces;
    }

    public function setTotalPlaces(int $totalPlaces): static
    {
        $this->totalPlaces = $totalPlaces;

        return $this;
    }

    public function getDateStart(): ?\DateTimeInterface
    {
        return $this->dateStart;
    }

    public function setDateStart(\DateTimeInterface $dateStart): static
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    public function getDateEnd(): ?\DateTimeInterface
    {
        return $this->dateEnd;
    }

    public function setDateEnd(\DateTimeInterface $dateEnd): static
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    public function getProgram(): ?string
    {
        return $this->program;
    }

    public function setProgram(string $program): static
    {
        $this->program = $program;

        return $this;
    }

    public function getFormation(): ?Formation
    {
        return $this->formation;
    }

    public function setFormation(?Formation $formation): static
    {
        $this->formation = $formation;

        return $this;
    }

    /**
     * @return Collection<int, Stagiaire>
     */
    public function getStagiaires(): Collection
    {
        return $this->stagiaires;
    }

    public function addStagiaire(Stagiaire $stagiaire): static
    {
        if (!$this->stagiaires->contains($stagiaire)) {
            $this->stagiaires->add($stagiaire);
            $stagiaire->addSession($this);
        }

        return $this;
    }

    public function removeStagiaire(Stagiaire $stagiaire): static
    {
        if ($this->stagiaires->removeElement($stagiaire)) {
            $stagiaire->removeSession($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Represent>
     */
    public function getRepresents(): Collection
    {
        return $this->represents;
    }

    public function addRepresent(Represent $represent): static
    {
        if (!$this->represents->contains($represent)) {
            $this->represents->add($represent);
            $represent->setSessions($this);
        }

        return $this;
    }

    public function removeRepresent(Represent $represent): static
    {
        if ($this->represents->removeElement($represent)) {
            // set the owning side to null (unless already changed)
            if ($represent->getSessions() === $this) {
                $represent->setSessions(null);
            }
        }

        return $this;
    }
}
