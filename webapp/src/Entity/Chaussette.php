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

    #[ORM\ManyToOne(inversedBy: 'type')]
    private ?Couleur $couleur = null;

    #[ORM\ManyToOne(inversedBy: 'taille')]
    private ?Type $relation = null;

    #[ORM\Column(length: 255,nullable: true)]
    private ?string $taille = null;

    #[ORM\Column]
    private ?bool $couple = false;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $dateCreation = null;

    #[ORM\ManyToOne(inversedBy: 'chaussettes')]
    private ?Type $type = null;

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

    public function getCouleur(): ?Couleur
    {
        return $this->couleur;
    }

    public function setCouleur(?Couleur $couleur): static
    {
        $this->couleur = $couleur;

        return $this;
    }

    public function getRelation(): ?Type
    {
        return $this->relation;
    }

    public function setRelation(?Type $relation): static
    {
        $this->relation = $relation;

        return $this;
    }

    public function getTaille(): ?string
    {
        return $this->taille;
    }

    public function setTaille(?string $taille): static
    {
        $this->taille = $taille;

        return $this;
    }

    public function isCouple(): ?bool
    {
        return $this->couple;
    }

    public function setCouple(bool $couple): static
    {
        $this->couple = $couple;

        return $this;
    }

    public function getDateCreation(): ?\DateTime
    {
        return $this->dateCreation;
    }

    public function setDateCreation(?\DateTime $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(?Type $type): static
    {
        $this->type = $type;

        return $this;
    }
}
