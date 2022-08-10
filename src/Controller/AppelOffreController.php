<?php

namespace App\Controller;

use App\Entity\AppelOffre;
use App\Entity\User;
use App\Repository\UserRepository;
use DateTimeInterface;
use App\Form\AppelOffreType;
use App\Repository\AppelOffreRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\FileBag;



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
	 * @Rest\Get("/api/mesappelOffres", name="mes_appel_offre")
	 *  @return Response
	 */
	public function AppelOffreByUser()
	{
		$user= $this->getUser();
		$appelOffres=$user->getAppelOffres();
		$data = $this->getDoctrine()->getRepository(
		AppelOffre::class)->findAll();
		return $this->handleView($this->view($appelOffres));
	}
	
	/**
	 * @param Request $request
	 * @Rest\Get("/appelsOffres", name="appel_offre_list")
	 * @return Response
	 */
	public function listOffres()
	{
		$repository = $this->getDoctrine()->getRepository(AppelOffre::class);
		$appelOffres = $repository->findAll();
		return $this->handleView($this->view($appelOffres));
	}
	/**
	 * @param Request $request
	 * @Rest\Get("/api/appelOffres", name="pappel_offre_list")
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
	 * @Rest\Post("/api/appelOffre")
	 * @return FOS\RestBundle\View\View|Response
	 */
	public function new(Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		$user = $this->getUser();
		$titre = $request->get('titre');
		$description = $request->get('description');
		$prix = $request->get('prix');
		$image = $request->files->get('image');
		$appelOffre=new AppelOffre();
		$appelOffre->setTitre($titre);
		$appelOffre->setDescription($description);
		$appelOffre->setPrix($prix);
		$user->addAppelOffre($appelOffre);
			// On génère un nouveau nom de fichier
			$fichier = md5(uniqid()) . '.' . $image->guessExtension();
			// On copie le fichier dans le dossier uploads
			$image->move(
				$this->getParameter('images_directory'),
				$fichier
			);
			// On crée l'image dans la base de données
		
			$appelOffre->setImage($fichier);
		
		
		
		$em->persist($appelOffre);
		$em->flush();
		return $this->handleView
		($this->view(['message' => 'offre enregistré'], Response::HTTP_CREATED));
	}
	
	
	/** suppression Appel offre
	 * @param Request $request
	 * @Rest\Delete("/api/appelOffre/{appelOffre}")
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
	 * @Rest\Put("/api/appelOffre/{appelOffre}")
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
		$propositions = $data->getPropositions();
		return $this->handleView($this->view($propositions));
	}
	
	/** liste des appels offres expiré
	 * @Rest\Get("/api/appelOffresExp", name="appel_offre_expire")
	 * @return Response
	 */
	/*public function Expire (AppelOffreRepository $appelOffreRepository)
	{
		$dateNow = new \DateTime();
		$list =$appelOffreRepository->createQueryBuilder('e')
			->andWhere('e.dateExp < :date')
			->setParameter('date', $dateNow)
			->getQuery()
			->getResult();
		return $this->handleView($this->view($list));
	}
	
	/** liste des appels offres dispo
	 * @Rest\Get("/api/appelOffresDispo", name="appel_offre_disponible")
	 * @return Response
	 */
	/*public function Disponible (AppelOffreRepository $appelOffreRepository)
	{
		$dateNow = new \DateTime();
		$list =$appelOffreRepository->createQueryBuilder('e')
			->andWhere('e.dateExp >= :date')
			->setParameter('date', $dateNow)
			->getQuery()
			->getResult();
		return $this->handleView($this->view($list));
	}
	*/
	
}
