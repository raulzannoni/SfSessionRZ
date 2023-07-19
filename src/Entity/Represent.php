<?php

namespace App\Entity;

use App\Repository\RepresentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RepresentRepository::class)]
class Represent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $days = null;

    #[ORM\ManyToOne(inversedBy: 'represents')]
    private ?Session $sessions = null;

    #[ORM\ManyToOne(inversedBy: 'represents')]
    private ?Module $modules = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDays(): ?int
    {
        return $this->days;
    }

    public function setDays(int $days): static
    {
        $this->days = $days;

        return $this;
    }

    public function getSessions(): ?Session
    {
        return $this->sessions;
    }

    public function setSessions(?Session $sessions): static
    {
        $this->sessions = $sessions;

        return $this;
    }

    public function getModules(): ?Module
    {
        return $this->modules;
    }

    public function setModules(?Module $modules): static
    {
        $this->modules = $modules;

        return $this;
    }
}
