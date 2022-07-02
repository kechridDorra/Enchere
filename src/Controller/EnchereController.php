<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Enchere;
use App\Repository\EnchereRepository;
use ContainerDKhXcz3\PaginatorInterface_82dac15;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Knp\Component\Pager\PaginatorInterface;
use phpDocumentor\Reflection\Types\Array_;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\EnchereType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints\Date;

class EnchereController extends AbstractFOSRestController
{
	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;
	
	/**
	 * @var EnchereRepository
	 */
	private $enchereRepository;
	
	
	public function __construct(EntityManagerInterface $entityManager, EnchereRepository $enchereRepository)
	{
		$this->entityManager = $entityManager;
		$this->enchereRepository = $enchereRepository;
	}
	
	/**
	 * @param Request $request
	 * @Rest\Get("/encheres", name="enchere_list")
	 * @return Response
	 */
	public function encheres_list()
	{
		$repository = $this->getDoctrine()->getRepository(Enchere::class);
		$encheres = $repository->findAll();
		return $this->handleView($this->view($encheres));
	}
	
	/**
	 * @param Request $request
	 * @Rest\Get("/enchere/{id}", name="enchere_show")
	 * @return \FOS\RestBundle\View\View
	 */
	public function show(Enchere $id)
	{
		$data = $this->getDoctrine()->getRepository
		(Enchere::class)->find($id);
		return $this->view($data, Response::HTTP_OK);
	}
	
	/** get appels selon le  vendeur
	 * @param Request $request
	 * @Rest\Get("/api/enchere", name="enchere_vendeur")
	 * @return Response
	 */
	public function enchereVendeur()
	{
		$profilVendeur = $this->getUser()->getProfilVendeur();
		$encheres = $profilVendeur->getEncheres();
		$data = $this->getDoctrine()->getRepository(
			Enchere::class)->findAll();
		return $this->handleView($this->view($encheres));
	}
	
	
	/** creation article
	 * @param Request $request
	 * @Rest\Post("/api/enchere")
	 * @return \FOS\RestBundle\View\View|Response
	 */
	public function new(Request $request)
	{
		$profilVendeur = $this->getUser()->getProfilVendeur();
		$em = $this->getDoctrine()->getManager();
		$description = $request->get('descriptionEnch');
		$dateDebut = $request->get('dateDebut');
		$dateFin = $request->get('dateFin');
		$statut = $request->get('statut');
		$enchere = new Enchere();
		$enchere->setDescriptionEnch($description);
		$enchere->setDateDebut(new \DateTime($dateDebut));
		$enchere->setDateFin(new \DateTime($dateFin));
		$enchere->setProfilVendeur($profilVendeur);
		$enchere->setStatut("p");
		$em->persist($enchere);
		$em->flush();
		return $this->handleView
		($this->view(['message' => 'Enchere enregistré'], Response::HTTP_CREATED));
		
	}
	
	/** modification appel offre
	 * @param Request $request
	 * @Rest\Put("/api/enchere/{enchere}")
	 * @return \FOS\RestBundle\View\View|Response
	 */
	public function update(Request $request, $enchere): Response
	{
		
		$profilVendeur = $this->getUser()->getProfilVendeur();
		$enchere = $this->getDoctrine()->getRepository
		(Enchere::class)->find($enchere);
		$parameter = json_decode($request->getContent(), true);
		$dateDebut = $request->get('dateDebut');
		$dateFin = $request->get('dateFin');
		$statut = $request->get('statut');
		$enchere->setDescriptionEnch($parameter['descriptionEnch']);
		$enchere->setDateDebut(new \DateTime($dateDebut));
		$enchere->setDateFin(new \DateTime($dateFin));
		$enchere->setDateFin($statut);
		$em = $this->getDoctrine()->getManager();
		$em->persist($enchere);
		$em->flush();
		return $this->handleView($this->view(['message' => 'enchere Modifie'], Response::HTTP_CREATED));
	}
	
	
	/** suppression enchere
	 * @param Request $request
	 * @Rest\Delete("/api/enchere/{enchere}")
	 * @return \FOS\RestBundle\View\View|Response
	 */
	public function deleteEnchere($enchere): Response
	{
		$profilVendeur = $this->getUser()->getProfilVendeur();
		$enchere = $this->getDoctrine()->getRepository
		(Enchere::class)->find($enchere);
		$em = $this->getDoctrine()->getManager();
		$em->remove($enchere);
		$em->flush();
		return $this->json('Enchere supprimé');
	}
	
	/** liste des user selon chaque enchere
	 * @Rest\Get("/listRejoint/{enchere}", name="liste_rejoint")
	 * @return \FOS\RestBundle\View\View
	 */
	public function listUser($enchere)
	{
		$enchere = $this->getDoctrine()->getRepository
	(Enchere::class)->find($enchere);
		$list = $enchere->getUsers();
		return $this->view($list, Response::HTTP_OK);
	}
	
	/** liste des enchere selon chaque user
	 * @Rest\Get("/enchereRejoint", name="liste_encher_user")
	 * @return \FOS\RestBundle\View\View
	 */
	public function listEnchere()
	{
		$user = $this->getUser();
		$list = $user->getEncheres();
		return $this->view($list, Response::HTTP_OK);
	}
	
	
	/** liste des encheres terminee
	 * @Rest\Get("/encheresTerminees", name="liste_enchere_termine")
	 * @return \FOS\RestBundle\View\View
	 */
	public function termine (EnchereRepository $enchereRepository)
	{
		$dateNow = new \DateTime();
		$list =$enchereRepository->createQueryBuilder('e')
			->andWhere('e.dateFin <= :date')
			->setParameter('date', $dateNow)
			->getQuery()
			->getResult();
		return $this->view($list,Response::HTTP_OK);
	}
	/** liste des encheres terminee
	 * @Rest\Get("/encheresPlanifiees", name="liste_enchere_planifie")
	 * @return \FOS\RestBundle\View\View
	 */
	public function planifie (EnchereRepository $enchereRepository)
	{
		$dateNow = new \DateTime();
		$list =$enchereRepository->createQueryBuilder('e')
			->andWhere('e.dateDebut > :date')
			->setParameter('date', $dateNow)
			->getQuery()
			->getResult();
		return $this->view($list,Response::HTTP_OK);
	}
	
	/** liste des encheres enCours
	 * @Rest\Get("/encheresEnCours", name="liste_enchere_enCours")
	 * @return \FOS\RestBundle\View\View
	 */
	public function enCours (EnchereRepository $enchereRepository)
	{
		$dateNow = new \DateTime();
		$list =$enchereRepository->createQueryBuilder('e')
			->Where(' e.dateDebut <= :date')
			->setParameter('date', $dateNow)
			->andWhere(' e.dateFin >= :date')
			->setParameter('date', $dateNow)
			->getQuery()
			->getResult();
		
		return $this->view($list,Response::HTTP_OK);
	}
	
	
	
	
}