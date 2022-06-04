<?php

namespace App\Controller;

use App\Entity\Enchere;
use App\Repository\EnchereRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * @Route("/api")
 */
class EnchereController extends AbstractFOSRestController
{/**
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
		$this->enchereRepository=$enchereRepository;
	}
	
	/**
	 * @param Request $request
	 * @Rest\Get("/encheres", name="enchere_list")
	 * @return Response
	 */
	public function list()
	{
		$repository = $this->getDoctrine()->getRepository(Enchere::class);
		$encheres = $repository->findAll();
		return $this->handleView($this->view($encheres));
	}
	
	/**
	 * @param Request $request
	 * @Rest\Get("/enchere/{enchere}", name="enchere_show")
	 * @return \FOS\RestBundle\View\View
	 */
	public function show(Enchere $enchere)
	{
		$data = $this->getDoctrine()->getRepository
		(Enchere::class)->find($enchere);
		return $this->view($data, Response::HTTP_OK);
	}
	/** get appels selon le  vendeur
	 * @param Request $request
	 * @Rest\Get("/enchere", name="enchere_get")
	 *  @return Response
	 */
	public function getEnchere()
	{
		$profilVendeur=$this->getUser()->getProfilVendeur();
		$encheres=$profilVendeur->getEncheres();
		$data = $this->getDoctrine()->getRepository(
			Enchere::class)->findAll();
		return $this->handleView($this->view($encheres));
	}
	
	
	/** creation article
	 * @param Request $request
	 * @Rest\Post("/enchere")
	 * @return \FOS\RestBundle\View\View|Response
	 */
	public function new(Request $request)
	{
		$profilVendeur=$this->getUser()->getProfilVendeur();
		$em = $this->getDoctrine()->getManager();
		$description = $request->get('descriptionEnch');
		$dateDebut = $request->get('dateDebut');
		$dateFin = $request->get('dateFin');
		$enchere = new Enchere();
		$enchere ->setDescriptionEnch($description);
		$enchere ->setDateDebut(new \DateTime($dateDebut));
		$enchere ->setDateFin(new \DateTime($dateFin));
		$enchere ->setProfilVendeur($profilVendeur);
		$em->persist($enchere);
		$em->flush();
		return $this->handleView
		($this->view(['message'=>'Enchere enregistré'], Response::HTTP_CREATED));
		
	}
	
	/** modification appel offre
	 * @param Request $request
	 * @Rest\Put("/enchere/{enchere}")
	 * @return \FOS\RestBundle\View\View|Response
	 */
	public function update(Request $request,$enchere):Response
	{
		
		$profilVendeur=$this->getUser()->getProfilVendeur();
		$enchere = $this->getDoctrine()->getRepository
		(Enchere::class)->find($enchere);
		$parameter = json_decode($request->getContent(),true);
		$dateDebut = $request ->get('dateDebut');
		$dateFin = $request ->get('dateFin');
		$enchere->setDescriptionEnch($parameter['descriptionEnch']);
		$enchere ->setDateDebut(new \DateTime($dateDebut));
		$enchere ->setDateFin(new \DateTime($dateFin));
		$em = $this->getDoctrine()->getManager();
		$em->persist($enchere);
		$em->flush();
		return $this->handleView($this->view(['message'=> 'enchere Modifie' ], Response::HTTP_CREATED));
	}
	
	
	/** suppression enchere
	 * @param Request $request
	 * @Rest\Delete("/enchere/{enchere}")
	 * @return \FOS\RestBundle\View\View|Response
	 */
	public function deleteEnchere($enchere):Response
	{
		$profilVendeur=$this->getUser()->getProfilVendeur();
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
		$enchere= $this->getDoctrine()->getRepository
		(Enchere::class)->find($enchere);
		$list= $enchere ->getUsers();
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
}
