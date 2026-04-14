<?php

namespace App\Entity;

use App\Repository\CouleurRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CouleurRepository::class)]
class Couleur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $NomCouleur = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomCouleur(): ?string
    {
        return $this->NomCouleur;
    }

    public function setNomCouleur(string $NomCouleur): static
    {
        $this->NomCouleur = $NomCouleur;

        return $this;
    }
}
