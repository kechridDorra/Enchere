<?php

namespace App\Controller;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
/**
 * Class UseController
 *
 */
class UserController extends AbstractFOSRestController
{
	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;
	
	/**
	 * @ UserRepository
	 */
	private $userRepository;
	
	/**
	 * @var UserPasswordEncoderInterface
	 */
	private $passwordEncoder;
	
	public function __construct( EntityManagerInterface $entityManager,
	                             UserPasswordEncoderInterface $passwordEncoder,
	                             UserRepository $userRepository)
	{
		
		$this->entityManager = $entityManager;
		$this->passwordEncoder = $passwordEncoder;
		$this->userRepository=$userRepository;
	}
	/** affichage liste des utilisateurs
	 * @Rest\Get("/api/users", name="users")
	 * @return Response
	 */
	public function getUsers()
	{
		$repository = $this->getDoctrine()->getRepository(User::class);
		$users = $repository->findAll();
		return $this->handleView($this->view($users));
	}
	
	/** affichage utilisateur
	 * @Rest\Get("/api/user/{id}", name="profil_user")
	 * @return \FOS\RestBundle\View\View
	 */
	public function getUserAction($id)
	{
		$data = $this->getDoctrine()->getRepository
		(User::class)->find($id);
		return $this->view($data, Response::HTTP_OK);
	}
	/** creation utilisateur
	 * @param Request $request
	 * @Rest\Post("/inscription")
	 * @return \FOS\RestBundle\View\View|Response
	 */
	public function new(Request $request, UserPasswordEncoderInterface $passwordEncoder)
	{   $em = $this->getDoctrine()->getManager();
		$nom = $request->get('nom');
		$prenom = $request->get('prenom');
		$telephone = $request->get('telephone');
		$adresse = $request->get('adresse');
		$ville= $request->get('ville');
		$codePostal = $request->get('codePostal');
		$email = $request->get('email');
		$password = $request->get('password');
		$genre = $request->get('genre');
		$numeroCarte=$request->get('numeroCarte');
		$codeSecurite = $request->get('codeSecurite');
		$moisExp = $request->get('moisExp');
		$anneeExp = $request->get('anneeExp');
		if ( empty($password) || empty($email)){
			return $this->view([
				'message' => 'utilisateur deja existe'], Response::HTTP_CONFLICT);
		}
		$user = new User();
		$user  ->setPassword($passwordEncoder->encodePassword($user  ,$password));
		$user  ->setEmail($email);
		$user  ->setNom($nom);
		$user  ->setPrenom($prenom);
		$user  ->setTelephone($telephone);
		$user  ->setAdresse($adresse);
		$user  ->setCodePostal($codePostal);
		$user  ->setVille($ville);
		$user  ->setGenre($genre);
		$user  ->setMoisExp($moisExp);
		$user  ->setAnneeExp($anneeExp);
		$user  ->setNumeroCarte($numeroCarte);
		$user  ->setCodeSecurite($codeSecurite);
		$user  ->setRoles(["ROLE_USER"]);
		$em->persist($user  );
		$em->flush();
		return $this->handleView
		($this->view(['utilisateur Enregistré' => 'ok'], Response::HTTP_CREATED));
		
		
	}
	
	/** modification user
	 * @param Request $request
	 * @Rest\Put("/api/user/{id}")
	 * @return \FOS\RestBundle\View\View|Response
	 */
	public function update(Request $request, $id):Response
	{
		$data = $this->getDoctrine()->getRepository
		(User::class)->find($id);
		$parameter = json_decode($request->getContent(),true);
		$data->setNom($parameter['nom']);
		$data->setPrenom($parameter['prenom']);
		$data->setTelephone($parameter['telephone']);
		$data->setGenre($parameter['genre']);
		$data->setAdresse($parameter['adresse']);
		$data->setVille($parameter['ville']);
		$data->setCodePostal($parameter['codePostal']);
		$data->setEmail($parameter['email']);
		$data->setPassword($parameter['password']);
		$data->setTypeCarte($parameter['typeCarte']);
		$data->setNumeroCarte($parameter['numeroCarte']);
		$data->setCodeSecurite($parameter['codeSecurite']);
		$em = $this->getDoctrine()->getManager();
		$em->persist($data);
		$em->flush();
		return $this->handleView($this->view(['Modifie' => 'ok'], Response::HTTP_CREATED));
	}
	
	
	
	/** suppression utilisateur
	 * @param Request $request
	 * @Rest\Delete("/api/user/{id}")
	 * @return \FOS\RestBundle\View\View|Response
	 */
	public function deleteUser($id):Response
	{
		$data = $this->getDoctrine()->getRepository
		(User::class)->find($id);
		$em = $this->getDoctrine()->getManager();
		$em->remove($data);
		$em->flush();
		return $this->json('Utilisateur supprimé');
	}
	
	

	
	
	
}
