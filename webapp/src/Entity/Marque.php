<?php

namespace App\Entity;

use App\Repository\MarqueRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MarqueRepository::class)]
class Marque
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $NomMarque = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomMarque(): ?string
    {
        return $this->NomMarque;
    }

    public function setNomMarque(string $NomMarque): static
    {
        $this->NomMarque = $NomMarque;

        return $this;
    }
}
