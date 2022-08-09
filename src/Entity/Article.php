<?php
	
	namespace App\Entity;
                  	
                  	use App\Repository\ArticleRepository;
                  	use Doctrine\Common\Collections\ArrayCollection;
                  	use Doctrine\Common\Collections\Collection;
                  	use Doctrine\ORM\Mapping as ORM;
                  	use PhpParser\Node\Expr\Cast\Double;
                  	use Symfony\Component\Form\Extension\Core\Type\FormType;
                  	
                  	/**
                  	 * @ORM\Entity(repositoryClass=ArticleRepository::class)
                  	 */
                  	class Article extends FormType
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
                  		private $titre;
                  		
                  		/**
                  		 * @ORM\Column(type="string", length=255, nullable=true)
                  		 */
                  		private $description;
                  		
                  	
                  		
                  	
                  		
                  		
                  		/**
                  		 * @ORM\ManyToOne(targetEntity=Enchere::class, inversedBy="articles")
                  		 */
                  		private $enchere;
                  		
                  		/**
                  		 * @ORM\ManyToOne(targetEntity=Categorie::class, inversedBy="articles")
                  		 */
                  		private $categorie;
      
                    /**
                     * @ORM\Column(type="string", length=255)
                     */
                    private $image;
                  		
                  		
                  		
                  		public function __construct()
                  		{
                  		
                  		}
                  		
                  		
                  		
                  		public function getId(): ?int
                  		{
                  			return $this->id;
                  		}
                  		
                  		public function getTitre(): ?string
                  		{
                  			return $this->titre;
                  		}
                  		
                  		public function setTitre(string $titre): self
                  		{
                  			$this->titre = $titre;
                  			return $this;
                  		}
                  		
                  		public function getDescription(): ?string
                  		{
                  			return $this->description;
                  		}
                  		
                  		public function setDescription(?string $description): self
                  		{
                  			$this->description = $description;
                  			
                  			return $this;
                  		}
                  		
                  		
                  	
                  		
                  		public function getCategorie(): ?Categorie
                  		{
                  			return $this->categorie;
                  		}
                  		
                  		public function setCategorie(?Categorie $categorie): self
                  		{
                  			$this->categorie = $categorie;
                  			
                  			return $this;
                  		}
                  		
                  		
                  		public function getEnchere(): ?Enchere
                  		{
                  			return $this->enchere;
                  		}
                  		
                  		public function setEnchere(?Enchere $enchere): self
                  		{
                  			$this->enchere = $enchere;
                  			
                  			return $this;
                  		}
   
                    public function getImage(): ?string
                    {
                        return $this->image;
                    }

                    public function setImage(string $image): self
                    {
                        $this->image = $image;

                        return $this;
                    }
                  		
                  		
            
                    
                  		
                  		
                  		
                  		
                  		
                  		
                  		
                  	}