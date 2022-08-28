<?php
	
	namespace App\Controller;
	use App\Entity\Enchere;
	use App\Entity\User;
	use App\Form\UserType;
	use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
	use Symfony\Bridge\Twig\Mime\TemplatedEmail;
	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
	use Symfony\Component\HttpFoundation\JsonResponse;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;
	use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
	use Symfony\Component\Security\Core\User\UserInterface;
	use App\Repository\UserRepository;
	use FOS\RestBundle\Context\Context;
	use Doctrine\ORM\EntityManagerInterface;
	use FOS\RestBundle\Controller\Annotations as Rest;
	use FOS\RestBundle\Controller\AbstractFOSRestController;
	use Symfony\Component\HttpFoundation\RedirectResponse;
	use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
	use Symfony\Component\Security\Core\Security;
	use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
	use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
	use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
	use Symfony\Component\Form\AbstractType;
	use Symfony\Component\Form\FormBuilderInterface;
	use Symfony\Component\OptionsResolver\OptionsResolver;
	use Symfony\Component\Serializer\Exception\NotEncodableValueException;
	use Knp\Component\Pager\PaginatorInterface;
	
	
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
		 * @var UserRepository
		 */
		private $userRepository;
		
		/**
		 * @var UserPasswordEncoderInterface
		 */
		private $passwordEncoder;
		
		/**
		 *
		 * @var Security
		 */
		private $security;
		/**
		 * @var TokenStorageInterface
		 */
		private $decorated;
		
		
		public function __construct(
			TokenStorageInterface $decorated, EntityManagerInterface $entityManager,
			UserPasswordEncoderInterface $passwordEncoder,
			UserRepository $userRepository, Security $security
		)
		{
			
			$this->entityManager = $entityManager;
			$this->passwordEncoder = $passwordEncoder;
			$this->userRepository = $userRepository;
			$this->security = $security;
			$this->decorated = $decorated;
			
		}
		
		/** affichage liste des utilisateurs
		 * @Rest\Get("/users", name="users")
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
		 * @return Response
		 */
		public function getUserbyId($id)
		{
			$data = $this->getDoctrine()->getRepository
			(User::class)->find($id);
			return $this->handleView($this->view($data));
		}
		
		/** affichage utilisateur
		 * @Rest\Get("/api/user/mail/{email}", name="dtail_user")
		 * @return Response
		 */
		public function getUserbyEmail($email,UserRepository $repository)
		{
			
			$data = $repository->findByEmail($email);
			return $this->handleView($this->view($data));
		}
		
		/** creation utilisateur
		 * @param Request $request
		 * @Rest\Post("/inscription")
		 * @return \FOS\RestBundle\View\View|Response
		 */
		public function inscription(Request $request, UserPasswordEncoderInterface $passwordEncoder)
		{
			$em = $this->getDoctrine()->getManager();
			$nom = $request->get('nom');
			$prenom = $request->get('prenom');
			$telephone = $request->get('telephone');
			$adresse = $request->get('adresse');
			$ville = $request->get('ville');
			$codePostal = $request->get('codePostal');
			$email = $request->get('email');
			$password = $request->get('password');
			$numeroCarte = $request->get('numeroCarte');
			$codeSecurite = $request->get('codeSecurite');
			$moisExp = $request->get('moisExp');
			$anneeExp = $request->get('anneeExp');
			if (empty($password) || empty($email)) {
				return $this->view([
					'message' => 'utilisateur deja existe'], Response::HTTP_CONFLICT);
			}
			$user = new User();
			$user->setPassword($passwordEncoder->encodePassword($user, $password));
			$user->setEmail($email);
			$user->setNom($nom);
			$user->setPrenom($prenom);
			$user->setTelephone($telephone);
			$user->setAdresse($adresse);
			$user->setCodePostal($codePostal);
			$user->setVille($ville);
			$user->setNumeroCarte($numeroCarte);
			$user->setCodeSecurite($codeSecurite);
			$user->setMoisExp($moisExp);
			$user->setAnneeExp($anneeExp);
			$user->setRoles(["ROLE_USER"]);
			$em->persist($user);
			$em->flush();
			return $this->handleView
			($this->view(['message' => 'utilisateur Enregistré'], Response::HTTP_CREATED));
			
			
		}
		
		/** modification user
		 * @param Request $request
		 * @Rest\Patch("/api/user/{id}")
		 * @return \FOS\RestBundle\View\View|Response
		 */
		public function update(Request $request, $id): Response
		{
			$data = $this->getDoctrine()->getRepository
			(User::class)->find($id);
			$parameter = json_decode($request->getContent(), true);
			$data->setNom($parameter['nom']);
			$data->setPrenom($parameter['prenom']);
			$data->setTelephone($parameter['telephone']);
			$data->setAdresse($parameter['adresse']);
			$data->setVille($parameter['ville']);
			$data->setCodePostal($parameter['codePostal']);
			$data->setEmail($parameter['email']);
			$data->setPassword($parameter['password']);
			$data->setNumeroCarte($parameter['numeroCarte']);
			$data->setCodeSecurite($parameter['codeSecurite']);
			$data->setMoisExp($parameter['moisExp']);
			$data->setAnneeExp($parameter['anneeExp']);
			$em = $this->getDoctrine()->getManager();
			$em->persist($data);
			$em->flush();
			return $this->handleView($this->view(['message' => 'utilisateur modifié'], Response::HTTP_CREATED));
		}
		
		/** suppression utilisateur
		 * @param Request $request
		 * @Rest\Delete("/api/user/{id}")
		 * @return \FOS\RestBundle\View\View|Response
		 */
		public function deleteUser($id): Response
		{
			$data = $this->getDoctrine()->getRepository
			(User::class)->find($id);
			$em = $this->getDoctrine()->getManager();
			$em->remove($data);
			$em->flush();
			return $this->json('Utilisateur supprimé');
		}
		
		/**
		 * @param UserInterface $user
		 * @param JWTTokenManagerInterface $JWTManager
		 * @return JsonResponse
		 */
		public function getTokenUser(UserInterface $user, JWTTokenManagerInterface $JWTManager)
		{
			return new JsonResponse(['token' => $JWTManager->create($user)]);
		}
		
		/**
		 *
		 * @Rest\Get("/api/logout")
		 */
		public function logout(): void
		{
			// controller can be blank: it will never be executed!
			throw new \Exception('Don\'t forget to activate logout in security.yaml');
		}
		
		/**
		 *
		 * @Rest\Get("/api/getUser")
		 */
		public function __invoke()
		{
			// returns User object or null if not authenticated
			$user = $this->security->getUser();
			return $user;
		}
		
		
		
		
		
		
	}