<?php

namespace App\Entity;

use App\Repository\AppelOffreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AppelOffreRepository::class)
 */
class AppelOffre
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $contexte;

    /**
     * @ORM\Column(type="date")
     */
    private $dateExp;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="appelOffres")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=Proposition::class, mappedBy="appelOffre")
     */
    private $propositons;

    public function __construct()
    {
        $this->propositons = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContexte(): ?string
    {
        return $this->contexte;
    }

    public function setContexte(string $contexte): self
    {
        $this->contexte = $contexte;

        return $this;
    }

    public function getDateExp(): ?\DateTimeInterface
    {
        return $this->dateExp;
    }

    public function setDateExp(\DateTimeInterface $dateExp): self
    {
        $this->dateExp = $dateExp;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Proposition>
     */
    public function getPropositons(): Collection
    {
        return $this->propositons;
    }

    public function addPropositon(Proposition $propositon): self
    {
        if (!$this->propositons->contains($propositon)) {
            $this->propositons[] = $propositon;
            $propositon->setAppelOffre($this);
        }

        return $this;
    }

    public function removePropositon(Proposition $propositon): self
    {
        if ($this->propositons->removeElement($propositon)) {
            // set the owning side to null (unless already changed)
            if ($propositon->getAppelOffre() === $this) {
                $propositon->setAppelOffre(null);
            }
        }

        return $this;
    }
}