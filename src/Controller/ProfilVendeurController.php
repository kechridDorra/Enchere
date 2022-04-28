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
	
	
	/** creation profil vendeur
	 * @param Request $request
	 * @Rest\Post("/profilVendeur/{user}", name="appel_offre_new")
	 * @return \FOS\RestBundle\View\View|Response
	 */
	public function new(Request $request, $user)
	{
		$data = $this->getDoctrine()->getRepository
		(User::class)->find($user);
		$em = $this->getDoctrine()->getManager();
		$activite = $request->get('activite');
		$nomEntreprise = $request->get('nomEntreprise');
		$profilVendeur = new ProfilVendeur();
		$profilVendeur->setActivite($activite);
		$profilVendeur->setNomEntreprise($nomEntreprise);
		$data->setProfilVendeur($profilVendeur);
		$em->persist($profilVendeur);
		$em->flush();
		return $this->handleView
		($this->view(['Profil Vendeur bien enregistré' => 'ok'], Response::HTTP_CREATED));
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
	
	/** modification vendeur
	 * @param Request $request
	 * @Rest\Put("/profilVendeur/{profilVendeur}/{user}")
	 * @return \FOS\RestBundle\View\View|Response
	 */
	public function update(Request $request, $user,$profilVendeur):Response
	{
		$dataUser = $this->getDoctrine()->getRepository
		(User::class)->find($user);
		$dataVendeur = $this->getDoctrine()->getRepository
		(ProfilVendeur::class)->find($profilVendeur);
		$parameter = json_decode($request->getContent(),true);
		$dataVendeur->setActivite($parameter['activite']);
		$dataVendeur->setNomEntreprise($parameter['nomEntreprise']);
		$em = $this->getDoctrine()->getManager();
		$em->persist($dataVendeur);
		$em->flush();
		return $this->handleView($this->view(['Vendeur Modifie' => 'ok'], Response::HTTP_CREATED));
	}
	
	
}
