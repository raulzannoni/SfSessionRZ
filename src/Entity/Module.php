<?php

namespace App\Entity;

use App\Repository\ModuleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ModuleRepository::class)]
class Module
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $title = null;

    #[ORM\ManyToOne(inversedBy: 'modules')]
    private ?Categorie $categorie = null;

    #[ORM\OneToMany(mappedBy: 'modules', targetEntity: Represent::class)]
    private Collection $represents;

    public function __construct()
    {
        $this->represents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): static
    {
        $this->categorie = $categorie;

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
            $represent->setModules($this);
        }

        return $this;
    }

    public function removeRepresent(Represent $represent): static
    {
        if ($this->represents->removeElement($represent)) {
            // set the owning side to null (unless already changed)
            if ($represent->getModules() === $this) {
                $represent->setModules(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->title;
    }
}
