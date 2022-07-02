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
		/**
		 * @param Request $request
		 * @Rest\Get("/categorie1", name="categorie_1")
		 * @return \FOS\RestBundle\View\View
		 */
		public function getCategorie1()
		{
			$categorie = 1;
			$data = $this->getDoctrine()->getRepository
			(Categorie::class)->find($categorie);
			return $this->view($data, Response::HTTP_OK);
		}
		/**
		 * @param Request $request
		 * @Rest\Get("/categorie2", name="categorie_2")
		 * @return \FOS\RestBundle\View\View
		 */
		public function getCategorie2()
		{
			$categorie = 2;
			$data = $this->getDoctrine()->getRepository
			(Categorie::class)->find($categorie);
			return $this->view($data, Response::HTTP_OK);
		}
		/**
		 * @param Request $request
		 * @Rest\Get("/categorie3", name="categorie_3")
		 * @return \FOS\RestBundle\View\View
		 */
		public function getCategorie3()
		{
			$categorie = 3;
			$data = $this->getDoctrine()->getRepository
			(Categorie::class)->find($categorie);
			return $this->view($data, Response::HTTP_OK);
		}
		/**
		 * @param Request $request
		 * @Rest\Get("/categorie4", name="categorie_4")
		 * @return \FOS\RestBundle\View\View
		 */
		public function getCategorie4()
		{
			$categorie = 4;
			$data = $this->getDoctrine()->getRepository
			(Categorie::class)->find($categorie);
			return $this->view($data, Response::HTTP_OK);
		}
		/**
		 * @param Request $request
		 * @Rest\Get("/categorie5", name="categorie_5")
		 * @return \FOS\RestBundle\View\View
		 */
		public function getCategorie5()
		{
			$categorie = 5;
			$data = $this->getDoctrine()->getRepository
			(Categorie::class)->find($categorie);
			return $this->view($data, Response::HTTP_OK);
		}/**
	 * @param Request $request
	 * @Rest\Get("/categorie6", name="categorie_6")
	 * @return \FOS\RestBundle\View\View
	 */
		public function getCategorie6()
		{
			$categorie = 6;
			$data = $this->getDoctrine()->getRepository
			(Categorie::class)->find($categorie);
			return $this->view($data, Response::HTTP_OK);
		}
		/**
	 * @param Request $request
	 * @Rest\Get("/categorie7", name="categorie_7")
	 * @return \FOS\RestBundle\View\View
	 */
		public function getCategorie7()
		{
			$categorie = 7;
			$data = $this->getDoctrine()->getRepository
			(Categorie::class)->find($categorie);
			return $this->view($data, Response::HTTP_OK);
		}
		
		/**
		 * @param Request $request
		 * @Rest\Get("/categorie8", name="categorie_8")
		 * @return \FOS\RestBundle\View\View
		 */
		public function getCategorie8()
		{
			$categorie = 8;
			$data = $this->getDoctrine()->getRepository
			(Categorie::class)->find($categorie);
			return $this->view($data, Response::HTTP_OK);
		}
		
		/**
		 * @param Request $request
		 * @Rest\Get("/categorie9", name="categorie_9")
		 * @return \FOS\RestBundle\View\View
		 */
		public function getCategorie9()
		{
			$categorie = 9;
			$data = $this->getDoctrine()->getRepository
			(Categorie::class)->find($categorie);
			return $this->view($data, Response::HTTP_OK);
		}
	}
	
