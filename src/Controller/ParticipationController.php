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
	 * @Rest\Post("/api/rejoindre/{user}/{enchere}")
	 * @return \FOS\RestBundle\View\View|Response
	 * @return Response
	 */
	public function addParticipation($user,$enchere): Response
	{
		$user = $this->getDoctrine()->getRepository
		(User::class)->find($user);
		$enchere = $this->getDoctrine()->getRepository
		(Enchere::class)->find($enchere);
		$em = $this->getDoctrine()->getManager();
		$participation = new Participation();
		$participation ->setUser($user);
		$participation -> setEnchere($enchere);
		$em->persist($participation);
		$em->flush();
		return $this->handleView
		($this->view($enchere ,Response::HTTP_CREATED));
		
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
		$enchere =$part->getEnchere();
		$prix= $request->request->get('augmentation');
		$part->setAugmentation($prix);
		$prixVente=$enchere->getPrixVente();
		$enchere->setPrixVente($prixVente + $prix);
		$em = $this->getDoctrine()->getManager();
		$em->flush();
		return $this->handleView($this->view(['message'=> 'prix Modifie' ], Response::HTTP_CREATED));
	}
	
	
	
}
