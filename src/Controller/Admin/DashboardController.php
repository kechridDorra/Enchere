<?php

namespace App\Controller\Admin;

use App\Entity\AppelOffre;
use App\Entity\Categorie;
use App\Entity\Enchere;
use App\Entity\Participation;
use App\Entity\ProfilVendeur;
use App\Entity\Proposition;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
	    return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Pfe Backend');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Appel Offre', 'fas fa-list', AppelOffre::class);
	    yield MenuItem::linkToCrud('Categories', 'fas fa-list', Categorie::class);
        yield MenuItem::linkToCrud('Enchere', 'fas fa-list', Enchere::class);
	    yield MenuItem::linkToCrud('Participation', 'fas fa-list', Participation::class);
	    yield MenuItem::linkToCrud('Profil Vendeur', 'fas fa-list', ProfilVendeur::class);
	    yield MenuItem::linkToCrud('Proposition', 'fas fa-list', Proposition::class);
	    yield MenuItem::linkToCrud('User', 'fas fa-list', User::class);
    }
}