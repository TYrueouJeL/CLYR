<?php

namespace App\Entity;

use App\Repository\TypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypeRepository::class)]
class Type
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $NomType = null;

    /**
     * @var Collection<int, Chaussette>
     */
    #[ORM\OneToMany(targetEntity: Chaussette::class, mappedBy: 'relation')]
    private Collection $taille;

    /**
     * @var Collection<int, Chaussette>
     */
    #[ORM\OneToMany(targetEntity: Chaussette::class, mappedBy: 'type')]
    private Collection $chaussettes;

    public function __construct()
    {
        $this->taille = new ArrayCollection();
        $this->chaussettes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomType(): ?string
    {
        return $this->NomType;
    }

    public function setNomType(string $NomType): static
    {
        $this->NomType = $NomType;

        return $this;
    }

    /**
     * @return Collection<int, Chaussette>
     */
    public function getTaille(): Collection
    {
        return $this->taille;
    }

    public function addTaille(Chaussette $taille): static
    {
        if (!$this->taille->contains($taille)) {
            $this->taille->add($taille);
            $taille->setRelation($this);
        }

        return $this;
    }

    public function removeTaille(Chaussette $taille): static
    {
        if ($this->taille->removeElement($taille)) {
            // set the owning side to null (unless already changed)
            if ($taille->getRelation() === $this) {
                $taille->setRelation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Chaussette>
     */
    public function getChaussettes(): Collection
    {
        return $this->chaussettes;
    }

    public function addChaussette(Chaussette $chaussette): static
    {
        if (!$this->chaussettes->contains($chaussette)) {
            $this->chaussettes->add($chaussette);
            $chaussette->setType($this);
        }

        return $this;
    }

    public function removeChaussette(Chaussette $chaussette): static
    {
        if ($this->chaussettes->removeElement($chaussette)) {
            // set the owning side to null (unless already changed)
            if ($chaussette->getType() === $this) {
                $chaussette->setType(null);
            }
        }

        return $this;
    }
}
