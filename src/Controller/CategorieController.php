<?php
	
	namespace App\Controller;
	
	use App\Entity\Categorie;
	
	use App\Repository\CategorieRepository;
	
	use Doctrine\ORM\EntityManagerInterface;
	use FOS\RestBundle\Controller\AbstractFOSRestController;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;
	use FOS\RestBundle\Controller\Annotations as Rest;
	
	/**
	 * @Route("/api")
	 */
	class CategorieController extends AbstractFOSRestController
	{
		/**
		 * @var EntityManagerInterface
		 */
		private $entityManager;
		
		/**
		 * @categorieRepository
		 */
		private $categorieRepository;
		
		
		public function __construct(EntityManagerInterface $entityManager,
		
		                            CategorieRepository $categorieRepository)
		{
			
			$this->entityManager = $entityManager;
			
			$this->profilVendeurRepository = $categorieRepository;
		}
		
		/**
		 * @param Request $request
		 * @Rest\Get("/categorie/{categorie}", name="categorie_show")
		 * @return \FOS\RestBundle\View\View
		 */
		public function show(Categorie $categorie)
		{
			$data = $this->getDoctrine()->getRepository
			(Categorie::class)->find($categorie);
			return $this->view($data, Response::HTTP_OK);
		}
	
		
		
		/**
		 * @param Request $request
		 * @Rest\Get("/categories", name="categorie_list")
		 * @return Response
		 */
		public function list()
		{
			
			$repository = $this->getDoctrine()->getRepository(Categorie::class);
			$categories = $repository->findAll();
			return $this->handleView($this->view($categories));
		}
		
		
		/** creation profil vendeur
		 * @param Request $request
		 * @Rest\Post("/categorie", name="categorie_new")
		 * @return \FOS\RestBundle\View\View|Response
		 */
		public function new(Request $request)
		{
		
			$em = $this->getDoctrine()->getManager();
			$nom = $request->get('nom');
			$categorie = new Categorie();
			$categorie->setNom($nom);
			$em->persist($categorie);
			$em->flush();
			return $this->handleView
			($this->view(['message' => 'categorie enregistré'], Response::HTTP_CREATED));
		}
		
		
		/** modification vendeur
		 * @param Request $request
		 * @Rest\Put("/profilVendeur")
		 * @return \FOS\RestBundle\View\View|Response
		 */
		/*public function update(Request $request):Response
		{
			$user = $this->getUser();
			$profilVendeur=$this->getUser()->getProfilVendeur();
			$parameter = json_decode($request->getContent(),true);
			$profilVendeur->setActivite($parameter['activite']);
			$profilVendeur->setNomEntreprise($parameter['nomEntreprise']);
			$em = $this->getDoctrine()->getManager();
			$em->persist($profilVendeur);
			$em->flush();
			return $this->handleView($this->view(['message'=> 'Vendeur Modifie' ], Response::HTTP_CREATED));
		}
		
		/** suppression Vendeur
		 * @param Request $request
		 * @Rest\Delete("/profilVendeur/{profilVendeur}")
		 * @return \FOS\RestBundle\View\View|Response
		 */
	/*	public function deleteVendeur($profilVendeur):Response
		{
			$user = $this->getUser();
			$profilVendeur = $this->getDoctrine()->getRepository
			(ProfilVendeur::class)->find($profilVendeur);
			$em = $this->getDoctrine()->getManager();
			$em->remove($profilVendeur);
			$em->flush();
			return $this->json('Vendeur supprimé');
		}*/
	}
