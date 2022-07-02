<?php
	
	namespace App\Controller;
	use App\Entity\Article;
	use App\Entity\Categorie;
	use App\Entity\Enchere;
	use App\Entity\Images;
	use App\Form\ArticleType;
	use App\Form\EnchereType;
	use App\Repository\ArticleRepository;
	use Doctrine\ORM\EntityManagerInterface;
	use FOS\RestBundle\Controller\AbstractFOSRestController;
	use Lcobucci\JWT\Signer\Ecdsa;
	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;
	use FOS\RestBundle\Controller\Annotations as Rest;
	
	
	class ArticleController extends AbstractFOSRestController
	{
		/**
		 * @var EntityManagerInterface
		 */
		private $entityManager;
		
		/**
		 * @ ArticleRepository
		 */
		private $articleRepository;
		
		
		
		public function __construct( EntityManagerInterface $entityManager,
		
		                             ArticleRepository $articleRepository)
		{
			
			$this->entityManager = $entityManager;
			
			$this->articleRepository=$articleRepository;
		}
		
		/**
		 * @param Request $request
		 * @Route("/article/{article}", name="article_show", methods={"GET"})
		 * @return \FOS\RestBundle\View\View
		 */
		public function show(Article $article)
		{
			$data = $this->getDoctrine()->getRepository
			(Article::class)->find($article);
			return $this->view($data, Response::HTTP_OK);
		}
		
		/**
		 * @param Request $request
		 * @Route("/articles", name="article_list", methods={"GET"})
		 * @return Response
		 */
		public function list()
		{
			
			$repository = $this->getDoctrine()->getRepository(Article::class);
			$articles = $repository->findAll();
			return $this->handleView($this->view($articles));
		}
		
		/** creation article
		 * @param Request $request
		 * @Route("/api/article/{enchere}", name="app_article_new", methods={"POST"})
		 * @return \FOS\RestBundle\View\View|Response
		 */
		public function new(Request $request,Enchere $enchere):Response
		{   $em = $this->getDoctrine()->getManager();
			$ench = $this->getDoctrine()->getRepository
			(Enchere::class)->find($enchere);
			$titre = $request->get('titre');
			$description = $request ->get('description');
			$prixInitial =$request ->get('prixInitial');
			$categorie = $request->get('categorie');
			$cat = $this->getDoctrine()->getRepository
			(Categorie::class)->find($categorie);
			$images = $request->files;
			$article= new Article();
			$article ->setTitre($titre);
			$article ->setDescription($description);
			$article->setPrixInitial($prixInitial);
			$article->setPrixVente($prixInitial);
			$article->setCategorie($cat);
			$article->setEnchere($ench);
			foreach($images as $image){
				// On génère un nouveau nom de fichier
				$fichier = md5(uniqid()).'.'.$image->guessExtension();
				// On copie le fichier dans le dossier uploads
				$image->move(
					$this->getParameter('images_directory'),
					$fichier
				);
				// On crée l'image dans la base de données
				$img = new Images();
				$img->setUrl($fichier);
				$article->addImage($img);
			}
			
		
			$em->persist($article);
			$em->flush();
			return $this->handleView
				($this->view(['message' => 'Article enregistré'], Response::HTTP_CREATED));}
			
			
		
		
		/**
		 *  creation article
		 * @param Request $request
		 * @return \FOS\RestBundle\View\View|Response
		 * @Route("/api/article/{article}", name="article_edit", methods={"PUT"})
		 */
		public function edit(Request $request, Article $article, ArticleRepository $articleRepository): Response
		{
			// erreur
			$art = $this->getDoctrine()->getRepository
			(Article::class)->find($article);
			$parameter = json_decode($request->getContent(),true);
			$art->setTitre($parameter['contexte']);
			$art->setDescription($parameter['description']);
			$art->setPrixInitial($parameter['prixInitial']);
			$art->setCategorie($parameter['categorie']);
			$em = $this->getDoctrine()->getManager();
			$em->persist($art);
			$em->flush();
			return $this->handleView($this->view(['message'=> 'article Modifie' ], Response::HTTP_CREATED));
			
		}
		
		/** suppression article
		 * @param Request $request
		 * @Rest\Delete("/api/article/{article}" name=""supp_article)
		 * @return \FOS\RestBundle\View\View|Response
		 */
		public function delete(Request $request,$article) :Response
		{
			$art = $this->getDoctrine()->getRepository
			(Article::class)->find($article);
			$em = $this->getDoctrine()->getManager();
			$em->remove($art);
			$em->flush();
			return $this->json('Article supprimé');
		}
	
	
	/** liste des article selon categorie
	 * @Rest\Get("/articleCategorie/{categorie}", name="liste_article_cat")
	 * @return \FOS\RestBundle\View\View|Response
	 */
	public function articleCategorie($categorie)
	{   $categorie= $this->getDoctrine()->getRepository
	(Categorie::class)->find($categorie);
		$list=$categorie->getArticles();
		
		return $this->view($list, Response::HTTP_OK);
	}
	
	/** liste des enchere selon chaque articles
	 * @Rest\Get("/encherearticle")
	 *@return \FOS\RestBundle\View\View|Response
	 */
	public function enchart()
	{
		$repository = $this->getDoctrine()->getRepository(Article::class);
		$articles = $repository->findAll();
		$dd =$articles->getEnchere();
		return $this->handleView($this->view($dd));
	}
	
	/**
	 * modifier prix article 50
	 * @param Request $request
	 * @return \FOS\RestBundle\View\View|Response)
	 * @Rest\Put("/api/augmenterPrix50/{id}", name="augmenter50")
	 * @return \FOS\RestBundle\View\View
	 */
	public function augmenterPrix50(Request $request, Article $id, ArticleRepository $articleRepository): Response
	{   $user = $this->getUser();
		$art = $this->getDoctrine()->getRepository
	(Article::class)->find($id);
		$parameter = json_decode($request->getContent(),true);
		$prix= $art->getPriXVente();
		$nvPrix= $prix + 50 ;
	    $art->setPrixVente($nvPrix);
	    $em = $this->getDoctrine()->getManager();
	    $em->flush(); 
	return $this->handleView($this->view(['message'=> 'prix Modifie' ], Response::HTTP_CREATED));
		                                                
	}
	
	
	/**
	 * modifier prix article100
	 * @param Request $request
	 * @return \FOS\RestBundle\View\View|Response)
	 * @Rest\Put("/api/augmenterPrix100/{id}", name="augmenter100")
	 * @return \FOS\RestBundle\View\View
	 */
	public function augmenterPrix100(Request $request, Article $id, ArticleRepository $articleRepository): Response
	{   $user = $this->getUser();
		$art = $this->getDoctrine()->getRepository
		(Article::class)->find($id);
		$parameter = json_decode($request->getContent(),true);
		$prix= $art->getPriXVente();
		$nvPrix= $prix + 100 ;
		$art->setPrixVente($nvPrix);
		$em = $this->getDoctrine()->getManager();
		$em->flush();
		return $this->handleView($this->view(['message'=> 'prix Modifie' ], Response::HTTP_CREATED));
		
	}
	
	/**
	 * modifier prix article200
	 * @param Request $request
	 * @return \FOS\RestBundle\View\View|Response)
	 * @Rest\Put("/api/augmenterPrix200/{id}", name="augmenter200")
	 * @return \FOS\RestBundle\View\View
	 */
	public function augmenterPrix200(Request $request, Article $id, ArticleRepository $articleRepository): Response
	{   $user = $this->getUser();
		$art = $this->getDoctrine()->getRepository
		(Article::class)->find($id);
		$parameter = json_decode($request->getContent(),true);
		$prix= $art->getPriXVente();
		$nvPrix= $prix + 200 ;
		$art->setPrixVente($nvPrix);
		$em = $this->getDoctrine()->getManager();
		$em->flush();
		return $this->handleView($this->view(['message'=> 'prix Modifie' ], Response::HTTP_CREATED));
		
	}
	
	/**
	 * modifier prix article500
	 * @param Request $request
	 * @return \FOS\RestBundle\View\View|Response)
	 * @Rest\Put("/api/augmenterPrix500/{id}", name="augmenter500")
	 * @return \FOS\RestBundle\View\View
	 */
	public function augmenterPrix500(Request $request, Article $id, ArticleRepository $articleRepository): Response
	{   $user = $this->getUser();
		$art = $this->getDoctrine()->getRepository
		(Article::class)->find($id);
		$parameter = json_decode($request->getContent(),true);
		$prix= $art->getPriXVente();
		$nvPrix= $prix + 500 ;
		$art->setPrixVente($nvPrix);
		$em = $this->getDoctrine()->getManager();
		$em->flush();
		return $this->handleView($this->view(['message'=> 'prix Modifie' ], Response::HTTP_CREATED));
		
	}
}
