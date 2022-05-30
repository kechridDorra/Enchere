<?php

namespace App\Controller;

use App\Entity\AppelOffre;
use App\Entity\User;
use DateTimeInterface;
use App\Form\AppelOffreType;
use App\Repository\AppelOffreRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
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
class AppelOffreController extends AbstractFOSRestController
{
	/**
    * @var EntityManagerInterface
    */
	private $entityManager;
	
	/**
	 * @ AppelOffreRepository
	 */
	private $appelOffreRepository;
	
	
	
	public function __construct( EntityManagerInterface $entityManager,
	
	                             AppelOffreRepository $appelOffreRepository)
	{
		
		$this->entityManager = $entityManager;

		$this->appelOffreRepository=$appelOffreRepository;
	}
	
	
	/**
	 * @param Request $request
	 * @Rest\Get("/appelOffre/{appelOffre}", name="appel_offre_show")
	 * @return \FOS\RestBundle\View\View
	 */
	public function show(AppelOffre $appelOffre)
	{
		$data = $this->getDoctrine()->getRepository
		(AppelOffre::class)->find($appelOffre);
		return $this->view($data, Response::HTTP_OK);
	}
	/** get appels selon user
	 * @param Request $request
	 * @Rest\Get("/appelOffre", name="appel_offre_get")
	 *  @return Response
	 */
	public function getAppel()
	{
		$user= $this->getUser();
		$appelOffres=$user->getAppelOffres();
		$data = $this->getDoctrine()->getRepository(
		AppelOffre::class)->findAll();
		return $this->handleView($this->view($appelOffres));
	}
	
	/**
	 * @param Request $request
	 * @Rest\Get("/appelOffres", name="pappel_offre_list")
	 * @return Response
	 */
	public function list()
	{
		
		$repository = $this->getDoctrine()->getRepository(AppelOffre::class);
		$appelOffres = $repository->findAll();
		return $this->handleView($this->view($appelOffres));
	}
	
	/** creation appelOffre
	 * @param Request $request
	 * @Rest\Post("/appelOffre")
	 * @return \FOS\RestBundle\View\View|Response
	 */
	public function new(Request $request)
	{   $user = $this->getUser();
		$em = $this->getDoctrine()->getManager();
		$contexte = $request->get('contexte');
		$dateExp = $request ->get('dateExp');
		$appelOffre= new AppelOffre();
		$appelOffre  ->setContexte($contexte);
		$appelOffre  ->setDateExp(new \DateTime($dateExp));
		$user->addAppelOffre($appelOffre);
		$em->persist($appelOffre);
		$em->flush();
		return $this->handleView
		($this->view(['message'=>'Appel Offre enregistré'], Response::HTTP_CREATED));
	}
	
	
	/** suppression Appel offre
	 * @param Request $request
	 * @Rest\Delete("/appelOffre/{appelOffre}")
	 * @return \FOS\RestBundle\View\View|Response
	 */
	public function deleteAppelOffre($appelOffre):Response
	{
		$user = $this->getUser();
		$appelOffre = $this->getDoctrine()->getRepository
		(AppelOffre::class)->find($appelOffre);
		$em = $this->getDoctrine()->getManager();
		$em->remove($appelOffre);
		$em->flush();
		return $this->json('Appel Offre supprimé');
	}
	/** modification appel offre
	 * @param Request $request
	 * @Rest\Put("/appelOffre/{appelOffre}")
	 * @return \FOS\RestBundle\View\View|Response
	 */
	public function update(Request $request,$appelOffre):Response
	{
		
		$user = $this->getUser();
		$appelOffre = $this->getDoctrine()->getRepository
		(AppelOffre::class)->find($appelOffre);
		$parameter = json_decode($request->getContent(),true);
		$dateExp = $request ->get('dateExp');
		$appelOffre->setContexte($parameter['contexte']);
		$appelOffre ->setDateExp(new \DateTime($dateExp));
		$em = $this->getDoctrine()->getManager();
		$em->persist($appelOffre);
		$em->flush();
		return $this->handleView($this->view(['message'=> 'appel Offre Modifie' ], Response::HTTP_CREATED));
	}
	
	/** get propositions selon appelOffre
	 * @param Request $request
	 * @Rest\Get("/appelOffreProp/{appelOffre}", name="prop_offre_get")
	 *  @return Response
	 */
	public function getPropositionOffre(AppelOffre $appelOffre)
	{
		$data = $this->getDoctrine()->getRepository
		(AppelOffre::class)->find($appelOffre);
		return $this->view($data, Response::HTTP_OK);
		$propositions = $data->getPropositions();
		return $this->handleView($this->view($propositions));
	}
	
	
	
}
