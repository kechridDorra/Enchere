<?php

namespace App\Entity;

use App\Repository\EnchereRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EnchereRepository::class)
 */
class Enchere
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
    private $description;

    /**
     * @ORM\Column(type="date")
     */
    private $dateDebut;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $DateFin;

    /**
     * @ORM\Column(type="integer")
     */
    private $heureDebut;

    /**
     * @ORM\Column(type="integer")
     */
    private $heureFin;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="encheres")
     */
    private $users;

    /**
     * @ORM\ManyToOne(targetEntity=ProfilVendeur::class, inversedBy="encheres")
     */
    private $profilVendeur;

    /**
     * @ORM\OneToMany(targetEntity=Article::class, mappedBy="enchere")
     */
    private $articles;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->articles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?string
    {
        return $this->DateFin;
    }

    public function setDateFin(string $DateFin): self
    {
        $this->DateFin = $DateFin;

        return $this;
    }

    public function getHeureDebut(): ?int
    {
        return $this->heureDebut;
    }

    public function setHeureDebut(int $heureDebut): self
    {
        $this->heureDebut = $heureDebut;

        return $this;
    }

    public function getHeureFin(): ?int
    {
        return $this->heureFin;
    }

    public function setHeureFin(int $heureFin): self
    {
        $this->heureFin = $heureFin;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addEnchere($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            $user->removeEnchere($this);
        }

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

    /**
     * @return Collection<int, Article>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles[] = $article;
            $article->setEnchere($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): self
    {
        if ($this->articles->removeElement($article)) {
            // set the owning side to null (unless already changed)
            if ($article->getEnchere() === $this) {
                $article->setEnchere(null);
            }
        }

        return $this;
    }
}
