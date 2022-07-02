<?php

namespace App\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

use DateTimeInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User implements UserInterface
{
	/**
	 * @ORM\Id
	 * @ORM\GeneratedValue
	 * @ORM\Column(type="integer")
	 */
	private $id;
	
	/**
	 * @ORM\Column(type="string", length=180, unique=true)
	 */
	private $email;
	
	/**
	 * @ORM\Column(type="array")
	 */
	private $roles = [];
	
	/**
	 * @var string The hashed password
	 * @ORM\Column(type="string")
	 */
	private $password;
	
	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $nom;
	
	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $prenom;
	
	
	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $adresse;
	
	/**
	 * @ORM\Column(type="integer")
	 */
	private $codePostal;
	
	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $ville;
	
	
	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $numeroCarte;
	
	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $codeSecurite;
	
	/**
	 * @ORM\OneToOne(targetEntity=ProfilVendeur::class, cascade={"persist", "remove"})
	 */
	private $profilVendeur;
	
	/**
	 * @ORM\OneToMany(targetEntity=Notification::class, mappedBy="user")
	 */
	private $notifications;
	
	/**
	 * @ORM\OneToMany(targetEntity=AppelOffre::class, mappedBy="user")
	 */
	private $appelOffres;
	
	/**
	 * @ORM\ManyToMany(targetEntity=Enchere::class, inversedBy="users")
	 */
	private $encheres;
	
	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $telephone;
	
	/**
	 * @ORM\Column(type="integer")
	 */
	private $moisEXp;

    /**
     * @ORM\Column(type="integer")
     */
    private $anneeExp;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    
    
	public function __construct()
                                    	{
                                    		$this->notifications = new ArrayCollection();
                                    		$this->appelOffres = new ArrayCollection();
                                    		$this->encheres = new ArrayCollection();
                                    	}
	
	public function getId(): ?int
                                    	{
                                    		return $this->id;
                                    	}
	
	public function getEmail(): ?string
                                    	{
                                    		return $this->email;
                                    	}
	
	public function setEmail(string $email): self
                                    	{
                                    		$this->email = $email;
                                    		
                                    		return $this;
                                    	}
	
	/**
	 * A visual identifier that represents this user.
	 *
	 * @see UserInterface
	 */
	public function getUsername(): string
                                    	{
                                    		return (string)$this->email;
                                    	}
	
	/**
	 * @see UserInterface
	 */
	public function getRoles(): array
                                    	{
                                    		$roles = $this->roles;
                                    		// guarantee every user at least has ROLE_USER
                                    		$roles[] = 'ROLE_USER';
                                    		
                                    		return array_unique($roles);
                                    	}
	
	public function setRoles(array $roles): self
                                    	{
                                    		$this->roles = $roles;
                                    		
                                    		return $this;
                                    	}
	
	/**
	 * @see UserInterface
	 */
	public function getPassword(): string
                                    	{
                                    		return (string)$this->password;
                                    	}
	
	public function setPassword(string $password): self
                                    	{
                                    		$this->password = $password;
                                    		return $this;
                                    	}
	
	/**
	 * Returning a salt is only needed, if you are not using a modern
	 * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
	 *
	 * @see UserInterface
	 */
	public function getSalt(): ?string
                                    	{
                                    		return null;
                                    	}
	
	/**
	 * @see UserInterface
	 */
	public function eraseCredentials()
                                    	{
                                    		// If you store any temporary, sensitive data on the user, clear it here
                                    		// $this->plainPassword = null;
                                    	}
	
	public function getNom(): ?string
                                    	{
                                    		return $this->nom;
                                    	}
	
	public function setNom(string $nom): self
                                    	{
                                    		$this->nom = $nom;
                                    		
                                    		return $this;
                                    	}
	
	public function getPrenom(): ?string
                                    	{
                                    		return $this->prenom;
                                    	}
	
	public function setPrenom(string $prenom): self
                                    	{
                                    		$this->prenom = $prenom;
                                    		
                                    		return $this;
                                    	}
	
	
	
	public function getAdresse(): ?string
                                    	{
                                    		return $this->adresse;
                                    	}
	
	public function setAdresse(string $adresse): self
                                    	{
                                    		$this->adresse = $adresse;
                                    		
                                    		return $this;
                                    	}
	
	public function getCodePostal(): ?int
                                    	{
                                    		return $this->codePostal;
                                    	}
	
	public function setCodePostal(int $codePostal): self
                                    	{
                                    		$this->codePostal = $codePostal;
                                    		
                                    		return $this;
                                    	}
	
	public function getVille(): ?string
                                    	{
                                    		return $this->ville;
                                    	}
	
	public function setVille(string $ville): self
                                    	{
                                    		$this->ville = $ville;
                                    		
                                    		return $this;
                                    	}
	
	
	public function getNumeroCarte(): ?string
                                    	{
                                    		return $this->numeroCarte;
                                    	}
	
	public function setNumeroCarte(string $numeroCarte): self
                                    	{
                                    		$this->numeroCarte = $numeroCarte;
                                    		
                                    		return $this;
                                    	}
	
	public function getCodeSecurite(): ?string
                                    	{
                                    		return $this->codeSecurite;
                                    	}
	
	public function setCodeSecurite(string $codeSecurite): self
                                    	{
                                    		$this->codeSecurite = $codeSecurite;
                                    		
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
	 * @return Collection<int, Notification>
	 */
	public function getNotifications(): Collection
                                    	{
                                    		return $this->notifications;
                                    	}
	
	public function addNotification(Notification $notification): self
                                    	{
                                    		if (!$this->notifications->contains($notification)) {
                                    			$this->notifications[] = $notification;
                                    			$notification->setUser($this);
                                    		}
                                    		
                                    		return $this;
                                    	}
	
	public function removeNotification(Notification $notification): self
                                    	{
                                    		if ($this->notifications->removeElement($notification)) {
                                    			// set the owning side to null (unless already changed)
                                    			if ($notification->getUser() === $this) {
                                    				$notification->setUser(null);
                                    			}
                                    		}
                                    		
                                    		return $this;
                                    	}
	
	/**
	 * @return Collection<int, AppelOffre>
	 */
	public function getAppelOffres(): Collection
                                    	{
                                    		return $this->appelOffres;
                                    	}
	
	public function addAppelOffre(AppelOffre $appelOffre): self
                                    	{
                                    		if (!$this->appelOffres->contains($appelOffre)) {
                                    			$this->appelOffres[] = $appelOffre;
                                    			$appelOffre->setUser($this);
                                    		}
                                    		
                                    		return $this;
                                    	}
	
	public function removeAppelOffre(AppelOffre $appelOffre): self
                                    	{
                                    		if ($this->appelOffres->removeElement($appelOffre)) {
                                    			// set the owning side to null (unless already changed)
                                    			if ($appelOffre->getUser() === $this) {
                                    				$appelOffre->setUser(null);
                                    			}
                                    		}
                                    		
                                    		return $this;
                                    	}
	
	/**
	 * @return Collection<int, Enchere>
	 */
	public function getEncheres(): Collection
                                    	{
                                    		return $this->encheres;
                                    	}
	
	public function addEnchere(Enchere $enchere): self
                                    	{
                                    		if (!$this->encheres->contains($enchere)) {
                                    			$this->encheres[] = $enchere;
                                    		}
                                    		
                                    		return $this;
                                    	}
	
	public function removeEnchere(Enchere $enchere): self
                                    	{
                                    		$this->encheres->removeElement($enchere);
                                    		
                                    		return $this;
                                    	}
	
	public function getTelephone(): ?string
                                    	{
                                    		return $this->telephone;
                                    	}
	
	public function setTelephone(string $telephone): self
                                    	{
                                    		$this->telephone = $telephone;
                                    		
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
	
	public function getMoisEXp(): ?int
                                    	{
                                    		return $this->moisEXp;
                                    	}
	
	public function setMoisEXp(int $moisEXp): self
                                    	{
                                    		$this->moisEXp = $moisEXp;
                                    		
                                    		return $this;
                                    	}

    public function getAnneeExp(): ?int
    {
        return $this->anneeExp;
    }

    public function setAnneeExp(int $anneeExp): self
    {
        $this->anneeExp = $anneeExp;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getIsVerified(): ?bool
    {
        return $this->isVerified;
    }

   
}
