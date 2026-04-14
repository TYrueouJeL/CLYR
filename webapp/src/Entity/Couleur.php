<?php

namespace App\Entity;

use App\Repository\CouleurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /**
     * @var Collection<int, Chaussette>
     */
    #[ORM\OneToMany(targetEntity: Chaussette::class, mappedBy: 'couleur')]
    private Collection $type;

    public function __construct()
    {
        $this->type = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Chaussette>
     */
    public function getType(): Collection
    {
        return $this->type;
    }

    public function addType(Chaussette $type): static
    {
        if (!$this->type->contains($type)) {
            $this->type->add($type);
            $type->setCouleur($this);
        }

        return $this;
    }

    public function removeType(Chaussette $type): static
    {
        if ($this->type->removeElement($type)) {
            // set the owning side to null (unless already changed)
            if ($type->getCouleur() === $this) {
                $type->setCouleur(null);
            }
        }

        return $this;
    }
}
