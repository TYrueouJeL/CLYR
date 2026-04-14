<?php

namespace App\Entity;

use App\Repository\ChaussetteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChaussetteRepository::class)]
class Chaussette
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $Commentaire = null;

    #[ORM\Column(length: 255)]
    private ?string $NomChaussette = null;

    #[ORM\Column]
    private ?bool $Statut = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommentaire(): ?string
    {
        return $this->Commentaire;
    }

    public function setCommentaire(string $Commentaire): static
    {
        $this->Commentaire = $Commentaire;

        return $this;
    }

    public function getNomChaussette(): ?string
    {
        return $this->NomChaussette;
    }

    public function setNomChaussette(string $NomChaussette): static
    {
        $this->NomChaussette = $NomChaussette;

        return $this;
    }

    public function isStatut(): ?bool
    {
        return $this->Statut;
    }

    public function setStatut(bool $Statut): static
    {
        $this->Statut = $Statut;

        return $this;
    }
}
