<?php

namespace App\Controller;
use App\Entity\Article;
use App\Entity\Images;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;

/**
 * @Route("/api")
 */
class ArticleController extends AbstractFOSRestController
{
	
	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;
	
	
	/**
	 * @ ArticleRepository
	 */
	private $articleRepository;
	
	public function __construct( EntityManagerInterface $entityManager, ArticleRepository $articleRepository)
	{ $this->entityManager = $entityManager;
	  $this->articleRepository = $articleRepository;}
		/**
     * @Route("/", name="app_article_index", methods={"GET"})
     */
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('article/index.html.twig', [
            'articles' => $articleRepository->findAll(),
        ]);
    }
    /**
     * @Rest\Post("/article", name="new_article")
     * @param Request $request
     * @return int|Response
     */
	public function new(Request $request, ArticleRepository $articleRepository)
    {
	    $titre = $request->get('titre');
	    $prixInitial = $request->get('prixInitial');
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
	    if ($form->isSubmitted() && $form->isValid()) {
		    // On récupère les images transmises
		    $images = $form->get('images')->getData();
		
		    // On boucle sur les images
		    foreach($images as $image){
			    // On génère un nouveau nom de fichier
			    $fichier = md5(uniqid()).'.'.$image->guessExtension();
			
			    // On copie le fichier dans le dossier uploads
			    $image->move(
				    $this->getParameter('images_directory'),
				    $fichier
			    );
			
			    // On crée l'image dans la base de données
			    $img = new Images();
			    $img->setNom($fichier);
			    $article->addImage($img);
			    $article  ->setTitre($titre);
			    $article  ->setPrixInitial($prixInitial);
		    }
		
		    $entityManager = $this->getDoctrine()->getManager();
		    $entityManager->persist($article);
		    $entityManager->flush();
		
		    return $this->handleView
		    ($this->view(['message' => 'article Enregistré'], Response::HTTP_CREATED));
	    }
	    
	    return $this->handleView
    ($this->view(['message' => 'article non Enregistré'], Response::HTTP_CREATED));
    
    }

    /**
     * @Route("/{id}", name="app_article_show", methods={"GET"})
     */
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_article_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Article $article, ArticleRepository $articleRepository): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $articleRepository->add($article);
            return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('article/edit.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="app_article_delete", methods={"POST"})
     */
    public function delete(Request $request, Article $article, ArticleRepository $articleRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $articleRepository->remove($article);
        }

        return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
    }
}
