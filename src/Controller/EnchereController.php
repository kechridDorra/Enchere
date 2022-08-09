<?php
namespace App\Controller;
use App\Entity\Categorie;
use App\Entity\Enchere;
use App\Entity\ProfilVendeur;
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
ini_set('memory_limit', '-1');
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
	 * @return Response
	 */
	public function getEnchereById(Enchere $id)
	{
		$data = $this->getDoctrine()->getRepository
		(Enchere::class)->find($id);
		return $this->handleView($this->view($data));
	}
	
	/** get appels selon le  vendeur
	 * @param Request $request
	 * @Rest\Get("/api/enchereByVendeur", name="enchere_vendeur")
	 * @return Response
	 */
	public function getEncherebyVendeur()
	{
		$profilVendeur = $this->getUser()->getProfilVendeur();
		$encheres = $profilVendeur->getEncheres();
		$data = $this->getDoctrine()->getRepository(
			Enchere::class)->findAll();
		return $this->handleView($this->view($encheres));
	}
	
	
	/** creation article
	 * @param Request $request
	 * @Rest\Post("/api/enchere/{profilVendeur}")
	 * @return \FOS\RestBundle\View\View|Response

	 */
	public function new(Request $request,ProfilVendeur $profilVendeur)
	{
		$profilVendeur= $this->getDoctrine()->getRepository
		(ProfilVendeur::class)->find($profilVendeur);
		$em = $this->getDoctrine()->getManager();
		$description_ench = $request->request->get('description_ench');
		$date_debut = $request->request->get('date_debut');
		$date_fin = $request->request->get('date_fin');
		$prix_depart = $request->request->get('prix_depart');
		$enchere = new Enchere();
		$enchere->setDescriptionEnch($description_ench);
		$enchere->setDateDebut(new \DateTime($date_debut));
		$enchere->setDateFin(new \DateTime($date_fin));
		$enchere->setPrixDepart($prix_depart);
		$enchere->setPrixVente($prix_depart);
		$enchere->setProfilVendeur($profilVendeur);
		$em->persist($enchere);
		$em->flush();
		return $this->handleView
		($this->view($enchere, Response::HTTP_CREATED));
	}
	
	/** modification appel offre
	 * @param Request $request
	 * @Rest\Patch("/api/enchere/{enchere}")
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
	
		$enchere->setNom($parameter['nom']);
		$enchere->setDateDebut(new \DateTime($dateDebut));
		$enchere->setDateFin(new \DateTime($dateFin));
	
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
	public function deleteEnchere( Enchere $enchere): Response
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
	 * @Rest\Get("/api/listeRejoints/{enchere}", name="liste_rejoint")
	 * @return Response
	 */
	public function listUserByEnchere($enchere)
	{
		$enchere = $this->getDoctrine()->getRepository
	(Enchere::class)->find($enchere);
		$list = $enchere->getUsers();
		return $this->handleView($this->view($list));
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
	 * @Rest\Get("/api/encheresTerminees", name="liste_enchere_termine")
	 * @return Response
	 */
	public function termine (EnchereRepository $enchereRepository)
	{
		$dateNow = new \DateTime();
		$list =$enchereRepository->createQueryBuilder('e')
			->andWhere('e.date_fin <= :date')
			->setParameter('date', $dateNow)
			->getQuery()
			->getResult();
		return $this->handleView($this->view($list));
	}
	/** liste des encheres terminee
	 * @Rest\Get("/api/encheresPlanifiees", name="liste_enchere_planifie")
	 * @return Response
	 */
	public function planifie (EnchereRepository $enchereRepository)
	{
		$dateNow = new \DateTime();
		$list =$enchereRepository->createQueryBuilder('e')
			->andWhere('e.date_debut > :date')
			->setParameter('date', $dateNow)
			->getQuery()
			->getResult();
		return $this->handleView($this->view($list));
	}
	
	/** liste des encheres enCours
	 * @Rest\Get("/api/encheresEnCours", name="liste_enchere_enCours")
	 * @return Response
	 */
	public function enCours (EnchereRepository $enchereRepository)
	{
		$dateNow = new \DateTime();
		$list =$enchereRepository->createQueryBuilder('e')
			->Where(' e.date_debut <= :date')
			->setParameter('date', $dateNow)
			->andWhere(' e.date_fin >= :date')
			->setParameter('date', $dateNow)
			->getQuery()
			->getResult();
		
		return $this->handleView($this->view($list));}
	
	
	
	/** liste des participants
	 * @Rest\Get("/api/listeParticipants/{enchere}", name="liste_participants_user")
	 * @return \FOS\RestBundle\View\View
	 */
	public function Participants($enchere)
	{
		$enchere = $this->getDoctrine()->getRepository
		(Enchere::class)->find($enchere);
		$participants = $enchere->getParticipations();
		return $this->view($participants, Response::HTTP_OK);
	}
	
	
	/** liste des encheres terminee
	 * @Rest\Get("/encheresT")
	 * @return Response
	*/
	public function enchereT(EnchereRepository $enchereRepository)
	{
		$dateNow = new \DateTime();
		$list =$enchereRepository->createQueryBuilder('e')
			->andWhere('e.date_fin <= :date')
			->setParameter('date', $dateNow)
			->getQuery()
			->getResult();
		return $this->handleView($this->view($list));
	}
	
	
	
}
