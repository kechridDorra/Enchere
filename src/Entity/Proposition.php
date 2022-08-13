<?php

namespace App\Entity;

use App\Repository\PropositionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PropositionRepository::class)
 */
class Proposition
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
    private $reponse;

  

    /**
     * @ORM\ManyToOne(targetEntity=ProfilVendeur::class, inversedBy="propositions")
     */
    private $profilVendeur;

    /**
     * @ORM\ManyToOne(targetEntity=AppelOffre::class, inversedBy="propositons")
     */
    private $appelOffre;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $prix;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReponse(): ?string
    {
        return $this->reponse;
    }

    public function setReponse(string $reponse): self
    {
        $this->reponse = $reponse;

        return $this;
    }

    

    public function getProfilVendeur(): ?ProfilVendeur
    {
        return $this->profilVendeur;
    }

    public function setProfilVendeur(?ProfilVendeur $profilVendeur): self
    {
        $this->profilVendeur = $profilVendeur;

        return $this;
    }

    public function getAppelOffre(): ?AppelOffre
    {
        return $this->appelOffre;
    }

    public function setAppelOffre(?AppelOffre $appelOffre): self
    {
        $this->appelOffre = $appelOffre;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(?float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }
}
