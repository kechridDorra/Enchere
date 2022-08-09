<?php

namespace App\Controller;

use App\Entity\ProfilVendeur;
use App\Entity\User;
use App\Form\ProfilVendeurType;
use App\Repository\ProfilVendeurRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyInfo\PropertyInfoCacheExtractor;
use Symfony\Component\Routing\Annotation\Route;

use FOS\RestBundle\Controller\Annotations as Rest;
/**
 * @Route("/api")
 */
class ProfilVendeurController extends AbstractFOSRestController
{
	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;
	
	/**
	 * @profilVendeurRepository
	 */
	private $profilVendeurRepository;
	
	
	public function __construct(EntityManagerInterface $entityManager,
	
	                            ProfilVendeurRepository $profilVendeurRepository)
	{
		
		$this->entityManager = $entityManager;
		
		$this->profilVendeurRepository = $profilVendeurRepository;
	}
	
	/**
	 * @param Request $request
	 * @Rest\Get("/profilVendeur/{profilVendeur}", name="profil_vendeur_show")
	 * @return \FOS\RestBundle\View\View
	 */
	public function show(ProfilVendeur $profilVendeur)
	{
		$data = $this->getDoctrine()->getRepository
		(ProfilVendeur::class)->find($profilVendeur);
		return $this->view($data, Response::HTTP_OK);
	}
	/** get vendeur apres auth
	 * @param Request $request
	 * @Rest\Get("/profilVendeur", name="profil_vendeur_get")
	 * @return \FOS\RestBundle\View\View
	 */
	public function getVendeur()
	{
		
		$user= $this->getUser();
		$profilVendeur=$this->getUser()->getProfilVendeur();
		$data = $this->getDoctrine()->getRepository
		(ProfilVendeur::class)->find($profilVendeur);
		return $this->view($data, Response::HTTP_OK);
	}
	
	/**
	 * @param Request $request
	 * @Rest\Get("/profilVendeurs", name="profil_vendeur_list")
	 * @return Response
	 */
	public function list()
	{
		
		$repository = $this->getDoctrine()->getRepository(ProfilVendeur::class);
		$profilVendeurs = $repository->findAll();
		return $this->handleView($this->view($profilVendeurs));
	}
	
	
	
	
	/** creation profil vendeur
	 * @param Request $request
	 * @Rest\Post("/profilVendeur", name="appel_offre_new")
	 * @return \FOS\RestBundle\View\View|Response
	 */
	public function newVendeur(Request $request)
	{
		$user = $this->getUser();
		$em = $this->getDoctrine()->getManager();
		$activite = $request->get('activite');
		$nomEntreprise = $request->get('nomEntreprise');
		$profilVendeur = new ProfilVendeur();
		$profilVendeur->setActivite($activite);
		$profilVendeur->setNomEntreprise($nomEntreprise);
		$user->setProfilVendeur($profilVendeur);
		$em->persist($profilVendeur);
		$em->flush();
		return $this->handleView
		($this->view(['message' => 'Vendeur bien enregistré'], Response::HTTP_CREATED));
	}
	
	/** modification vendeur
	 * @param Request $request
	 * @Rest\Put("/profilVendeur")
	 * @return \FOS\RestBundle\View\View|Response
	 */
	public function update(Request $request):Response
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
	public function deleteVendeur($profilVendeur):Response
	{
		$user = $this->getUser();
		$profilVendeur = $this->getDoctrine()->getRepository
		(ProfilVendeur::class)->find($profilVendeur);
		$em = $this->getDoctrine()->getManager();
		$em->remove($profilVendeur);
		$em->flush();
		return $this->json('Vendeur supprimé');
	}
	
	/** liste des user selon chaque enchere
	 * @Rest\Get("/mesEncheres", name="mes_enchere")
	 * @return Response
	 */
	public function enchereVendeur( ):Response
	{
		$user= $this->getUser();
		$profilVendeur=$user->getProfilVendeur();
		$mesEncheres=$profilVendeur->getEncheres();
		return $this->handleView
		($this->view($mesEncheres, Response::HTTP_CREATED));
	}
	
}
