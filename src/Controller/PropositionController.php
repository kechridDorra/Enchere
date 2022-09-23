<?php

namespace App\Controller;

use App\Entity\AppelOffre;
use App\Entity\ProfilVendeur;
use App\Entity\Proposition;
use App\Repository\PropositionRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use App\Entity\User;
use App\Repository\AppelOffreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
 /**
 * @Route("/api")
 */
class PropositionController extends AbstractFOSRestController
{
	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;
	
	/**
	 * @ PropositionRepository
	 */
	private $propositionRepository;
	
	
	
	public function __construct( EntityManagerInterface $entityManager,
	
	                             PropositionRepository $propositionRepository)
	{
		
		$this->entityManager = $entityManager;
		
		$this->propositionRepository=$propositionRepository;
	}
     /** afficher proposition selon id
     * @param Request $request
     * @Rest\Get("/proposition/{proposition}", name="proposition_show")
     * @return \FOS\RestBundle\View\View
      */
	public function show(Proposition $proposition)
	{
		$data = $this->getDoctrine()->getRepository
		(Proposition::class)->find($proposition);
		return $this->view($data, Response::HTTP_OK);
	}
	
	/** get propositions selon le vendeur
	 * @param Request $request
	 * @Rest\Get("/propositionVendeur", name="proposition_vendeur_get")
	 *  @return Response
	 */
	public function getPropositionVendeur()
	{
		$user= $this->getUser();
		$vendeur = $user ->getProfilVendeur();
		$propositions=$vendeur->getPropositions();
		$data = $this->getDoctrine()->getRepository(
			Proposition::class)->findAll();
		return $this->handleView($this->view($propositions));
	}
	
	
	/** list des propositions
	 * @param Request $request
	 * @Rest\Get("/propositions", name="proposition_list")
	 * @return Response
	 */
	public function list()
	{
		
		$repository = $this->getDoctrine()->getRepository(Proposition::class);
		$propositions = $repository->findAll();
		return $this->handleView($this->view($propositions));
	}
	
	/** creation proposition
	 * @param Request $request
	 * @Rest\Post("/proposition/{profilVendeur}/{appelOffre}")
	 * @return \FOS\RestBundle\View\View|Response
	 */
	public function new(Request $request ,$appelOffre,$profilVendeur)
	{  //$user = $this->getUser();
		$vendeur = $this->getDoctrine()->getRepository
		(ProfilVendeur::class)->find($profilVendeur);
		$offre = $this->getDoctrine()->getRepository
		(AppelOffre::class)->find($appelOffre);
		$em = $this->getDoctrine()->getManager();
		$reponse = $request->get('reponse');
		$prix = $request->get('prix');
		$proposition= new Proposition();
		$proposition ->setReponse($reponse);
		$proposition ->setPrix($prix);
		$proposition->setProfilVendeur($vendeur);
		$proposition->setAppelOffre($offre);
		$em->persist($proposition);
		$em->flush();
		return $this->handleView
		($this->view($proposition , Response::HTTP_CREATED));
	}
	
	/** modification proposition
	 * @param Request $request
	 * @Rest\Put("/proposition/{proposition}")
	 * @return \FOS\RestBundle\View\View|Response
	 */
	public function update(Request $request,$proposition):Response
	{
		
		$user = $this->getUser();
		$ProfilVendeur=$user->getProfilVendeur();
		$proposition = $this->getDoctrine()->getRepository
		(Proposition::class)->find($proposition);
		$parameter = json_decode($request->getContent(),true);
		$proposition->setReponse($parameter['reponse']);
		$proposition->setPrix($parameter['prix']);
		$em = $this->getDoctrine()->getManager();
		$em->persist($proposition);
		$em->flush();
		return $this->handleView($this->view(['message'=> 'Proposition Modifie' ], Response::HTTP_CREATED));
	}
	
	/** suppression Proposition
	 * @param Request $request
	 * @Rest\Delete("/proposition/{proposition}")
	 * @return \FOS\RestBundle\View\View|Response
	 */
	public function deleteProposition($proposition):Response
	{
		$user = $this->getUser();
		$profilVendeur = $user->getProfilVendeur();
		$proposition = $this->getDoctrine()->getRepository
		(Proposition::class)->find($proposition);
		$em = $this->getDoctrine()->getManager();
		$em->remove($proposition);
		$em->flush();
		return $this->json('Proposition supprimé');
	}
	
	
}
