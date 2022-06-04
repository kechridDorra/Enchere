<?php

namespace App\Controller;
use App\Entity\Article;
use App\Entity\Categorie;
use App\Entity\Enchere;
use App\Entity\Images;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Lcobucci\JWT\Signer\Ecdsa;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
	
	
	
	public function __construct( EntityManagerInterface $entityManager,
	
	                            ArticleRepository $articleRepository)
	{
		
		$this->entityManager = $entityManager;
		
		$this->articleRepository=$articleRepository;
	}
	
	/**
	 * @param Request $request
	 * @Route("/article/{article}", name="article_show", methods={"GET"})
	 * @return \FOS\RestBundle\View\View
	 */
	public function show(Article $article)
	{
		$data = $this->getDoctrine()->getRepository
		(Article::class)->find($article);
		return $this->view($data, Response::HTTP_OK);
	}
	
	/**
	 * @param Request $request
	 * @Route("/articles", name="article_list", methods={"GET"})
	 * @return Response
	 */
	public function list()
	{
		
		$repository = $this->getDoctrine()->getRepository(Article::class);
		$articles = $repository->findAll();
		return $this->handleView($this->view($articles));
	}
    
    /** creation article
     * @param Request $request
     * @Route("/article/{enchere}", name="app_article_new", methods={"POST"})
     * @return \FOS\RestBundle\View\View|Response
     */
    public function new(Request $request,Enchere $enchere)
    {
	    $em = $this->getDoctrine()->getManager();
	    $ench = $this->getDoctrine()->getRepository
	    (Enchere::class)->find($enchere);
	    $titre = $request->get('titre');
	    $description = $request ->get('description');
	    $prixInitial =$request ->get('prixInitial');
	    $categorie = $request->get('categorie');
	    $cat = $this->getDoctrine()->getRepository
	    (Categorie::class)->find($categorie);
	    $images = $request->files;
	    $article= new Article();
	    $article ->setTitre($titre);
	    $article ->setDescription($description);
	    $article->setPrixInitial($prixInitial);
	    $article->setCategorie($cat);
	    $article->setEnchere($ench);

	    foreach($images as $image) {
		    $fichier = md5(uniqid()) . '.' . $image->guessExtension();
		    $image->move(
			    $this->getParameter('images_directory'),
			    $fichier
		    );
		    $img = new Images();
		    $img->setNom($fichier);
		    $article->addImage($img);
	    }
	    $em->persist($article);
	    $em->flush();
	    return $this->handleView
	    ($this->view(['message'=>'article enregistré'], Response::HTTP_CREATED));}
 
 
    /**
     *  creation article
     * @param Request $request
     * @return \FOS\RestBundle\View\View|Response
     * @Route("/article/{article}", name="article_edit", methods={"PUT"})
     */
    public function edit(Request $request, Article $article, ArticleRepository $articleRepository): Response
    {
	    // erreur
	    $art = $this->getDoctrine()->getRepository
	    (Article::class)->find($article);
	    $parameter = json_decode($request->getContent(),true);
	    $art->setTitre($parameter['contexte']);
	    $art->setDescription(['description']);
	    $art->setPrixInitial(['prixInitial']);
	    $art->setCategorie(['categorie']);
	    $em = $this->getDoctrine()->getManager();
	    $em->persist($art);
	    $em->flush();
	    return $this->handleView($this->view(['message'=> 'article Modifie' ], Response::HTTP_CREATED));
	
    }
	
	/** suppression article
	 * @param Request $request
	 * @Rest\Delete("/article/{article}")
	 * @return \FOS\RestBundle\View\View|Response
	 */
    public function delete(Request $request,$article) :Response
    {
	    $art = $this->getDoctrine()->getRepository
	    (Article::class)->find($article);
	    $em = $this->getDoctrine()->getManager();
	    $em->remove($art);
	    $em->flush();
	    return $this->json('Article supprimé');
    }
}
