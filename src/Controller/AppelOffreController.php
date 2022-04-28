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
	
	
	/** creation appelOffre
	 * @param Request $request
	 * @Rest\Post("/appelOffre/{user}", name="appel_offre_new")
	 * @return \FOS\RestBundle\View\View|Response
	 */
	public function new(Request $request, $user)
	{
		$data = $this->getDoctrine()->getRepository
		(User::class)->find($user);
		$em = $this->getDoctrine()->getManager();
		$contexte = $request->get('contexte');
		$appelOffre= new AppelOffre();
		$appelOffre  ->setContexte($contexte);
		$appelOffre  ->setDateExp(new \ DateTime());
		$appelOffre  -> setUser($data);
		$em->persist($appelOffre);
		$em->flush();
		return $this->handleView
		($this->view(['Appel Offre enregistré' => 'ok'], Response::HTTP_CREATED));
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
    
    
	
	/**
	 * @param Request $request
	 * @Rest\Get("/appelOffres", name="appel_offre_list")
	 * @return Response
	 */
	public function list()
	{
		$repository = $this->getDoctrine()->getRepository(AppelOffre::class);
		$appelOffres = $repository->findAll();
		return $this->handleView($this->view($appelOffres));
	}
	
	/** modification user
	 * @param Request $request
	 * @Rest\Put("/appelOffre/{appelOffre}/{user}")
	 * @return \FOS\RestBundle\View\View|Response
	 */
	public function update(Request $request, $user,$appelOffre):Response
	{
		$dataUser = $this->getDoctrine()->getRepository
		(User::class)->find($user);
		$dataOffre = $this->getDoctrine()->getRepository
		(AppelOffre::class)->find($appelOffre);
		$parameter = json_decode($request->getContent(),true);
		$dataOffre->setContexte($parameter['contexte']);
		$dataOffre->setdateExp($parameter['DateExp']);
		$em = $this->getDoctrine()->getManager();
		$em->persist($dataOffre);
		$em->flush();
		return $this->handleView($this->view(['Appel offre Modifie' => 'ok'], Response::HTTP_CREATED));
	}
	
	/**
     * @param Request $request
     * @Rest\Delete("/appelOffre/{appelOffre}/{user}", name="appel_offre_delete")
     * @return \FOS\RestBundle\View\View|Response
     */
    public function delete(Request $request,$appelOffre, $user,AppelOffreRepository $appelOffreRepository): Response
    {
	    $data1 = $this->getDoctrine()->getRepository
	    (User::class)->find($user);
	    $data2 = $this->getDoctrine()->getRepository
	    (AppelOffre::class)->find($appelOffre);
	    $em = $this->getDoctrine()->getManager();
	    $em->remove($data2);
	    $em->flush();
	    return $this->json('vendeur supprimé');
    }
}
