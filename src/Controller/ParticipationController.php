<?php
	
	namespace App\Controller;
	use App\Entity\Enchere;
	use App\Entity\Participation;
	
	use App\Entity\User;
	use App\Repository\ParticipationRepository;
	use App\Repository\PropositionRepository;
	use Doctrine\ORM\EntityManagerInterface;
	use FOS\RestBundle\Controller\AbstractFOSRestController;
	use Lcobucci\JWT\Signer\Ecdsa;
	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;
	use FOS\RestBundle\Controller\Annotations as Rest;
	
	
	ini_set('memory_limit', '-1');


class ParticipationController extends AbstractFOSRestController
{
	
	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;
	
	/**
	 * @ ParticipationRepository
	 */
	private $participationRepository;
	
	
	
	public function __construct( EntityManagerInterface $entityManager,
	
	                             ParticipationRepository $participationRepository)
	{
		
		$this->entityManager = $entityManager;
		
		$this->participationRepository= $participationRepository;
	}
	
	/** ajouter une participation
	 * @param Request $request
	 * @Rest\Post("/api/rejoindre/{enchere}")
	 * @return \FOS\RestBundle\View\View|Response
	 * @return Response
	 */
	public function addParticipation($enchere): Response
	{
		$user = $this->getUser();
		$enchere = $this->getDoctrine()->getRepository
		(Enchere::class)->find($enchere);
		$em = $this->getDoctrine()->getManager();
		$participation = new Participation();
		$participation ->setUser($user);
		$participation -> setEnchere($enchere);
		$em->persist($participation);
		$em->flush();
		return $this->handleView
		($this->view($participation, Response::HTTP_CREATED));
		
	}
	
	/**
	 * @param Request $request
	 * @Rest\Get("/api/participation/{participation}", name="participation_show")
	 * @return Response
	 */
	public function show(Participation $participation)
	{
		$data = $this->getDoctrine()->getRepository
		(Participation::class)->find($participation);
		return $this->handleView
	($this->view($data, Response::HTTP_CREATED));
	}
	
	/**
	 * @param Request $request
	 * @Rest\Post("/api/augmenter/{participation}", name="augmenter")
	 * @return Response
	 */
	public function Augmentation(Participation $participation ,Request $request)
	{   $user = $this->getUser();
		$part = $this->getDoctrine()->getRepository
		(Participation::class)->find($participation);
		$prix= $request->request->get('augmentation');
		$part->setAugmentation($prix);
		$em = $this->getDoctrine()->getManager();
		$em->flush();
		return $this->handleView($this->view(['message'=> 'prix Modifie' ], Response::HTTP_CREATED));
	}
	
	
	
	
	
}